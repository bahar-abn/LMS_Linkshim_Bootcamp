<h1>Admin Dashboard</h1>
<h2>Users</h2>
<ul>
    <?php foreach ($users as $user): ?>
        <li><?= $user->name ?> (<?= $user->role ?>)</li>
    <?php endforeach; ?>
</ul>

<h2>Courses</h2>
<ul>
    <?php foreach ($courses as $course): ?>
        <li><?= $course->title ?></li>
    <?php endforeach; ?>
</ul>
