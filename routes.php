<?php

global $app;

use controllers\AuthController;
use core\Application;

$app->router->get('/', [AuthController::class, 'login']);
$app->router->get('/login', [AuthController::class, 'login']);
$app->router->post('/login', [AuthController::class, 'loginPost']);

$app->router->get('/register', [AuthController::class, 'register']);
$app->router->post('/register', [AuthController::class, 'registerPost']);

$app->router->get('/logout', [AuthController::class, 'logout']);

$app->router->get('/dashboard', function() {
    // You can replace this with a proper controller method later
    echo "Welcome to your dashboard!";
});
