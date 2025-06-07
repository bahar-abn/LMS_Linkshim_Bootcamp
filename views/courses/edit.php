<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!defined('BASE_URL')) define('BASE_URL', '/lms-php-mvc/public');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Course</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
<div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-md">
    <h1 class="text-2xl font-bold text-blue-800 mb-6">Edit Course</h1>

    <form method="post" action="<?= BASE_URL ?>/courses/<?= urlencode($course->id) ?>/update" class="space-y-4">
        <div>
            <label class="block text-sm font-medium">Title</label>
            <input type="text" name="title" value="<?= htmlspecialchars($course->title) ?>" class="w-full p-2 border border-gray-300 rounded">
        </div>

        <div>
            <label class="block text-sm font-medium">Description</label>
            <textarea name="description" rows="5" class="w-full p-2 border border-gray-300 rounded"><?= htmlspecialchars($course->description) ?></textarea>
        </div>

        <div>
            <label class="block text-sm font-medium">Category</label>
            <select name="category_id" class="w-full p-2 border border-gray-300 rounded">
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category->id ?>" <?= $course->category_id == $category->id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category->name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Update Course
            </button>
        </div>
    </form>
</div>
</body>
</html>
