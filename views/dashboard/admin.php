<?php
// Initialize variables with proper defaults
$users = $users ?? [];
$courses = $courses ?? [];
$reviews = $reviews ?? [];
$userName = $userName ?? 'Admin';
$baseUrl = defined('BASE_URL') ? BASE_URL : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 min-h-screen">

<!-- Navbar -->
<nav class="bg-white shadow-sm border-b mb-6">
    <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-red-700">📊 Admin Dashboard</h1>
        <div>
            <span class="mr-4">Welcome, <strong><?= htmlspecialchars($userName) ?></strong></span>
            <a href="<?= $baseUrl ?>/logout" class="text-sm text-red-600 hover:underline">Logout</a>
        </div>
    </div>
</nav>

<main class="max-w-6xl mx-auto p-4">
    <!-- Flash Message -->
    <?php if (!empty($_SESSION['flash'])): ?>
        <div class="mb-6 p-4 rounded shadow text-sm font-medium
            <?= match ($_SESSION['flash']['type']) {
            'success' => 'bg-green-100 text-green-700',
            'error' => 'bg-red-100 text-red-700',
            default => 'bg-yellow-100 text-yellow-700'
        } ?>">
            <?= htmlspecialchars($_SESSION['flash']['message']) ?>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <!-- Stats Overview -->
    <section class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <?php foreach ([
                           ['label' => 'Total Users', 'count' => count($users)],
                           ['label' => 'Total Courses', 'count' => count($courses)],
                           ['label' => 'Total Reviews', 'count' => count($reviews)],
                       ] as $stat): ?>
            <div class="bg-white border border-gray-200 p-6 rounded shadow text-center">
                <h3 class="text-lg text-gray-600"><?= $stat['label'] ?></h3>
                <p class="text-2xl font-bold text-red-700"><?= $stat['count'] ?></p>
            </div>
        <?php endforeach; ?>
    </section>

    <!-- Manage Users Section -->
    <section class="mb-10 bg-white shadow p-6 rounded border border-red-100">
        <h2 class="text-xl font-semibold text-red-700 mb-4">👥 Manage Users</h2>
        <?php if (!empty($users)): ?>
            <div class="overflow-x-auto">
                <table class="w-full table-auto border text-sm">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">ID</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">Name</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">Email</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">Role</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr class="border-t">
                            <td class="px-4 py-2"><?= htmlspecialchars($user->id) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($user->name) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($user->email) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($user->role) ?></td>
                            <td class="px-4 py-2">
                                <?php if ($user->role !== 'admin'): ?>
                                    <a href="<?= $baseUrl ?>/admin/delete-user/<?= $user->id ?>"
                                       class="text-red-600 hover:underline"
                                       onclick="return confirm('Delete this user?')">
                                        Delete
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-gray-600">No users found.</p>
        <?php endif; ?>
    </section>

    <!-- Manage Courses Section -->
    <section class="mb-10 bg-white shadow p-6 rounded border border-red-100">
        <h2 class="text-xl font-semibold text-red-700 mb-4">📘 Manage Courses</h2>
        <?php if (!empty($courses)): ?>
            <div class="overflow-x-auto">
                <table class="w-full table-auto border text-sm">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">ID</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">Title</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">Instructor</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">Status</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($courses as $course): ?>
                        <tr class="border-t">
                            <td class="px-4 py-2"><?= htmlspecialchars($course->id) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($course->title) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($course->instructor_name ?? 'Unknown') ?></td>
                            <td class="px-4 py-2">
                                <span class="font-semibold <?=
                                $course->status === 'approved' ? 'text-green-600' :
                                    ($course->status === 'rejected' ? 'text-red-600' : 'text-yellow-600')
                                ?>">
                                    <?= htmlspecialchars($course->status ?? 'pending') ?>
                                </span>
                            </td>
                            <td class="px-4 py-2 space-x-2">
                                <?php if ($course->status === 'pending'): ?>
                                    <a href="<?= $baseUrl ?>/admin/approve-course/<?= $course->id ?>"
                                       class="text-green-600 hover:underline"
                                       onclick="return confirm('Approve this course?')">
                                        Approve
                                    </a>
                                    <a href="<?= $baseUrl ?>/admin/reject-course/<?= $course->id ?>"
                                       class="text-red-600 hover:underline"
                                       onclick="return confirm('Reject this course?')">
                                        Reject
                                    </a>
                                <?php else: ?>
                                    <span class="text-gray-500">No action needed</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-gray-600">No courses found.</p>
        <?php endif; ?>
    </section>

    <!-- Manage Reviews Section -->
    <section class="mb-10 bg-white shadow p-6 rounded border border-red-100">
        <h2 class="text-xl font-semibold text-red-700 mb-4">📝 Manage Reviews</h2>
        <?php if (!empty($reviews)): ?>
            <div class="overflow-x-auto">
                <table class="w-full table-auto border text-sm">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">ID</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">User</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">Course</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">Comment</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">Rating</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($reviews as $review): ?>
                        <tr class="border-t">
                            <td class="px-4 py-2"><?= htmlspecialchars($review->id) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($review->user_name ?? 'Unknown') ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($review->course_title ?? 'Unknown') ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($review->comment) ?></td>
                            <td class="px-4 py-2">
                                <?= isset($review->rating) ? str_repeat('⭐', (int)$review->rating) : '—' ?>
                            </td>
                            <td class="px-4 py-2">
                                <a href="<?= $baseUrl ?>/admin/delete-review/<?= $review->id ?>"
                                   class="text-red-600 hover:underline"
                                   onclick="return confirm('Delete this review?')">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-gray-600">No reviews found.</p>
        <?php endif; ?>
    </section>
</main>
</body>
</html>