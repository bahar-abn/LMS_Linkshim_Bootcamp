<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>My Enrolled Courses</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">My Enrolled Courses</h1>

    <?php if (!empty($courses)): ?>
        <ul class="space-y-4">
            <?php foreach ($courses as $course): ?>
                <li class="p-4 border rounded bg-gray-50">
                    <h2 class="text-xl font-semibold"><?= htmlspecialchars($course->title ?? '') ?></h2>
                    <p class="text-gray-700"><?= htmlspecialchars($course->description ?? '') ?></p>
                    <a href="<?= BASE_URL ?>/courses/<?= $course->id ?? '' ?>" class="text-blue-600 hover:underline">View Details</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="text-gray-600">You have not enrolled in any courses yet.</p>
    <?php endif; ?>
</div>
</body>
</html>