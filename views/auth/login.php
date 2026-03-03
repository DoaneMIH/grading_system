<?php
// views/auth/login.php — standalone login page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body class="login-body">

<div class="login-card">
    <div class="login-logo">
        <div class="icon">🎓</div>
        <h1><?= APP_NAME ?></h1>
        <p>Student Grading System</p>
    </div>

    <?php if ($err = flash('error')): ?>
        <div class="alert alert-error" data-autohide>⚠️ <?= e($err) ?></div>
    <?php endif; ?>

    <div class="demo-box">
        <strong>🧪 Demo Accounts &mdash; password: <code>password123</code></strong>
        👨‍🏫 Instructor: <code>instructor@school.edu</code><br>
        👩‍🎓 Students: <code>maria@school.edu</code>, <code>juan@school.edu</code>,
        <code>ana@school.edu</code>, <code>carlo@school.edu</code>, <code>jasmine@school.edu</code>
    </div>

    <form method="POST" action="<?= BASE_URL ?>/index.php">
        <input type="hidden" name="action"     value="login">
        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="you@school.edu"
                   value="<?= e($_POST['email'] ?? '') ?>" required autofocus>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn btn-primary btn-full">🔑 Sign In</button>
    </form>
</div>

<script src="<?= BASE_URL ?>/assets/js/app.js"></script>
</body>
</html>