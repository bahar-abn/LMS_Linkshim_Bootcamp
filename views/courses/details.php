<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!defined('BASE_URL')) define('BASE_URL', '/lms-php-mvc/public');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Course Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
<div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
    <?php if (!empty($course)): ?>
        <h1 class="text-3xl font-bold mb-4"><?= htmlspecialchars($course['title']) ?></h1>
        <p class="text-gray-700 mb-4"><?= nl2br(htmlspecialchars($course['description'])) ?></p>
        <p class="text-sm text-gray-600 mb-6">Status: <?= htmlspecialchars($course['status']) ?></p>

        <?php if (($_SESSION['role'] ?? '') === 'student'): ?>
            <form action="<?= BASE_URL ?>/courses/<?= $course['id'] ?>/enroll" method="post">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    Enroll in this Course
                </button>
            </form>
        <?php endif; ?>
    <?php else: ?>
        <p class="text-red-600">Course not found.</p>
    <?php endif; ?>
</div>
</body>
</html>
