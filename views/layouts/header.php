<?php
// views/layouts/header.php — shared sidebar + topbar for all authenticated pages
// Expects: $pageTitle (string), $activeNav (string)

$role     = $_SESSION['role']  ?? '';
$name     = $_SESSION['name']  ?? 'User';
$initials = strtoupper(implode('', array_map(
    fn($w) => $w[0],
    array_slice(explode(' ', trim($name)), 0, 2)
)));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Dashboard') ?> — <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="app-wrapper">

<!-- ===== SIDEBAR ===== -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <span class="brand-icon">🎓</span>
        <span class="brand-name"><?= APP_NAME ?></span>
    </div>

    <div class="sidebar-user">
        <div class="avatar"><?= $initials ?></div>
        <div class="user-info">
            <div class="user-name" title="<?= e($name) ?>"><?= e($name) ?></div>
            <div class="user-role">
                <span class="badge <?= $role === 'instructor' ? 'badge-instructor' : 'badge-student' ?>">
                    <?= ucfirst($role) ?>
                </span>
            </div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <?php if ($role === 'instructor'): ?>
        <div class="nav-section-title">Instructor</div>
        <a href="<?= BASE_URL ?>/views/instructor/dashboard.php"
           class="nav-item <?= ($activeNav ?? '') === 'dashboard' ? 'active' : '' ?>">
            <span class="nav-icon">📊</span> Dashboard
        </a>
        <a href="<?= BASE_URL ?>/views/instructor/students.php"
           class="nav-item <?= ($activeNav ?? '') === 'students' ? 'active' : '' ?>">
            <span class="nav-icon">👥</span> Students
        </a>
        <a href="<?= BASE_URL ?>/views/instructor/courses.php"
           class="nav-item <?= ($activeNav ?? '') === 'courses' ? 'active' : '' ?>">
            <span class="nav-icon">📚</span> Courses
        </a>
        <a href="<?= BASE_URL ?>/views/instructor/grades.php"
           class="nav-item <?= ($activeNav ?? '') === 'grades' ? 'active' : '' ?>">
            <span class="nav-icon">📝</span> Grades
        </a>
        <?php else: ?>
        <div class="nav-section-title">Student</div>
        <a href="<?= BASE_URL ?>/views/student/dashboard.php"
           class="nav-item <?= ($activeNav ?? '') === 'dashboard' ? 'active' : '' ?>">
            <span class="nav-icon">🏠</span> Dashboard
        </a>
        <a href="<?= BASE_URL ?>/views/student/grades.php"
           class="nav-item <?= ($activeNav ?? '') === 'grades' ? 'active' : '' ?>">
            <span class="nav-icon">📋</span> My Grades
        </a>
        <?php endif; ?>

        <div class="divider"></div>
        <div class="nav-section-title">Account</div>
        <a href="<?= BASE_URL ?>/index.php?action=logout" class="nav-item">
            <span class="nav-icon">🚪</span> Logout
        </a>
    </nav>
</aside>

<!-- ===== MAIN CONTENT ===== -->
<div class="main-content">
    <header class="topbar">
        <div class="flex-center gap-1">
            <button class="hamburger" id="hamburger">☰</button>
            <span class="topbar-title"><?= e($pageTitle ?? 'Dashboard') ?></span>
        </div>
        <div class="topbar-right">
            <span class="text-muted small">👤 <?= e($name) ?></span>
            <a href="<?= BASE_URL ?>/index.php?action=logout" class="btn btn-sm btn-outline">Logout</a>
        </div>
    </header>

    <div class="page-body">