<?php
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Controllers\ActivityController;

return function ($app) {

    // 1. Root Handshake Endpoint (Updated for GreenStep Context)
    $app->get('/', function (Request $req, Response $res) { 
        $res->getBody()->write(json_encode([ 
            'name'    => 'GreenStep REST API', 
            'version' => '1.0.0', 
            'status'  => 'Healthy'
        ])); 
        return $res->withHeader('Content-Type', 'application/json; charset=utf-8'); 
    }); 
  
    // 2. Core API Endpoint Collections
    $app->group('/api', function (RouteCollectorProxy $g) { 
        
        // --- DASHBOARD PAGE ---
        // Delivers today's balance, breakdown charts, weekly graph datasets, active tip, and active challenge
        $g->get('/dashboard', [ActivityController::class, 'getDashboardSummary']); 
        
        // --- LOG ACTIVITY PAGE ---
        // Retrieves the 9 operational carbon factor options (e.g., Car - 0.21kg/km)
        $g->get('/activities/types', [ActivityController::class, 'getActivityTypes']); 
        // Posts active metrics (amount driven, date, etc.) to mutate daily footprint stats
        $g->post('/activities/log', [ActivityController::class, 'logActivity']); 
        // Fetches logs explicitly recorded for the current single date context
        $g->get('/activities/today', [ActivityController::class, 'getTodayLogs']); 

        // --- MY HISTORY PAGE ---
        // Returns chronological historically logged entries grouped by calendar day
        $g->get('/activities/history', [ActivityController::class, 'getHistory']); 

        // --- CHALLENGES PAGE ---
        // Gets entries; supports frontend client filter strings via URL queries: ?filter=joined
        $g->get('/challenges', [ActivityController::class, 'getChallenges']); 
        // Registers custom challenges submitted via your "Create a Challenge" form
        $g->post('/challenges', [ActivityController::class, 'createChallenge']); 

        // --- TIP LIBRARY PAGE ---
        // Returns the list of tips; filters seamlessly via category tabs: ?category=Food
        $g->get('/tips', [ActivityController::class, 'getTips']); 

        // --- GLOBAL ADMIN / DEMO RESET ---
        // Restores json files back to the baseline seed data array instantly
        $g->post('/reset', [ActivityController::class, 'reset']); 
    }); 
};