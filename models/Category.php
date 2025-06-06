<?php

namespace models;

use core\Application;
use PDO;

class Category
{
    public $id;
    public $name;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? '';
    }

    /**
     * Get all categories.
     * @return array
     */
    public static function all(): array
    {
        $stmt = Application::$app->db->pdo->query("SELECT * FROM categories ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find a category by ID.
     */
    public static function findById(int $id): ?Category
    {
        $stmt = Application::$app->db->pdo->prepare("SELECT * FROM categories WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? new Category($data) : null;
    }

    /**
     * Save a new category.
     */
    public function save(): bool
    {
        $stmt = Application::$app->db->pdo->prepare("INSERT INTO categories (name) VALUES (:name)");
        return $stmt->execute([':name' => $this->name]);
    }

    /**
     * Delete a category by ID.
     */
    public static function delete(int $id): bool
    {
        $stmt = Application::$app->db->pdo->prepare("DELETE FROM categories WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
