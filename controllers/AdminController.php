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

    public function dashboard(): void
    {
        $this->ensureAdmin();

        $userName = Application::$app->user->name ?? 'Admin';

        $users = User::getAll();                // All users
        $courses = Course::getAll();            // All courses, not just admin's
        $reviews = Review::getAllWithDetails(); // All reviews with JOINs

        $this->render('dashboard/admin', [
            'userName' => $userName,
            'users' => $users,
            'courses' => $courses,
            'reviews' => $reviews
        ]);
    }


    public function approveCourse(Request $request, $id): void
    {
        $this->ensureAdmin();

        $course = Course::find($id);
        if ($course) {
            $course->status = 'approved';
            $course->save();
            Application::$app->session->setFlash('success', 'Course approved successfully');
        }

        Application::$app->response->redirect($this->baseUrl . '/admin-dashboard');
    }

    public function rejectCourse(Request $request, $id): void
    {
        $this->ensureAdmin();

        $course = Course::find($id);
        if ($course) {
            $course->status = 'rejected';
            $course->save();
            Application::$app->session->setFlash('warning', 'Course rejected');
        }

        Application::$app->response->redirect($this->baseUrl . '/admin-dashboard');
    }

    public function deleteUser(Request $request, $id): void
    {
        $this->ensureAdmin();

        $user = User::find($id);
        if ($user && $user->role !== 'admin') {
            $user->delete();
            Application::$app->session->setFlash('success', 'User deleted successfully');
        } else {
            Application::$app->session->setFlash('error', 'Cannot delete admin user');
        }

        Application::$app->response->redirect($this->baseUrl . '/admin-dashboard');
    }

    public function deleteReview(Request $request, $id): void
    {
        $this->ensureAdmin();

        $review = Review::find($id);
        if ($review) {
            $review->delete();
            Application::$app->session->setFlash('success', 'Review deleted successfully');
        }

        Application::$app->response->redirect($this->baseUrl . '/admin-dashboard');
    }
}