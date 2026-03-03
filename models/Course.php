<?php
// models/Course.php

require_once BASE_PATH . '/config/database.php';

class Course {
    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    /** Get all courses */
    public function getAll(): array {
        return $this->db->query(
            "SELECT * FROM courses ORDER BY course_id ASC"
        )->fetchAll();
    }

    /** Get course by primary key */
    public function findById(int $id): array|false {
        $stmt = $this->db->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /** Add a new course */
    public function create(string $courseId, string $courseName, int $units): bool|string {
        // Check uniqueness
        $stmt = $this->db->prepare("SELECT id FROM courses WHERE course_id = ?");
        $stmt->execute([$courseId]);
        if ($stmt->fetch()) return 'Course ID already exists.';

        $stmt = $this->db->prepare(
            "INSERT INTO courses (course_id, course_name, units) VALUES (?,?,?)"
        );
        $stmt->execute([$courseId, $courseName, $units]);
        return true;
    }

    /** Update course */
    public function update(int $id, string $courseId, string $courseName, int $units): bool|string {
        $stmt = $this->db->prepare(
            "SELECT id FROM courses WHERE course_id = ? AND id != ?"
        );
        $stmt->execute([$courseId, $id]);
        if ($stmt->fetch()) return 'Course ID already used by another course.';

        $stmt = $this->db->prepare(
            "UPDATE courses SET course_id = ?, course_name = ?, units = ? WHERE id = ?"
        );
        $stmt->execute([$courseId, $courseName, $units, $id]);
        return true;
    }

    /** Delete course (cascades grades) */
    public function delete(int $id): void {
        $this->db->prepare("DELETE FROM courses WHERE id = ?")->execute([$id]);
    }

    /** Count all courses */
    public function count(): int {
        return (int) $this->db->query("SELECT COUNT(*) FROM courses")->fetchColumn();
    }
}