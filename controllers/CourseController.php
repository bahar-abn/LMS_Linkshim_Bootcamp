<?php
namespace controllers;

use core\Application;
use core\Request;
use models\Course;
use models\Category;
use models\Review;

class CourseController
{
    public function index()
    {
        $this->ensureSessionStarted();
        $courses = Course::allApproved();
        require_once Application::$ROOT_DIR . '/views/courses/index.php';
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
            exit;
        }

        $course = new Course([
            'title' => trim($data['title']),
            'description' => trim($data['description'] ?? ''),
            'category_id' => (int)($data['category_id'] ?? 1),
            'instructor_id' => $_SESSION['user']['id'],
            'status' => 'pending'
        ]);

        if ($course->save()) {
            $_SESSION['success'] = 'Course submitted for admin approval';
        } else {
            $_SESSION['error'] = 'Failed to create course';
        }

        Application::$app->response->redirect(BASE_URL . '/my-courses');
    }

    public function details(Request $request)
    {
        $this->ensureSessionStarted();
        $id = $request->getRouteParam('id');
        $course = Course::findById($id);

        if (!$course) {
            Application::$app->response->setStatusCode(404);
            echo "Course not found.";
            return;
        }

        $reviews = Review::getByCourseId($id);
        require_once Application::$ROOT_DIR . '/views/courses/details.php';
    }

    public function edit(Request $request)
    {
        $this->ensureSessionStarted();
        $this->checkInstructorAccess();

        $id = $request->getRouteParam('id');
        $course = Course::findById($id);

        if (!$course || (int)$course['instructor_id'] !== (int)$_SESSION['user']['id']) {
            $_SESSION['error'] = 'Unauthorized access';
            Application::$app->response->redirect(BASE_URL . '/dashboard');
            exit;
        }

        $categories = Category::all();
        require_once Application::$ROOT_DIR . '/views/courses/edit.php';
    }

    public function update(Request $request)
    {
        $this->ensureSessionStarted();
        $this->checkInstructorAccess();

        $id = $request->getRouteParam('id');
        $existingCourse = Course::findById($id);

        if (!$existingCourse || (int)$existingCourse['instructor_id'] !== (int)$_SESSION['user']['id']) {
            $_SESSION['error'] = 'Unauthorized access';
            Application::$app->response->redirect(BASE_URL . '/dashboard');
            exit;
        }

        $data = $request->getBody();

        $course = new Course([
            'title' => trim($data['title'] ?? $existingCourse['title']),
            'description' => trim($data['description'] ?? $existingCourse['description']),
            'category_id' => (int)($data['category_id'] ?? $existingCourse['category_id']),
            'instructor_id' => $_SESSION['user']['id'],
            'status' => 'pending' // reset to pending after update
        ]);

        if ($course->update($id)) {
            $_SESSION['success'] = 'Course updated and submitted for review';
        } else {
            $_SESSION['error'] = 'Failed to update course';
        }

        Application::$app->response->redirect(BASE_URL . '/my-courses');
    }

    public function enroll(Request $request)
    {
        $this->ensureSessionStarted();

        if ($_SESSION['user']['role'] !== 'student') {
            $_SESSION['error'] = 'Only students can enroll in courses';
            Application::$app->response->redirect(BASE_URL . '/dashboard');
            exit;
        }

        $courseId = $request->getRouteParam('id');
        $userId = $_SESSION['user']['id'];

        $pdo = Application::$app->db->pdo;

        // Check if already enrolled
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM enrollments WHERE user_id = ? AND course_id = ?");
        $stmt->execute([$userId, $courseId]);
        if ($stmt->fetchColumn() > 0) {
            $_SESSION['error'] = 'You are already enrolled in this course';
            Application::$app->response->redirect(BASE_URL . "/courses/$courseId");
            exit;
        }

        // Enroll
        $stmt = $pdo->prepare("INSERT INTO enrollments (user_id, course_id) VALUES (?, ?)");
        $stmt->execute([$userId, $courseId]);

        $_SESSION['success'] = 'Successfully enrolled in course';
        Application::$app->response->redirect(BASE_URL . '/courses/' . $courseId);
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
}
