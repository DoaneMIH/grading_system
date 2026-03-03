<?php
// config/app.php — Central config, helpers, session bootstrap

defined('APP_NAME')  || define('APP_NAME',  'GradePortal');
defined('BASE_PATH') || define('BASE_PATH', dirname(__DIR__));  // filesystem root

// AUTO-DETECT the web subfolder so redirects work whether the project lives at
// localhost/ OR localhost/sgs/ OR localhost/student_management/ etc.
if (!defined('BASE_URL')) {
    $docRoot = rtrim(str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'])), '/');
    $appRoot = rtrim(str_replace('\\', '/', realpath(BASE_PATH)), '/');
    $base    = str_replace($docRoot, '', $appRoot);   // e.g. "" or "/sgs"
    define('BASE_URL', $base);
}

// Start session once
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn(): bool { return isset($_SESSION['user_id']); }
function hasRole(string $role): bool { return ($_SESSION['role'] ?? '') === $role; }

function redirect(string $path): void {
    header('Location: ' . BASE_URL . $path);
    exit;
}
function requireLogin(): void { if (!isLoggedIn()) redirect('/index.php'); }
function requireRole(string $role): void {
    requireLogin();
    if (!hasRole($role)) redirect('/index.php');
}

function e(string $val): string { return htmlspecialchars($val, ENT_QUOTES, 'UTF-8'); }

function flash(string $key, string $msg = ''): string {
    if ($msg !== '') { $_SESSION['flash'][$key] = $msg; return ''; }
    $val = $_SESSION['flash'][$key] ?? '';
    unset($_SESSION['flash'][$key]);
    return $val;
}

function letterGrade(float $g): string {
    if ($g >= 95) return 'A+'; if ($g >= 90) return 'A';  if ($g >= 87) return 'A-';
    if ($g >= 83) return 'B+'; if ($g >= 80) return 'B';  if ($g >= 77) return 'B-';
    if ($g >= 73) return 'C+'; if ($g >= 70) return 'C';  if ($g >= 67) return 'C-';
    if ($g >= 60) return 'D';
    return 'F';
}
function remarks(float $g): string { return $g >= 60 ? 'Passed' : 'Failed'; }

function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}
function verifyCsrf(): void {
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
        http_response_code(403); die('Invalid CSRF token. Go back and try again.');
    }
}