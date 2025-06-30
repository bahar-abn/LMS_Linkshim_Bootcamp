<?php

namespace controllers;

use core\Application;
use core\Request;
use models\User;
use PDO;
use PDOException;

class AuthController {
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
         if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
        $data = $request->getBody();
        error_log('Login attempt with data: ' . print_r($data, true));

        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        if (empty($email) || empty($password)) {
            error_log('Empty email or password');
            $_SESSION['login_error'] = 'Email and password are required.';
            Application::$app->response->redirect(BASE_URL . '/login');
            return;
        }

        $user = User::findByEmail($email);
        error_log('User found: ' . print_r($user, true));

        if (!$user || !password_verify($password, $user->password)) {
            error_log('Invalid login');
            $_SESSION['login_error'] = 'Invalid email or password.';
            Application::$app->response->redirect(BASE_URL . '/login');
            return;
        }

        Application::$app->user = $user;

        $_SESSION['user'] = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ];
        $_SESSION['user_role'] = $user->role; // ✅ Add this line to fix the redirection

        $_SESSION['login_success'] = 'Welcome back, ' . htmlspecialchars($user->name) . '!';

        switch ($user->role) {
            case 'admin':
                Application::$app->response->redirect(BASE_URL . '/admin-dashboard');
                break;
            case 'instructor':
                Application::$app->response->redirect(BASE_URL . '/instructor-dashboard');
                break;
            case 'student':
                Application::$app->response->redirect(BASE_URL . '/dashboard');
                break;
            default:
                $_SESSION['login_warning'] = 'Your account has an unrecognized role';
                Application::$app->response->redirect(BASE_URL . '/dashboard');
        }

        exit;
    }

    public function registerPost(Request $request): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $user = new User();
        $data = $request->getBody();

        $data['role'] = 'student';
        $user->loadData($data);

        if (!$user->validate()) {
            $_SESSION['register_error'] = 'Invalid registration data: ' .
                (empty($user->name) ? 'Name required. ' : (!filter_var($user->email, FILTER_VALIDATE_EMAIL) ? 'Invalid email. ' : ((('' .
                (strlen($user->password) < 6) ? 'Password too short. ' : '' .
                    (User::findByEmail($user->email))) ? 'Email already exists. ' : ''))));
            Application::$app->response->redirect(BASE_URL . '/register');
            return;
        }

        if ($user->save()) {
            $newUser = User::findByEmail($user->email);

            if ($newUser) {
                $_SESSION['user'] = [
                    'id' => $newUser->id,
                    'name' => $newUser->name,
                    'email' => $newUser->email,
                    'role' => $newUser->role,
                ];
                $_SESSION['user_role'] = $newUser->role;

                Application::$app->user = $newUser;

                switch ($newUser->role) {
                    case 'admin':
                        Application::$app->response->redirect(BASE_URL . '/admin-dashboard');
                        break;
                    case 'instructor':
                        Application::$app->response->redirect(BASE_URL . '/instructor-dashboard');
                        break;
                    case 'student':
                        Application::$app->response->redirect(BASE_URL . '/dashboard');
                        break;
                    default:
                        Application::$app->response->redirect(BASE_URL . '/dashboard');
                }
            } else {
                $_SESSION['register_error'] = 'Registration succeeded but failed to log in. Please try logging in.';
                Application::$app->response->redirect(BASE_URL . '/login');
            }
        } else {
            $_SESSION['register_error'] = 'Registration failed. Please try again.';
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

    public function redirect($url) {
        error_log("Attempting redirect to: " . $url);
        header("Location: $url");
        exit;
    }
    // Add this temporary test route in your AuthController
public function testConnection() {
    try {
        $pdo = Application::$app->db->pdo;
        $pdo->query('SELECT 1');
        echo "Database connection successful!";
        
        // Test if table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
        echo $stmt->rowCount() ? "Users table exists" : "Users table MISSING";
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}
}
