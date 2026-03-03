<?php
// models/User.php
// Handles all user-related database operations

require_once BASE_PATH . '/config/database.php';

class User {
    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    /** Find user by email (for login) */
    public function findByEmail(string $email): array|false {
        $stmt = $this->db->prepare(
            "SELECT id, name, email, password, role FROM users WHERE email = ? LIMIT 1"
        );
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    /** Find user by ID */
    public function findById(int $id): array|false {
        $stmt = $this->db->prepare(
            "SELECT id, name, email, role FROM users WHERE id = ? LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /** Get all students with their student record ID */
    public function getAllStudents(string $search = ''): array {
        $sql = "SELECT u.id, u.name, u.email, u.created_at, s.id AS student_id
                FROM users u
                JOIN students s ON u.id = s.user_id
                WHERE u.role = 'student'";
        $params = [];
        if ($search) {
            $sql    .= " AND (u.name LIKE ? OR u.email LIKE ?)";
            $params  = ["%$search%", "%$search%"];
        }
        $sql .= " ORDER BY u.name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Create a new student (user + students row) */
    public function createStudent(string $name, string $email, string $password): bool|string {
        // Check email uniqueness
        if ($this->findByEmail($email)) return 'Email already exists.';

        try {
            $this->db->beginTransaction();
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare(
                "INSERT INTO users (name, email, password, role) VALUES (?,?,?,'student')"
            );
            $stmt->execute([$name, $email, $hash]);
            $userId = (int) $this->db->lastInsertId();

            $stmt2 = $this->db->prepare("INSERT INTO students (user_id) VALUES (?)");
            $stmt2->execute([$userId]);
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return 'Database error: ' . $e->getMessage();
        }
    }

    /** Update a student's name and email */
    public function updateStudent(int $userId, string $name, string $email): bool|string {
        // Check uniqueness excluding this user
        $stmt = $this->db->prepare(
            "SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1"
        );
        $stmt->execute([$email, $userId]);
        if ($stmt->fetch()) return 'Email already used by another account.';

        $stmt = $this->db->prepare(
            "UPDATE users SET name = ?, email = ? WHERE id = ?"
        );
        $stmt->execute([$name, $email, $userId]);
        return true;
    }

    /** Delete a student (cascades to students + grades) */
    public function deleteStudent(int $userId): void {
        $this->db->prepare("DELETE FROM users WHERE id = ?")->execute([$userId]);
    }

    /** Count all students */
    public function countStudents(): int {
        return (int) $this->db->query(
            "SELECT COUNT(*) FROM users WHERE role = 'student'"
        )->fetchColumn();
    }
}