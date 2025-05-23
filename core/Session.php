<?php

namespace core;

class Session {
    public function __construct() {
        session_start();
    }

    public function set(string $key, $value) {
        $_SESSION[$key] = $value;
    }

    public function get(string $key) {
        return $_SESSION[$key] ?? false;
    }

    public function remove(string $key) {
        unset($_SESSION[$key]);
    }
}
