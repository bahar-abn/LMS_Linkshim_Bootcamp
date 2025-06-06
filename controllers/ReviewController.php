<?php

namespace controllers;

use core\Request;
use core\Response;
use core\Session;
use models\Review;

class ReviewController
{
    public function storeReview(Request $request, Response $response): void
    {
        $session = new Session();
        $user = $session->get('user');

        if (!$user || !isset($user['id'])) {
            $response->redirect('/login');
            return;
        }

        $userId = $user['id'];
        $data = $request->getBody();

        if (Review::addReview($userId, $data['course_id'], $data['rating'], $data['comment'])) {
            $session->setflash('success', 'Your review has been saved');
        } else {
            $session->setflash('error', 'Error while saving your review');
        }

        $response->redirect("/courses/{$data['course_id']}");
    }

    public function index(Request $request): void
    {
        $courseId = $request->getRouteParam('id') ?? null;

        if (!$courseId) {
            (new Response())->redirect('/courses');
            return;
        }

        $reviews = Review::getByCourseId($courseId);

        require_once \core\Application::$ROOT_DIR . '/views/reviews/index.php';
    }

    public function myCourseReviews(Request $request): void
    {
        $session = new Session();
        $user = $session->get('user');

        if (!$user || !isset($user['id'])) {
            (new Response())->redirect('/login');
            return;
        }

        $userId = $user['id'];
        $reviews = Review::getByUserId($userId); // This method must exist in Review model

        require_once \core\Application::$ROOT_DIR . '/views/reviews/my-reviews.php';
    }
}
