<?php
// views/instructor/students.php

require_once dirname(__DIR__, 2) . '/config/app.php';
require_once BASE_PATH . '/models/User.php';
requireRole('instructor');

$userModel = new User();
$search    = trim($_GET['q'] ?? '');
$students  = $userModel->getAllStudents($search);

$pageTitle = 'Manage Students';
$activeNav = 'students';

include BASE_PATH . '/views/layouts/header.php';
?>

<!-- Flash -->
<?php if ($msg = flash('success')): ?>
    <div class="alert alert-success" data-autohide>✅ <?= e($msg) ?></div>
<?php endif; ?>
<?php if ($err = flash('error')): ?>
    <div class="alert alert-error" data-autohide>⚠️ <?= e($err) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h2>👥 Students (<?= count($students) ?>)</h2>
        <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
            <!-- Server-side search -->
            <form method="GET" class="search-bar">
                <input type="text" name="q" placeholder="Search name or email…"
                       value="<?= e($search) ?>" id="studentSearch">
                <button type="submit" class="btn btn-sm btn-primary">🔍</button>
                <?php if ($search): ?>
                    <a href="students.php" class="btn btn-sm btn-secondary">✕ Clear</a>
                <?php endif; ?>
            </form>
            <button class="btn btn-primary" onclick="openModal('modalAddStudent')">➕ Add Student</button>
        </div>
    </div>
    <div class="card-body no-pad">
        <?php if (empty($students)): ?>
            <div class="empty-state">
                <div class="empty-icon">👤</div>
                <p><?= $search ? "No students matching \"$search\"." : 'No students yet. Add one!' ?></p>
            </div>
        <?php else: ?>
        <div class="table-wrap">
            <table id="studentsTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Student ID</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $i => $s): ?>
                    <tr>
                        <td class="text-muted small"><?= $i + 1 ?></td>
                        <td class="fw-bold"><?= e($s['name']) ?></td>
                        <td class="text-muted"><?= e($s['email']) ?></td>
                        <td><span class="badge badge-info"><?= e($s['student_id']) ?></span></td>
                        <td class="text-muted small"><?= date('M d, Y', strtotime($s['created_at'])) ?></td>
                        <td>
                            <div class="td-actions">
                                <button class="btn btn-sm btn-outline"
                                    onclick="editStudent(<?= $s['id'] ?>, '<?= addslashes(e($s['name'])) ?>', '<?= e($s['email']) ?>')">
                                    ✏️ Edit
                                </button>
                                <!-- Delete form -->
                                <form id="del_stu_<?= $s['id'] ?>" method="POST" action="<?= BASE_URL ?>/action.php" style="display:inline">
                                    <input type="hidden" name="action"     value="delete_student">
                                    <input type="hidden" name="user_id"    value="<?= $s['id'] ?>">
                                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                                </form>
                                <button class="btn btn-sm btn-danger"
                                    onclick="confirmDelete('del_stu_<?= $s['id'] ?>', '<?= addslashes(e($s['name'])) ?>')">
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

<!-- ===== ADD STUDENT MODAL ===== -->
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
                <label>Initial Password * (min 6 chars)</label>
                <input type="text" name="password" placeholder="Set initial password" required minlength="6">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalAddStudent')">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Student</button>
            </div>
        </form>
    </div>
</div>

<!-- ===== EDIT STUDENT MODAL ===== -->
<div class="modal-overlay" id="modalEditStudent">
    <div class="modal">
        <div class="modal-header">
            <h3>✏️ Edit Student</h3>
            <button class="modal-close" onclick="closeModal('modalEditStudent')">✕</button>
        </div>
        <form method="POST" action="<?= BASE_URL ?>/action.php">
            <input type="hidden" name="action"     value="update_student">
            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
            <input type="hidden" name="user_id"    id="edit_user_id">
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="name" id="edit_name" required>
            </div>
            <div class="form-group">
                <label>Email Address *</label>
                <input type="email" name="email" id="edit_email" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditStudent')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- ===== CONFIRM DELETE MODAL ===== -->
<div class="modal-overlay" id="modalConfirmDelete">
    <div class="modal">
        <div class="modal-header">
            <h3>⚠️ Confirm Delete</h3>
            <button class="modal-close" onclick="closeModal('modalConfirmDelete')">✕</button>
        </div>
        <p>Are you sure you want to delete <strong id="deleteItemName"></strong>?
           This will also remove all their grades and cannot be undone.</p>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeModal('modalConfirmDelete')">Cancel</button>
            <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Yes, Delete</button>
        </div>
    </div>
</div>

<?php include BASE_PATH . '/views/layouts/footer.php'; ?>