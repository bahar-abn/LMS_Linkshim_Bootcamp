<?php

namespace controllers;

use core\Application;
use core\Request;
use models\User;
use PDO;
use PDOException;

class AuthController {
    // 👇 New method to handle root `/` route
    public function home(): void {
        if (isset($_SESSION['user'])) {
            Application::$app->response->redirect(BASE_URL . '/login');
        } else {
            Application::$app->response->redirect(BASE_URL . '/login');
        }
    }


    public function login(Request $request): void {
        require_once Application::$ROOT_DIR . '/views/auth/login.php';
    }

    public function register(Request $request): void {
        $error = $_SESSION['register_error'] ?? null;
        $success = $_SESSION['register_success'] ?? null;
        unset($_SESSION['register_error'], $_SESSION['register_success']);
        require_once Application::$ROOT_DIR . '/views/auth/register.php';
    }

    public function loginPost(Request $request)
    {
        $data = $request->getBody();
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        if (!$email || !$password) {
            $_SESSION['login_error'] = 'Email and password are required.';
            Application::$app->response->redirect(BASE_URL . '/login');
            return;
        }

        // Fetch user
        $stmt = Application::$app->db->pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['login_error'] = 'Invalid email or password.';
            Application::$app->response->redirect(BASE_URL . '/login');
            return;
        }

        // Store user in session
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];
        $_SESSION['user_role'] = $user['role']; // Set user role for dashboard redirection
        Application::$app->user = new \models\User($user);

        // Redirect based on role
        Application::$app->response->redirect(BASE_URL . '/dashboard');
    }

    public function registerPost(Request $request): void {
        $user = new User();
        $data = $request->getBody();

        // Ensure 'role' is set from form
        $user->loadData($data);
        $user->role = $data['role'] ?? 'student'; // Default to student if not provided

        if (!$user->validate()) {
            $_SESSION['register_error'] = 'Invalid registration data';
            Application::$app->response->redirect(BASE_URL . '/register');
            return;
        }

        if ($user->save()) {
            $_SESSION['user_email'] = $user->email;
            $_SESSION['user_name'] = $user->name;
            $_SESSION['user_role'] = $user->role;

            Application::$app->response->redirect(BASE_URL . '/dashboard');
        } else {
            $_SESSION['register_error'] = 'Registration failed. Email may already exist.';
            Application::$app->response->redirect(BASE_URL . '/register');
        }
    }

    public function dashboard(): void {
        if (!isset($_SESSION['user_role'])) {
            Application::$app->response->redirect(BASE_URL . '/login');
            return;
        }

        $role = $_SESSION['user_role'];

        switch ($role) {
            case 'admin':
                require_once Application::$ROOT_DIR . '/views/dashboard/admin.php';
                break;
            case 'instructor':
                require_once Application::$ROOT_DIR . '/views/dashboard/instructor.php';
                break;
            case 'student':
                require_once Application::$ROOT_DIR . '/views/dashboard/student.php';
                break;
            default:
                echo "Unknown role: " . htmlspecialchars($role);
        }
    }

    public function logout(): void {
        session_destroy();
        Application::$app->response->redirect(BASE_URL . '/login');
    }

    public function testDb(): void {
        try {
            $status = Application::$app->db->pdo->getAttribute(\PDO::ATTR_CONNECTION_STATUS);
            echo "Connection: $status<br>";

            $stmt = Application::$app->db->pdo->query("SELECT COUNT(*) FROM users");
            $count = $stmt->fetchColumn();
            echo "Users count: $count<br>";

            $testEmail = 'test_' . time() . '@example.com';
            $stmt = Application::$app->db->pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute(['Test User', $testEmail, password_hash('test123', PASSWORD_DEFAULT), 'student']);
            echo "Inserted test user: $testEmail";
        } catch (PDOException $e) {
            echo "ERROR: " . $e->getMessage();
        }
    }
}
