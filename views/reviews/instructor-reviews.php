<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!defined('BASE_URL')) define('BASE_URL', '/lms-php-mvc/public');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Course Reviews</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
<div class="max-w-6xl mx-auto bg-white p-6 rounded-lg shadow-md">
    <h1 class="text-2xl font-bold text-blue-800 mb-6">My Course Reviews</h1>

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

    <?php if (!empty($reviews)): ?>
        <div class="space-y-4">
            <?php foreach ($reviews as $review): ?>
                <div class="border border-gray-200 p-4 rounded-lg">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-semibold text-lg">
                                <?= htmlspecialchars($review->course_title) ?>
                            </h3>
                            <p class="text-gray-600 text-sm">
                                By <?= htmlspecialchars($review->student_name) ?>
                                on <?= date('M j, Y', strtotime($review->created_at)) ?>
                            </p>
                        </div>
                        <div class="flex items-center bg-yellow-100 px-2 py-1 rounded">
                            <span class="text-yellow-600 mr-1">★</span>
                            <span><?= htmlspecialchars($review->rating) ?>/5</span>
                        </div>
                    </div>
                    <div class="mt-2 text-gray-800">
                        <?= nl2br(htmlspecialchars($review->comment)) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="bg-blue-50 border border-blue-200 text-blue-800 p-4 rounded">
            No reviews found for your courses yet.
        </div>
    <?php endif; ?>

    <div class="mt-6">
        <a href="<?= BASE_URL ?>/instructor-dashboard" 
           class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Back to Dashboard
        </a>
    </div>
</div>
</body>
</html>