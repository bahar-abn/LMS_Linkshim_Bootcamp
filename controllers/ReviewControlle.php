<?php

namespace controllers;

use core\Request;
use core\Response;
use core\Session;
use models\Enrollment;
use models\Review;

class ReviewController
{
    public function enroll(Request $request, Response $response)
    {
        $userId = Session::get('user')['id'];
        $courseId = $request->getBody()['course_id'] ?? null;

        if (!$courseId) {
            $response->redirect('/courses');
            return;
        }

        if (Enrollment::enroll($userId, $courseId)) {
            Session::setFlash('success', 'you have successfully enrolled in this course');
        } else {
            Session::setFlash('error', 'you are not enrolled in this course');
        }

        $response->redirect("/courses/details?id=$courseId");
    }

    public function storeReview(Request $request, Response $response)
    {
        $userId = Session::get('user')['id'];
        $data = $request->getBody();

        if (Review::addReview($userId, $data['course_id'], $data['rating'], $data['comment'])) {
            Session::setFlash('success', 'your review has been saved');
        } else {
            Session::setFlash('error', 'error while saving your review');
        }

        $response->redirect("/courses/details?id=" . $data['course_id']);
    }

    public function index(Request $request)
    {
        $courseId = $request->getBody()['course_id'] ?? null;

        if (!$courseId) {
            Response::redirect('/courses');
            return;
        }

        $reviews = Review::getByCourseId($courseId);

        return view('reviews/index', [
            'reviews' => $reviews,
            'course_id' => $courseId
        ]);
    }
}
