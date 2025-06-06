<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<?php $baseUrl = defined('BASE_URL') ? BASE_URL : ''; ?>

<!-- Navbar -->
<nav class="bg-white shadow mb-8">
    <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-red-700">📊 Admin Dashboard</h1>
        <div>
            <span class="mr-4 text-gray-700">Welcome, <strong><?= htmlspecialchars($userName ?? 'Admin') ?></strong></span>
            <a href="<?= $baseUrl ?>/logout" class="text-blue-600 hover:underline">🚪 Logout</a>
        </div>
    </div>
</nav>

<div class="max-w-6xl mx-auto bg-white p-6 rounded shadow">

    <!-- Flash Message -->
    <?php if (!empty($_SESSION['flash'])): ?>
        <div class="mb-4 p-4 rounded
            <?= match ($_SESSION['flash']['type']) {
            'success' => 'bg-green-100 text-green-800',
            'error' => 'bg-red-100 text-red-800',
            default => 'bg-yellow-100 text-yellow-800'
        } ?>">
            <?= htmlspecialchars($_SESSION['flash']['message']) ?>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <!-- Manage Users -->
    <section class="bg-red-100 hover:bg-red-200 p-4 rounded mb-6">
        <h2 class="text-xl font-semibold text-red-800 mb-3">👥 Manage Users</h2>
        <?php if (!empty($users)): ?>
            <div class="overflow-x-auto">
                <table class="w-full border text-sm">
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
                            <td class="px-4 py-2"><?= htmlspecialchars($user->id) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($user->name) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($user->email) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($user->role) ?></td>
                            <td class="px-4 py-2">
                                <?php if ($user->role !== 'admin'): ?>
                                    <a href="<?= $baseUrl ?>/admin/delete-user/<?= $user->id ?>"
                                       class="text-red-600 hover:underline"
                                       onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
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

    <!-- Manage Courses -->
    <section class="bg-red-100 hover:bg-red-200 p-4 rounded mb-6">
        <h2 class="text-xl font-semibold text-red-800 mb-3">📘 Manage Courses</h2>
        <?php if (!empty($courses)): ?>
            <div class="overflow-x-auto">
                <table class="w-full border text-sm">
                    <thead>
                    <tr class="bg-gray-200">
                        <th class="px-4 py-2">ID</th>
                        <th class="px-4 py-2">Title</th>
                        <th class="px-4 py-2">Instructor</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($courses as $course): ?>
                        <tr class="border-t">
                            <td class="px-4 py-2"><?= htmlspecialchars($course->id) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($course->title) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($course->instructor_name ?? 'Unknown') ?></td>
                            <td class="px-4 py-2">
                                <span class="<?= match($course->status) {
                                    'approved' => 'text-green-600',
                                    'rejected' => 'text-red-600',
                                    default => 'text-yellow-600'
                                } ?>">
                                    <?= htmlspecialchars($course->status ?? 'pending') ?>
                                </span>
                            </td>
                            <td class="px-4 py-2 space-x-2">
                                <?php if ($course->status !== 'approved'): ?>
                                    <a href="<?= $baseUrl ?>/admin/approve-course/<?= $course->id ?>"
                                       class="text-green-600 hover:underline"
                                       onclick="return confirm('Approve this course?')">Approve</a>
                                <?php endif; ?>
                                <?php if ($course->status !== 'rejected'): ?>
                                    <a href="<?= $baseUrl ?>/admin/reject-course/<?= $course->id ?>"
                                       class="text-yellow-600 hover:underline"
                                       onclick="return confirm('Reject this course?')">Reject</a>
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

    <!-- Manage Reviews -->
    <section class="bg-red-100 hover:bg-red-200 p-4 rounded mb-6">
        <h2 class="text-xl font-semibold text-red-800 mb-3">📝 Manage Reviews</h2>
        <?php if (!empty($reviews)): ?>
            <div class="overflow-x-auto">
                <table class="w-full border text-sm">
                    <thead>
                    <tr class="bg-gray-200">
                        <th class="px-4 py-2">ID</th>
                        <th class="px-4 py-2">User</th>
                        <th class="px-4 py-2">Course</th>
                        <th class="px-4 py-2">Comment</th>
                        <th class="px-4 py-2">Rating</th>
                        <th class="px-4 py-2">Action</th>
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
                                <?= isset($review->rating) ? str_repeat('⭐', (int)$review->rating) : '' ?>
                            </td>
                            <td class="px-4 py-2">
                                <a href="<?= $baseUrl ?>/admin/delete-review/<?= $review->id ?>"
                                   class="text-red-600 hover:underline"
                                   onclick="return confirm('Delete this review?')">Delete</a>
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

</div>
</body>
</html>
