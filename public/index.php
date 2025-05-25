<html>
<head>
    <link href="../src/input.css" rel="stylesheet">
</head>
</html>
<?php

use controllers\AuthController;
$config = require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../vendor/autoload.php';

$app = new \core\Application(dirname(__DIR__), $config);

// Register routes
$app->router->get('/', [AuthController::class, 'login']);
$app->router->get('/login', [AuthController::class, 'login']);
$app->router->post('/login', [AuthController::class, 'loginPost']);
$app->router->get('/register', [AuthController::class, 'register']);
$app->router->post('/register', [AuthController::class, 'registerPost']);
$app->router->get('/logout', [AuthController::class, 'logout']);

$app->router->get('/dashboard', function () {
    echo "Welcome to the dashboard!";
});

$app->run();
