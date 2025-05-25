<?php

use core\Application;

class m0004_create_enrollments_table
{
    public function up()
    {
        $db = Application::$app->db;
        $sql = "CREATE TABLE enrollments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            course_id INT NOT NULL,
            enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
            UNIQUE KEY unique_enrollment (user_id, course_id),
            INDEX idx_user_id (user_id),
            INDEX idx_course_id (course_id)
        ) ENGINE=INNODB;";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE IF EXISTS enrollments;";
        $db->pdo->exec($sql);
    }
}
