<?php

namespace controllers;

use core\Request;
use core\Response;
use core\Session;
use core\Application;
use models\Review;
use core\MainController;

class ReviewController extends MainController
{
    public function storeReview(Request $request): void
    {
        $response = new Response();
        $session = new Session();
        $user = $session->get('user');

        if (!$user || !isset($user['id'])) {
            $session->setFlash('error', 'You need to login to submit a review');
            $response->redirect(BASE_URL . '/login');
            return;
        }

        $userId = $user['id'];
        $data = $request->getBody();

        // Validate required fields
        if (empty($data['course_id']) || empty($data['rating'])) {
            $session->setFlash('error', 'Rating is required');
            $response->redirect(BASE_URL . "/courses/{$data['course_id']}");
            return;
        }

        $comment = $data['comment'] ?? '';
        $courseId = (int) $data['course_id'];
        $rating = (int) $data['rating'];

        // Validate rating range
        if ($rating < 1 || $rating > 5) {
            $session->setFlash('error', 'Rating must be between 1 and 5');
            $response->redirect(BASE_URL . "/courses/{$courseId}");
            return;
        }

        if (Review::addReview($userId, $courseId, $rating, $comment)) {
            $session->setFlash('success', 'Your review has been saved');
        } else {
            $session->setFlash('error', 'You have already reviewed this course');
        }

        $response->redirect(BASE_URL . "/courses/{$courseId}");
    }

    public function index(Request $request): void
    {
        $courseId = $request->getRouteParam('id') ?? null;

        if (!$courseId) {
            (new Response())->redirect(BASE_URL . '/courses');
            return;
        }

        $reviews = Review::getByCourseId($courseId);
        $averageRating = Review::getAverageRating($courseId);
        $ratingCount = Review::getRatingCount($courseId);

        require_once Application::$ROOT_DIR . '/views/reviews/index.php';
    }

    public function myCourseReviews()
{
    // Verify instructor is logged in
    if (($_SESSION['user']['role'] ?? '') !== 'instructor') {
        Application::$app->response->redirect(BASE_URL . '/login');
        exit;
    }

    $instructorId = $_SESSION['user']['id'];
    $reviews = Review::getByInstructorId($instructorId);

    return $this->render3('reviews/instructor-reviews', [
        'reviews' => $reviews
    ]);
}

    public function deleteReview(Request $request): void
    {
        $session = new Session();
        $user = $session->get('user');
        $response = new Response();

        if (!$user || !isset($user['id'])) {
            $session->setFlash('error', 'You need to login to perform this action');
            $response->redirect(BASE_URL . '/login');
            return;
        }

        $reviewId = $request->getRouteParam('id');
        $review = Review::find($reviewId);

        if (!$review) {
            $session->setFlash('error', 'Review not found');
            $response->redirect(BASE_URL . '/courses');
            return;
        }

        // Check if the review belongs to the current user or if user is admin
        if ($review->user_id != $user['id'] && ($user['role'] ?? 'user') !== 'admin') {
            $session->setFlash('error', 'You are not authorized to delete this review');
            $response->redirect(BASE_URL . "/courses/{$review->course_id}");
            return;
        }

        $db = Application::$app->db->pdo;
        $stmt = $db->prepare("DELETE FROM reviews WHERE id = :id");
        $stmt->bindValue(':id', $reviewId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $session->setFlash('success', 'Review deleted successfully');
        } else {
            $session->setFlash('error', 'Failed to delete review');
        }

        $redirectUrl = ($user['role'] ?? 'user') === 'admin'
            ? BASE_URL . '/admin/reviews'
            : BASE_URL . "/courses/{$review->course_id}";

        $response->redirect($redirectUrl);
    }
}