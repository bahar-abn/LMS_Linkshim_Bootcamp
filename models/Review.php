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

    public static function tableName(): string
    {
        return 'reviews';
    }

    public function save(): bool
    {
        $pdo = Application::$app->db->pdo;

        $stmt = $pdo->prepare("
            INSERT INTO reviews (user_id, course_id, comment, rating, created_at)
            VALUES (:user_id, :course_id, :comment, :rating, NOW())
        ");

        return $stmt->execute([
            ':user_id' => $this->user_id,
            ':course_id' => $this->course_id,
            ':comment' => $this->comment,
            ':rating' => $this->rating,
        ]);
    }

    public static function getAllWithDetails(): array
    {
        $stmt = Application::$app->db->pdo->query("
        SELECT r.*, u.name AS user_name, c.title AS course_title
        FROM reviews r
        JOIN users u ON r.user_id = u.id
        JOIN courses c ON r.course_id = c.id
        ORDER BY r.id DESC
    ");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


    public static function find($id): ?Review
    {
        $stmt = Application::$app->db->pdo->prepare("SELECT * FROM reviews WHERE id = ?");
        $stmt->execute([$id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, self::class);
        return $stmt->fetch() ?: null;
    }

    public function delete(): bool
    {
        $stmt = Application::$app->db->pdo->prepare("DELETE FROM reviews WHERE id = ?");
        return $stmt->execute([$this->id]);
    }

}