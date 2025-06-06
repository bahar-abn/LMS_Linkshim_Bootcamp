<?php

namespace models;

use core\Application;
use PDO;

class Enrollment
{
    public static function isEnrolled($userId, $courseId): bool
    {
        $stmt = Application::$app->db->pdo->prepare(
            "SELECT * FROM enrollments WHERE user_id = ? AND course_id = ?"
        );
        $stmt->execute([$userId, $courseId]);
        return (bool)$stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function enroll($userId, $courseId): bool
    {
        $stmt = Application::$app->db->pdo->prepare(
            "INSERT INTO enrollments (user_id, course_id) VALUES (?, ?)"
        );
        return $stmt->execute([$userId, $courseId]);
    }

    public static function getUserCourses($userId): array
    {
        $stmt = Application::$app->db->pdo->prepare("
            SELECT c.* FROM enrollments e 
            JOIN courses c ON e.course_id = c.id 
            WHERE e.user_id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
