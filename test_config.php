<?php

// Autoloader ساده (یا دستی include کن)
require_once __DIR__ . '/core/Application.php';
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Request.php';
require_once __DIR__ . '/core/Response.php';
require_once __DIR__ . '/core/Router.php';
require_once __DIR__ . '/core/Session.php'; // ✅ اگر Session وجود دارد
require_once __DIR__ . '/models/User.php';

use models\User;
use core\Application;

// پیکربندی اتصال دیتابیس
$config = [
    'db' => [
        'dsn' => 'mysql:host=localhost;dbname=bootcamp_db;charset=utf8mb4',
        'user' => 'root',
        'password' => ''
    ],
    'BASE_URL' => 'http://localhost/projects/bootcamp_course_site/public'
];

// ✅ ابتدا Application را مقداردهی کن
$app = new Application(__DIR__, $config);

// سپس از User استفاده کن
$user = new User();
$user->loadData([
    'name' => 'Test User',
    'email' => 'test_' . time() . '@example.com',
    'password' => '123456'
]);

if ($user->save()) {
    echo "✅ User saved successfully!";
} else {
    echo "❌ Failed to save user.";
}
