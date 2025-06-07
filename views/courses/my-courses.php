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
<div class="max-w-5xl mx-auto">
    <h1 class="text-3xl font-bold text-blue-800 mb-6">My Courses</h1>

    <?php if (!empty($courses)): ?>
        <div class="space-y-4">
            <?php foreach ($courses as $course): ?>
                <?php if (!isset($course->id)) continue; // skip invalid entries ?>
                <div class="p-4 bg-white rounded-lg shadow hover:shadow-md transition">
                    <h2 class="text-xl font-semibold text-blue-700"><?= htmlspecialchars($course->title) ?></h2>
                    <p class="text-gray-700 mt-1"><?= htmlspecialchars($course->description) ?></p>
                    <p class="text-sm text-gray-600 mt-2">
                        <strong>Status:</strong> <span class="font-medium"><?= htmlspecialchars($course->status) ?></span>
                    </p>
                    <div class="mt-3">
                        <a href="<?= BASE_URL ?>/courses/<?= urlencode($course->id) ?>" class="text-blue-600 hover:underline text-sm">
                            View Details →
                        </a>
                        <a href="<?= BASE_URL ?>/courses/<?= urlencode($course->id) ?>/edit" class="ml-4 text-yellow-600 hover:underline text-sm">
                            Edit →
                        </a>
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
