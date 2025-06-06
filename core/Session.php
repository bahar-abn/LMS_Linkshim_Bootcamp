<?php

namespace core;

class Session {
    public function __construct() {
        session_start();
    }

    public function setflash(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function get(string $key) {
        return $_SESSION[$key] ?? false;
    }

    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }
}
