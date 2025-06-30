<?php

global $app;

use controllers\AuthController;
use controllers\CourseController;
use controllers\AdminController;
use controllers\ReviewController;

// ----------------- Auth Routes -----------------
$app->router->get('/', [AuthController::class, 'login']);
$app->router->get('/login', [AuthController::class, 'login']);
$app->router->post('/login', [AuthController::class, 'loginPost']);
$app->router->get('/register', [AuthController::class, 'register']);
$app->router->post('/register', [AuthController::class, 'registerPost']);
$app->router->get('/logout', [AuthController::class, 'logout']);

// ----------------- Dashboard Routes -----------------
$app->router->get('/dashboard', [AuthController::class, 'dashboard']);
$app->router->get('/admin-dashboard', [AdminController::class, 'dashboard']);
$app->router->get('/instructor-dashboard', [CourseController::class, 'instructorDashboard']);

// ----------------- Admin Routes -----------------
$app->router->get('/admin/dashboard', [AdminController::class, 'dashboard']);
$app->router->get('/admin/manage-users', [AdminController::class, 'manageUsers']);
$app->router->get('/admin/manage-courses', [AdminController::class, 'manageCourses']);
$app->router->get('/admin/manage-reviews', [AdminController::class, 'manageReviews']);
$app->router->get('/admin/manage-categories', [AdminController::class, 'manageCategories']);

// Course Approval Routes
$app->router->get('/admin/approve-course/{id}', [AdminController::class, 'approveCourse']);
$app->router->get('/admin/reject-course/{id}', [AdminController::class, 'rejectCourse']);

// Delete Routes
$app->router->get('/admin/delete-user/{id}', [AdminController::class, 'deleteUser']);
$app->router->get('/admin/delete-review/{id}', [AdminController::class, 'deleteReview']);

// ----------------- Course Routes -----------------
$app->router->get('/courses', [CourseController::class, 'index']);
$app->router->get('/courses/create', [CourseController::class, 'create']);
$app->router->post('/courses/create', [CourseController::class, 'store']);
$app->router->get('/courses/{id}', [CourseController::class, 'details']);
$app->router->post('/courses/{id}/enroll', [CourseController::class, 'enroll']);
$app->router->get('/courses/{id}/edit', [CourseController::class, 'edit']);
$app->router->post('/courses/{id}/update', [CourseController::class, 'update']);

// ----------------- Instructor Routes -----------------
$app->router->get('/instructor/my-courses', [CourseController::class, 'instructorCourses']);
$app->router->get('/my-courses', [CourseController::class, 'enrolledCourses']);
$app->router->get('/my-course-reviews', [ReviewController::class, 'myCourseReviews']);

// ----------------- Review Routes -----------------
$app->router->post('/reviews/add', [ReviewController::class, 'storeReview']);
$app->router->get('/my-course-reviews', [ReviewController::class, 'myCourseReviews']);

// ----------------- Test Routes -----------------
$app->router->get('/test-connection', function() {
    require_once __DIR__ . '/../core/TestConnection.php';
});
$app->router->get('/test-db', function() {
    core\TestConnection::run();
});
$app->router->get('/my-courses', [CourseController::class, 'enrolledCourses']);
$app->router->get('/my-courses/{id}', [CourseController::class, 'enrolledCourses']);