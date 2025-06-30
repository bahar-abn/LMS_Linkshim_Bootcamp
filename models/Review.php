<?php

namespace models;

use core\Application;
use PDO;

class Review
{
    public int $id;
    public int $user_id;
    public int $course_id;
    public string $comment = '';
    public int $rating;
    public string $created_at;
    public string $user_name;
    public string $course_title;

    public static function tableName(): string
    {
        return 'reviews';
    }

    public static function getAll(): array
    {
        $stmt = Application::$app->db->pdo->query("
            SELECT r.*, u.name AS user_name, c.title AS course_title
            FROM reviews r
            LEFT JOIN users u ON r.user_id = u.id
            LEFT JOIN courses c ON r.course_id = c.id
            ORDER BY r.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public static function getAllWithDetails(): array
    {
        $sql = "
            SELECT reviews.*, users.name AS user_name, courses.title AS course_title
            FROM reviews
            JOIN users ON reviews.user_id = users.id
            JOIN courses ON reviews.course_id = courses.id
            ORDER BY reviews.created_at DESC
        ";
        $stmt = Application::$app->db->pdo->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    public static function count(): int
    {
        $stmt = Application::$app->db->pdo->prepare("SELECT COUNT(*) FROM " . self::tableName());
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public static function getByCourseId(int $courseId): array
    {
        $db = Application::$app->db->pdo;

        $stmt = $db->prepare("
            SELECT r.*, u.name AS user_name 
            FROM reviews r
            JOIN users u ON r.user_id = u.id
            WHERE r.course_id = :course_id 
            ORDER BY r.created_at DESC
        ");
        $stmt->bindValue(':course_id', $courseId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public static function getByUserId(int $userId): array
    {
        $db = Application::$app->db->pdo;

        $stmt = $db->prepare("
            SELECT r.*, c.title AS course_title 
            FROM reviews r
            JOIN courses c ON r.course_id = c.id
            WHERE r.user_id = :user_id 
            ORDER BY r.created_at DESC
        ");
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public static function find(int $id): ?self
    {
        $statement = Application::$app->db->prepare("
            SELECT r.*, u.name AS user_name, c.title AS course_title 
            FROM reviews r
            LEFT JOIN users u ON r.user_id = u.id
            LEFT JOIN courses c ON r.course_id = c.id
            WHERE r.id = :id
        ");
        $statement->bindValue(':id', $id);
        $statement->execute();

        return $statement->fetchObject(self::class) ?: null;
    }

    public static function addReview(int $userId, int $courseId, int $rating, string $comment): bool
    {
        // Check if user already reviewed this course
        $existingReview = self::getUserCourseReview($userId, $courseId);
        if ($existingReview) {
            return false;
        }

        $stmt = Application::$app->db->pdo->prepare("
            INSERT INTO reviews (user_id, course_id, rating, comment)
            VALUES (:user_id, :course_id, :rating, :comment)
        ");

        return $stmt->execute([
            ':user_id' => $userId,
            ':course_id' => $courseId,
            ':rating' => $rating,
            ':comment' => $comment,
        ]);
    }

    public static function getUserCourseReview(int $userId, int $courseId): ?self
    {
        $stmt = Application::$app->db->pdo->prepare("
            SELECT * FROM reviews 
            WHERE user_id = :user_id AND course_id = :course_id
            LIMIT 1
        ");
        $stmt->execute([
            ':user_id' => $userId,
            ':course_id' => $courseId
        ]);

        return $stmt->fetchObject(self::class) ?: null;
    }

    public static function getAverageRating(int $courseId): float
    {
        $stmt = Application::$app->db->pdo->prepare("
            SELECT AVG(rating) FROM reviews 
            WHERE course_id = :course_id
        ");
        $stmt->bindValue(':course_id', $courseId, PDO::PARAM_INT);
        $stmt->execute();

        return (float) $stmt->fetchColumn();
    }

    public static function getRatingCount(int $courseId): int
    {
        $stmt = Application::$app->db->pdo->prepare("
            SELECT COUNT(*) FROM reviews 
            WHERE course_id = :course_id
        ");
        $stmt->bindValue(':course_id', $courseId, PDO::PARAM_INT);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    // ----- Add this method -----
    public function delete(): bool
    {
        if (!isset($this->id)) {
            return false; // id لازم است
        }

        $stmt = Application::$app->db->pdo->prepare("DELETE FROM reviews WHERE id = :id");
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public static function getByInstructorId(int $instructorId): array
{
    try {
        $stmt = Application::$app->db->pdo->prepare(
            "SELECT r.*, c.title as course_title, u.name as student_name 
             FROM reviews r
             JOIN courses c ON r.course_id = c.id
             JOIN users u ON r.user_id = u.id
             WHERE c.instructor_id = :instructor_id
             ORDER BY r.created_at DESC"
        );
        $stmt->execute([':instructor_id' => $instructorId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        error_log("Error fetching instructor reviews: " . $e->getMessage());
        return [];
    }
}
}
