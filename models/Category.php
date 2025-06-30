<?php
namespace models;

use core\Application;
use PDO;
use PDOException;

class Category
{
    public $id;
    public $name;
    public $created_at;

    public static function getAll(): array
    {
        try {
            $stmt = Application::$app->db->pdo->query("SELECT * FROM categories ORDER BY name ASC");
            return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
        } catch (PDOException $e) {
            error_log("Error fetching categories: " . $e->getMessage());
            return [];
        }
    }

    public static function find(int $id): ?self
    {
        try {
            $stmt = Application::$app->db->pdo->prepare("SELECT * FROM categories WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $stmt->setFetchMode(PDO::FETCH_CLASS, self::class);
            return $stmt->fetch() ?: null;
        } catch (PDOException $e) {
            error_log("Error finding category: " . $e->getMessage());
            return null;
        }
    }

    public function save(): bool
    {
        try {
            if ($this->id) {
                // Update existing category
                $stmt = Application::$app->db->pdo->prepare(
                    "UPDATE categories SET name = :name WHERE id = :id"
                );
                $stmt->execute([':name' => $this->name, ':id' => $this->id]);
            } else {
                // Create new category
                $stmt = Application::$app->db->pdo->prepare(
                    "INSERT INTO categories (name) VALUES (:name)"
                );
                $stmt->execute([':name' => $this->name]);
                $this->id = (int)Application::$app->db->pdo->lastInsertId();
            }
            return true;
        } catch (PDOException $e) {
            error_log("Category save error: " . $e->getMessage());
            return false;
        }
    }

    public function delete(): bool
    {
        try {
            $stmt = Application::$app->db->pdo->prepare(
                "DELETE FROM categories WHERE id = :id"
            );
            return $stmt->execute([':id' => $this->id]);
        } catch (PDOException $e) {
            error_log("Category delete error: " . $e->getMessage());
            return false;
        }
    }
}