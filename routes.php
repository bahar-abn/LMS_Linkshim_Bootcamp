<?php

global $app;

use controllers\AuthController;
use controllers\CourseController;
use controllers\AdminController;

// ----------------- Auth Routes -----------------
$app->router->get('/', [AuthController::class, 'login']); // now redirects to /login

$app->router->get('/login', [AuthController::class, 'login']);
$app->router->post('/login', [AuthController::class, 'loginPost']);

$app->router->get('/register', [AuthController::class, 'register']);
$app->router->post('/register', [AuthController::class, 'registerPost']);

$app->router->get('/logout', [AuthController::class, 'logout']);
$app->router->get('/dashboard', [AuthController::class, 'dashboard']);

// ----------------- Course Routes -----------------
$app->router->get('/courses', [CourseController::class, 'index']);
$app->router->get('/create-course', [CourseController::class, 'create']);
$app->router->post('/create-course', [CourseController::class, 'store']);
$app->router->get('/my-courses', [CourseController::class, 'myCourses']);
$app->router->get('/courses/{id}', [CourseController::class, 'details']);
$app->router->post('/courses/{id}/enroll', [CourseController::class, 'enroll']);

// ----------------- Admin Routes -----------------
$app->router->get('/manage-users', [AdminController::class, 'manageUsers']);
$app->router->get('/manage-courses', [AdminController::class, 'manageCourses']);
$app->router->get('/manage-reviews', [AdminController::class, 'manageReviews']);
$app->router->get('/admin-dashboard', [AdminController::class, 'adminDashboard']);
