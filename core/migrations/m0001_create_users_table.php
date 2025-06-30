<?php

class m0001_create_users_table
{
    public function up(PDO $pdo)
    {
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role VARCHAR(50) NOT NULL DEFAULT 'student',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        try {
            $pdo->exec($sql);
            echo "✅ Created users table successfully\n";
        } catch (PDOException $e) {
            echo "❌ Error creating users table: " . $e->getMessage() . "\n";
            // Output the SQL for manual debugging
            echo "SQL being executed:\n" . $sql . "\n";
        }
    }

    public function down(PDO $pdo)
    {
        $sql = "DROP TABLE IF EXISTS users;";

        try {
            $pdo->exec($sql);
            echo "✅ Dropped users table successfully\n";
        } catch (PDOException $e) {
            echo "❌ Error dropping users table: " . $e->getMessage() . "\n";
        }
    }
}