<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!defined('BASE_URL')) define('BASE_URL', '/lms-php-mvc/public');

// Ensure $categories is initialized
$categories = $categories ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Course</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
<div class="w-full max-w-lg bg-white p-8 rounded shadow">
    <h1 class="text-2xl font-bold mb-6 text-center text-blue-700">Create New Course</h1>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/courses/create" method="post" class="space-y-4">
        <div>
            <label for="title" class="block mb-1 font-medium">Title</label>
            <input
                    type="text"
                    name="title"
                    id="title"
                    required
                    class="w-full border border-gray-300 rounded px-3 py-2"
            />
        </div>

        <div>
            <label for="description" class="block mb-1 font-medium">Description</label>
            <textarea
                    name="description"
                    id="description"
                    rows="4"
                    required
                    class="w-full border border-gray-300 rounded px-3 py-2"
            ></textarea>
        </div>

        <div>
            <label for="category" class="block mb-1 font-medium">Category</label>
            <select
                    name="category_id"
                    id="category"
                    required
                    class="w-full border border-gray-300 rounded px-3 py-2"
            >
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category->id ?>">
                            <?= htmlspecialchars($category->name) ?>
                        </option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option disabled>No categories available</option>
                <?php endif; ?>
            </select>
        </div>

        <button
                type="submit"
                class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700"
        >
            Create Course
        </button>
    </form>
</div>
</body>
</html>
