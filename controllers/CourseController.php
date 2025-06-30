<?php

namespace controllers;

use core\Application;
use core\MainController;
use core\Request;
use models\Course;
use models\Category;
use models\Review;
use core\Session;
use models\Enrollment;

class CourseController extends MainController
{
    public function index()
    {
        $this->ensureSessionStarted();

        $search = $_GET['search'] ?? '';
        $category = $_GET['category'] ?? '';

        $categories = Category::getAll();
        $courses = Course::searchAndFilter($search, $category);

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

        $categories = Category::getAll();
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

        $course = Course::findById($courseId);
        if (!$course) {
            $_SESSION['error'] = 'Course not found.';
            Application::$app->response->redirect(BASE_URL . '/courses');
            return;
        }

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

    public function enrolledCourses(Request $request)
{
    $this->ensureSessionStarted();

    $user = $_SESSION['user'] ?? null;
    if (!$user || $user['role'] !== 'student') {
        $_SESSION['error'] = 'Unauthorized';
        Application::$app->response->redirect(BASE_URL . '/login');
        return;
    }

    // Get enrolled courses (now returns Course objects)
    $courses = Enrollment::findByUser($user['id']);

    // Handle selected course
    $selectedCourseId = $request->getRouteParam('id');
    $selectedCourse = null;
    $reviews = [];

    if ($selectedCourseId) {
        // Find the selected course from the enrolled courses
        foreach ($courses as $course) {
            if ($course->id == $selectedCourseId) {
                $selectedCourse = $course;
                break;
            }
        }
        
        if ($selectedCourse) {
            $reviews = Review::getByCourseId($selectedCourseId);
        }
    }

    require_once Application::$ROOT_DIR . '/views/courses/my-courses.php';
}

    public function instructorDashboard()
    {
        $this->ensureSessionStarted();

        $instructorId = $_SESSION['user']['id'] ?? null;

        if (!$instructorId) {
            Application::$app->response->redirect(BASE_URL . '/login');
            return;
        }

        $courses = Course::findByInstructor($instructorId);
        include Application::$ROOT_DIR . '/views/dashboard/instructor.php';
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
    public function edit(Request $request, $id) {
        $course = Course::find($id);
        if (!$course) {
            $this->response->redirect(BASE_URL . '/courses');
            return;
        }

        // Check if current user is the course instructor or admin
        if ($_SESSION['user']['id'] !== $course->instructor_id && $_SESSION['user']['role'] !== 'admin') {
            $_SESSION['error'] = 'You are not authorized to edit this course';
            $this->response->redirect(BASE_URL . '/courses');
            return;
        }

        $categories = Category::getAll();
        return $this->render2('courses/edit', [
            'course' => $course,
            'categories' => $categories
        ]);
    }
    public function instructorCourses()
    {
        // Ensure only instructors can access this
        if (($_SESSION['user']['role'] ?? '') !== 'instructor') {
            Application::$app->response->redirect(BASE_URL . '/login');
            exit;
        }

        $instructorId = $_SESSION['user']['id'] ?? null;
        $courses = Course::findAllByInstructor($instructorId);
        $categories = Category::getAll(); // Now using the proper method

        return $this->render2('courses/instructor-courses', [
            'courses' => $courses,
            'categories' => $categories
        ]);
    }
    public function update(Request $request, $id)
    {
        // Ensure only instructors or admins can update courses
        if (!isset($_SESSION['user']) ||
            ($_SESSION['user']['role'] !== 'instructor' && $_SESSION['user']['role'] !== 'admin')) {
            Application::$app->response->redirect(BASE_URL . '/login');
            exit;
        }

        $course = Course::find($id);
        if (!$course) {
            $_SESSION['error'] = 'Course not found';
            Application::$app->response->redirect(BASE_URL . '/courses');
            return;
        }

        // Verify the instructor owns the course (unless admin)
        if ($_SESSION['user']['role'] === 'instructor' && $course->instructor_id !== $_SESSION['user']['id']) {
            $_SESSION['error'] = 'You can only edit your own courses';
            Application::$app->response->redirect(BASE_URL . '/courses');
            return;
        }

        // Get form data
        $data = $request->getBody();
        $course->loadData($data);

        if ($course->save()) {
            $_SESSION['success'] = 'Course updated successfully';
            Application::$app->response->redirect(BASE_URL . '/courses/' . $course->id);
        } else {
            $_SESSION['error'] = 'Failed to update course';
            Application::$app->response->redirect(BASE_URL . '/courses/' . $course->id . '/edit');
        }
    }
}