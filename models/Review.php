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
    public $created_at;

    /**
     * Save a new review to the database.
     */
    public function save(): bool
    {
        $pdo = Application::$app->db->pdo;

        $stmt = $pdo->prepare("
            INSERT INTO reviews (user_id, course_id, comment, created_at)
            VALUES (:user_id, :course_id, :comment, :created_at)
        ");

        return $stmt->execute([
            ':user_id' => $this->user_id,
            ':course_id' => $this->course_id,
            ':comment' => $this->comment,
            ':created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Get all reviews for a specific course.
     */
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
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Delete a review by ID.
     */
    public static function delete(int $id): bool
    {
        $pdo = Application::$app->db->pdo;

        $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
