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
    <h1 class="text-3xl font-bold mb-6 text-blue-800">My Courses</h1>

    <?php if (!empty($courses)): ?>
        <div class="space-y-4">
            <?php foreach ($courses as $course): ?>
                <div class="p-4 bg-white rounded shadow hover:shadow-md transition">
                    <h2 class="text-xl font-semibold text-blue-700"><?= htmlspecialchars($course->title) ?></h2>
                    <p class="text-gray-700"><?= htmlspecialchars($course->description) ?></p>
                    <p class="text-sm text-gray-600 mt-1">Status: <span class="font-medium"><?= htmlspecialchars($course->status) ?></span></p>
                    <a href="<?= BASE_URL ?>/courses/<?= $course->id ?>" class="text-sm text-blue-600 hover:underline inline-block mt-2">
                        View Details →
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-gray-600">You have not created any courses yet.</p>
    <?php endif; ?>
</div>
</body>
</html>
