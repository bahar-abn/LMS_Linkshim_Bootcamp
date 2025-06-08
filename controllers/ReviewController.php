<?php

namespace controllers;

use core\Request;
use core\Response;
use core\Session;
use core\Application;
use models\Review;

class ReviewController
{
    public function storeReview(Request $request): void
    {
        $response = new Response(); // Create manually
        $session = new Session();
        $user = $session->get('user');

        if (!$user || !isset($user['id'])) {
            $response->redirect(BASE_URL . '/login');
            return;
        }

        $userId = $user['id'];
        $data = $request->getBody();

        if (Review::addReview($userId, $data['course_id'], $data['rating'], $data['comment'])) {
            $session->setFlash('success', 'Your review has been saved');
        } else {
            $session->setFlash('error', 'Error while saving your review');
        }

        $response->redirect(BASE_URL . "/courses/{$data['course_id']}");
    }

    public function index(Request $request): void
    {
        $courseId = $request->getRouteParam('id') ?? null;

        if (!$courseId) {
            (new Response())->redirect(BASE_URL . '/courses');
            return;
        }

        $reviews = Review::getByCourseId($courseId);

        require_once Application::$ROOT_DIR . '/views/reviews/index.php';
    }

    public function myCourseReviews(Request $request): void
    {
        $session = new Session();
        $user = $session->get('user');

        if (!$user || !isset($user['id'])) {
            (new Response())->redirect(BASE_URL . '/login');
            return;
        }

        $userId = $user['id'];
        $reviews = Review::getByUserId($userId);

        require_once Application::$ROOT_DIR . '/views/reviews/my-reviews.php';
    }
}
