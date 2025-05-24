<h1>Instructor Dashboard</h1>
<h2>Your Courses</h2>
<ul>
    <?php foreach ($courses as $course): ?>
        <li><?= $course->title ?></li>
    <?php endforeach; ?>
</ul>
