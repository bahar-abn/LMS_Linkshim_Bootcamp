<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!defined('BASE_URL')) define('BASE_URL', '/lms-php-mvc/public');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Courses</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
<div class="max-w-6xl mx-auto">
    <h1 class="text-3xl font-bold mb-6 text-blue-800">My Courses</h1>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="bg-green-100 text-green-800 p-3 mb-4 rounded">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="bg-red-100 text-red-800 p-3 mb-4 rounded">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="mb-4">
        <a href="<?= BASE_URL ?>/courses/create" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Create New Course
        </a>
    </div>

    <?php if (!empty($courses)): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($courses as $course): ?>
                <div class="bg-white p-4 rounded shadow hover:shadow-md transition">
                    <h2 class="text-xl font-semibold mb-2 text-blue-700">
                        <?= htmlspecialchars($course->title) ?>
                    </h2>
                    <p class="text-gray-700 mb-3 text-sm line-clamp-3">
                        <?= htmlspecialchars($course->description) ?>
                    </p>
                    <div class="flex justify-between items-center">
                        <span class="text-xs <?=
                        $course->status === 'approved' ? 'text-green-600' :
                            ($course->status === 'pending' ? 'text-yellow-600' : 'text-red-600')
                        ?> font-semibold">
                            <?= htmlspecialchars($course->status) ?>
                        </span>
                        <div class="space-x-2">
                            <a href="<?= BASE_URL ?>/courses/<?= $course->id ?>/edit"
                               class="text-sm bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600">
                                Edit
                            </a>
                            <a href="<?= BASE_URL ?>/courses/<?= $course->id ?>"
                               class="text-sm bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600">
                                View
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-gray-600">You haven't created any courses yet.</p>
    <?php endif; ?>
</div>
</body>
</html>