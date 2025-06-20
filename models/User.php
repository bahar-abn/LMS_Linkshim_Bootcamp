<?php

namespace models;

use core\Application;
use PDO;
use PDOException;

class User {
    public int $id;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $role = '';
    public string $created_at;

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

    public static function findByEmail(string $email): ?self {
        $stmt = Application::$app->db->pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, self::class);
        $user = $stmt->fetch() ?: null;

        // Debug the fetched user
        error_log('User fetched from DB: ' . print_r($user, true));

        return $user;
    }
    public static function findById(int $id): ?self {
        $stmt = Application::$app->db->pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, self::class);
        return $stmt->fetch() ?: null;
    }

    public static function getAll(): array
    {
        $pdo = Application::$app->db->pdo;

        $stmt = $pdo->query("SELECT * FROM users");
        $raw = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $users = [];
        foreach ($raw as $row) {
            $u = new self();
            $u->id = (int)$row['id'];
            $u->name = $row['name'] ?? $row['username'] ?? '';
            $u->email = $row['email'] ?? '';
            $u->role = $row['role'] ?? 'student';
            $u->created_at = $row['created_at'] ?? '';
            $users[] = $u;
        }

        return $users;
    }
    public static function count(): int {
        $stmt = Application::$app->db->pdo->prepare("SELECT COUNT(*) FROM users");
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public static function latest(int $limit = 100): array
    {
        $stmt = Application::$app->db->pdo->prepare("SELECT * FROM users ORDER BY created_at DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }
    public static function find($id)
    {
        $stmt = Application::$app->db->pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$userData) {
            return null;
        }

        return new self($userData);
    }
    public function delete()
    {
        if (empty($this->id)) {
            return false;
        }

        $stmt = Application::$app->db->pdo->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute([':id' => $this->id]);
    }
    // In your core/Response class or equivalent
    public function redirect($url) {
        error_log("Attempting redirect to: " . $url);
        header("Location: $url");
        exit; // Make sure to exit after redirect
    }
}
