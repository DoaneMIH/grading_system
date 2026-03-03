<?php
// views/student/dashboard.php

require_once dirname(__DIR__, 2) . '/config/app.php';
require_once BASE_PATH . '/models/Grade.php';
require_once BASE_PATH . '/models/User.php';
requireRole('student');

$gradeModel = new Grade();
$studentId  = $gradeModel->getStudentRecordId($_SESSION['user_id']);
$grades     = $studentId ? $gradeModel->getByStudentId($studentId) : [];
$average    = $studentId ? $gradeModel->getAverage($studentId)     : 0;
$letter     = $average > 0 ? letterGrade($average) : '—';
$passed     = array_filter($grades, fn($g) => (float)$g['grade'] >= 60);
$failed     = array_filter($grades, fn($g) => (float)$g['grade'] <  60);

$pageTitle = 'My Dashboard';
$activeNav = 'dashboard';

include BASE_PATH . '/views/layouts/header.php';
?>

<!-- Welcome Banner -->
<div class="avg-banner">
    <div>
        <div class="avg-label">Welcome back</div>
        <div style="font-size:1.4rem;font-weight:700;margin-top:4px"><?= e($_SESSION['name']) ?></div>
        <div style="opacity:0.75;font-size:0.82rem;margin-top:4px"><?= e($_SESSION['email']) ?></div>
    </div>
    <div style="text-align:right">
        <div class="avg-label">Overall Average</div>
        <div class="avg-value"><?= $average > 0 ? number_format($average, 1) : '—' ?></div>
        <div class="avg-letter"><?= $letter ?></div>
    </div>
</div>

<!-- Quick stats -->
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon blue">📚</div>
        <div>
            <div class="stat-value"><?= count($grades) ?></div>
            <div class="stat-label">Enrolled Courses</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">✅</div>
        <div>
            <div class="stat-value"><?= count($passed) ?></div>
            <div class="stat-label">Passed</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange">❌</div>
        <div>
            <div class="stat-value"><?= count($failed) ?></div>
            <div class="stat-label">Failed</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon teal">📊</div>
        <div>
            <div class="stat-value"><?= $average > 0 ? number_format($average, 1) : '—' ?></div>
            <div class="stat-label">GPA Average</div>
        </div>
    </div>
</div>

<!-- Recent Grades Preview -->
<div class="card">
    <div class="card-header">
        <h2>📋 Recent Grades</h2>
        <a href="grades.php" class="btn btn-sm btn-outline">View All</a>
    </div>
    <div class="card-body no-pad">
        <?php if (empty($grades)): ?>
            <div class="empty-state">
                <div class="empty-icon">📋</div>
                <p>No grades recorded yet. Check back soon!</p>
            </div>
        <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Course</th><th>Grade</th><th>Letter</th><th>Remarks</th></tr>
                </thead>
                <tbody>
                    <?php foreach (array_slice($grades, 0, 5) as $g):
                        $pass = (float)$g['grade'] >= 60;
                    ?>
                    <tr class="<?= $pass ? 'grade-row-pass' : 'grade-row-fail' ?>">
                        <td>
                            <span class="badge badge-grade"><?= e($g['course_id']) ?></span>
                            <span class="small text-muted"> <?= e($g['course_name']) ?></span>
                        </td>
                        <td class="fw-bold"><?= number_format((float)$g['grade'], 2) ?></td>
                        <td><span class="badge badge-info"><?= letterGrade((float)$g['grade']) ?></span></td>
                        <td><span class="badge <?= $pass ? 'badge-pass' : 'badge-fail' ?>"><?= remarks((float)$g['grade']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include BASE_PATH . '/views/layouts/footer.php'; ?>