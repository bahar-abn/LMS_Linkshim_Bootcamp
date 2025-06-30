<?php

require_once __DIR__ . '/vendor/autoload.php';

use core\Application;

$config = require_once __DIR__ . '/config/config.php';

$app = new Application(__DIR__, $config);

$app->db->applyMigrations();
// After applyMigrations()
$app->db->addDefaultCategories();