<?php
// In core/MainController.php
namespace core;

class MainController
{
    public function render($viewPath, $params = [])
    {
        extract($params); // makes $users, $courses, etc. available in the view
        require_once __DIR__ . '/../views/' . $viewPath . '.php';
    }
}
