<?php
global $app;

use controllers\AuthController;
use controllers\CourseController;
use controllers\AdminController;
use controllers\ReviewController;

// Debug current request
error_log("Request: " . $_SERVER['REQUEST_METHOD'] . ' ' . $_SERVER['REQUEST_URI']);

// ----------------- Auth Routes -----------------
$app->router->get('/', [AuthController::class, 'login']);
$app->router->get('/login', [AuthController::class, 'login']);
$app->router->post('/login', [AuthController::class, 'loginPost']);

$app->router->get('/register', [AuthController::class, 'register']);
$app->router->post('/register', [AuthController::class, 'registerPost']);

$app->router->get('/logout', [AuthController::class, 'logout']);
$app->router->get('/dashboard', [AuthController::class, 'dashboard']);

// ----------------- Course Routes -----------------
$app->router->get('/courses', [CourseController::class, 'index']);
$app->router->get('/courses/create', [CourseController::class, 'create']);
$app->router->post('/courses/create', [CourseController::class, 'store']);
$app->router->get('/courses/{id}', [CourseController::class, 'details']);
$app->router->post('/courses/{id}/enroll', [CourseController::class, 'enroll']);
$app->router->get('/courses/{id}/edit', [CourseController::class, 'edit']);
$app->router->post('/courses/{id}/update', [CourseController::class, 'update']);

// ----------------- Instructor Routes -----------------
$app->router->get('/my-courses', [CourseController::class, 'myCourses']);
$app->router->get('/my-course-reviews', [ReviewController::class, 'myCourseReviews']);

// ----------------- Admin Routes -----------------
$app->router->get('/admin-dashboard', [AdminController::class, 'dashboard']);
$app->router->get('/manage-users', [AdminController::class, 'manageUsers']);
$app->router->get('/manage-courses', [AdminController::class, 'manageCourses']);
$app->router->get('/manage-reviews', [AdminController::class, 'manageReviews']);
$app->router->get('/courses/{id}/approve', [AdminController::class, 'approveCourse']);
$app->router->get('/courses/{id}/reject', [AdminController::class, 'rejectCourse']);
$app->router->get('/users/{id}/delete', [AdminController::class, 'deleteUser']);
$app->router->get('/reviews/{id}/delete', [AdminController::class, 'deleteReview']);