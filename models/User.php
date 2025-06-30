<?php

namespace models;

use core\Application;
use PDO;
use PDOException;

class User {
    public ?int $id = null;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $role = 'student';
    public ?string $created_at = null;
    public ?string $updated_at = null;


    public function __construct(array $data = []) {
        if (!empty($data)) {
            $this->loadData($data);
        }
    }

    public function loadData(array $data): void {
        $this->id = $data['id'] ?? null;
        $this->name = trim($data['name'] ?? '');
        $this->email = trim($data['email'] ?? '');
        $this->password = $data['password'] ?? '';
        $this->role = $data['role'] ?? 'student';
        $this->created_at = $data['created_at'] ?? null;
    }

    public function validate(): bool {
        $valid = true;

        if (empty($this->name)) {
            error_log("Validation failed: Name is empty");
            $valid = false;
        }

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            error_log("Validation failed: Invalid email format");
            $valid = false;
        }

        if (strlen($this->password) < 6) {
            error_log("Validation failed: Password too short");
            $valid = false;
        }

        // Only check for duplicate emails for new users
        if ($this->id === null) {
            $existing = self::findByEmail($this->email);
            if ($existing) {
                error_log("Validation failed: Email already exists");
                $valid = false;
            }
        }

        return $valid;
    }

    public function save(): bool
    {
        if (!$this->validate()) {
            error_log("Save aborted: Validation failed for user: " . print_r($this, true));
            return false;
        }

        try {
            $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);
            error_log("Attempting to save user with email: " . $this->email);

            if ($this->id === null) {
                $sql = "INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)";
            } else {
                $sql = "UPDATE users SET name = :name, email = :email, password = :password, role = :role WHERE id = :id";
            }

            $stmt = Application::$app->db->pdo->prepare($sql);

            $params = [
                ':name' => $this->name,
                ':email' => $this->email,
                ':password' => $hashedPassword,
                ':role' => $this->role
            ];

            if ($this->id !== null) {
                $params[':id'] = $this->id;
            }

            $result = $stmt->execute($params);

            if (!$result) {
                $error = $stmt->errorInfo();
                error_log("SQL Error: " . print_r($error, true));
                throw new \Exception("Database error: " . $error[2]);
            }

            if ($this->id === null) {
                $this->id = (int)Application::$app->db->pdo->lastInsertId();
                error_log("New user created with ID: " . $this->id);
            }

            return true;
        } catch (PDOException $e) {
            error_log("PDO Exception during save: " . $e->getMessage());
            error_log("SQLSTATE: " . $e->getCode());
            return false;
        } catch (\Exception $e) {
            error_log("Exception during save: " . $e->getMessage());
            return false;
        }
    }

    public static function findByEmail(string $email): ?self {
        try {
            $stmt = Application::$app->db->pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);
            
            $user = $stmt->fetchObject(self::class);
            if ($user === false) {
                return null;
            }
            
            return $user;
        } catch (PDOException $e) {
            error_log("Error finding user by email: " . $e->getMessage());
            return null;
        }
    }

    public static function findById(int $id): ?self {
        try {
            $stmt = Application::$app->db->pdo->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->execute([':id' => $id]);
            
            $user = $stmt->fetchObject(self::class);
            if ($user === false) {
                return null;
            }
            
            return $user;
        } catch (PDOException $e) {
            error_log("Error finding user by ID: " . $e->getMessage());
            return null;
        }
    }

    public static function getAll(): array {
        try {
            $stmt = Application::$app->db->pdo->query("SELECT * FROM users");
            return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
        } catch (PDOException $e) {
            error_log("Error getting all users: " . $e->getMessage());
            return [];
        }
    }

    public static function count(): int {
        try {
            $stmt = Application::$app->db->pdo->query("SELECT COUNT(*) FROM users");
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error counting users: " . $e->getMessage());
            return 0;
        }
    }

    public static function latest(int $limit = 100): array {
        try {
            $stmt = Application::$app->db->pdo->prepare("SELECT * FROM users ORDER BY created_at DESC LIMIT :limit");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
        } catch (PDOException $e) {
            error_log("Error getting latest users: " . $e->getMessage());
            return [];
        }
    }

    public function delete(): bool {
        if (empty($this->id)) {
            error_log("Delete failed: No ID specified");
            return false;
        }

        try {
            $stmt = Application::$app->db->pdo->prepare("DELETE FROM users WHERE id = :id");
            return $stmt->execute([':id' => $this->id]);
        } catch (PDOException $e) {
            error_log("Error deleting user: " . $e->getMessage());
            return false;
        }
    }
    public static function find(int $id): ?self {
        try {
            $stmt = Application::$app->db->pdo->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->execute([':id' => $id]);

            $stmt->setFetchMode(PDO::FETCH_CLASS, self::class);
            $user = $stmt->fetch();

            return $user ?: null;
        } catch (PDOException $e) {
            error_log("Error finding user by ID: " . $e->getMessage());
            return null;
        }
    }
}