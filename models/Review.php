<?php

namespace models;

use core\Database;
use PDO;

class Review
{
    public int $id;
    public int $user_id;
    public int $course_id;
    public int $rating;
    public string $comment;
    public string $created_at;

    public static function addReview($userId, $courseId, $rating, $comment): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO reviews (user_id, course_id, rating, comment, created_at) VALUES (:user_id, :course_id, :rating, :comment, NOW())");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':course_id', $courseId);
        $stmt->bindParam(':rating', $rating);
        $stmt->bindParam(':comment', $comment);
        return $stmt->execute();
    }

    public static function getByCourseId($courseId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM reviews WHERE course_id = :course_id ORDER BY created_at DESC");
        $stmt->bindParam(':course_id', $courseId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }
}
