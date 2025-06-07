<?php

namespace models;

use core\Application;
use PDO;

class Course
{
    public int $id;
    public string $title = '';
    public string $description = '';
    public int $category_id;
    public int $instructor_id;
    public string $status = 'pending';
    public string $created_at;
    public string $instructor_name;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? 0;
        $this->title = $data['title'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->category_id = $data['category_id'] ?? 0;
        $this->instructor_id = $data['instructor_id'] ?? 0;
        $this->status = $data['status'] ?? 'pending';
        $this->created_at = $data['created_at'] ?? '';
        $this->instructor_name = $data['instructor_name'] ?? '';
    }

    public function save(): bool
    {
        if ($this->id) {
            return $this->update();
        }

        $stmt = Application::$app->db->pdo->prepare("
            INSERT INTO courses (title, description, category_id, instructor_id, status, created_at)
            VALUES (:title, :description, :category_id, :instructor_id, :status, NOW())
        ");

        $result = $stmt->execute([
            ':title' => $this->title,
            ':description' => $this->description,
            ':category_id' => $this->category_id,
            ':instructor_id' => $this->instructor_id,
            ':status' => $this->status,
        ]);

        if ($result) {
            $this->id = (int)Application::$app->db->pdo->lastInsertId();
        }

        return $result;
    }

    public function update(): bool
    {
        $stmt = Application::$app->db->pdo->prepare("
            UPDATE courses
            SET title = :title,
                description = :description,
                category_id = :category_id,
                instructor_id = :instructor_id,
                status = :status
            WHERE id = :id
        ");

        return $stmt->execute([
            ':title' => $this->title,
            ':description' => $this->description,
            ':category_id' => $this->category_id,
            ':instructor_id' => $this->instructor_id,
            ':status' => $this->status,
            ':id' => $this->id,
        ]);
    }

    public static function getAll(): array
    {
        $stmt = Application::$app->db->pdo->query("
            SELECT c.*, u.name AS instructor_name 
            FROM courses c
            LEFT JOIN users u ON c.instructor_id = u.id
            ORDER BY c.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    /**
     * Find a course by its ID
     * @param int $id
     * @return self|null
     */
    public static function find(int $id): ?self
    {
        $stmt = Application::$app->db->pdo->prepare("
            SELECT c.*, u.name AS instructor_name 
            FROM courses c
            LEFT JOIN users u ON c.instructor_id = u.id
            WHERE c.id = :id
        ");
        $stmt->execute([':id' => $id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, self::class);
        return $stmt->fetch() ?: null;
    }

    public static function count(): int
    {
        $stmt = Application::$app->db->pdo->prepare("SELECT COUNT(*) FROM courses");
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public static function findByInstructor(int $instructorId): array
    {
        $stmt = Application::$app->db->pdo->prepare("
            SELECT c.*, u.name AS instructor_name 
            FROM courses c
            LEFT JOIN users u ON c.instructor_id = u.id
            WHERE c.instructor_id = :instructor_id
            ORDER BY c.created_at DESC
        ");
        $stmt->execute([':instructor_id' => $instructorId]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public static function latest(int $limit = 100): array
    {
        $stmt = Application::$app->db->pdo->prepare("
            SELECT c.*, u.name AS instructor_name 
            FROM courses c
            LEFT JOIN users u ON c.instructor_id = u.id
            ORDER BY c.created_at DESC 
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }
    public static function findById(int $id): ?array
    {
        $stmt = Application::$app->db->pdo->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}
