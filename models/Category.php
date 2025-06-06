<?php

namespace models;

use core\Application;
use PDO;

class Category
{
    public $id;
    public $name;

    public function __construct($data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? '';
    }

    /**
     * Retrieve all categories from the database.
     */
    public static function getAll(): array
    {
        $pdo = Application::$app->db->pdo;

        $stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find a category by its ID.
     */
    public static function findById(int $id): ?Category
    {
        $pdo = Application::$app->db->pdo;

        $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? new Category($data) : null;
    }

    /**
     * Save a new category.
     */
    public function save(): bool
    {
        $pdo = Application::$app->db->pdo;

        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (:name)");
        return $stmt->execute([':name' => $this->name]);
    }

    /**
     * Delete category by ID.
     */
    public static function delete(int $id): bool
    {
        $pdo = Application::$app->db->pdo;

        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
    public static function all(): array
    {
        $stmt = Application::$app->db->pdo->query("SELECT * FROM categories");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
