<?php

namespace models;

use core\Database;

class Course
{
    public $id;
    public $title;
    public $description;
    public $instructor_id;
    public $category_id;
    public $status;

    public static function allApproved()
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM courses WHERE status = 'approved'");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function find($id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function save()
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO courses (title, description, instructor_id, category_id, status) VALUES (?, ?, ?, ?, 'pending')");
        return $stmt->execute([$this->title, $this->description, $this->instructor_id, $this->category_id]);
    }

    public static function findByInstructor($instructor_id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM courses WHERE instructor_id = ?");
        $stmt->execute([$instructor_id]);
        return $stmt->fetchAll();
    }
}
