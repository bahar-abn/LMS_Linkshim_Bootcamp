<?php

namespace models;

use core\Application;
use PDO;
use PDOException;

class User {

    public string $name = '';
    public string $email = '';
    public string $password = '';

    public function loadData(array $data): void {
        $this->name = trim($data['name'] ?? '');
        $this->email = trim($data['email'] ?? '');
        $this->password = $data['password'] ?? '';
    }

    public function save(): bool {
        // Add validation check
        if (!$this->validate()) {
            error_log("Validation failed for user: " . print_r($this, true));
            return false;
        }

        try {
            $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);
            error_log("Attempting to save user with hashed password: " . $hashedPassword);

            $sql = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";
            $stmt = Application::$app->db->pdo->prepare($sql);

            $params = [
                ':name' => $this->name,
                ':email' => $this->email,
                ':password' => $hashedPassword
            ];
            error_log("Executing with params: " . print_r($params, true));

            $result = $stmt->execute($params);

            if (!$result) {
                $error = Application::$app->db->pdo->errorInfo();
                error_log("SQL Error: " . print_r($error, true));
                throw new PDOException($error[2]);
            }

            error_log("Save successful for: " . $this->email);
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
    public function validate(): bool
    {
        if (empty($this->name)) {
            return false;
        }

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        if (strlen($this->password) < 6) {
            return false;
        }

        return true;
    }
}
