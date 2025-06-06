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
    public $created_at;

    public static function tableName(): string
    {
        return 'courses';
    }

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->title = $data['title'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->category_id = $data['category_id'] ?? null;
        $this->instructor_id = $data['instructor_id'] ?? null;
        $this->status = $data['status'] ?? 'pending';
        $this->created_at = $data['created_at'] ?? null;
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
            $this->id = Application::$app->db->pdo->lastInsertId();
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

    public static function getAllWithInstructor(): array
    {
        $stmt = Application::$app->db->pdo->query("
            SELECT c.*, u.name AS instructor_name 
            FROM courses c
            LEFT JOIN users u ON c.instructor_id = u.id
            ORDER BY c.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public static function find($id): ?Course
    {
        $stmt = Application::$app->db->pdo->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->execute([$id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, self::class);
        return $stmt->fetch() ?: null;
    }
    public static function getAll(): array
    {
        $stmt = Application::$app->db->pdo->query("
        SELECT c.*, u.name AS instructor_name 
        FROM courses c
        JOIN users u ON c.instructor_id = u.id
        ORDER BY c.id DESC
    ");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

}