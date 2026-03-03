<?php
// views/instructor/courses.php

require_once dirname(__DIR__, 2) . '/config/app.php';
require_once BASE_PATH . '/models/Course.php';
requireRole('instructor');

$courseModel = new Course();
$courses     = $courseModel->getAll();

$pageTitle = 'Manage Courses';
$activeNav = 'courses';

include BASE_PATH . '/views/layouts/header.php';
?>

<?php if ($msg = flash('success')): ?>
    <div class="alert alert-success" data-autohide>✅ <?= e($msg) ?></div>
<?php endif; ?>
<?php if ($err = flash('error')): ?>
    <div class="alert alert-error" data-autohide>⚠️ <?= e($err) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h2>📚 Courses (<?= count($courses) ?>)</h2>
        <div style="display:flex;gap:10px;align-items:center;">
            <div class="search-bar">
                <input type="text" id="courseSearch" placeholder="Filter courses…">
            </div>
            <button class="btn btn-primary" onclick="openModal('modalAddCourse')">➕ Add Course</button>
        </div>
    </div>
    <div class="card-body no-pad">
        <?php if (empty($courses)): ?>
            <div class="empty-state">
                <div class="empty-icon">📚</div>
                <p>No courses yet. Add the first one!</p>
            </div>
        <?php else: ?>
        <div class="table-wrap">
            <table id="coursesTable">
                <thead>
                    <tr><th>#</th><th>Course ID</th><th>Course Name</th><th>Units</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($courses as $i => $c): ?>
                    <tr>
                        <td class="text-muted small"><?= $i + 1 ?></td>
                        <td><span class="badge badge-grade"><?= e($c['course_id']) ?></span></td>
                        <td class="fw-bold"><?= e($c['course_name']) ?></td>
                        <td><?= $c['units'] ?> units</td>
                        <td>
                            <div class="td-actions">
                                <button class="btn btn-sm btn-outline"
                                    onclick="editCourse(<?= $c['id'] ?>, '<?= e($c['course_id']) ?>', '<?= addslashes(e($c['course_name'])) ?>', <?= $c['units'] ?>)">
                                    ✏️ Edit
                                </button>
                                <form id="del_crs_<?= $c['id'] ?>" method="POST" action="<?= BASE_URL ?>/action.php" style="display:inline">
                                    <input type="hidden" name="action"     value="delete_course">
                                    <input type="hidden" name="id"         value="<?= $c['id'] ?>">
                                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                                </form>
                                <button class="btn btn-sm btn-danger"
                                    onclick="confirmDelete('del_crs_<?= $c['id'] ?>', '<?= addslashes(e($c['course_name'])) ?>')">
                                    🗑 Delete
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

<!-- ADD COURSE MODAL -->
<div class="modal-overlay" id="modalAddCourse">
    <div class="modal">
        <div class="modal-header">
            <h3>➕ Add New Course</h3>
            <button class="modal-close" onclick="closeModal('modalAddCourse')">✕</button>
        </div>
        <form method="POST" action="<?= BASE_URL ?>/action.php">
            <input type="hidden" name="action"     value="add_course">
            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
            <div class="form-row">
                <div class="form-group" style="max-width:140px;">
                    <label>Course ID *</label>
                    <input type="text" name="course_id" placeholder="CS101" required style="text-transform:uppercase">
                </div>
                <div class="form-group">
                    <label>Course Name *</label>
                    <input type="text" name="course_name" placeholder="Introduction to Computing" required>
                </div>
            </div>
            <div class="form-group" style="max-width:120px;">
                <label>Units *</label>
                <input type="number" name="units" value="3" min="1" max="6" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalAddCourse')">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Course</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT COURSE MODAL -->
<div class="modal-overlay" id="modalEditCourse">
    <div class="modal">
        <div class="modal-header">
            <h3>✏️ Edit Course</h3>
            <button class="modal-close" onclick="closeModal('modalEditCourse')">✕</button>
        </div>
        <form method="POST" action="<?= BASE_URL ?>/action.php">
            <input type="hidden" name="action"       value="update_course">
            <input type="hidden" name="csrf_token"   value="<?= csrfToken() ?>">
            <input type="hidden" name="id"           id="edit_course_db_id">
            <div class="form-row">
                <div class="form-group" style="max-width:140px;">
                    <label>Course ID *</label>
                    <input type="text" name="course_id" id="edit_course_id" required style="text-transform:uppercase">
                </div>
                <div class="form-group">
                    <label>Course Name *</label>
                    <input type="text" name="course_name" id="edit_course_name" required>
                </div>
            </div>
            <div class="form-group" style="max-width:120px;">
                <label>Units *</label>
                <input type="number" name="units" id="edit_units" min="1" max="6" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditCourse')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
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
        <p>Are you sure you want to delete <strong id="deleteItemName"></strong>?
           All related grades will also be removed.</p>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeModal('modalConfirmDelete')">Cancel</button>
            <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Yes, Delete</button>
        </div>
    </div>
</div>

<?php include BASE_PATH . '/views/layouts/footer.php'; ?>