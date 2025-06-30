<?php
namespace core;

class TestConnection {
    public static function run() {
        // Buffer output to prevent header issues
        ob_start();

        try {
            $config = require __DIR__ . '/../config/config.php';
            $app = new Application(__DIR__ . '/..', $config);

            // Only send headers if not in CLI mode
            if (php_sapi_name() !== 'cli') {
                header('Content-Type: text/plain');
            }

            echo "=== Database Connection Test ===\n\n";

            // 1. Test basic connection
            echo "1. Testing connection... ";
            $status = $app->db->pdo->getAttribute(\PDO::ATTR_CONNECTION_STATUS);
            echo "SUCCESS! ($status)\n";

            // 2. Check if users table exists with correct structure
            echo "2. Checking users table structure... ";
            $stmt = $app->db->pdo->query("DESCRIBE users");
            $columns = $stmt->fetchAll(\PDO::FETCH_COLUMN);

            $requiredColumns = ['id', 'name', 'email', 'password', 'role', 'created_at'];
            $missingColumns = array_diff($requiredColumns, $columns);

            if (empty($missingColumns)) {
                echo "VALID\n";

                // 3. Test insert operation
                echo "3. Testing insert operation... ";
                $testEmail = 'test_' . time() . '@example.com';
                $stmt = $app->db->pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
                $result = $stmt->execute([
                    'Test User',
                    $testEmail,
                    password_hash('test123', PASSWORD_DEFAULT),
                    'student'
                ]);

                if ($result) {
                    $id = $app->db->pdo->lastInsertId();
                    echo "SUCCESS! (ID: $id)\n";

                    // Clean up
                    $app->db->pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
                } else {
                    echo "FAILED! Error: " . implode(' ', $stmt->errorInfo()) . "\n";
                }
            } else {
                echo "INVALID! Missing columns: " . implode(', ', $missingColumns) . "\n";
                echo "Please run this SQL:\n";
                echo "CREATE TABLE IF NOT EXISTS users (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    email VARCHAR(255) NOT NULL UNIQUE,
                    password VARCHAR(255) NOT NULL,
                    role VARCHAR(50) NOT NULL DEFAULT 'student',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                );\n";
            }
        } catch (\PDOException $e) {
            echo "\nERROR: " . $e->getMessage() . "\n";
        } finally {
            ob_end_flush();
        }

        echo "\n=== Test completed ===\n";
    }
}