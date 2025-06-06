<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!defined('BASE_URL')) {
    define('BASE_URL', '/lms-php-mvc/public');
}

$userName = $_SESSION['user_name'] ?? 'Admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
<div class="w-full max-w-2xl bg-white p-8 rounded shadow">
    <h1 class="text-2xl font-bold mb-4 text-red-700">Admin Dashboard</h1>
    <p class="text-gray-700 mb-6">
        Welcome, <span class="font-semibold"><?= htmlspecialchars($userName) ?></span> (admin)
    </p>

    <ul class="space-y-3">
        <li>
            <a href="<?= BASE_URL ?>/manage-users"
               class="block bg-red-100 hover:bg-red-200 px-4 py-2 rounded text-red-800">
                👥 Manage Users
            </a>
        </li>
        <li>
            <a href="<?= BASE_URL ?>/manage-courses"
               class="block bg-red-100 hover:bg-red-200 px-4 py-2 rounded text-red-800">
                📘 Manage Courses
            </a>
        </li>
        <li>
            <a href="<?= BASE_URL ?>/manage-reviews"
               class="block bg-red-100 hover:bg-red-200 px-4 py-2 rounded text-red-800">
                📝 Manage Reviews
            </a>
        </li>
        <li>
            <a href="<?= BASE_URL ?>/logout"
               class="block bg-red-100 hover:bg-red-200 px-4 py-2 rounded text-red-800">
                🚪 Logout
            </a>
        </li>
    </ul>
</div>
</body>
</html>
