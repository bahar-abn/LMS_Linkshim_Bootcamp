<?php
require __DIR__ . '/../vendor/autoload.php';

// Initialize the application
$config = require __DIR__ . '/../config/config.php';
$app = new core\Application(dirname(__DIR__), $config);

// Run the tests
require __DIR__ . '/../core/TestConnection.php';
core\TestConnection::run();