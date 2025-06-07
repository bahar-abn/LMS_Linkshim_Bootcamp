<?php

namespace controllers;

use core\Application;
use core\Request;
use models\User;
use models\Course;
use models\Enrollment;
use models\Review;  // assuming you have this model

class AdminController
{
    public function adminDashboard(Request $request)
    {
        if (!Application::$app->isAdmin()) {
            Application::$app->response->redirect(BASE_URL . '/login');
            return;
        }

        try {
            // Test DB connection and get total users count (optional)
            $pdo = Application::$app->db->pdo;
            $stmt = $pdo->query("SELECT COUNT(*) AS total_users FROM users");
            $result = $stmt->fetch();
            $totalUsersFromTest = $result['total_users'] ?? 0;

            // Get total reviews count (if no Review model, use raw query)
            $stmtReviews = $pdo->query("SELECT COUNT(*) AS total_reviews FROM reviews");
            $resultReviews = $stmtReviews->fetch();
            $totalReviewsFromTest = $resultReviews['total_reviews'] ?? 0;

            // Get latest 100 reviews from DB (optional, for admin panel listing)
            $stmtLatestReviews = $pdo->query("SELECT * FROM reviews ORDER BY created_at DESC LIMIT 100");
            $latestReviewsFromDb = $stmtLatestReviews->fetchAll(\PDO::FETCH_ASSOC);

        } catch (\PDOException $e) {
            echo "Database connection failed: " . $e->getMessage();
            exit;
        }

        // Prepare stats array with keys matching view expectations
        $stats = [
            'users' => User::count(),
            'courses' => Course::count(),
            'reviews' => Review::count() ?? $totalReviewsFromTest,  // fallback if Review::count() missing
        ];

        // Fetch data arrays for the view
        $users = User::latest(100);        // latest 100 users
        $courses = Course::latest(100);    // latest 100 courses

        // Use Review model or fallback to raw DB results
        if (method_exists(Review::class, 'latest')) {
            $reviews = Review::latest(100);
        } else {
            // convert DB results to objects or pass raw arrays
            $reviews = $latestReviewsFromDb;
        }

        // Logged in admin name for greeting in the view
        $userName = Application::$app->user['name'] ?? 'Admin';

        // Base URL for generating links in the view
        $baseUrl = defined('BASE_URL') ? BASE_URL : '';

        // Load the admin dashboard view
        require_once Application::$ROOT_DIR . '/views/dashboard/admin.php';
    }

    public function studentDashboard(Request $request): void
    {
        if (!Application::$app->isStudent()) {
            Application::$app->response->redirect(BASE_URL . '/login');
            return;
        }

        $userId = Application::$app->user['id'] ?? null;
        $enrollments = Enrollment::findByUser($userId);

        require_once Application::$ROOT_DIR . '/views/dashboard/student.php';
    }

    public function manageUsers(Request $request)
    {
        if (!Application::$app->isAdmin()) {
            Application::$app->response->redirect(BASE_URL . '/login');
            return;
        }

        $users = User::latest(100);
        require_once Application::$ROOT_DIR . '/views/admin/users.php';
    }
}
