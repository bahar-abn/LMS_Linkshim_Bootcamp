<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
</head>
<body>
<!-- Debug output -->
<h1>LAYOUT START</h1>
<pre>Variables in layout: <?php print_r(get_defined_vars()) ?></pre>

<?= $content ?? 'No content' ?>

<h1>LAYOUT END</h1>
</body>
</html>