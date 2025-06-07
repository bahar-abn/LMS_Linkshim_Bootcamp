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

    public static function count(): int
    {
        $stmt = Application::$app->db->pdo->prepare("SELECT COUNT(*) FROM reviews");
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }
}
