<?php
// views/instructor/dashboard.php

// Load app — BASE_PATH is defined inside config/app.php
require_once dirname(__DIR__, 2) . '/config/app.php';
require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/models/Course.php';
require_once BASE_PATH . '/models/Grade.php';
requireRole('instructor');

$userModel   = new User();
$courseModel = new Course();
$gradeModel  = new Grade();

$totalStudents = $userModel->countStudents();
$totalCourses  = $courseModel->count();
$totalGrades   = $gradeModel->count();

// Recent students (last 5)
$students = $userModel->getAllStudents();
$recent   = array_slice($students, 0, 5);

$pageTitle = 'Dashboard';
$activeNav = 'dashboard';

include BASE_PATH . '/views/layouts/header.php';
?>

<!-- Flash -->
<?php if ($msg = flash('success')): ?>
    <div class="alert alert-success" data-autohide>✅ <?= e($msg) ?></div>
<?php endif; ?>
<?php if ($err = flash('error')): ?>
    <div class="alert alert-error" data-autohide>⚠️ <?= e($err) ?></div>
<?php endif; ?>

<!-- Stat Cards -->
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon blue">👥</div>
        <div>
            <div class="stat-value"><?= $totalStudents ?></div>
            <div class="stat-label">Total Students</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">📚</div>
        <div>
            <div class="stat-value"><?= $totalCourses ?></div>
            <div class="stat-label">Total Courses</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange">📝</div>
        <div>
            <div class="stat-value"><?= $totalGrades ?></div>
            <div class="stat-label">Grade Records</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon teal">📊</div>
        <div>
            <div class="stat-value"><?= $totalStudents > 0 ? round($totalGrades / $totalStudents, 1) : 0 ?></div>
            <div class="stat-label">Avg Courses / Student</div>
        </div>
    </div>
</div>

<!-- Quick actions -->
<div class="card" style="margin-bottom:22px">
    <div class="card-header"><h2>⚡ Quick Actions</h2></div>
    <div class="card-body" style="display:flex;gap:12px;flex-wrap:wrap;">
        <button class="btn btn-primary" onclick="openModal('modalAddStudent')">➕ Add Student</button>
        <a href="courses.php" class="btn btn-success">📚 Manage Courses</a>
        <a href="grades.php"  class="btn btn-secondary">📝 Manage Grades</a>
    </div>
</div>

<!-- Recent Students -->
<div class="card">
    <div class="card-header">
        <h2>👥 Recent Students</h2>
        <a href="students.php" class="btn btn-sm btn-outline">View All</a>
    </div>
    <div class="card-body no-pad">
        <?php if (empty($recent)): ?>
            <div class="empty-state"><div class="empty-icon">👤</div><p>No students yet.</p></div>
        <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Name</th><th>Email</th><th>Joined</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($recent as $s): ?>
                    <tr>
                        <td class="fw-bold"><?= e($s['name']) ?></td>
                        <td class="text-muted"><?= e($s['email']) ?></td>
                        <td class="text-muted small"><?= date('M d, Y', strtotime($s['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add Student Modal -->
<div class="modal-overlay" id="modalAddStudent">
    <div class="modal">
        <div class="modal-header">
            <h3>➕ Add New Student</h3>
            <button class="modal-close" onclick="closeModal('modalAddStudent')">✕</button>
        </div>
        <form method="POST" action="<?= BASE_URL ?>/action.php">
            <input type="hidden" name="action"     value="add_student">
            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="name" placeholder="Maria Santos" required>
            </div>
            <div class="form-group">
                <label>Email Address *</label>
                <input type="email" name="email" placeholder="student@school.edu" required>
            </div>
            <div class="form-group">
                <label>Password * (min. 6 chars)</label>
                <input type="text" name="password" placeholder="Set initial password" required minlength="6">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalAddStudent')">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Student</button>
            </div>
        </form>
    </div>
</div>

<?php include BASE_PATH . '/views/layouts/footer.php'; ?>