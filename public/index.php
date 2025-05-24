<?php

require_once __DIR__ . '/../vendor/autoload.php';

use core\Application;

$config = require_once __DIR__ . '/../config/config.php';

$app = new Application(dirname(__DIR__), $config);

require_once __DIR__ . '/../routes.php';

$app->run();
