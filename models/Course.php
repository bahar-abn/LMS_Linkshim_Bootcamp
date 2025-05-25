<?php

namespace models;

use core\Application;
use PDO;

class Course
{
    public static function count(): int {
        $stmt = Application::$app->db->pdo->prepare("SELECT COUNT(*) FROM courses");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public static function findByInstructor(int $instructorId): array {
        $stmt = Application::$app->db->pdo->prepare("
            SELECT * FROM courses 
            WHERE instructor_id = :instructor_id
            ORDER BY created_at DESC
        ");
        $stmt->execute([':instructor_id' => $instructorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findAll(): array {
        $stmt = Application::$app->db->pdo->prepare("
            SELECT * FROM courses 
            ORDER BY created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}