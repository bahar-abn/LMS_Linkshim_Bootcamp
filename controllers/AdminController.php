<?php

namespace controllers;

use core\Application;
use core\Request;
use models\User;
use models\Course;
use models\Enrollment;
use models\Review;

class AdminController
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = Application::$app->config['BASE_URL'] ?? '/login';
    }

    private function ensureAdmin(): void
    {
        $user = Application::$app->user;
        $role = is_array($user) ? ($user['role'] ?? null) : ($user->role ?? null);

        if ($role !== 'admin') {
            Application::$app->response->redirect($this->baseUrl . '/login');
            exit;
        }
    }

    public function adminDashboard(Request $request): void
    {
        $this->ensureAdmin();

        $users = User::latest(100) ?: [];
        $courses = Course::latest(100) ?: [];
        $reviews = Review::latest(100) ?: [];

        require_once Application::$ROOT_DIR . '/views/dashboard/admin.php';
    }

    public function approveCourse($id): void
    {
        $this->ensureAdmin();

        $course = Course::find($id);
        if ($course) {
            $course->status = 'approved';
            $course->save();
        }

        Application::$app->response->redirect($this->baseUrl . '/admin-dashboard');
    }

    public function rejectCourse($id): void
    {
        $this->ensureAdmin();

        $course = Course::find($id);
        if ($course) {
            $course->status = 'rejected';
            $course->save();
        }

        Application::$app->response->redirect($this->baseUrl . '/admin-dashboard');
    }

    public function deleteUser($id): void
    {
        $this->ensureAdmin();

        $user = User::find($id);
        if ($user && $user->role !== 'admin') {
            $user->delete();
        }

        Application::$app->response->redirect($this->baseUrl . '/admin-dashboard');
    }

    public function deleteReview($id): void
    {
        $this->ensureAdmin();

        $review = Review::find($id);
        if ($review) {
            $review->delete();
        }

        Application::$app->response->redirect($this->baseUrl . '/admin-dashboard');
    }
}
