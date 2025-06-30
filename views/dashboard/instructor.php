<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 86400,
        'cookie_secure' => isset($_SERVER['HTTPS']),
        'cookie_httponly' => true,
        'cookie_samesite' => 'Strict'
    ]);
}

if (!defined('BASE_URL')) {
    define('BASE_URL', '/lms-php-mvc/public');
}

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'instructor') {
    header('Location: ' . BASE_URL . '/login');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
<div class="w-full max-w-3xl bg-white p-8 rounded-lg shadow-md">
    <h1 class="text-3xl font-bold text-green-700 mb-4">Instructor Dashboard</h1>
    <p class="text-gray-700 mb-6">
        Welcome, <span class="font-semibold"><?= htmlspecialchars($_SESSION['user']['name'] ?? 'Instructor') ?></span> (Instructor)
    </p>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <!-- My Courses -->
        <a href="<?= BASE_URL ?>/instructor/my-courses"
           class="block bg-green-100 hover:bg-green-200 text-green-800 font-medium px-4 py-3 rounded shadow text-center transition">
            📚 View & Manage My Courses
        </a>

        <!-- Create New Course -->
        <a href="<?= BASE_URL ?>/courses/create"
           class="block bg-blue-100 hover:bg-blue-200 text-blue-800 font-medium px-4 py-3 rounded shadow text-center transition">
            ➕ Create New Course
        </a>

        <!-- View Reviews -->
        <a href="<?= BASE_URL ?>/my-course-reviews"
           class="block bg-purple-100 hover:bg-purple-200 text-purple-800 font-medium px-4 py-3 rounded shadow text-center transition">
            📝 View Course Reviews
        </a>

        <!-- Logout -->
        <a href="<?= BASE_URL ?>/logout"
           class="block bg-red-100 hover:bg-red-200 text-red-800 font-medium px-4 py-3 rounded shadow text-center transition">
            🚪 Logout
        </a>
    </div>
</div>
</body>
</html>