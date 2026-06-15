<?php 
namespace App\Controllers; 
  
use Psr\Http\Message\ResponseInterface as Response; 
use Psr\Http\Message\ServerRequestInterface as Request; 

final class ActivityController 
{ 
    private static array $store = []; 
    private static bool  $loaded = false; 
  
    /* ---------- JSON File Persistence Layer ---------- */ 
    private static function storeFile(): string { 
        $dir = __DIR__ . '/../../var'; 
        if (!is_dir($dir)) @mkdir($dir, 0777, true); 
        return $dir . DIRECTORY_SEPARATOR . 'greenstep_data.json'; 
    } 

    private static function load(): void { 
        if (self::$loaded) return; 
        $file = self::storeFile(); 
        if (is_file($file)) { 
            $data = json_decode((string)@file_get_contents($file), true); 
            if (is_array($data)) { 
                self::$store = $data; 
                self::$loaded = true; 
                return; 
            } 
        } 
        // First run — seed from your new customized src/Data/data.php
        self::$store  = require __DIR__ . '/../Data/data.php'; 
        self::$loaded = true; 
        self::save(); 
    } 

    private static function save(): void { 
        @file_put_contents( 
            self::storeFile(), 
            json_encode(self::$store, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), 
            LOCK_EX 
        ); 
    } 
  
    /* ---------- GET /api/dashboard ---------- */ 
    public function getDashboardSummary(Request $req, Response $res): Response { 
        self::load(); 
        
        // Dynamic stitching: Grab referenced components directly out of the seed pools
        $tipId = self::$store['dashboard']['referenced_today_tip_id'];
        $challengeId = self::$store['dashboard']['referenced_active_challenge_id'];

        $todayTip = array_filter(self::$store['tip_library'], fn($t) => $t['id'] === $tipId);
        $activeChallenge = array_filter(self::$store['challenges'], fn($c) => $c['id'] === $challengeId);

        $dashboardPayload = [
            'panels' => self::$store['dashboard']['panels'],
            'charts' => self::$store['dashboard']['charts'],
            'todays_tip' => array_values($todayTip)[0] ?? null,
            'active_challenge' => array_values($activeChallenge)[0] ?? null
        ];

        return $this->json($res, $dashboardPayload); 
    } 
  
    /* ---------- GET /api/activities/types ---------- */ 
    public function getActivityTypes(Request $req, Response $res): Response { 
        self::load(); 
        return $this->json($res, self::$store['activity_types']); 
    } 

    /* ---------- POST /api/activities/log ---------- */ 
    public function logActivity(Request $req, Response $res): Response { 
        self::load(); 
        $body = (array)($req->getParsedBody() ?? []); 
        
        // Validation rules
        if (empty($body['activity_type_id']) || empty($body['date'])) {
            return $this->json($res, ['error' => 'activity_type_id and date are required fields'], 400);
        }

        $typeId = (int)$body['activity_type_id'];
        $amount = isset($body['amount']) ? (float)$body['amount'] : 1.0;

        // Locating the respective operational configuration logic
        $activityType = null;
        foreach (self::$store['activity_types'] as $type) {
            if ($type['id'] === $typeId) {
                $activityType = $type;
                break;
            }
        }

        if (!$activityType) {
            return $this->json($res, ['error' => 'Invalid activity type selected'], 404);
        }

        // Calculate Emissions Impact (Value can be negative for recycling actions)
        $calculatedEmissions = $activityType['kg_co2_per_unit'] * $amount;

        // Generate Log Record
        $newLogId = max(array_column(self::$store['today_log_record'], 'id') ?: [0]) + 1;
        $newLog = [
            'id' => $newLogId,
            'time' => date('h:i A'),
            'activity' => $activityType['name'],
            'amount' => $amount,
            'unit' => $activityType['unit'],
            'emissions_kg' => round($calculatedEmissions, 2)
        ];

        // Push to running tables
        self::$store['today_log_record'][] = $newLog;

        // Update real-time state metrics on Dashboard
        self::$store['dashboard']['panels']['today_emissions_kg'] += $calculatedEmissions;
        self::$store['dashboard']['panels']['weekly_total_kg'] += $calculatedEmissions;
        
        // Reward Eco-Points (+15 points per recorded action, +40 handled via uploads)
        self::$store['dashboard']['panels']['eco_points']['current_total'] += 15;
        self::$store['dashboard']['panels']['eco_points']['gained_today'] += 15;

        // Update matching categories inside Dashboard Charts
        $categoryKey = strtolower($activityType['category']);
        if (isset(self::$store['dashboard']['charts']['today_breakdown'][$categoryKey])) {
            self::$store['dashboard']['charts']['today_breakdown'][$categoryKey] += $calculatedEmissions;
        }

        self::save(); 
        return $this->json($res, ['message' => 'Activity recorded successfully!', 'logged_item' => $newLog], 201); 
    } 

    /* ---------- GET /api/activities/today ---------- */ 
    public function getTodayLogs(Request $req, Response $res): Response { 
        self::load(); 
        return $this->json($res, self::$store['today_log_record']); 
    } 

    /* ---------- GET /api/activities/history ---------- */ 
    public function getHistory(Request $req, Response $res): Response { 
        self::load(); 
        return $this->json($res, self::$store['my_history']); 
    } 

    /* ---------- GET /api/challenges ---------- */ 
    public function getChallenges(Request $req, Response $res): Response { 
        self::load(); 
        $params = $req->getQueryParams(); 
        $items = self::$store['challenges']; 
        
        // Filter functionality for: all, joined, active, completed
        if (!empty($params['filter'])) { 
            $filter = strtolower(trim((string)$params['filter'])); 
            if ($filter !== 'all') {
                $items = array_values(array_filter($items, fn($c) => in_array($filter, $c['filters']))); 
            }
        } 
        return $this->json($res, $items); 
    } 

    /* ---------- POST /api/challenges ---------- */ 
    public function createChallenge(Request $req, Response $res): Response { 
        self::load(); 
        $body = (array)($req->getParsedBody() ?? []); 

        if (empty($body['title']) || empty($body['description']) || empty($body['target_type'])) {
            return $this->json($res, ['error' => 'title, description, and target_type are mandatory fields'], 400);
        }

        $newId = max(array_column(self::$store['challenges'], 'id') ?: [0]) + 1;
        $newChallenge = [
            'id' => $newId,
            'title' => trim($body['title']),
            'description' => trim($body['description']),
            'target_type' => trim($body['target_type']),
            'filters' => ['all', 'active'],
            'has_joined' => false,
            'group_progress_percent' => 0.0
        ];

        self::$store['challenges'][] = $newChallenge;
        self::save();

        return $this->json($res, ['message' => 'Custom challenge posted successfully', 'challenge' => $newChallenge], 201);
    }

    /* ---------- GET /api/tips ---------- */ 
    public function getTips(Request $req, Response $res): Response { 
        self::load(); 
        $params = $req->getQueryParams(); 
        $items = self::$store['tip_library']; 

        // Filter functionality for individual tabs (Transport, Food, Energy, Waste)
        if (!empty($params['category'])) { 
            $cat = strtolower(trim((string)$params['category'])); 
            $items = array_values(array_filter($items, function($tip) use ($cat) {
                foreach ($tip['labels'] as $label) {
                    if (strtolower($label) === $cat) return true;
                }
                return false;
            }));
        } 
        return $this->json($res, $items); 
    }

    /* ---------- POST /api/reset ---------- */ 
    public function reset(Request $req, Response $res): Response { 
        self::$store  = require __DIR__ . '/../Data/data.php'; 
        self::$loaded = true; 
        self::save(); 
        return $this->json($res, ['message' => 'Application metrics reset to baseline arrays successfully']); 
    }

    /* ---------- Helper Utilities ---------- */
    private function json(Response $res, mixed $data, int $status = 200): Response { 
        $res->getBody()->write(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); 
        return $res->withHeader('Content-Type','application/json; charset=utf-8')->withStatus($status); 
    } 
}