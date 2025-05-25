<?php

namespace models;

use core\Database;
use PDO;

class Enrollment
{
    public int $id;
    public int $user_id;
    public int $course_id;
    public string $enrolled_at;

    public static function isEnrolled($userId, $courseId): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM enrollments WHERE user_id = :user_id AND course_id = :course_id");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':course_id', $courseId);
        $stmt->execute();
        return (bool) $stmt->fetch();
    }

    public static function enroll($userId, $courseId): bool
    {
        if (self::isEnrolled($userId, $courseId)) {
            return false;
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO enrollments (user_id, course_id, enrolled_at) VALUES (:user_id, :course_id, NOW())");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':course_id', $courseId);
        return $stmt->execute();
    }

    public static function getEnrolledCourses($userId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT course_id FROM enrollments WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
