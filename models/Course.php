<?php

namespace models;

use core\Application;
use PDO;

class Course
{
    public $id;
    public $title;
    public $description;
    public $category_id;
    public $instructor_id;
    public $status;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->title = $data['title'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->category_id = $data['category_id'] ?? null;
        $this->instructor_id = $data['instructor_id'] ?? null;
        $this->status = $data['status'] ?? 'pending';
    }

    /**
     * Save a new course.
     */
    public function save(): bool
    {
        $stmt = Application::$app->db->pdo->prepare("
            INSERT INTO courses (title, description, category_id, instructor_id, status)
            VALUES (:title, :description, :category_id, :instructor_id, :status)
        ");

        return $stmt->execute([
            ':title' => $this->title,
            ':description' => $this->description,
            ':category_id' => $this->category_id,
            ':instructor_id' => $this->instructor_id,
            ':status' => $this->status,
        ]);
    }

    /**
     * Update an existing course.
     */
    public function update(int $id): bool
    {
        $stmt = Application::$app->db->pdo->prepare("
            UPDATE courses
            SET title = :title,
                description = :description,
                category_id = :category_id,
                status = :status
            WHERE id = :id AND instructor_id = :instructor_id
        ");

        return $stmt->execute([
            ':title' => $this->title,
            ':description' => $this->description,
            ':category_id' => $this->category_id,
            ':status' => $this->status,
            ':id' => $id,
            ':instructor_id' => $this->instructor_id,
        ]);
    }

    /**
     * Get all approved courses.
     */
    public static function allApproved(): array
    {
        $stmt = Application::$app->db->pdo->query("
            SELECT * FROM courses WHERE status = 'approved'
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find a course by ID.
     */
    public static function findById(int $id): ?array
    {
        $stmt = Application::$app->db->pdo->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Find courses by instructor.
     */
    public static function findByInstructor(int $instructorId): array
    {
        $stmt = Application::$app->db->pdo->prepare("
            SELECT * FROM courses WHERE instructor_id = ?
        ");
        $stmt->execute([$instructorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
