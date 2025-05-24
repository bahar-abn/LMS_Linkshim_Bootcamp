<?php
namespace controllers;

use core\Request;
use core\Response;
use core\Session;
use models\Course;
use models\Category;

class CourseController
{
    public function index()
    {
        $courses = Course::allApproved();
        return view('courses/index', ['courses' => $courses]);
    }

    public function create()
    {
        if (!Session::get('user') || Session::get('user')['role'] !== 'instructor') {
            return Response::redirect('/login');
        }

        $categories = Category::all();
        return view('courses/create', ['categories' => $categories]);
    }

    public function store(Request $request)
    {
        $data = $request->getBody();
        $course = new Course();
        $course->title = $data['title'];
        $course->description = $data['description'];
        $course->category_id = $data['category_id'];
        $course->instructor_id = Session::get('user')['id'];
        $course->save();
        Session::setFlash('success', 'Course submitted for review.');
        return Response::redirect('/dashboard');
    }

    public function show($id)
    {
        $course = Course::find($id);
        return view('courses/details', ['course' => $course]);
    }
}
