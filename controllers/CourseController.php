<?php

namespace controllers;

use core\Application;
use core\Request;
use core\Response;
use models\Course;
use models\Category;
use models\Review;
use core\Session;

class CourseController
{
    public function index()
    {
        $this->ensureSessionStarted();

        $search = $_GET['search'] ?? '';
        $category = $_GET['category'] ?? '';

        $categories = Category::all(); // Must return all category objects
        $courses = Course::searchAndFilter($search, $category);

        // Make variables available to the view
        include Application::$ROOT_DIR . '/views/courses/index.php';
    }


    public function myCourses()
    {
        $this->ensureSessionStarted();
        $this->checkInstructorAccess();

        $courses = Course::findByInstructor($_SESSION['user']['id']);
        require_once Application::$ROOT_DIR . '/views/courses/my-courses.php';
    }

    public function create()
    {
        $this->ensureSessionStarted();
        $this->checkInstructorAccess();

        $categories = Category::all();
        require_once Application::$ROOT_DIR . '/views/courses/create.php';
    }

    public function store(Request $request)
    {
        $this->ensureSessionStarted();
        $this->checkInstructorAccess();

        $data = $request->getBody();

        if (empty($data['title'])) {
            $_SESSION['error'] = 'Course title is required';
            Application::$app->response->redirect(BASE_URL . '/courses/create');
            return;
        }

        $course = new Course([
            'title' => trim($data['title']),
            'description' => trim($data['description'] ?? ''),
            'category_id' => (int)($data['category_id'] ?? 1),
            'instructor_id' => $_SESSION['user']['id'],
            'status' => 'pending'
        ]);
        $course->save();

        Application::$app->response->redirect(BASE_URL . '/courses/' . urlencode($course->id));
    }

    public function details(Request $request)
    {
        $this->ensureSessionStarted();

        $id = $request->getRouteParam('id');
        $course = Course::findById((int)$id);

        if (!$course) {
            Application::$app->response->setStatusCode(404);
            echo "Course not found.";
            return;
        }

        $reviews = Review::getByCourseId($id);
        require_once Application::$ROOT_DIR . '/views/courses/details.php';
    }

    public function enroll(Request $request): void
    {
        $this->ensureSessionStarted();

        $user = $_SESSION['user'] ?? null;
        if (!$user || !isset($user['id'])) {
            $_SESSION['error'] = 'You must be logged in to enroll.';
            Application::$app->response->redirect(BASE_URL . '/login');
            return;
        }

        $courseId = (int)$request->getRouteParam('id');

        // Check if course exists
        $course = \models\Course::findById($courseId);
        if (!$course) {
            $_SESSION['error'] = 'Course not found.';
            Application::$app->response->redirect(BASE_URL . '/courses');
            return;
        }

        // Prevent duplicate enrollment
        $checkStmt = Application::$app->db->pdo->prepare("
        SELECT COUNT(*) FROM enrollments WHERE user_id = :user_id AND course_id = :course_id
    ");
        $checkStmt->execute([
            ':user_id' => $user['id'],
            ':course_id' => $courseId,
        ]);
        if ($checkStmt->fetchColumn() > 0) {
            $_SESSION['error'] = 'You are already enrolled in this course.';
            Application::$app->response->redirect(BASE_URL . "/courses/{$courseId}");
            return;
        }

        // Insert enrollment
        $stmt = Application::$app->db->pdo->prepare("
        INSERT INTO enrollments (user_id, course_id, enrolled_at)
        VALUES (:user_id, :course_id, NOW())
    ");

        try {
            $stmt->execute([
                ':user_id' => $user['id'],
                ':course_id' => $courseId,
            ]);
            $_SESSION['success'] = 'You have been enrolled in the course.';
        } catch (\PDOException $e) {
            $_SESSION['error'] = 'An error occurred while enrolling. Please try again.';
        }

        Application::$app->response->redirect(BASE_URL . "/courses/{$courseId}");
    }


    private function ensureSessionStarted(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start([
                'cookie_lifetime' => 86400,
                'cookie_secure' => isset($_SERVER['HTTPS']),
                'cookie_httponly' => true,
                'cookie_samesite' => 'Strict'
            ]);
        }
    }

    private function checkInstructorAccess(): void
    {
        if (($_SESSION['user']['role'] ?? null) !== 'instructor') {
            $_SESSION['error'] = 'Unauthorized access';
            Application::$app->response->redirect(BASE_URL . '/dashboard');
            exit;
        }
    }
    public function enrolledCourses()
    {
        $this->ensureSessionStarted();

        $user = $_SESSION['user'] ?? null;
        if (!$user || $user['role'] !== 'student') {
            $_SESSION['error'] = 'Unauthorized';
            Application::$app->response->redirect(BASE_URL . '/login');
            return;
        }

        $courses = \models\Enrollment::findByUser($user['id']);
        require_once Application::$ROOT_DIR . '/views/courses/my-courses.php';
    }

}
