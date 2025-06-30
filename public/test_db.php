<?php
require 'vendor/autoload.php'; // Adjust path as needed

$pdo = new PDO(
    "mysql:host=localhost;dbname=lms_db",
    "root",
    "",
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]
);

// Test write
$pdo->exec("INSERT INTO test (name) VALUES ('test')");
$lastId = $pdo->lastInsertId();

// Test read
$stmt = $pdo->query("SELECT * FROM test WHERE id = $lastId");
$result = $stmt->fetch();

var_dump($result);