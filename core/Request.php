<?php
namespace core;

class Request {
    public function getPath(): string {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $path = strtok($path, '?'); // Remove query string

        // Adjust base path (set to your project public folder route)
        $basePath = '/lms-php-mvc/public';
        if (str_starts_with($path, $basePath)) {
            $path = substr($path, strlen($basePath));
        }

        return $path === '' ? '/' : $path;
    }

    public function getMethod(): string {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function getBody(): array {
        $body = [];

        if ($this->getMethod() === 'get') {
            foreach ($_GET as $key => $value) {
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        if ($this->getMethod() === 'post') {
            foreach ($_POST as $key => $value) {
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        return $body;
    }
}
