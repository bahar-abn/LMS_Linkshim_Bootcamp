<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!defined('BASE_URL')) define('BASE_URL', '/lms-php-mvc/public');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Courses</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
<div class="max-w-6xl mx-auto">
    <h1 class="text-3xl font-bold mb-6 text-blue-800">All Courses</h1>

    <!-- Search and Filter -->
    <form method="get" class="mb-6 flex flex-wrap gap-4">
        <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
               placeholder="Search by title..." class="px-4 py-2 border rounded w-full sm:w-auto">

        <select name="category" class="px-4 py-2 border rounded w-full sm:w-auto bg-white text-black">
            <option value="">All Categories</option>
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat->id ?>" <?= ($_GET['category'] ?? '') == $cat->id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat->name) ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Search
        </button>
    </form>

    <!-- Course Grid -->
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
                    <a href="<?= BASE_URL ?>/courses/<?= $course->id ?>"
                       class="inline-block mt-2 text-sm text-blue-600 hover:underline font-medium">
                        View Details →
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-gray-600">No courses found.</p>
    <?php endif; ?>
</div>
</body>
</html>
