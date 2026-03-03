<?php
// controllers/AuthController.php
// Handles login and logout

require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/models/User.php';

class AuthController {
    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    /** Show login page */
    public function showLogin(): void {
        if (isLoggedIn()) $this->redirectByRole();
        require BASE_PATH . '/views/auth/login.php';
    }

    /** Handle login form POST */
    public function login(): void {
        verifyCsrf();

        $email    = trim($_POST['email']    ?? '');
        $password = trim($_POST['password'] ?? '');

        // Basic validation
        if (!$email || !$password) {
            flash('error', 'Email and password are required.');
            redirect('/index.php');
        }

        $user = $this->userModel->findByEmail($email);

        // Verify password hash
        if (!$user || !password_verify($password, $user['password'])) {
            flash('error', 'Invalid email or password.');
            redirect('/index.php');
        }

        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name']    = $user['name'];
        $_SESSION['email']   = $user['email'];
        $_SESSION['role']    = $user['role'];

        $this->redirectByRole();
    }

    /** Logout and destroy session */
    public function logout(): void {
        session_destroy();
        redirect('/index.php');
    }

    /** Redirect user to their role dashboard */
    private function redirectByRole(): void {
        if ($_SESSION['role'] === 'instructor') {
            redirect('/views/instructor/dashboard.php');
        } else {
            redirect('/views/student/dashboard.php');
        }
    }
}