<?php

use core\Application;

$config = require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../vendor/autoload.php';

$app = new Application(dirname(__DIR__), $config);

// ✅ Centralized routes file
require_once __DIR__ . '/../routes.php';

$app->run();
