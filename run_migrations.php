<?php
require_once __DIR__ . '/vendor/autoload.php';

use core\Application;

$config = require_once __DIR__ . '/config/config.php';

$app = new Application(dirname(__DIR__), $config);

$pdo = $app->db->pdo;
$migrations = [
    'm0001_create_users_table',
    'm0002_create_categories_table',
    'm0003_create_courses_table',
    'm0004_create_enrollments_table',
    'm0005_create_reviews_table'
];

foreach ($migrations as $migration) {
    require_once __DIR__ . '/core/migrations/' . $migration . '.php';
    $class = new $migration();
    echo "Running migration: $migration\n";
    $class->up($pdo);
}

echo "✅ All migrations completed!\n";
