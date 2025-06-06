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

        $this->pdo = new PDO($dsn, $user, $password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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
                echo "Class $className not found in migration file $migration" . PHP_EOL;
                continue;
            }

            $instance = new $className();

            echo "Applying migration $migration" . PHP_EOL;
            try {
                $instance->up($this->pdo);
                echo "Applied migration $migration" . PHP_EOL;
                $newMigrations[] = $migration;
            } catch (\Throwable $e) {
                echo "Failed to apply migration $migration: " . $e->getMessage() . PHP_EOL;
            }
        }

        if (!empty($newMigrations)) {
            $this->saveMigrations($newMigrations);
        } else {
            echo "All migrations are already applied." . PHP_EOL;
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
}
