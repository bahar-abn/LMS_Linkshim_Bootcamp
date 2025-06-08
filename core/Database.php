<?php

namespace core;

use PDO;
use PDOException;

class Database {
    public PDO $pdo;

    public function __construct(array $config) {
        $dsn = $config['dsn'] ?? '';
        $user = $config['user'] ?? '';
        $password = $config['password'] ?? '';

        try {
            $this->pdo = new PDO($dsn, $user, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
            echo "❌ Database connection failed: " . $e->getMessage() . PHP_EOL;
            exit(1);
        }
    }

    public function applyMigrations(): void {
        $this->createMigrationsTable();
        $appliedMigrations = $this->getAppliedMigrations();

        $newMigrations = [];
        $migrationDir = Application::$ROOT_DIR . '/core/migrations';

        if (!is_dir($migrationDir)) {
            mkdir($migrationDir, 0777, true);
        }

        $files = scandir($migrationDir);
        $toApplyMigrations = array_diff($files, $appliedMigrations);

        foreach ($toApplyMigrations as $migration) {
            if ($migration === '.' || $migration === '..') continue;

            require_once "$migrationDir/$migration";
            $className = pathinfo($migration, PATHINFO_FILENAME);

            if (!class_exists($className)) {
                echo "⚠️ Class $className not found in $migration" . PHP_EOL;
                continue;
            }

            $instance = new $className();

            echo "🔄 Applying migration: $migration" . PHP_EOL;
            try {
                $instance->up($this->pdo);
                echo "✅ Applied migration: $migration" . PHP_EOL;
                $newMigrations[] = $migration;
            } catch (\Throwable $e) {
                echo "❌ Failed to apply migration $migration: " . $e->getMessage() . PHP_EOL;
                // Optional rollback logic can go here if your migration supports down()
            }
        }

        if (!empty($newMigrations)) {
            $this->saveMigrations($newMigrations);
        } else {
            echo "📦 All migrations already applied." . PHP_EOL;
        }
    }

    private function createMigrationsTable(): void {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=INNODB;
        ");
    }

    private function getAppliedMigrations(): array {
        $stmt = $this->pdo->prepare("SELECT migration FROM migrations");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    private function saveMigrations(array $migrations): void {
        $values = implode(",", array_map(fn($m) => "('$m')", $migrations));
        $stmt = $this->pdo->prepare("INSERT INTO migrations (migration) VALUES $values");
        $stmt->execute();
    }
    public function prepare(string $sql): \PDOStatement {
        return $this->pdo->prepare($sql);
    }
    public function query(string $sql)
    {
        try {
            return $this->pdo->query($sql);
        } catch (\PDOException $e) {
            die("Query failed: " . $e->getMessage());
        }
    }



}
