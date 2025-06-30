<?php

namespace controllers;

use core\Application;
use core\MainController;
use core\Request;
use models\User;
use models\Course;
use models\Review;

class AdminController extends MainController
{
    private string $baseUrl;

    public function __construct()
    {
        // No need to call parent::__construct() since MainController has no constructor
        $this->baseUrl = $this->determineBaseUrl();
    }

    private function determineBaseUrl(): string
    {
        // First try to get from config
        if (!empty(Application::$app->config['BASE_URL'])) {
            return Application::$app->config['BASE_URL'];
        }

        // Fallback to auto-detection
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $path = dirname($_SERVER['SCRIPT_NAME'] ?? '');

        // Special handling for public directory
        $publicPath = (strpos($path, '/public') !== false) ? '' : '/public';

        return "$protocol://$host$path$publicPath";
    }

    private function redirectToDashboardWithMessage(string $type, string $message): void
    {
        Application::$app->session->setFlash($type, $message);
        $redirectUrl = rtrim($this->baseUrl, '/') . '/admin-dashboard';
        Application::$app->response->redirect($redirectUrl);
        exit;
    }

    public function manageCourses(): array
    {
        $this->ensureAdmin();

        // Get all distinct courses with their latest status
        $query = "SELECT c.*, u.name as instructor_name 
              FROM courses c
              LEFT JOIN users u ON c.instructor_id = u.id
              WHERE c.id IN (
                  SELECT MAX(id) FROM courses GROUP BY title, instructor_id
              )
              ORDER BY c.status, c.created_at DESC";

        $stmt = Application::$app->db->pdo->query($query);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    private function ensureAdmin(): void
    {
        // First check session
        if (empty($_SESSION['user'])) {
            error_log('No user session - redirecting to login');
            Application::$app->response->redirect($this->baseUrl . '/login');
            exit;
        }

        // Then check application user if available
        if (!empty(Application::$app->user)) {
            $role = Application::$app->user->role ?? null;
        } else {
            // Fallback to session data
            $role = $_SESSION['user']['role'] ?? null;
        }

        if ($role !== 'admin') {
            error_log('Unauthorized access attempt by role: ' . ($role ?? 'none'));
            Application::$app->session->setFlash('error', 'Admin privileges required');

            // Redirect to appropriate dashboard based on role
            $redirect = $this->baseUrl;
            if ($role === 'instructor') {
                $redirect .= '/instructor-dashboard';
            } elseif ($role === 'student') {
                $redirect .= '/dashboard';
            } else {
                $redirect .= '/login';
            }

            Application::$app->response->redirect($redirect);
            exit;
        }
    }

    public function dashboard(): void
    {
        // Get data
        $users = User::getAll() ?? [];
        $courses = $this->manageCourses(); // Updated to use the new method
        $reviews = Review::getAll() ?? [];

        // Debug: Check data before rendering
        error_log("Users count: " . count($users));
        error_log("Courses count: " . count($courses));
        error_log("Reviews count: " . count($reviews));

        // Render with explicit variables
        $this->render('dashboard/admin', [
            'userName' => Application::$app->user->name ?? 'Admin',
            'users' => $users,
            'courses' => $courses,
            'reviews' => $reviews
        ], false); // 🔥 disables layout
    }

    public function approveCourse(Request $request, $id): void
    {
        $this->ensureAdmin();

        $course = Course::find($id);
        if (!$course) {
            $this->redirectToDashboardWithMessage('error', 'Course not found');
            return;
        }

        // Update existing record instead of creating new one
        $course->status = 'approved';
        $course->save();

        $this->redirectToDashboardWithMessage('success', 'Course approved successfully');
    }

    public function rejectCourse(Request $request, $id): void
    {
        $this->ensureAdmin();

        $course = Course::find($id);
        if (!$course) {
            $this->redirectToDashboardWithMessage('error', 'Course not found');
            return;
        }

        // Update existing record instead of creating new one
        $course->status = 'rejected';
        $course->save();

        $this->redirectToDashboardWithMessage('warning', 'Course rejected');
    }

    public function deleteUser(Request $request): void
    {
        $this->ensureAdmin();

        // Get ID from route parameters
        $id = $request->getRouteParam('id') ?? $request->getBody()['id'] ?? null;

        if (!$id) {
            $this->redirectToDashboardWithMessage('error', 'User ID is required');
        }

        try {
            $user = User::find($id);

            if (!$user) {
                $this->redirectToDashboardWithMessage('error', 'User not found');
            }

            if ($user->role === 'admin') {
                $this->redirectToDashboardWithMessage('error', 'Cannot delete admin user');
            }

            // Delete from database
            $stmt = Application::$app->db->pdo->prepare("DELETE FROM users WHERE id = :id");
            $success = $stmt->execute([':id' => $id]);

            if ($success) {
                $this->redirectToDashboardWithMessage('success', 'User deleted successfully');
            } else {
                $this->redirectToDashboardWithMessage('error', 'Failed to delete user');
            }
        } catch (\PDOException $e) {
            error_log("Delete user error: " . $e->getMessage());
            $this->redirectToDashboardWithMessage('error', 'Database error occurred');
        }
    }

    public function deleteReview(Request $request, $id): void
    {
        $this->ensureAdmin();

        $review = Review::find($id);
        if (!$review) {
            $this->redirectToDashboardWithMessage('error', 'Review not found');
        }

        $review->delete();
        $this->redirectToDashboardWithMessage('success', 'Review deleted successfully');
    }
}