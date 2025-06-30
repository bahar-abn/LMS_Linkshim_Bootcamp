<?php

namespace models;

use core\Application;
use PDO;

class Course
{
    public int $id = 0;
    public string $title = '';
    public string $description = '';
    public string $status = 'pending';
    public ?int $instructor_id = null;
    public ?int $category_id = null;
    public string $created_at = '';
    public $updated_at;
    public string $instructor_name = '';

    // Constructor is useful only when manually creating course objects
    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->id = $data['id'] ?? 0;
            $this->title = $data['title'] ?? '';
            $this->description = $data['description'] ?? '';
            $this->category_id = $data['category_id'] ?? 0;
            $this->instructor_id = $data['instructor_id'] ?? 0;
            $this->status = $data['status'] ?? 'pending';
            $this->created_at = $data['created_at'] ?? '';
            $this->instructor_name = $data['instructor_name'] ?? '';
        }
    }

    public function update(): bool
    {
        $stmt = Application::$app->db->pdo->prepare("
            UPDATE courses
            SET title = :title,
                description = :description,
                category_id = :category_id,
                instructor_id = :instructor_id,
                status = :status
            WHERE id = :id
        ");

        return $stmt->execute([
            ':title' => $this->title,
            ':description' => $this->description,
            ':category_id' => $this->category_id,
            ':instructor_id' => $this->instructor_id,
            ':status' => $this->status,
            ':id' => $this->id,
        ]);
    }

    public static function getAll(): array
    {
        $sql = "SELECT * FROM courses";
        $stmt = Application::$app->db->pdo->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::class);
    }


    public static function find(int $id): ?self
    {
        try {
            $stmt = Application::$app->db->pdo->prepare(
                "SELECT * FROM courses WHERE id = :id"
            );
            $stmt->execute([':id' => $id]);
            $stmt->setFetchMode(PDO::FETCH_CLASS, self::class);
            return $stmt->fetch() ?: null;
        } catch (PDOException $e) {
            error_log("Error finding course: " . $e->getMessage());
            return null;
        }
    }

    public function loadData(array $data): void
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function save(): bool
    {
        try {
            if ($this->id) {
                // Update existing course
                $stmt = Application::$app->db->pdo->prepare(
                    "UPDATE courses SET 
                title = :title,
                description = :description,
                category_id = :category_id,
                status = :status,
                updated_at = NOW()
                WHERE id = :id"
                );
                $params = [
                    ':title' => $this->title,
                    ':description' => $this->description,
                    ':category_id' => $this->category_id,
                    ':status' => $this->status,
                    ':id' => $this->id
                ];
            } else {
                // Create new course
                $stmt = Application::$app->db->pdo->prepare(
                    "INSERT INTO courses (
                title, description, category_id, 
                instructor_id, status, created_at
                ) VALUES (
                :title, :description, :category_id,
                :instructor_id, :status, NOW()
                )"
                );
                $params = [
                    ':title' => $this->title,
                    ':description' => $this->description,
                    ':category_id' => $this->category_id,
                    ':instructor_id' => $this->instructor_id,
                    ':status' => $this->status ?? 'pending'
                ];
            }

            $success = $stmt->execute($params);

            if ($success && !$this->id) {
                $this->id = (int)Application::$app->db->pdo->lastInsertId();
            }

            return $success;
        } catch (PDOException $e) {
            error_log("Course save error: " . $e->getMessage());
            return false;
        }
    }
    public static function findById(int $id): ?self
    {
        $stmt = Application::$app->db->pdo->prepare("
            SELECT c.*, u.name AS instructor_name 
            FROM courses c
            LEFT JOIN users u ON c.instructor_id = u.id
            WHERE c.id = ?
        ");
        $stmt->execute([$id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, self::class);
        return $stmt->fetch() ?: null;
    }

    public static function count(): int
    {
        $stmt = Application::$app->db->pdo->prepare("SELECT COUNT(*) FROM courses");
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public static function findByInstructor(int $instructorId): array
    {
        $stmt = Application::$app->db->pdo->prepare("
            SELECT c.*, u.name AS instructor_name 
            FROM courses c
            LEFT JOIN users u ON c.instructor_id = u.id
            WHERE c.instructor_id = :instructor_id
            ORDER BY c.created_at DESC
        ");
        $stmt->execute([':instructor_id' => $instructorId]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public static function latest(int $limit = 100): array
    {
        $stmt = Application::$app->db->pdo->prepare("
            SELECT c.*, u.name AS instructor_name 
            FROM courses c
            LEFT JOIN users u ON c.instructor_id = u.id
            ORDER BY c.created_at DESC 
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public static function allApproved(): array
    {
        $stmt = Application::$app->db->pdo->query("
            SELECT c.*, u.name AS instructor_name 
            FROM courses c
            LEFT JOIN users u ON c.instructor_id = u.id
            WHERE c.status = 'approved'
            ORDER BY c.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }
    public static function searchAndFilter(string $search = '', string $category = ''): array
    {
        $sql = "SELECT * FROM courses WHERE status = 'approved'";
        $params = [];

        if ($search) {
            $sql .= " AND title LIKE :search";
            $params[':search'] = "%$search%";
        }

        if ($category) {
            $sql .= " AND category_id = :category_id";
            $params[':category_id'] = (int)$category;
        }

        $stmt = Application::$app->db->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::class); // Returns array of Course objects
    }


    public static function findAllByInstructor(int $instructorId): array
    {
        try {
            $stmt = Application::$app->db->pdo->prepare(
                "SELECT * FROM courses WHERE instructor_id = :instructor_id ORDER BY created_at DESC"
            );
            $stmt->execute([':instructor_id' => $instructorId]);
            return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
        } catch (PDOException $e) {
            error_log("Error finding instructor courses: " . $e->getMessage());
            return [];
        }
    }
}
