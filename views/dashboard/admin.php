<?php
// Fallback data for testing/debug
if (!isset($users)) {
    error_log('Users not passed to view. Using dummy data.');
    $users = [(object)['id' => 1, 'name' => 'Test User', 'email' => 'test@example.com', 'role' => 'admin']];
}

if (!isset($courses)) {
    $courses = [];
}

if (!isset($reviews)) {
    $reviews = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 text-gray-800 min-h-screen">
<?php $baseUrl = defined('BASE_URL') ? BASE_URL : ''; ?>

<!-- Navbar -->
<nav class="bg-white shadow-sm border-b mb-6">
    <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-red-700">📊 Admin Dashboard</h1>
        <div>
            <span class="mr-4">Welcome, <strong><?= htmlspecialchars($userName ?? 'Admin') ?></strong></span>
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
    <?php
    $totalUsers = is_array($users) ? count($users) : 0;
    $totalCourses = is_array($courses) ? count($courses) : 0;
    $totalReviews = is_array($reviews) ? count($reviews) : 0;
    ?>
    <section class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <?php foreach ([
                           ['label' => 'Total Users', 'count' => $totalUsers],
                           ['label' => 'Total Courses', 'count' => $totalCourses],
                           ['label' => 'Total Reviews', 'count' => $totalReviews],
                       ] as $stat): ?>
            <div class="bg-white border border-gray-200 p-6 rounded shadow text-center">
                <h3 class="text-lg text-gray-600"><?= $stat['label'] ?></h3>
                <p class="text-2xl font-bold text-red-700"><?= $stat['count'] ?></p>
            </div>
        <?php endforeach; ?>
    </section>

    <!-- Section Renderer -->
    <?php
    function renderTableSection($title, $icon, $headers, $rows, $emptyMsg) {
        echo "<section class='mb-10 bg-white shadow p-6 rounded border border-red-100'>";
        echo "<h2 class='text-xl font-semibold text-red-700 mb-4'>{$icon} {$title}</h2>";
        if (!empty($rows)) {
            echo "<div class='overflow-x-auto'>";
            echo "<table class='w-full table-auto border text-sm'>";
            echo "<thead class='bg-gray-100'><tr>";
            foreach ($headers as $header) {
                echo "<th class='px-4 py-2 text-left font-medium text-gray-600'>{$header}</th>";
            }
            echo "</tr></thead><tbody>";
            foreach ($rows as $row) echo $row;
            echo "</tbody></table></div>";
        } else {
            echo "<p class='text-gray-600'>{$emptyMsg}</p>";
        }
        echo "</section>";
    }
    ?>

    <!-- Manage Users -->
    <?php
    renderTableSection(
        'Manage Users',
        '👥',
        ['ID', 'Name', 'Email', 'Role', 'Action'],
        array_map(fn($user) => "
            <tr class='border-t'>
                <td class='px-4 py-2'>" . htmlspecialchars($user->id) . "</td>
                <td class='px-4 py-2'>" . htmlspecialchars($user->name) . "</td>
                <td class='px-4 py-2'>" . htmlspecialchars($user->email) . "</td>
                <td class='px-4 py-2'>" . htmlspecialchars($user->role) . "</td>
                <td class='px-4 py-2'>" . ($user->role !== 'admin' ? "
                    <a href='{$baseUrl}/admin/delete-user/{$user->id}' 
                        class='text-red-600 hover:underline'
                        onclick='return confirm(\"Delete this user?\")'>
                        Delete
                    </a>" : '') . "
                </td>
            </tr>", $users),
        'No users found.'
    );
    ?>

    <!-- Manage Courses -->
    <?php
    renderTableSection(
        'Manage Courses',
        '📘',
        ['ID', 'Title', 'Instructor', 'Status', 'Action'],
        array_map(function($course) use ($baseUrl) {
            $statusClass = match($course->status) {
                'approved' => 'text-green-600',
                'rejected' => 'text-red-600',
                default => 'text-yellow-600',
            };
            return "
                <tr class='border-t'>
                    <td class='px-4 py-2'>" . htmlspecialchars($course->id) . "</td>
                    <td class='px-4 py-2'>" . htmlspecialchars($course->title) . "</td>
                    <td class='px-4 py-2'>" . htmlspecialchars($course->instructor_name ?? 'Unknown') . "</td>
                    <td class='px-4 py-2'>
                        <span class='font-semibold {$statusClass}'>" . htmlspecialchars($course->status ?? 'pending') . "</span>
                    </td>
                    <td class='px-4 py-2 space-x-2'>" .
                ($course->status !== 'approved' ? "
                            <a href='{$baseUrl}/admin/approve-course/{$course->id}' 
                                class='text-green-600 hover:underline'
                                onclick='return confirm(\"Approve this course?\")'>
                                Approve
                            </a>" : '') .
                ($course->status !== 'rejected' ? "
                            <a href='{$baseUrl}/admin/reject-course/{$course->id}' 
                                class='text-yellow-600 hover:underline'
                                onclick='return confirm(\"Reject this course?\")'>
                                Reject
                            </a>" : '') . "
                    </td>
                </tr>";
        }, $courses),
        'No courses found.'
    );
    ?>

    <!-- Manage Reviews -->
    <?php
    renderTableSection(
        'Manage Reviews',
        '📝',
        ['ID', 'User', 'Course', 'Comment', 'Rating', 'Action'],
        array_map(fn($review) => "
            <tr class='border-t'>
                <td class='px-4 py-2'>" . htmlspecialchars($review->id) . "</td>
                <td class='px-4 py-2'>" . htmlspecialchars($review->user_name ?? 'Unknown') . "</td>
                <td class='px-4 py-2'>" . htmlspecialchars($review->course_title ?? 'Unknown') . "</td>
                <td class='px-4 py-2'>" . htmlspecialchars($review->comment) . "</td>
                <td class='px-4 py-2'>" .
            (isset($review->rating) ? str_repeat('⭐', (int)$review->rating) : '—') . "
                </td>
                <td class='px-4 py-2'>
                    <a href='{$baseUrl}/admin/delete-review/{$review->id}' 
                        class='text-red-600 hover:underline'
                        onclick='return confirm(\"Delete this review?\")'>
                        Delete
                    </a>
                </td>
            </tr>", $reviews),
        'No reviews found.'
    );
    ?>
</main>
</body>
</html>
