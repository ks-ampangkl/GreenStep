<?php
use Slim\App;
use App\Controllers\ActivityController;
use App\Repositories\ActivityRepository;
use App\Repositories\ChallengeRepository;
use App\Repositories\TipRepository;
use App\Repositories\UserRepository;
use App\Repositories\GoalRepository;
use App\Database;

return function (App $app) {

    // 1. Initialize the shared Controller Factory using a closure routine
    $getController = function(): ActivityController {
        // Fetch the secure global singleton PDO handle
        $pdo = Database::get();

        // Instantiate individual domain repositories injecting the database connection handle
        $activityRepo  = new ActivityRepository($pdo);
        $challengeRepo = new ChallengeRepository($pdo);
        $tipRepo       = new TipRepository($pdo);
        $userRepo      = new UserRepository($pdo);
        $goalRepo      = new GoalRepository($pdo);

        // Inject the complete repository collection cluster straight into the Controller constructor
        return new ActivityController(
            $activityRepo,
            $challengeRepo,
            $tipRepo,
            $userRepo,
            $goalRepo
        );
    };

    /* ---------- CORE SYSTEM DASHBOARD AGGREGATIONS ---------- */
    $app->get('/api/dashboard', function ($request, $response) use ($getController) {
        return $getController()->getDashboardSummary($request, $response);
    });

    /* ---------- CARBON LOGGING ENGINE & TRACKING SYSTEMS ---------- */
    $app->get('/api/activities/types', function ($request, $response) use ($getController) {
        return $getController()->getActivityTypes($request, $response);
    });

    $app->post('/api/activities/log', function ($request, $response) use ($getController) {
        return $getController()->logActivity($request, $response);
    });

    $app->get('/api/activities/today', function ($request, $response) use ($getController) {
        return $getController()->getTodayLogs($request, $response);
    });

    $app->get('/api/activities/history', function ($request, $response) use ($getController) {
        return $getController()->getHistory($request, $response);
    });

    /* ---------- COMMUNITY CHALLENGES TAB INTERACTIONS ---------- */
    $app->get('/api/challenges', function ($request, $response) use ($getController) {
        return $getController()->getChallenges($request, $response);
    });

    $app->post('/api/challenges', function ($request, $response) use ($getController) {
        return $getController()->createChallenge($request, $response);
    });

    /* ---------- INTEGRATED ECO-LIFESTYLE TIPS MATRIX ---------- */
    $app->get('/api/tips', function ($request, $response) use ($getController) {
        return $getController()->getTips($request, $response);
    });

    /* ---------- SYSTEM MANAGEMENT & DEMO ENVIRONMENT RESETS ---------- */
    $app->post('/api/reset', function ($request, $response) use ($getController) {
        return $getController()->reset($request, $response);
    });
};