<?php
use core\Application;

$userEmail = $_SESSION['user'] ?? 'Guest';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="<?= Application::$app->config['BASE_URL'] ?>/assets/css/output.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

<!-- Header -->
<header class="bg-blue-600 text-white shadow">
    <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
        <h1 class="text-xl font-bold">My Dashboard</h1>
        <div class="flex items-center space-x-4">
            <span><?= htmlspecialchars($userEmail) ?></span>
            <a href="<?= Application::$app->config['BASE_URL'] ?>/logout" class="bg-red-500 px-3 py-1 rounded hover:bg-red-600 transition">Logout</a>
        </div>
    </div>
</header>

<!-- Main Content -->
<main class="flex-grow">
    <div class="max-w-4xl mx-auto px-4 py-10">
        <h2 class="text-2xl font-semibold mb-4">Welcome to your dashboard 👋</h2>
        <p class="text-gray-700 mb-6">Here you can manage your account, view courses, and more.</p>

        <!-- Example Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded shadow hover:shadow-md transition">
                <h3 class="text-lg font-semibold mb-2">My Courses</h3>
                <p class="text-gray-600">View and manage your enrolled courses.</p>
                <a href="<?= Application::$app->config['BASE_URL'] ?>/courses" class="inline-block mt-3 text-blue-600 hover:underline">Go to Courses →</a>
            </div>

            <div class="bg-white p-6 rounded shadow hover:shadow-md transition">
                <h3 class="text-lg font-semibold mb-2">Profile</h3>
                <p class="text-gray-600">Update your personal information and preferences.</p>
                <a href="#" class="inline-block mt-3 text-blue-600 hover:underline">Edit Profile →</a>
            </div>
        </div>
    </div>
</main>

<!-- Footer -->
<footer class="bg-gray-800 text-gray-200 py-4 mt-8">
    <div class="text-center text-sm">
        &copy; <?= date('Y') ?> IRStars Bootcamp. All rights reserved.
    </div>
</footer>

</body>
</html>
