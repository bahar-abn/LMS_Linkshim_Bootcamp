<?php

namespace controllers;

use core\Application;
use core\Request;
use models\User;
use models\Course;
use models\Enrollment;

class AdminController
{
    public function adminDashboard(Request $request)
    {
        if (!Application::$app->isAdmin()) {
            Application::$app->response->redirect(BASE_URL . '/login');
            return;
        }

        $stats = [
            'totalUsers' => User::count(),
            'totalCourses' => Course::count(),
            'totalEnrollments' => Enrollment::count(),
            'latestUsers' => User::latest(5)
        ];

        require_once Application::$ROOT_DIR . '/views/dashboard/admin.php';
    }

    public function instructorDashboard(Request $request)
    {
        if (!Application::$app->isInstructor()) {
            Application::$app->response->redirect(BASE_URL . '/login');
            return;
        }

        $userId = Application::$app->user['id'] ?? null;
        $courses = Course::findByInstructor($userId);

        require_once Application::$ROOT_DIR . '/views/dashboard/instructor.php';
    }

    public function studentDashboard(Request $request)
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