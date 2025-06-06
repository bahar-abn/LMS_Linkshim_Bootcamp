<?php

namespace core;

class Response {
    /**
     * Sets the HTTP response status code
     */
    public function setStatusCode(int $code): void {
        http_response_code($code);
    }

    /**
     * Redirects to a different URL
     */
    public function redirect(string $url): void {
        header("Location: $url");
        exit;
    }
}
