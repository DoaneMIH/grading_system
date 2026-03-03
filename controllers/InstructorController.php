<?php
// controllers/InstructorController.php
// All instructor CRUD actions

require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/models/Course.php';
require_once BASE_PATH . '/models/Grade.php';

class InstructorController {
    private User   $userModel;
    private Course $courseModel;
    private Grade  $gradeModel;

    public function __construct() {
        requireRole('instructor');
        $this->userModel   = new User();
        $this->courseModel = new Course();
        $this->gradeModel  = new Grade();
    }

    // ============ STUDENTS ============

    public function addStudent(): void {
        verifyCsrf();
        $name     = trim($_POST['name']     ?? '');
        $email    = trim($_POST['email']    ?? '');
        $password = trim($_POST['password'] ?? '');

        if (!$name || !$email || !$password) {
            flash('error', 'All student fields are required.');
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Invalid email address.');
        } elseif (strlen($password) < 6) {
            flash('error', 'Password must be at least 6 characters.');
        } else {
            $result = $this->userModel->createStudent($name, $email, $password);
            if ($result === true) {
                flash('success', "Student \"$name\" added successfully.");
            } else {
                flash('error', $result);
            }
        }
        redirect('/views/instructor/students.php');
    }

    public function updateStudent(): void {
        verifyCsrf();
        $userId = (int)($_POST['user_id'] ?? 0);
        $name   = trim($_POST['name']     ?? '');
        $email  = trim($_POST['email']    ?? '');

        if (!$userId || !$name || !$email) {
            flash('error', 'All fields are required.');
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Invalid email address.');
        } else {
            $result = $this->userModel->updateStudent($userId, $name, $email);
            if ($result === true) {
                flash('success', 'Student updated.');
            } else {
                flash('error', $result);
            }
        }
        redirect('/views/instructor/students.php');
    }

    public function deleteStudent(): void {
        verifyCsrf();
        $userId = (int)($_POST['user_id'] ?? 0);
        if ($userId) {
            $this->userModel->deleteStudent($userId);
            flash('success', 'Student deleted.');
        }
        redirect('/views/instructor/students.php');
    }

    // ============ COURSES ============

    public function addCourse(): void {
        verifyCsrf();
        $courseId   = strtoupper(trim($_POST['course_id']   ?? ''));
        $courseName = trim($_POST['course_name'] ?? '');
        $units      = (int)($_POST['units'] ?? 3);

        if (!$courseId || !$courseName || $units < 1) {
            flash('error', 'All course fields are required.');
        } else {
            $result = $this->courseModel->create($courseId, $courseName, $units);
            if ($result === true) {
                flash('success', "Course \"$courseName\" added.");
            } else {
                flash('error', $result);
            }
        }
        redirect('/views/instructor/courses.php');
    }

    public function updateCourse(): void {
        verifyCsrf();
        $id         = (int)($_POST['id']          ?? 0);
        $courseId   = strtoupper(trim($_POST['course_id']   ?? ''));
        $courseName = trim($_POST['course_name'] ?? '');
        $units      = (int)($_POST['units'] ?? 3);

        if (!$id || !$courseId || !$courseName) {
            flash('error', 'All course fields are required.');
        } else {
            $result = $this->courseModel->update($id, $courseId, $courseName, $units);
            if ($result === true) {
                flash('success', 'Course updated.');
            } else {
                flash('error', $result);
            }
        }
        redirect('/views/instructor/courses.php');
    }

    public function deleteCourse(): void {
        verifyCsrf();
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $this->courseModel->delete($id);
            flash('success', 'Course deleted.');
        }
        redirect('/views/instructor/courses.php');
    }

    // ============ GRADES ============

    public function saveGrade(): void {
        verifyCsrf();
        $studentId = (int)($_POST['student_id'] ?? 0);
        $courseId  = (int)($_POST['course_id']  ?? 0);
        $grade     = (float)($_POST['grade']    ?? -1);

        if (!$studentId || !$courseId || $grade < 0 || $grade > 100) {
            flash('error', 'Invalid grade input.');
        } else {
            $this->gradeModel->upsert($studentId, $courseId, $grade);
            flash('success', 'Grade saved.');
        }
        redirect('/views/instructor/grades.php');
    }

    public function deleteGrade(): void {
        verifyCsrf();
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $this->gradeModel->delete($id);
            flash('success', 'Grade deleted.');
        }
        redirect('/views/instructor/grades.php');
    }
}