<?php
namespace models;

use core\Application;
use PDO;

class Course
{
    public $title;
    public $description;
    public $category_id;
    public $instructor_id;
    public $status;

    public function save(): bool
    {
        $stmt = Application::$app->db->pdo->prepare("INSERT INTO courses (title, description, category_id, instructor_id, status) VALUES (:title, :description, :category_id, :instructor_id, :status)");
        return $stmt->execute([
            ':title' => $this->title,
            ':description' => $this->description,
            ':category_id' => $this->category_id,
            ':instructor_id' => $this->instructor_id,
            ':status' => $this->status,
        ]);
    }

    public static function allApproved(): array
    {
        return Application::$app->db->pdo->query("SELECT * FROM courses WHERE status = 'approved'")->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findById($id): ?array
    {
        $stmt = Application::$app->db->pdo->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public static function findByInstructor($instructorId): array
    {
        $stmt = Application::$app->db->pdo->prepare("SELECT * FROM courses WHERE instructor_id = ?");
        $stmt->execute([$instructorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}