<?php

class m0002_create_categories_table
{
    public function up(PDO $pdo)
    {
        $stmt = $pdo->query("SHOW TABLES LIKE 'categories'");
        if ($stmt->rowCount() === 0) {
            $pdo->exec("
            CREATE TABLE categories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=INNODB;
        ");
        } else {
            echo "Table 'categories' already exists. Skipping...\n";
        }
    }


    public function down(PDO $pdo)
    {
        $pdo->exec("DROP TABLE IF EXISTS categories;");
    }
}
