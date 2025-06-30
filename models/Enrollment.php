<?php

namespace models;

use core\Application;
use PDO;

class Enrollment
{
    public static function count(): int {
        $stmt = Application::$app->db->pdo->prepare("SELECT COUNT(*) FROM enrollments");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public static function findByUser(int $userId): array {
    $stmt = Application::$app->db->pdo->prepare("
        SELECT c.* 
        FROM courses c
        JOIN enrollments e ON e.course_id = c.id
        WHERE e.user_id = :user_id
        ORDER BY e.enrolled_at DESC
    ");
    $stmt->execute([':user_id' => $userId]);
    return $stmt->fetchAll(PDO::FETCH_OBJ); // Return as objects for consistency
}
    public static function findByUserWithDetails(int $userId): array {
    $stmt = Application::$app->db->pdo->prepare("
        SELECT 
            e.*,
            c.id as course_id,
            c.title as course_title,
            c.description as course_description,
            c.status as course_status
        FROM enrollments e
        JOIN courses c ON e.course_id = c.id
        WHERE e.user_id = :user_id
        ORDER BY e.enrolled_at DESC
    ");
    $stmt->execute([':user_id' => $userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}