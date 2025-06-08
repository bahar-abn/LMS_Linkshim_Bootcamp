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
    public function redirect(string $url): void
    {
        if (!headers_sent()) {
            header("Location: $url");
            exit;
        }

        // Fallback for when headers are sent
        echo "<script>window.location.href='$url';</script>";
        exit;
    }
}
