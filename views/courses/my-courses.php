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
<h1 class="text-2xl font-bold mb-4">My Courses</h1>

<?php if (!empty($courses)): ?>
    <div class="space-y-4">
        <?php foreach ($courses as $course): ?>
            <div class="p-4 bg-white rounded shadow">
                <h2 class="text-xl font-semibold"><?= htmlspecialchars($course['title']) ?></h2>
                <p class="text-gray-700"><?= htmlspecialchars($course['description']) ?></p>
                <p class="text-sm text-gray-500">Status: <?= htmlspecialchars($course['status']) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p class="text-gray-600">You have not created any courses yet.</p>
<?php endif; ?>
</body>
</html>
