<?php
class m0002_create_categories_table {
    public function up(PDO $pdo) {
        $sql = "CREATE TABLE IF NOT EXISTS categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        $pdo->exec($sql);
    }

    public function down(PDO $pdo) {
        $pdo->exec("DROP TABLE IF EXISTS categories");
    }
}