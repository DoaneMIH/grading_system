<?php
// ============================================
// auth.php - Handles Login & Logout
// Uses PHP sessions to track the logged-in user
// ============================================

session_start();        // Start or resume the session
header('Content-Type: application/json');
require_once 'db.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {

    // ---- Handle Login ----
    case 'login':
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (!$username || !$password) {
            echo json_encode(['success' => false, 'message' => 'Username and password are required.']);
            exit;
        }

        // Find user by username
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        // Verify password using PHP's built-in password_verify()
        if ($user && password_verify($password, $user['password'])) {
            // Store user info in session
            $_SESSION['user_id']    = $user['id'];
            $_SESSION['username']   = $user['username'];
            $_SESSION['role']       = $user['role'];         // 'instructor' or 'student'
            $_SESSION['student_id'] = $user['student_id'];   // null if instructor

            echo json_encode([
                'success' => true,
                'role'    => $user['role'],
                'message' => 'Login successful!'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);
        }
        break;

    // ---- Handle Logout ----
    case 'logout':
        session_destroy(); // Clear the session
        echo json_encode(['success' => true]);
        break;

    // ---- Check current session (used on page load) ----
    case 'check':
        if (isset($_SESSION['user_id'])) {
            echo json_encode([
                'logged_in'  => true,
                'role'       => $_SESSION['role'],
                'username'   => $_SESSION['username'],
                'student_id' => $_SESSION['student_id']
            ]);
        } else {
            echo json_encode(['logged_in' => false]);
        }
        break;

    default:
        echo json_encode(['error' => 'Unknown action.']);
}
?>