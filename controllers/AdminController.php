<?php

namespace controllers;

use core\Application;
use core\MainController;
use core\Request;
use models\User;
use models\Course;
use models\Review;

class AdminController extends MainController
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

    private function redirectToDashboardWithMessage(string $type, string $message): void
    {
        Application::$app->session->setFlash($type, $message);
        Application::$app->response->redirect($this->baseUrl . '/admin-dashboard');
        exit;
    }

    public function dashboard(): void
    {
        $this->ensureAdmin();

        $userName = Application::$app->user->name ?? 'Admin';

        $users = User::getAll() ?? [];
        $courses = Course::getAll() ?? [];
        $reviews = Review::getAll() ?? [];

        $stats = [
            'users' => count($users),
            'courses' => count($courses),
            'reviews' => count($reviews),
        ];

        $this->render('dashboard/admin', [
            'userName' => $userName,
            'users' => $users,
            'courses' => $courses,
            'reviews' => $reviews,
            'stats' => $stats
        ]);
    }

    public function approveCourse(Request $request, $id): void
    {
        $this->ensureAdmin();

        $course = Course::find($id);
        if (!$course) {
            $this->redirectToDashboardWithMessage('error', 'Course not found');
        }

        $course->status = 'approved';
        $course->save();

        $this->redirectToDashboardWithMessage('success', 'Course approved successfully');
    }

    public function rejectCourse(Request $request, $id): void
    {
        $this->ensureAdmin();

        $course = Course::find($id);
        if (!$course) {
            $this->redirectToDashboardWithMessage('error', 'Course not found');
        }

        $course->status = 'rejected';
        $course->save();

        $this->redirectToDashboardWithMessage('warning', 'Course rejected');
    }

    public function deleteUser(Request $request, $id): void
    {
        $this->ensureAdmin();

        $user = User::find($id);
        if (!$user) {
            $this->redirectToDashboardWithMessage('error', 'User not found');
        }

        if ($user->role === 'admin') {
            $this->redirectToDashboardWithMessage('error', 'Cannot delete admin user');
        }

        $user->delete();
        $this->redirectToDashboardWithMessage('success', 'User deleted successfully');
    }

    public function deleteReview(Request $request, $id): void
    {
        $this->ensureAdmin();

        $review = Review::find($id);
        if (!$review) {
            $this->redirectToDashboardWithMessage('error', 'Review not found');
        }

        $review->delete();
        $this->redirectToDashboardWithMessage('success', 'Review deleted successfully');
    }
}
