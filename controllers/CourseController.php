<?php
// File: controllers/CourseController.php

namespace controllers;

use core\Application;
use core\Request;
use models\Course;
use models\Category;
use models\Enrollment;

class CourseController
{
    public function index()
    {
        $courses = Course::allApproved();
        require_once Application::$ROOT_DIR . '/views/courses/index.php';
    }

    public function myCourses()
    {
        session_start();
        $userId = $_SESSION['user_id'] ?? null;
        $userRole = $_SESSION['role'] ?? null;

        if (!$userId || $userRole !== 'instructor') {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $courses = Course::findByInstructor($userId);
        require_once Application::$ROOT_DIR . '/views/courses/my-courses.php';
    }

    public function create()
    {
        session_start();
        $userRole = $_SESSION['role'] ?? null;

        if ($userRole !== 'instructor') {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $categories = Category::all();
        require_once Application::$ROOT_DIR . '/views/courses/create.php';
    }

    public function store(Request $request)
    {
        session_start();
        $userId = $_SESSION['user_id'] ?? null;
        $userRole = $_SESSION['role'] ?? null;

        if (!$userId || $userRole !== 'instructor') {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $data = $request->getBody();
        $course = new Course();
        $course->title = trim($data['title'] ?? '');
        $course->description = trim($data['description'] ?? '');
        $course->category_id = (int)($data['category_id'] ?? 1);
        $course->instructor_id = $userId;
        $course->status = 'pending';

        if ($course->save()) {
            $_SESSION['success'] = 'Course submitted for admin approval.';
        } else {
            $_SESSION['error'] = 'Failed to create course.';
        }

        header('Location: ' . BASE_URL . '/my-courses');
    }

    public function details(Request $request)
    {
        $id = $request->getRouteParam('id');
        $course = Course::findById($id);

        if (!$course) {
            http_response_code(404);
            echo "Course not found.";
            return;
        }

        require_once Application::$ROOT_DIR . '/views/courses/details.php';
    }

    public function enroll(Request $request)
    {
        session_start();
        $userId = $_SESSION['user_id'] ?? null;
        $userRole = $_SESSION['role'] ?? null;
        $courseId = $request->getRouteParam('id');

        if (!$userId || $userRole !== 'student') {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        if (Enrollment::isEnrolled($userId, $courseId)) {
            $_SESSION['info'] = 'Already enrolled.';
        } else {
            Enrollment::enroll($userId, $courseId);
            $_SESSION['success'] = 'Enrolled successfully!';
        }

        header('Location: ' . BASE_URL . "/courses/$courseId");
    }
}
