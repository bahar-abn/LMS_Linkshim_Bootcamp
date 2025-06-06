<?php

namespace models;

use core\Application;
use PDO;
use PDOException;

class User
{
    public $id;
    public $name = '';
    public $email = '';
    public $password = '';
    public $role = 'student';

    public static function tableName(): string
    {
        return 'users';
    }

    public function loadData(array $data): void
    {
        $this->name = trim($data['name'] ?? '');
        $this->email = trim($data['email'] ?? '');
        $this->password = $data['password'] ?? '';
        $this->role = $data['role'] ?? 'student';
    }

    public function validate(): bool
    {
        if (empty($this->name)) return false;
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) return false;
        if (strlen($this->password) < 6) return false;
        return true;
    }

    public function save(): bool
    {
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

            $this->id = Application::$app->db->pdo->lastInsertId();
            return true;

        } catch (PDOException $e) {
            error_log("EXCEPTION during save: " . $e->getMessage());
            return false;
        }
    }

    public static function findByEmail(string $email): ?User
    {
        $stmt = Application::$app->db->pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, self::class);
        return $stmt->fetch() ?: null;
    }

    public static function getAll(): array
    {
        $stmt = Application::$app->db->pdo->query("SELECT * FROM users ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public static function find($id): ?User
    {
        $stmt = Application::$app->db->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, self::class);
        return $stmt->fetch() ?: null;
    }

    public function delete(): bool
    {
        $stmt = Application::$app->db->pdo->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$this->id]);
    }
}