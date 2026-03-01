<?php
// ============================================
// api.php - Backend API with Role Protection
// All sensitive actions check the user's role
// before executing.
// ============================================

session_start();
header('Content-Type: application/json');
require_once 'db.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// ---- Helper: is user logged in? ----
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['error' => 'Not authenticated. Please log in.']);
        exit;
    }
}

// ---- Helper: is user an instructor? ----
function requireInstructor() {
    requireLogin();
    if ($_SESSION['role'] !== 'instructor') {
        echo json_encode(['error' => 'Access denied. Instructor only.']);
        exit;
    }
}

// ---- Helper: compute letter grade ----
function getLetterGrade($grade) {
    if ($grade >= 95) return 'A+';
    if ($grade >= 90) return 'A';
    if ($grade >= 87) return 'A-';
    if ($grade >= 83) return 'B+';
    if ($grade >= 80) return 'B';
    if ($grade >= 77) return 'B-';
    if ($grade >= 73) return 'C+';
    if ($grade >= 70) return 'C';
    if ($grade >= 67) return 'C-';
    if ($grade >= 60) return 'D';
    return 'F';
}

// Route to the correct action handler
switch ($action) {

    // ============================================
    // SHARED ACTIONS (both roles can use)
    // ============================================

    // ---- Get all courses ----
    case 'get_courses':
        requireLogin();
        $stmt = $pdo->query("SELECT * FROM courses ORDER BY course_id");
        echo json_encode($stmt->fetchAll());
        break;

    // ---- Get grades ----
    // Instructor: can get all or filter by student
    // Student: can ONLY get their own grades
    case 'get_grades':
        requireLogin();

        if ($_SESSION['role'] === 'student') {
            // Force student_id to their own — they can't see others
            $student_id = $_SESSION['student_id'];
        } else {
            // Instructor can filter by any student or get all
            $student_id = $_GET['student_id'] ?? '';
        }

        if ($student_id) {
            $stmt = $pdo->prepare("
                SELECT g.*, s.first_name, s.last_name, c.course_name, c.units
                FROM grades g
                JOIN students s ON g.student_id = s.student_id
                JOIN courses  c ON g.course_id  = c.course_id
                WHERE g.student_id = ?
                ORDER BY c.course_id
            ");
            $stmt->execute([$student_id]);
        } else {
            $stmt = $pdo->query("
                SELECT g.*, s.first_name, s.last_name, c.course_name, c.units
                FROM grades g
                JOIN students s ON g.student_id = s.student_id
                JOIN courses  c ON g.course_id  = c.course_id
                ORDER BY s.student_id, c.course_id
            ");
        }
        echo json_encode($stmt->fetchAll());
        break;

    // ============================================
    // INSTRUCTOR-ONLY ACTIONS
    // ============================================

    // ---- Get all students (instructor only) ----
    case 'get_students':
        requireInstructor();
        $stmt = $pdo->query("SELECT * FROM students ORDER BY student_id");
        echo json_encode($stmt->fetchAll());
        break;

    // ---- Add a new student (instructor only) ----
    case 'add_student':
        requireInstructor();
        $student_id = strtoupper(trim($_POST['student_id'] ?? ''));
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name  = trim($_POST['last_name']  ?? '');
        $email      = trim($_POST['email']       ?? '');
        $username   = trim($_POST['username']    ?? '');
        $password   = trim($_POST['password']    ?? '');

        if (!$student_id || !$first_name || !$last_name || !$email || !$username || !$password) {
            echo json_encode(['success' => false, 'message' => 'All fields are required.']);
            break;
        }

        try {
            // Insert into students table
            $stmt = $pdo->prepare("INSERT INTO students (student_id, first_name, last_name, email) VALUES (?,?,?,?)");
            $stmt->execute([$student_id, $first_name, $last_name, $email]);

            // Create a login account for the student
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt2  = $pdo->prepare("INSERT INTO users (username, password, role, student_id) VALUES (?,?,?,?)");
            $stmt2->execute([$username, $hashed, 'student', $student_id]);

            echo json_encode(['success' => true, 'message' => "Student $student_id added with login account!"]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Student ID, email, or username already exists.']);
        }
        break;

    // ---- Delete a student (instructor only) ----
    case 'delete_student':
        requireInstructor();
        $student_id = $_POST['student_id'] ?? '';
        if (!$student_id) { echo json_encode(['success' => false, 'message' => 'Missing student ID.']); break; }

        // Delete user account too
        $pdo->prepare("DELETE FROM users WHERE student_id = ?")->execute([$student_id]);
        $pdo->prepare("DELETE FROM students WHERE student_id = ?")->execute([$student_id]);
        echo json_encode(['success' => true, 'message' => 'Student deleted.']);
        break;

    // ---- Add a new course (instructor only) ----
    case 'add_course':
        requireInstructor();
        $course_id   = strtoupper(trim($_POST['course_id']   ?? ''));
        $course_name = trim($_POST['course_name'] ?? '');
        $units       = intval($_POST['units'] ?? 3);

        if (!$course_id || !$course_name || $units < 1) {
            echo json_encode(['success' => false, 'message' => 'All course fields are required.']);
            break;
        }
        try {
            $stmt = $pdo->prepare("INSERT INTO courses (course_id, course_name, units) VALUES (?,?,?)");
            $stmt->execute([$course_id, $course_name, $units]);
            echo json_encode(['success' => true, 'message' => "Course $course_id added!"]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Course ID already exists.']);
        }
        break;

    // ---- Delete a course (instructor only) ----
    case 'delete_course':
        requireInstructor();
        $course_id = $_POST['course_id'] ?? '';
        if (!$course_id) { echo json_encode(['success' => false, 'message' => 'Missing course ID.']); break; }
        $pdo->prepare("DELETE FROM courses WHERE course_id = ?")->execute([$course_id]);
        echo json_encode(['success' => true, 'message' => 'Course deleted.']);
        break;

    // ---- Save or update a grade (instructor only) ----
    case 'save_grade':
        requireInstructor();
        $student_id = $_POST['student_id'] ?? '';
        $course_id  = $_POST['course_id']  ?? '';
        $grade      = floatval($_POST['grade'] ?? -1);

        if (!$student_id || !$course_id || $grade < 0 || $grade > 100) {
            echo json_encode(['success' => false, 'message' => 'Invalid input.']);
            break;
        }

        $letter  = getLetterGrade($grade);
        $remarks = ($grade >= 60) ? 'Passed' : 'Failed';

        $stmt = $pdo->prepare("
            INSERT INTO grades (student_id, course_id, grade, letter, remarks)
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE grade=VALUES(grade), letter=VALUES(letter), remarks=VALUES(remarks)
        ");
        $stmt->execute([$student_id, $course_id, $grade, $letter, $remarks]);
        echo json_encode(['success' => true, 'message' => 'Grade saved!', 'letter' => $letter, 'remarks' => $remarks]);
        break;

    // ---- Delete a grade (instructor only) ----
    case 'delete_grade':
        requireInstructor();
        $id = intval($_POST['id'] ?? 0);
        if (!$id) { echo json_encode(['success' => false, 'message' => 'Invalid ID.']); break; }
        $pdo->prepare("DELETE FROM grades WHERE id = ?")->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'Grade deleted.']);
        break;

    default:
        echo json_encode(['error' => 'Unknown action.']);
}
?>