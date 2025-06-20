<?php

namespace models;

use core\Application;
use PDO;

class Category
{
    public ?int $id = null;
    public string $name = '';
    public ?string $created_at = null;

    /**
     * Get all categories ordered by name.
     *
     * @return Category[] Array of Category objects
     */
    public static function all(): array
    {
        $stmt = Application::$app->db->pdo->prepare("SELECT * FROM categories ORDER BY name ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    /**
     * Find a category by ID.
     *
     * @param int $id
     * @return Category|null
     */
    public static function findById(int $id): ?Category
    {
        $stmt = Application::$app->db->pdo->prepare("SELECT * FROM categories WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, self::class);
        return $stmt->fetch() ?: null;
    }

    /**
     * Save a new category to the database.
     *
     * @return bool
     */
    public function save(): bool
    {
        $stmt = Application::$app->db->pdo->prepare("INSERT INTO categories (name) VALUES (:name)");
        return $stmt->execute([':name' => $this->name]);
    }

    /**
     * Delete a category by ID.
     *
     * @param int $id
     * @return bool
     */
    public static function delete(int $id): bool
    {
        $stmt = Application::$app->db->pdo->prepare("DELETE FROM categories WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
