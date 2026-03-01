-- ============================================
-- Student Grading System v2 - Database Setup
-- With User Roles: instructor & student
-- ============================================

CREATE DATABASE IF NOT EXISTS grading_system;
USE grading_system;

-- ----------------------------------------
-- Users Table: login credentials + role
-- role: 'instructor' or 'student'
-- ----------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(50) UNIQUE NOT NULL,
    password   VARCHAR(255) NOT NULL,          -- store hashed passwords (password_hash)
    role       ENUM('instructor','student') NOT NULL,
    student_id VARCHAR(10) DEFAULT NULL,       -- links to students table if role=student
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ----------------------------------------
-- Students Table: personal info
-- ----------------------------------------
CREATE TABLE IF NOT EXISTS students (
    student_id VARCHAR(10) PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name  VARCHAR(50) NOT NULL,
    email      VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ----------------------------------------
-- Courses Table: available courses
-- ----------------------------------------
CREATE TABLE IF NOT EXISTS courses (
    course_id   VARCHAR(10) PRIMARY KEY,
    course_name VARCHAR(100) NOT NULL,
    units       INT NOT NULL DEFAULT 3
);

-- ----------------------------------------
-- Grades Table: student grades per course
-- ----------------------------------------
CREATE TABLE IF NOT EXISTS grades (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(10) NOT NULL,
    course_id  VARCHAR(10) NOT NULL,
    grade      DECIMAL(5,2) NOT NULL,
    letter     VARCHAR(2),
    remarks    VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (course_id)  REFERENCES courses(course_id)   ON DELETE CASCADE,
    UNIQUE KEY unique_grade (student_id, course_id)
);

-- ============================================
-- Sample Students
-- ============================================
INSERT INTO students (student_id, first_name, last_name, email) VALUES
('STU001', 'Maria',   'Santos',     'maria.santos@school.edu'),
('STU002', 'Juan',    'dela Cruz',  'juan.delacruz@school.edu'),
('STU003', 'Ana',     'Reyes',      'ana.reyes@school.edu'),
('STU004', 'Carlo',   'Mendoza',    'carlo.mendoza@school.edu'),
('STU005', 'Jasmine', 'Villanueva', 'jasmine.villanueva@school.edu');

-- ============================================
-- Sample Courses
-- ============================================
INSERT INTO courses (course_id, course_name, units) VALUES
('CS101',   'Introduction to Computing', 3),
('MATH101', 'College Algebra',           3),
('ENG101',  'English Communication',     3),
('SCI101',  'General Science',           3),
('PE101',   'Physical Education',        2);

-- ============================================
-- Sample Grades
-- ============================================
INSERT INTO grades (student_id, course_id, grade, letter, remarks) VALUES
('STU001','CS101',   92.5, 'A',  'Passed'),
('STU001','MATH101', 88.0, 'B+', 'Passed'),
('STU001','ENG101',  95.0, 'A',  'Passed'),
('STU002','CS101',   78.0, 'C+', 'Passed'),
('STU002','MATH101', 65.0, 'D',  'Passed'),
('STU002','SCI101',  55.0, 'F',  'Failed'),
('STU003','ENG101',  90.0, 'A-', 'Passed'),
('STU003','PE101',   85.0, 'B+', 'Passed'),
('STU003','CS101',   88.5, 'B+', 'Passed'),
('STU004','MATH101', 72.0, 'C',  'Passed'),
('STU004','SCI101',  80.5, 'B',  'Passed'),
('STU004','PE101',   91.0, 'A-', 'Passed'),
('STU005','CS101',   96.0, 'A',  'Passed'),
('STU005','ENG101',  93.0, 'A',  'Passed'),
('STU005','MATH101', 89.5, 'B+', 'Passed');

-- ============================================
-- User Accounts
-- Passwords below are hashed versions of:
--   instructor → password: instructor123
--   student accounts → password: student123
-- Generated with PHP password_hash()
-- ============================================

-- 1 Instructor account
INSERT INTO users (username, password, role) VALUES
('instructor', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'instructor');
-- Note: the hash above is for 'password' (Laravel default test hash)
-- To use real passwords, run this in PHP:
-- echo password_hash('instructor123', PASSWORD_DEFAULT);

-- 5 Student accounts linked to student records
INSERT INTO users (username, password, role, student_id) VALUES
('maria.santos',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'STU001'),
('juan.delacruz',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'STU002'),
('ana.reyes',       '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'STU003'),
('carlo.mendoza',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'STU004'),
('jasmine.v',       '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'STU005');

-- ============================================
-- DEFAULT LOGIN CREDENTIALS (all use: password)
-- ============================================
-- Role       | Username         | Password
-- -----------|------------------|----------
-- Instructor | instructor       | password
-- Student    | maria.santos     | password
-- Student    | juan.delacruz   | password
-- Student    | ana.reyes        | password
-- Student    | carlo.mendoza    | password
-- Student    | jasmine.v        | password
-- ============================================
-- To change passwords, update them in PHP using:
-- password_hash('your_new_password', PASSWORD_DEFAULT)
-- ============================================