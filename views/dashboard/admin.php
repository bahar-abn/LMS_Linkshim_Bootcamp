<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!defined('BASE_URL')) {
    define('BASE_URL', '/lms-php-mvc/public');
}

$userName = $_SESSION['user_name'] ?? 'Admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
<div class="max-w-6xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-3xl font-bold text-red-700 mb-6">Admin Dashboard</h1>
    <p class="mb-4">Welcome, <strong><?= htmlspecialchars($userName) ?></strong></p>

    <!-- Manage Users -->
    <div class="bg-red-100 hover:bg-red-200 p-4 rounded mb-6">
        <h2 class="text-xl font-semibold text-red-800 mb-3">👥 Manage Users</h2>
        <?php if (!empty($users)): ?>
            <table class="w-full border">
                <thead>
                <tr class="bg-gray-200">
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Name</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">Role</th>
                    <th class="px-4 py-2">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $user): ?>
                    <tr class="border-t">
                        <td class="px-4 py-2"><?= $user->id ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($user->name) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($user->email) ?></td>
                        <td class="px-4 py-2"><?= $user->role ?></td>
                        <td class="px-4 py-2">
                            <?php if ($user->role !== 'admin'): ?>
                                <a href="<?= BASE_URL ?>/admin/delete-user/<?= $user->id ?>"
                                   class="text-red-600 hover:underline">Delete</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-gray-600">No users found.</p>
        <?php endif; ?>
    </div>

    <!-- Manage Courses -->
    <div class="bg-red-100 hover:bg-red-200 p-4 rounded mb-6">
        <h2 class="text-xl font-semibold text-red-800 mb-3">📘 Manage Courses</h2>
        <?php if (!empty($courses)): ?>
            <table class="w-full border">
                <thead>
                <tr class="bg-gray-200">
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Title</th>
                    <th class="px-4 py-2">Instructor ID</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($courses as $course): ?>
                    <tr class="border-t">
                        <td class="px-4 py-2"><?= $course->id ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($course->title) ?></td>
                        <td class="px-4 py-2"><?= $course->instructor_id ?></td>
                        <td class="px-4 py-2"><?= $course->status ?></td>
                        <td class="px-4 py-2 space-x-2">
                            <a href="<?= BASE_URL ?>/admin/approve-course/<?= $course->id ?>"
                               class="text-green-600 hover:underline">Approve</a>
                            <a href="<?= BASE_URL ?>/admin/reject-course/<?= $course->id ?>"
                               class="text-yellow-600 hover:underline">Reject</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-gray-600">No courses found.</p>
        <?php endif; ?>
    </div>

    <!-- Manage Reviews -->
    <div class="bg-red-100 hover:bg-red-200 p-4 rounded mb-6">
        <h2 class="text-xl font-semibold text-red-800 mb-3">📝 Manage Reviews</h2>
        <?php if (!empty($reviews)): ?>
            <table class="w-full border">
                <thead>
                <tr class="bg-gray-200">
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">User ID</th>
                    <th class="px-4 py-2">Course ID</th>
                    <th class="px-4 py-2">Comment</th>
                    <th class="px-4 py-2">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($reviews as $review): ?>
                    <tr class="border-t">
                        <td class="px-4 py-2"><?= $review->id ?></td>
                        <td class="px-4 py-2"><?= $review->user_id ?></td>
                        <td class="px-4 py-2"><?= $review->course_id ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($review->comment) ?></td>
                        <td class="px-4 py-2">
                            <a href="<?= BASE_URL ?>/admin/delete-review/<?= $review->id ?>"
                               class="text-red-600 hover:underline">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-gray-600">No reviews found.</p>
        <?php endif; ?>
    </div>

    <div class="mt-6">
        <a href="<?= BASE_URL ?>/logout" class="text-blue-600 hover:underline">🚪 Logout</a>
    </div>
</div>
</body>
</html>
