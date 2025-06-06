<?php

namespace models;

use core\Application;
use PDO;

class Review
{
    public $id;
    public $user_id;
    public $course_id;
    public $comment;
    public $rating;
    public $created_at;

    public function save(): bool
    {
        $pdo = Application::$app->db->pdo;

        $stmt = $pdo->prepare("
            INSERT INTO reviews (user_id, course_id, comment, rating, created_at)
            VALUES (:user_id, :course_id, :comment, :rating, :created_at)
        ");

        return $stmt->execute([
            ':user_id' => $this->user_id,
            ':course_id' => $this->course_id,
            ':comment' => $this->comment,
            ':rating' => $this->rating,
            ':created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public static function getByCourseId(int $courseId): array
    {
        $pdo = Application::$app->db->pdo;

        $stmt = $pdo->prepare("
            SELECT reviews.*, users.name AS user_name
            FROM reviews
            JOIN users ON reviews.user_id = users.id
            WHERE course_id = :course_id
            ORDER BY created_at DESC
        ");
        $stmt->execute([':course_id' => $courseId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public static function delete(int $id): bool
    {
        $pdo = Application::$app->db->pdo;

        $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public static function getByInstructorId($instructorId): array
    {
        $pdo = Application::$app->db->pdo;

        $stmt = $pdo->prepare("
            SELECT r.*, c.title AS course_title
            FROM reviews r
            JOIN courses c ON r.course_id = c.id
            WHERE c.instructor_id = :instructorId
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([':instructorId' => $instructorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ✅ NEW: Get all reviews written by a specific user
    public static function getByUserId(int $userId): array
    {
        $pdo = Application::$app->db->pdo;

        $stmt = $pdo->prepare("
            SELECT r.*, c.title AS course_title
            FROM reviews r
            JOIN courses c ON r.course_id = c.id
            WHERE r.user_id = :user_id
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
