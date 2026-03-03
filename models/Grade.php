<?php
// models/Grade.php

require_once BASE_PATH . '/config/database.php';

class Grade {
    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    /** Get all grades with student and course info (instructor view) */
    public function getAll(string $search = ''): array {
        $sql = "SELECT g.id, g.grade, g.updated_at,
                       u.name AS student_name, u.email AS student_email,
                       c.course_id, c.course_name, c.units,
                       s.id AS student_id
                FROM grades g
                JOIN students s ON g.student_id = s.id
                JOIN users    u ON s.user_id     = u.id
                JOIN courses  c ON g.course_id   = c.id";
        $params = [];
        if ($search) {
            $sql   .= " WHERE u.name LIKE ? OR c.course_name LIKE ? OR c.course_id LIKE ?";
            $params = ["%$search%", "%$search%", "%$search%"];
        }
        $sql .= " ORDER BY u.name, c.course_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Get grades for a single student (student portal view) */
    public function getByStudentId(int $studentId): array {
        $stmt = $this->db->prepare(
            "SELECT g.id, g.grade, c.course_id, c.course_name, c.units
             FROM grades g
             JOIN courses c ON g.course_id = c.id
             WHERE g.student_id = ?
             ORDER BY c.course_id"
        );
        $stmt->execute([$studentId]);
        return $stmt->fetchAll();
    }

    /** Get the students.id from a user_id */
    public function getStudentRecordId(int $userId): int|false {
        $stmt = $this->db->prepare("SELECT id FROM students WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    }

    /** Add or update a grade */
    public function upsert(int $studentId, int $courseId, float $grade): void {
        $stmt = $this->db->prepare(
            "INSERT INTO grades (student_id, course_id, grade)
             VALUES (?,?,?)
             ON DUPLICATE KEY UPDATE grade = VALUES(grade)"
        );
        $stmt->execute([$studentId, $courseId, $grade]);
    }

    /** Delete one grade record */
    public function delete(int $id): void {
        $this->db->prepare("DELETE FROM grades WHERE id = ?")->execute([$id]);
    }

    /** Find grade by ID */
    public function findById(int $id): array|false {
        $stmt = $this->db->prepare(
            "SELECT g.*, s.id AS student_id, c.id AS course_db_id
             FROM grades g
             JOIN students s ON g.student_id = s.id
             JOIN courses  c ON g.course_id  = c.id
             WHERE g.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /** Average of a student's grades */
    public function getAverage(int $studentId): float {
        $stmt = $this->db->prepare(
            "SELECT AVG(grade) FROM grades WHERE student_id = ?"
        );
        $stmt->execute([$studentId]);
        return (float) $stmt->fetchColumn();
    }

    /** Count total grade records */
    public function count(): int {
        return (int) $this->db->query("SELECT COUNT(*) FROM grades")->fetchColumn();
    }
}