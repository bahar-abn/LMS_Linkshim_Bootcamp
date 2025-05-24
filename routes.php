<?php

use controllers\CourseController;
use controllers\AuthController;
use core\Application;

global $app;

// Courses
$app->router->get('/courses', [CourseController::class, 'index']);
$app->router->get('/courses/create', [CourseController::class, 'create']);
$app->router->post('/courses/store', [CourseController::class, 'store']);
$app->router->get('/courses/{id}', [CourseController::class, 'show']);

// Auth
$app->router->get('/', [AuthController::class, 'login']);
$app->router->get('/login', [AuthController::class, 'login']);
$app->router->post('/login', [AuthController::class, 'loginPost']);
$app->router->get('/register', [AuthController::class, 'register']);
$app->router->post('/register', [AuthController::class, 'registerPost']);
$app->router->get('/logout', [AuthController::class, 'logout']);

// Dashboard
$app->router->get('/dashboard', function() {
    echo "Welcome to your dashboard!";
});