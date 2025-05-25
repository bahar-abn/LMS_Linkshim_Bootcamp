<?php

namespace models;

use core\Application;
use PDO;
use PDOException;

class User {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $role = 'student';

    public function loadData(array $data): void {
        $this->name = trim($data['name'] ?? '');
        $this->email = trim($data['email'] ?? '');
        $this->password = $data['password'] ?? '';
        $this->role = $data['role'] ?? 'student';
    }

    public function validate(): bool {
        if (empty($this->name)) return false;
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) return false;
        if (strlen($this->password) < 6) return false;
        return true;
    }

    public function save(): bool {
        if (!$this->validate()) {
            error_log("Validation failed for user: " . print_r($this, true));
            return false;
        }

        try {
            $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)";
            $stmt = Application::$app->db->pdo->prepare($sql);

            $params = [
                ':name' => $this->name,
                ':email' => $this->email,
                ':password' => $hashedPassword,
                ':role' => $this->role
            ];

            $result = $stmt->execute($params);

            if (!$result) {
                $error = $stmt->errorInfo();
                error_log("SQL Error: " . print_r($error, true));
                return false;
            }

            return true;

        } catch (PDOException $e) {
            error_log("EXCEPTION during save: " . $e->getMessage());
            return false;
        }
    }

    public static function findByEmail(string $email): ?array {
        $stmt = Application::$app->db->pdo->prepare("
            SELECT * FROM users WHERE email = :email
        ");
        $stmt->execute([':email' => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public static function count(): int {
        $stmt = Application::$app->db->pdo->prepare("SELECT COUNT(*) FROM users");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public static function latest(int $limit = 5): array {
        $stmt = Application::$app->db->pdo->prepare("
            SELECT * FROM users 
            ORDER BY created_at DESC 
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findById(int $id): ?array {
        $stmt = Application::$app->db->pdo->prepare("
            SELECT * FROM users WHERE id = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}