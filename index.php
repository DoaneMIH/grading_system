<?php
// index.php — Application entry point / login page
// Boots the app, routes POST to AuthController

// BASE_PATH is defined inside config/app.php — do not redefine it here
require_once __DIR__ . '/config/app.php';
require_once 'controllers/AuthController.php';

$auth = new AuthController();

// Route POST (login / logout actions)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    match($action) {
        'login'  => $auth->login(),
        'logout' => $auth->logout(),
        default  => $auth->showLogin(),
    };
} else {
    // Handle GET ?action=logout
    if (($_GET['action'] ?? '') === 'logout') {
        $auth->logout();
    }
    $auth->showLogin();
}