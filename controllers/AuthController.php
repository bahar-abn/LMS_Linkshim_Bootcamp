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

        $_SESSION['user'] = $user['email'];
        $_SESSION['login_success'] = 'با موفقیت وارد شدید.';
        Application::$app->response->redirect(BASE_URL . '/dashboard');
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
            $_SESSION['user'] = $user->email;
            Application::$app->response->redirect(BASE_URL . '/dashboard');
        } else {
            $_SESSION['register_error'] = 'Registration failed. Email may already exist.';
            Application::$app->response->redirect(BASE_URL . '/register');
        }
    }
    public function logout(): void
    {
        session_destroy();
        Application::$app->response->redirect(BASE_URL . '/dashboard');
    }
    // Add to AuthController.php
    public function testDb() {
        try {
            // Test connection
            $status = Application::$app->db->pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS);
            echo "Connection: $status<br>";

            // Test query
            $stmt = Application::$app->db->pdo->query("SELECT COUNT(*) FROM users");
            $count = $stmt->fetchColumn();
            echo "Users count: $count<br>";

            // Test insert
            $testEmail = 'test_' . time() . '@example.com';
            $stmt = Application::$app->db->pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute(['Test User', $testEmail, password_hash('test123', PASSWORD_DEFAULT)]);
            echo "Inserted test user: $testEmail";

        } catch (PDOException $e) {
            echo "ERROR: " . $e->getMessage();
        }
    }
}
