<?php
// views/student/grades.php — Full grade table for the student

require_once dirname(__DIR__, 2) . '/config/app.php';
require_once BASE_PATH . '/models/Grade.php';
requireRole('student');

$gradeModel = new Grade();
$studentId  = $gradeModel->getStudentRecordId($_SESSION['user_id']);
$grades     = $studentId ? $gradeModel->getByStudentId($studentId) : [];
$average    = $studentId ? $gradeModel->getAverage($studentId)     : 0;
$letter     = $average > 0 ? letterGrade($average) : '—';
$remark     = $average > 0 ? remarks($average) : '—';
$totalUnits = array_sum(array_column($grades, 'units'));
$passed     = count(array_filter($grades, fn($g) => (float)$g['grade'] >= 60));
$failed     = count($grades) - $passed;

$pageTitle = 'My Grades';
$activeNav = 'grades';

include BASE_PATH . '/views/layouts/header.php';
?>

<!-- Average Banner -->
<div class="avg-banner">
    <div>
        <div class="avg-label">Student</div>
        <div style="font-size:1.3rem;font-weight:700;margin-top:4px"><?= e($_SESSION['name']) ?></div>
        <div style="opacity:0.75;font-size:0.82rem;margin-top:2px"><?= e($_SESSION['email']) ?></div>
    </div>
    <div style="text-align:right;">
        <div class="avg-label">Overall Average</div>
        <div class="avg-value"><?= $average > 0 ? number_format($average, 2) : '—' ?></div>
        <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:6px;flex-wrap:wrap;">
            <span class="avg-letter"><?= $letter ?></span>
            <?php if ($average > 0): ?>
            <span class="badge <?= $average >= 60 ? 'badge-pass' : 'badge-fail' ?>" style="font-size:0.85rem;padding:4px 12px;">
                <?= $remark ?>
            </span>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Summary row -->
<div class="stat-grid" style="margin-bottom:22px;">
    <div class="stat-card">
        <div class="stat-icon blue">📚</div>
        <div><div class="stat-value"><?= count($grades) ?></div><div class="stat-label">Courses</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon teal">⚖️</div>
        <div><div class="stat-value"><?= $totalUnits ?></div><div class="stat-label">Total Units</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">✅</div>
        <div><div class="stat-value"><?= $passed ?></div><div class="stat-label">Passed</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange">❌</div>
        <div><div class="stat-value"><?= $failed ?></div><div class="stat-label">Failed</div></div>
    </div>
</div>

<!-- Full grades table -->
<div class="card">
    <div class="card-header">
        <h2>📋 Grade Report</h2>
        <?php if (count($grades)): ?>
        <span class="small text-muted"><?= count($grades) ?> record(s)</span>
        <?php endif; ?>
    </div>
    <div class="card-body no-pad">
        <?php if (empty($grades)): ?>
            <div class="empty-state">
                <div class="empty-icon">📋</div>
                <p>No grades have been posted for you yet.<br>Please check back later or contact your instructor.</p>
            </div>
        <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Course ID</th>
                        <th>Course Name</th>
                        <th>Units</th>
                        <th>Grade</th>
                        <th>Letter</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($grades as $i => $g):
                        $g_num  = (float)$g['grade'];
                        $pass   = $g_num >= 60;
                    ?>
                    <tr class="<?= $pass ? 'grade-row-pass' : 'grade-row-fail' ?>">
                        <td class="text-muted small"><?= $i + 1 ?></td>
                        <td><span class="badge badge-grade"><?= e($g['course_id']) ?></span></td>
                        <td><?= e($g['course_name']) ?></td>
                        <td><?= $g['units'] ?></td>
                        <td class="fw-bold" style="font-size:1rem;">
                            <?= number_format($g_num, 2) ?>
                        </td>
                        <td><span class="badge badge-info"><?= letterGrade($g_num) ?></span></td>
                        <td>
                            <span class="badge <?= $pass ? 'badge-pass' : 'badge-fail' ?>">
                                <?= remarks($g_num) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <!-- Footer row with average -->
                <tfoot>
                    <tr style="background:#f8f9fa;font-weight:700;">
                        <td colspan="4" style="padding:12px 16px;text-align:right;color:var(--text-muted);font-size:0.85rem;">
                            OVERALL AVERAGE
                        </td>
                        <td style="padding:12px 16px;font-size:1.05rem;color:var(--primary);">
                            <?= number_format($average, 2) ?>
                        </td>
                        <td><span class="badge badge-info"><?= $letter ?></span></td>
                        <td>
                            <span class="badge <?= $average >= 60 ? 'badge-pass' : 'badge-fail' ?>">
                                <?= $remark ?>
                            </span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Grading scale reference -->
        <div style="padding:16px 22px;border-top:1px solid var(--border);">
            <p class="small text-muted fw-bold" style="margin-bottom:8px;">GRADING SCALE</p>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <?php
                $scale = [
                    ['A+','95–100','pass'],['A','90–94','pass'],['A-','87–89','pass'],
                    ['B+','83–86','pass'],['B','80–82','pass'],['B-','77–79','pass'],
                    ['C+','73–76','pass'],['C','70–72','pass'],['C-','67–69','pass'],
                    ['D','60–66','pass'],['F','0–59','fail'],
                ];
                foreach ($scale as [$l, $r, $t]): ?>
                <span style="font-size:0.75rem;white-space:nowrap;">
                    <span class="badge badge-info"><?= $l ?></span>
                    <span class="text-muted"><?= $r ?></span>
                </span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include BASE_PATH . '/views/layouts/footer.php'; ?>