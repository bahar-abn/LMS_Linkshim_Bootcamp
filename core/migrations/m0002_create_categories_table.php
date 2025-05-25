<?php

class m0002_create_categories_table
{
    public function up(PDO $pdo)
    {
        $sql = "CREATE TABLE categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=INNODB;";

        $pdo->exec($sql);
    }

    public function down(PDO $pdo)
    {
        $pdo->exec("DROP TABLE IF EXISTS categories;");
    }
}
