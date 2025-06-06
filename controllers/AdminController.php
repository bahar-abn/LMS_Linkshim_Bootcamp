<?php

namespace controllers;

use core\Application;
use core\Request;
use models\User;
use models\Course;
use models\Enrollment;

class AdminController
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = Application::$app->config['BASE_URL'] ?? '/login';
    }

    private function ensureAdmin()
    {
        // Assuming Application::$app->user is an array with 'role' key OR an object with role property
        $user = Application::$app->user;

        if (!$user) {
            Application::$app->response->redirect($this->baseUrl . '/login');
            exit;
        }

        // Adjust this check based on your user structure:
        $role = is_array($user) ? ($user['role'] ?? null) : ($user->role ?? null);

        if ($role !== 'admin') {
            Application::$app->response->redirect($this->baseUrl . '/login');
            exit;
        }
    }

    public function adminDashboard(Request $request): void
    {
        $this->ensureAdmin();

        $stats = [
            'totalUsers' => User::count() ?: 0,
            'totalCourses' => Course::count() ?: 0,
            'totalEnrollments' => Enrollment::count() ?: 0,
            'latestUsers' => User::latest(5) ?: [],
        ];

        require_once Application::$ROOT_DIR . '/views/dashboard/admin.php';
    }

    public function manageUsers(Request $request): void
    {
        $this->ensureAdmin();

        $users = User::latest(100) ?: [];

        require_once Application::$ROOT_DIR . '/views/admin/users.php';
    }

    public function manageCourses(Request $request): void
    {
        $this->ensureAdmin();

        $courses = Course::latest(100) ?: [];

        require_once Application::$ROOT_DIR . '/views/admin/courses.php';
    }

    public function manageReviews(Request $request): void
    {
        $this->ensureAdmin();

        // Make sure you have a Review model and latest() method:
        $reviews = \models\Review::latest(100) ?: [];

        require_once Application::$ROOT_DIR . '/views/admin/reviews.php';
    }

    // You can add similar methods for instructorDashboard, studentDashboard, etc. with their own role checks
}
