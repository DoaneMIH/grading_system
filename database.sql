-- ============================================================
-- Student Grading System - Full Database
-- ============================================================

CREATE DATABASE IF NOT EXISTS sgs_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sgs_db;

-- users table
CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    email      VARCHAR(150) UNIQUE NOT NULL,
    password   VARCHAR(255) NOT NULL,
    role       ENUM('instructor','student') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- students table
CREATE TABLE IF NOT EXISTS students (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- courses table
CREATE TABLE IF NOT EXISTS courses (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    course_id   VARCHAR(20) UNIQUE NOT NULL,
    course_name VARCHAR(150) NOT NULL,
    units       TINYINT DEFAULT 3,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- grades table
CREATE TABLE IF NOT EXISTS grades (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_id  INT NOT NULL,
    grade      DECIMAL(5,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id)  REFERENCES courses(id)  ON DELETE CASCADE,
    UNIQUE KEY unique_grade (student_id, course_id)
);

-- ============================================================
-- SAMPLE DATA
-- ⚠️  ALL PASSWORDS = "password"
-- This hash was generated with: password_hash('password', PASSWORD_DEFAULT)
-- After importing, run reset_passwords.php to change all to "password123"
-- ============================================================

SET @hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

INSERT INTO users (name, email, password, role) VALUES
('Prof. Ricardo Santos', 'instructor@school.edu', @hash, 'instructor'),
('Maria Clara Santos',   'maria@school.edu',      @hash, 'student'),
('Juan Dela Cruz',       'juan@school.edu',        @hash, 'student'),
('Ana Reyes Flores',     'ana@school.edu',         @hash, 'student'),
('Carlo Mendoza Diaz',   'carlo@school.edu',       @hash, 'student'),
('Jasmine Villanueva',   'jasmine@school.edu',     @hash, 'student');

INSERT INTO students (user_id) VALUES (2),(3),(4),(5),(6);

INSERT INTO courses (course_id, course_name, units) VALUES
('CS101',   'Introduction to Computing',    3),
('MATH101', 'College Algebra',              3),
('ENG101',  'English Communication',        3),
('SCI101',  'General Science',              3),
('PE101',   'Physical Education',           2),
('IT101',   'Web Development Fundamentals', 3);

INSERT INTO grades (student_id, course_id, grade) VALUES
(1,1,92.50),(1,2,88.00),(1,3,95.00),(1,4,85.50),
(2,1,78.00),(2,2,65.00),(2,3,72.50),(2,5,88.00),
(3,1,90.00),(3,3,87.50),(3,4,93.00),(3,6,96.00),
(4,2,72.00),(4,4,80.50),(4,5,91.00),(4,6,77.50),
(5,1,96.00),(5,2,89.50),(5,3,93.00),(5,6,94.50);

-- ============================================================
-- LOGIN CREDENTIALS SUMMARY
-- ============================================================
-- Role        | Email                    | Password
-- ------------|--------------------------|----------
-- Instructor  | instructor@school.edu    | password
-- Student     | maria@school.edu         | password
-- Student     | juan@school.edu          | password
-- Student     | ana@school.edu           | password
-- Student     | carlo@school.edu         | password
-- Student     | jasmine@school.edu       | password
-- ============================================================
-- To change passwords to "password123", visit:
--   http://localhost/sgs/reset_passwords.php
-- ============================================================