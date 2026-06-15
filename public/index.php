<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

// 1. Load Composer Autoloader
require __DIR__ . '/../vendor/autoload.php';

// 2. Instantiate Slim App
$app = AppFactory::create();

// 3. Explicitly load middleware files manually to bypass Composer autoloader bugs
require_once __DIR__ . '/../src/Middleware/JsonBodyParser.php';
require_once __DIR__ . '/../src/Middleware/Cors.php';

// 4. Instantiate and attach middleware class objects explicitly
$app->add(new \App\Middleware\JsonBodyParser());
$app->add(new \App\Middleware\Cors());

// 5. Add Built-in Slim Routing Middleware
$app->addRoutingMiddleware();

// 6. Global Error Handling Middleware
$app->addErrorMiddleware(true, true, true);

// 7. Load Modular Application Routes
$routes = require __DIR__ . '/../src/routes.php';
$routes($app);

// 8. Execute Application Lifecycle
$app->run();