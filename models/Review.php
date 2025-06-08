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
    ";
        $stmt = Application::$app->db->pdo->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }


    public static function count(): int
    {
        $stmt = Application::$app->db->pdo->prepare("SELECT COUNT(*) FROM reviews");
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }
    public static function getByCourseId(int $courseId): array {
        $db = \core\Application::$app->db->pdo;

        $stmt = $db->prepare("SELECT * FROM reviews WHERE course_id = :course_id ORDER BY created_at DESC");
        $stmt->bindValue(':course_id', $courseId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }
    public static function find($id)
    {
        $statement = Application::$app->db->prepare("SELECT * FROM reviews WHERE id = :id");
        $statement->bindValue(':id', $id);
        $statement->execute();

        return $statement->fetchObject(Review::class) ?: null;
    }
    public static function addReview(int $userId, int $courseId, int $rating, string $comment): bool
    {
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

}