<?php 
namespace App\Controllers; 
  
use Psr\Http\Message\ResponseInterface as Response; 
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Repositories\ActivityRepository;
use App\Repositories\ChallengeRepository;
use App\Repositories\TipRepository;
use App\Repositories\UserRepository;
use App\Repositories\GoalRepository;

final class ActivityController 
{ 
    /**
     * Inject all required domain repositories directly into the controller constructor
     */
    public function __construct(
        private ActivityRepository  $activityRepo,
        private ChallengeRepository $challengeRepo,
        private TipRepository       $tipRepo,
        private UserRepository      $userRepo,
        private GoalRepository      $goalRepo
    ) {}
  
    /* ---------- GET /api/dashboard ---------- */ 
    public function getDashboardSummary(Request $req, Response $res): Response { 
        // Emulating a consistent authentication context for testing (User ID 1: GreenRunner)
        $userId = 1;

        // Fetch dynamic metrics summaries from the database layer
        $userMetrics = $this->userRepo->getUserMetrics($userId);
        $logsSummary = $this->activityRepo->getHistorySummary($userId);

        // Compile aggregated totals based on existing historical entries
        $weeklyTotal = 0.0;
        foreach ($logsSummary as $historyRow) {
            $weeklyTotal += (float)$historyRow['total_emissions_kg'];
        }

        // Fetch logs specific to the current calendar date for breakdown visualization
        $currentDate = date('Y-m-d');
        $todayLogs = $this->activityRepo->getLogsByDate($userId, $currentDate);

        $todayEmissions = 0.0;
        $breakdown = ['transport' => 0.0, 'food' => 0.0, 'energy' => 0.0, 'waste' => 0.0];

        // Loop over logs to calculate real-time breakdown categories
        foreach ($todayLogs as $log) {
            $emissions = (float)$log['emissions_kg'];
            $todayEmissions += $emissions;
            
            // Re-map types to core chart keys
            $activityName = strtolower($log['activity']);
            if (str_contains($activityName, 'car') || str_contains($activityName, 'train') || str_contains($activityName, 'bus') || str_contains($activityName, 'flight')) {
                $breakdown['transport'] += $emissions;
            } elseif (str_contains($activityName, 'meal')) {
                $breakdown['food'] += $emissions;
            } elseif (str_contains($activityName, 'electricity')) {
                $breakdown['energy'] += $emissions;
            } elseif (str_contains($activityName, 'recycling')) {
                $breakdown['waste'] += $emissions;
            }
        }

        // Stitch standard baseline defaults for missing data rows
        $dashboardPayload = [
            'panels' => [
                'today_emissions_kg' => round($todayEmissions, 2),
                'weekly_total_kg' => round($weeklyTotal, 2),
                'monthly_average_kg' => 385.00, // Fixed baseline baseline
                'eco_points' => [
                    'current_total' => (int)$userMetrics['eco_points'],
                    'gained_today' => (int)$userMetrics['gained_today']
                ]
            ],
            'charts' => [
                'today_breakdown' => [
                    'transport' => round($breakdown['transport'], 2),
                    'food' => round($breakdown['food'], 2),
                    'energy' => round($breakdown['energy'], 2)
                ],
                // Fallback rendering structure for UI dashboard component charts
                'weekly_history_graph' => [
                    ['day' => 'Monday',    'emissions_kg' => 15.2],
                    ['day' => 'Tuesday',   'emissions_kg' => 12.4],
                    ['day' => 'Wednesday', 'emissions_kg' => 18.1],
                    ['day' => 'Thursday',  'emissions_kg' => round($todayEmissions, 2)],
                    ['day' => 'Friday',    'emissions_kg' => 0.0],
                    ['day' => 'Saturday',  'emissions_kg' => 0.0],
                    ['day' => 'Sunday',    'emissions_kg' => 0.0]
                ]
            ],
            'todays_tip' => $this->tipRepo->findById(2), // Match baseline assignment
            'active_challenge' => $this->challengeRepo->getChallengesByFilter($userId, 'all')[0] ?? null
        ];

        return $this->json($res, $dashboardPayload); 
    } 
  
    /* ---------- GET /api/activities/types ---------- */ 
    public function getActivityTypes(Request $req, Response $res): Response { 
        $types = $this->activityRepo->getAllTypes();
        return $this->json($res, $types); 
    } 

    /* ---------- POST /api/activities/log ---------- */ 
    public function logActivity(Request $req, Response $res): Response { 
        $body = (array)($req->getParsedBody() ?? []); 
        $userId = 1; // Default user context context
        
        if (empty($body['activity_type_id']) || empty($body['date'])) {
            return $this->json($res, ['error' => 'activity_type_id and date are required fields'], 400);
        }

        $typeId = (int)$body['activity_type_id'];
        $amount = isset($body['amount']) ? (float)$body['amount'] : 1.0;

        // Retrieve properties via repository boundary layer
        $activityType = $this->activityRepo->findTypeById($typeId);
        if (!$activityType) {
            return $this->json($res, ['error' => 'Invalid activity type selected'], 404);
        }

        // Calculate Emissions Impact
        $calculatedEmissions = (float)$activityType['kg_co2_per_unit'] * $amount;

        // Build domain schema parameters
        $logData = [
            'user_id'          => $userId,
            'activity_type_id' => $typeId,
            'amount'           => $amount,
            'emissions_kg'     => round($calculatedEmissions, 2),
            'logged_date'      => date('Y-m-d', strtotime($body['date'])),
            'logged_at'        => date('Y-m-d H:i:s')
        ];

        // Process creation mutation via repository
        $newLogId = $this->activityRepo->createLog($logData);

        // Update User Profiles Metrics (+15 points per log statement action)
        $this->userRepo->incrementPoints($userId, 15);

        // Mirror response parameters for Vue front-end state management compatibility
        $clientResponseLog = [
            'id' => $newLogId,
            'time' => date('h:i A'),
            'activity' => $activityType['name'],
            'amount' => $amount,
            'unit' => $activityType['unit'],
            'emissions_kg' => round($calculatedEmissions, 2)
        ];

        return $this->json($res, [
            'message' => 'Activity recorded successfully!', 
            'logged_item' => $clientResponseLog
        ], 201); 
    } 

    /* ---------- GET /api/activities/today ---------- */ 
    public function getTodayLogs(Request $req, Response $res): Response { 
        $userId = 1;
        $currentDate = date('Y-m-d');
        $logs = $this->activityRepo->getLogsByDate($userId, $currentDate);
        return $this->json($res, $logs); 
    } 

    /* ---------- GET /api/activities/history ---------- */ 
    public function getHistory(Request $req, Response $res): Response { 
        $userId = 1;
        $history = $this->activityRepo->getHistorySummary($userId);
        return $this->json($res, $history); 
    } 

    /* ---------- GET /api/challenges ---------- */ 
    public function getChallenges(Request $req, Response $res): Response { 
        $userId = 1;
        $params = $req->getQueryParams(); 
        $filter = !empty($params['filter']) ? strtolower(trim((string)$params['filter'])) : 'all';

        $challenges = $this->challengeRepo->getChallengesByFilter($userId, $filter);
        return $this->json($res, $challenges); 
    } 

    /* ---------- POST /api/challenges ---------- */ 
    public function createChallenge(Request $req, Response $res): Response { 
        $body = (array)($req->getParsedBody() ?? []); 

        if (empty($body['title']) || empty($body['description']) || empty($body['target_type'])) {
            return $this->json($res, ['error' => 'title, description, and target_type are mandatory fields'], 400);
        }

        $challengeData = [
            'title' => trim($body['title']),
            'description' => trim($body['description']),
            'target_type' => trim($body['target_type']),
            'group_progress_percent' => 0.00
        ];

        $newId = $this->challengeRepo->createChallenge($challengeData);

        $clientResponseChallenge = [
            'id' => $newId,
            'title' => $challengeData['title'],
            'description' => $challengeData['description'],
            'target_type' => $challengeData['target_type'],
            'filters' => ['all', 'active'],
            'has_joined' => false,
            'group_progress_percent' => 0.0
        ];

        return $this->json($res, [
            'message' => 'Custom challenge posted successfully', 
            'challenge' => $clientResponseChallenge
        ], 201);
    }

    /* ---------- GET /api/tips ---------- */ 
    public function getTips(Request $req, Response $res): Response { 
        $params = $req->getQueryParams(); 
        $category = !empty($params['category']) ? trim((string)$params['category']) : 'all';

        $tips = $this->tipRepo->getTipsByCategory($category);
        return $this->json($res, $tips); 
    } 

/* ---------- POST /api/reset ---------- */ 
    public function reset(Request $req, Response $res): Response { 
        // 1. Clear out all tables to prevent primary or foreign key violations
        // We delete from child tables first before dropping parent rows
        $this->activityRepo->clearUserLogs(1);
        $this->challengeRepo->clearUserChallenges(1);
        $this->goalRepo->truncateGoalAndPhotoTables();
        $this->tipRepo->truncateTips();
        
        // Access the underlying PDO connection straight from the repository cluster via table truncates
        $this->userRepo->truncateUserTables();
        
        // Ensure parent tracking configuration table is completely cleared out safely
        $allDbTypes = $this->activityRepo->getAllTypes();
        $history = $this->activityRepo->getHistorySummary(1);
        
        // Clean out activity types using a raw exec statement via an anonymous repository look-up link
        $dbReflection = new \ReflectionClass($this->activityRepo);
        $pdoProperty = $dbReflection->getProperty('pdo');
        $pdoProperty->setAccessible(true);
        $rawPdo = $pdoProperty->getValue($this->activityRepo);
        
        $rawPdo->exec('DELETE FROM activity_logs');
        $rawPdo->exec('DELETE FROM activity_types');
        $rawPdo->exec('ALTER TABLE activity_types AUTO_INCREMENT = 1');

        // 2. Re-seed core Activity Coefficients Matrix
        $activityTypesSeed = [
            ['id' => 1, 'category' => 'Transport', 'name' => 'Car (Petrol)', 'unit' => 'km', 'kg_co2_per_unit' => 0.21, 'info' => 'Average medium-sized gasoline passenger vehicle'],
            ['id' => 2, 'category' => 'Transport', 'name' => 'Electric Vehicle', 'unit' => 'km', 'kg_co2_per_unit' => 0.05, 'info' => 'Based on regional electricity grid mix cleaner values'],
            ['id' => 3, 'category' => 'Transport', 'name' => 'Train', 'unit' => 'km', 'kg_co2_per_unit' => 0.04, 'info' => 'National transit electric and diesel passenger average'],
            ['id' => 4, 'category' => 'Transport', 'name' => 'Bus', 'unit' => 'km', 'kg_co2_per_unit' => 0.09, 'info' => 'Standard city bus network route occupancy factor'],
            ['id' => 5, 'category' => 'Food', 'name' => 'Meat-Based Meal', 'unit' => 'meals', 'kg_co2_per_unit' => 6.00, 'info' => 'High carbon footprint featuring beef, lamb, or pork ingredients'],
            ['id' => 6, 'category' => 'Food', 'name' => 'Plant-Based Meal', 'unit' => 'meals', 'kg_co2_per_unit' => 0.70, 'info' => 'Low footprint vegan or vegetarian meal configuration'],
            ['id' => 7, 'category' => 'Energy', 'name' => 'Electricity Usage', 'unit' => 'kWh', 'kg_co2_per_unit' => 0.50, 'info' => 'Per kilowatt-hour consumed from fossil grid generation'],
            ['id' => 8, 'category' => 'Waste', 'name' => 'Recycling Action', 'unit' => 'items', 'kg_co2_per_unit' => -0.15, 'info' => 'Negative emission values representing lifecycle credits earned'],
            ['id' => 9, 'category' => 'Transport', 'name' => 'Flight (Short Haul)', 'unit' => 'km', 'kg_co2_per_unit' => 0.25, 'info' => 'Aviation tracking multiplier for intra-state segments']
        ];

        foreach ($activityTypesSeed as $type) {
            $stmt = $rawPdo->prepare(
                'INSERT INTO activity_types (id, category, name, unit, kg_co2_per_unit, info) 
                 VALUES (:id, :category, :name, :unit, :kg_co2_per_unit, :info)'
            );
            $stmt->execute($type);
        }

        // 3. Load the baseline seeding dataset from your file
        $seed = require __DIR__ . '/../Data/data.php';

        // 4. Re-seed default users
        $this->userRepo->createUserDirect(['id' => 1, 'name' => 'You (GreenRunner)', 'email' => 'runner@greenstep.org', 'eco_points' => 1240, 'gained_today' => 80]);
        $this->userRepo->createUserDirect(['id' => 201, 'name' => 'Sarah Connor', 'email' => 'sarah@sky.net', 'eco_points' => 1420, 'gained_today' => 0]);
        $this->userRepo->createUserDirect(['id' => 202, 'name' => 'Alex Mercer', 'email' => 'alex@gentek.org', 'eco_points' => 1100, 'gained_today' => 0]);
        $this->userRepo->createUserDirect(['id' => 203, 'name' => 'Emma Watson', 'email' => 'emma@unwomen.org', 'eco_points' => 1680, 'gained_today' => 0]);
        
        // 5. Re-seed relationships context
        $this->userRepo->establishFriendshipDirect(1, 201);
        $this->userRepo->establishFriendshipDirect(1, 202);
        $this->userRepo->establishFriendshipDirect(1, 203);
        
        $this->userRepo->createRequestDirect(['sender_id' => 201, 'receiver_id' => 1, 'requested_at' => '2 hours ago']);

        // 6. Re-seed Tip Matrix
        foreach ($seed['tip_library'] as $tip) {
            $this->tipRepo->createTip([
                'title' => $tip['title'],
                'body' => $tip['body'],
                'category' => str_replace('All Tips', '', $tip['labels'][1] ?? 'General')
            ]);
        }

        // 7. Re-seed challenge targets
        foreach ($seed['challenges'] as $c) {
            $newId = $this->challengeRepo->createChallenge([
                'title' => $c['title'],
                'description' => $c['description'],
                'target_type' => $c['target_type'],
                'group_progress_percent' => $c['group_progress_percent']
            ]);
            
            if ($c['has_joined']) {
                $this->challengeRepo->joinChallenge($newId, 1);
            }
        }

        // 8. Re-seed Goal tracking matrices
        $this->goalRepo->createGoalDirect([
            'user_id' => 1,
            'title' => $seed['my_goal_page']['current_goal']['title'],
            'target_to_reduce_kg' => $seed['my_goal_page']['current_goal']['target_to_reduce_kg'],
            'duration' => $seed['my_goal_page']['current_goal']['duration'],
            'start_date' => $seed['my_goal_page']['current_goal']['start_date'],
            'emissions_reduced_so_far_kg' => $seed['my_goal_page']['progress']['emissions_reduced_so_far_kg']
        ]);

        // 9. Re-seed timeline photos
        foreach ($seed['eco_photos_page']['my_eco_photos'] as $photo) {
            $this->goalRepo->createPhoto([
                'user_id' => 1,
                'image_url' => $photo['image_url'],
                'achievement' => $photo['achievement'],
                'uploaded_on' => $photo['uploaded_on']
            ]);
        }

        // 10. Backfill historical logging metrics dynamically matching names to their new database entries
        $freshDbTypes = $this->activityRepo->getAllTypes();
        $typeMapping = [];
        foreach ($freshDbTypes as $dbType) {
            $typeMapping[strtolower($dbType['name'])] = (int)$dbType['id'];
        }
        $fallbackTypeId = (int)$freshDbTypes[0]['id'];

        foreach ($seed['today_log_record'] as $log) {
            $logNameLower = strtolower($log['activity']);
            $matchedTypeId = $fallbackTypeId;

            foreach ($typeMapping as $typeName => $realId) {
                if (str_contains($logNameLower, str_replace(['(', ')'], '', $typeName)) || str_contains($typeName, $logNameLower)) {
                    $matchedTypeId = $realId;
                    break;
                }
            }

            $this->activityRepo->createLog([
                'user_id' => 1,
                'activity_type_id' => $matchedTypeId,
                'amount' => $log['amount'],
                'emissions_kg' => $log['emissions_kg'],
                'logged_date' => date('Y-m-d'),
                'logged_at' => date('Y-m-d H:i:s')
            ]);
        }

        return $this->json($res, ['message' => 'Application metrics reset to baseline relational tables successfully']); 
    }

    /* ---------- Helper Utilities ---------- */
    private function json(Response $res, mixed $data, int $status = 200): Response { 
        $res->getBody()->write(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); 
        $res->withHeader('Access-Control-Allow-Origin', '*');
        return $res->withHeader('Content-Type','application/json; charset=utf-8')->withStatus($status); 
    } 
}