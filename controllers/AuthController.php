<?php

namespace controllers;

use core\Application;
use core\Request;
use models\User;

class AuthController {
    public function login(Request $request): void
    {
        require_once Application::$ROOT_DIR . '/views/auth/login.php';
    }

    public function register(Request $request) {
        $error = $_SESSION['register_error'] ?? null;
        $success = $_SESSION['register_success'] ?? null;

        unset($_SESSION['register_error'], $_SESSION['register_success']);

        require_once Application::$ROOT_DIR . '/views/auth/register.php';
    }

    public function loginPost(Request $request): void
    {
        $data = $request->getBody();
        $user = User::findByEmail($data['email'] ?? '');

        if (!$user || !password_verify($data['password'], $user['password'])) {
            $_SESSION['login_error'] = 'ایمیل یا رمز عبور نادرست است.';
            Application::$app->response->redirect(BASE_URL . '/login');
            return;
        }

        $_SESSION['user'] = $user;
        $_SESSION['login_success'] = 'با موفقیت وارد شدید.';

        switch ($user['role']) {
            case 'admin':
                Application::$app->response->redirect(BASE_URL . '/admin/dashboard');
                break;
            case 'instructor':
                Application::$app->response->redirect(BASE_URL . '/instructor/dashboard');
                break;
            default:
                Application::$app->response->redirect(BASE_URL . '/student/dashboard');
        }
    }

    public function registerPost(Request $request) {
        $user = new User();
        $user->loadData($request->getBody());

        if (!$user->validate()) {
            $_SESSION['register_error'] = 'Invalid registration data';
            Application::$app->response->redirect(BASE_URL . '/register');
            return;
        }

        if ($user->save()) {
            $_SESSION['user'] = [
                'id' => Application::$app->db->pdo->lastInsertId(),
                'email' => $user->email,
                'role' => $user->role
            ];
            Application::$app->response->redirect(BASE_URL . '/dashboard');
        } else {
            $_SESSION['register_error'] = 'Registration failed. Email may already exist.';
            Application::$app->response->redirect(BASE_URL . '/register');
        }
    }

    public function logout(): void
    {
        session_destroy();
        Application::$app->response->redirect(BASE_URL . '/login');
    }
}