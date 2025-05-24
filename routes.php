<?php

//Courses:

use controllers\CourseController;

$router->get('/courses', [CourseController::class, 'index']);
$router->get('/courses/create', [CourseController::class, 'create']);
$router->post('/courses/store', [CourseController::class, 'store']);
$router->get('/courses/{id}', [CourseController::class, 'show']);

//...
