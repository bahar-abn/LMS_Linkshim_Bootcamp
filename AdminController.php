<?php

namespace app\controllers;

use app\core\Controller;
use app\core\Application;
use app\models\User;
use app\models\Course;
use app\models\Enrollment;

class AdminController extends Controller
{
    public function dashboard()
    {
        $user = Application::$app->user;

        if (!$user) {
            Application::$app->response->redirect('/login');
            return;
        }

        switch ($user->role) {
            case 'admin':
                $users = User::findAll();
                $courses = Course::findAll();
                return $this->render('dashboard/admin', [
                    'users' => $users,
                    'courses' => $courses
                ]);
            case 'instructor':
                $courses = Course::findWhere(['created_by' => $user->id]);
                return $this->render('dashboard/instructor', [
                    'courses' => $courses
                ]);
            case 'student':
                $enrollments = Enrollment::findWhere(['user_id' => $user->id]);
                return $this->render('dashboard/student', [
                    'enrollments' => $enrollments
                ]);
            default:
                Application::$app->response->redirect('/');
        }
    }
}
