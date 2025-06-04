<?php
namespace controllers;

use core\Application;
use core\Request;
use models\User;

class AuthController {
    private string $baseUrl;

    public function __construct() {
        $this->baseUrl = Application::$app->config['BASE_URL'];
    }

    public function login(Request $request): void
    {
        require_once Application::$ROOT_DIR . '/views/auth/login.php';
    }

    public function register(Request $request)
    {
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
            $_SESSION['login_error'] = 'email or password is incorrect';
            Application::$app->response->redirect($this->baseUrl . '/login');
            return;
        }

        $_SESSION['user'] = $user['email'];
        $_SESSION['login_success'] = 'login success';
        Application::$app->response->redirect($this->baseUrl . '/courses'); // تغییر به /courses
    }

    public function dashboard(): void
    {

//        if (!isset($_SESSION['user'])) {
//            Application::$app->response->redirect($this->baseUrl . '/login');
//            return;
//        }


        require_once Application::$ROOT_DIR . '/views/dashboard.php';
    }

    public function registerPost(Request $request) {
        $user = new User();
        $user->loadData($request->getBody());

        if (!$user->validate()) {
            $_SESSION['register_error'] = 'Invalid registration data';
            Application::$app->response->redirect($this->baseUrl . '/register');
            return;
        }

        if ($user->save()) {
            $_SESSION['user'] = $user->email;
            $_SESSION['register_success'] = 'Registration successful!';
            Application::$app->response->redirect($this->baseUrl . '/dashboard');
        } else {
            $_SESSION['register_error'] = 'Registration failed. Email may already exist.';
            Application::$app->response->redirect($this->baseUrl . '/register');
        }
    }

    public function logout(): void
    {
        session_destroy();
        Application::$app->response->redirect($this->baseUrl . '/login'); // تغییر به /login
    }

    public function testDb() {
        try {
            $status = Application::$app->db->pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS);
            echo "Connection: $status<br>";

            $stmt = Application::$app->db->pdo->query("SELECT COUNT(*) FROM users");
            $count = $stmt->fetchColumn();
            echo "Users count: $count<br>";

            $testEmail = 'test_' . time() . '@example.com';
            $stmt = Application::$app->db->pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute(['Test User', $testEmail, password_hash('test123', PASSWORD_DEFAULT)]);
            echo "Inserted test user: $testEmail";
        } catch (PDOException $e) {
            echo "ERROR: " . $e->getMessage();
        }
    }
}