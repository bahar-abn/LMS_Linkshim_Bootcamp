<?php

class m0003_create_courses_table
{
    public function up(\PDO $pdo): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS courses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        instructor_id INT NOT NULL,
        category_id INT NOT NULL,
        status VARCHAR(50) DEFAULT 'pending', -- 👈 ADD THIS LINE
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=INNODB;";
        $pdo->exec($sql);
    }

    public function down(PDO $pdo)
    {
        $pdo->exec("DROP TABLE IF EXISTS courses;");
    }
}
