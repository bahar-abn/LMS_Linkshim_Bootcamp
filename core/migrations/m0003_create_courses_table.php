<?php

class m0003_create_courses_table
{
    public function up(PDO $pdo)
    {
        $sql = "CREATE TABLE IF NOT EXISTS courses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            instructor_id INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (instructor_id) REFERENCES users(id)
        ) ENGINE=INNODB;";

        $pdo->exec($sql);
    }

    public function down(PDO $pdo)
    {
        $pdo->exec("DROP TABLE IF EXISTS courses;");
    }
}
