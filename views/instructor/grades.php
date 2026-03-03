<?php
// views/instructor/grades.php

require_once dirname(__DIR__, 2) . '/config/app.php';
require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/models/Course.php';
require_once BASE_PATH . '/models/Grade.php';
requireRole('instructor');

$userModel   = new User();
$courseModel = new Course();
$gradeModel  = new Grade();

$search  = trim($_GET['q'] ?? '');
$grades  = $gradeModel->getAll($search);
$students = $userModel->getAllStudents();
$courses  = $courseModel->getAll();

$pageTitle = 'Manage Grades';
$activeNav = 'grades';

include BASE_PATH . '/views/layouts/header.php';
?>

<?php if ($msg = flash('success')): ?>
    <div class="alert alert-success" data-autohide>✅ <?= e($msg) ?></div>
<?php endif; ?>
<?php if ($err = flash('error')): ?>
    <div class="alert alert-error" data-autohide>⚠️ <?= e($err) ?></div>
<?php endif; ?>

<!-- Assign Grade Card -->
<div class="card">
    <div class="card-header"><h2>➕ Assign / Update Grade</h2></div>
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/action.php">
            <input type="hidden" name="action"     value="save_grade">
            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
            <div class="form-row">
                <div class="form-group">
                    <label>Student *</label>
                    <select name="student_id" id="add_grade_student" required>
                        <option value="">— Select Student —</option>
                        <?php foreach ($students as $s): ?>
                            <option value="<?= $s['student_id'] ?>"><?= e($s['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Course *</label>
                    <select name="course_id" required>
                        <option value="">— Select Course —</option>
                        <?php foreach ($courses as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= e($c['course_id']) ?> — <?= e($c['course_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" style="max-width:160px;">
                    <label>Grade (0–100) *</label>
                    <input type="number" name="grade" id="add_grade_value"
                           min="0" max="100" step="0.01" placeholder="85.00" required>
                </div>
                <div class="form-group" style="justify-content:flex-end;">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-success">💾 Save Grade</button>
                </div>
            </div>
            <!-- Live preview -->
            <div id="add_grade_preview" style="margin-top:-6px;"></div>
        </form>
    </div>
</div>

<!-- Grades Table -->
<div class="card">
    <div class="card-header">
        <h2>📝 All Grade Records (<?= count($grades) ?>)</h2>
        <div style="display:flex;gap:10px;align-items:center;">
            <form method="GET" class="search-bar">
                <input type="text" name="q" placeholder="Search student / course…"
                       value="<?= e($search) ?>" id="gradeSearch">
                <button type="submit" class="btn btn-sm btn-primary">🔍</button>
                <?php if ($search): ?>
                    <a href="grades.php" class="btn btn-sm btn-secondary">✕ Clear</a>
                <?php endif; ?>
            </form>
        </div>
    </div>
    <div class="card-body no-pad">
        <?php if (empty($grades)): ?>
            <div class="empty-state">
                <div class="empty-icon">📝</div>
                <p><?= $search ? "No records matching \"$search\"." : 'No grades assigned yet.' ?></p>
            </div>
        <?php else: ?>
        <div class="table-wrap">
            <table id="gradesTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Units</th>
                        <th>Grade</th>
                        <th>Letter</th>
                        <th>Remarks</th>
                        <th>Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($grades as $i => $g):
                        $letter  = letterGrade((float)$g['grade']);
                        $remark  = remarks((float)$g['grade']);
                        $pass    = $remark === 'Passed';
                    ?>
                    <tr>
                        <td class="text-muted small"><?= $i + 1 ?></td>
                        <td class="fw-bold"><?= e($g['student_name']) ?></td>
                        <td>
                            <span class="badge badge-grade"><?= e($g['course_id']) ?></span>
                            <span class="small text-muted"> <?= e($g['course_name']) ?></span>
                        </td>
                        <td><?= $g['units'] ?></td>
                        <td class="fw-bold"><?= number_format((float)$g['grade'], 2) ?></td>
                        <td><span class="badge badge-info"><?= $letter ?></span></td>
                        <td><span class="badge <?= $pass ? 'badge-pass' : 'badge-fail' ?>"><?= $remark ?></span></td>
                        <td class="text-muted small"><?= date('M d, Y', strtotime($g['updated_at'])) ?></td>
                        <td>
                            <div class="td-actions">
                                <!-- Edit opens a modal pre-filled -->
                                <button class="btn btn-sm btn-outline"
                                    onclick="editGrade(<?= $g['id'] ?>, <?= $g['student_id'] ?>, <?= $g['course_id'] ?>, <?= $g['grade'] ?>)">
                                    ✏️ Edit
                                </button>
                                <form id="del_grd_<?= $g['id'] ?>" method="POST" action="<?= BASE_URL ?>/action.php" style="display:inline">
                                    <input type="hidden" name="action"     value="delete_grade">
                                    <input type="hidden" name="id"         value="<?= $g['id'] ?>">
                                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                                </form>
                                <button class="btn btn-sm btn-danger"
                                    onclick="confirmDelete('del_grd_<?= $g['id'] ?>', '<?= addslashes(e($g['student_name'])) ?> — <?= e($g['course_id']) ?>')">
                                    🗑
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- EDIT GRADE MODAL -->
<div class="modal-overlay" id="modalEditGrade">
    <div class="modal">
        <div class="modal-header">
            <h3>✏️ Edit Grade</h3>
            <button class="modal-close" onclick="closeModal('modalEditGrade')">✕</button>
        </div>
        <form method="POST" action="<?= BASE_URL ?>/action.php">
            <input type="hidden" name="action"     value="save_grade">
            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
            <!-- student_id and course_id are set by JS -->
            <input type="hidden" name="student_id" id="edit_grade_student">
            <input type="hidden" name="course_id"  id="edit_grade_course">
            <input type="hidden"                   id="edit_grade_id">
            <div class="form-group">
                <label>New Grade (0–100) *</label>
                <input type="number" name="grade" id="edit_grade_value"
                       min="0" max="100" step="0.01" required>
            </div>
            <div id="edit_grade_preview" style="margin-top:6px;margin-bottom:6px;"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditGrade')">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Grade</button>
            </div>
        </form>
    </div>
</div>

<!-- CONFIRM DELETE -->
<div class="modal-overlay" id="modalConfirmDelete">
    <div class="modal">
        <div class="modal-header">
            <h3>⚠️ Confirm Delete</h3>
            <button class="modal-close" onclick="closeModal('modalConfirmDelete')">✕</button>
        </div>
        <p>Delete grade for <strong id="deleteItemName"></strong>? This cannot be undone.</p>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeModal('modalConfirmDelete')">Cancel</button>
            <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Yes, Delete</button>
        </div>
    </div>
</div>

<?php include BASE_PATH . '/views/layouts/footer.php'; ?>