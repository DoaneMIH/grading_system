// assets/js/app.js — Student Grading System

// ============================================
// MOBILE SIDEBAR TOGGLE
// ============================================
const hamburger = document.getElementById('hamburger');
const sidebar   = document.getElementById('sidebar');
const overlay   = document.getElementById('sidebarOverlay');

if (hamburger) {
    hamburger.addEventListener('click', () => {
        sidebar.classList.toggle('open');
        overlay.style.display = sidebar.classList.contains('open') ? 'block' : 'none';
    });
}
if (overlay) {
    overlay.addEventListener('click', () => {
        sidebar.classList.remove('open');
        overlay.style.display = 'none';
    });
}

// ============================================
// MODAL SYSTEM
// ============================================
/**
 * Open a modal by its ID
 */
function openModal(id) {
    const el = document.getElementById(id);
    if (el) {
        el.classList.add('open');
        document.body.style.overflow = 'hidden';
    }
}

/**
 * Close a modal by its ID
 */
function closeModal(id) {
    const el = document.getElementById(id);
    if (el) {
        el.classList.remove('open');
        document.body.style.overflow = '';
    }
}

// Close modal when clicking the overlay background (not inside .modal)
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) closeModal(this.id);
    });
});

// Close on Escape key
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.open').forEach(m => {
            m.classList.remove('open');
            document.body.style.overflow = '';
        });
    }
});

// ============================================
// EDIT MODALS — populate form fields
// ============================================

/** Open edit-student modal, fill in current values */
function editStudent(userId, name, email) {
    document.getElementById('edit_user_id').value = userId;
    document.getElementById('edit_name').value    = name;
    document.getElementById('edit_email').value   = email;
    openModal('modalEditStudent');
}

/** Open edit-course modal */
function editCourse(id, courseId, courseName, units) {
    document.getElementById('edit_course_db_id').value  = id;
    document.getElementById('edit_course_id').value     = courseId;
    document.getElementById('edit_course_name').value   = courseName;
    document.getElementById('edit_units').value         = units;
    openModal('modalEditCourse');
}

/** Open edit-grade modal */
function editGrade(gradeId, studentId, courseId, grade) {
    document.getElementById('edit_grade_id').value     = gradeId;
    document.getElementById('edit_grade_student').value = studentId;
    document.getElementById('edit_grade_course').value  = courseId;
    document.getElementById('edit_grade_value').value  = grade;
    updateGradePreview('edit_grade_value', 'edit_grade_preview');
    openModal('modalEditGrade');
}

// ============================================
// DELETE CONFIRMATION HELPERS
// ============================================

/** Fills a generic confirm-delete modal and submits form */
function confirmDelete(formId, itemName) {
    const label = document.getElementById('deleteItemName');
    if (label) label.textContent = itemName;
    openModal('modalConfirmDelete');
    // Store which form to submit on confirm
    document.getElementById('modalConfirmDelete')
            .dataset.targetForm = formId;
}

// Wire up the "Yes, Delete" button
const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
if (confirmDeleteBtn) {
    confirmDeleteBtn.addEventListener('click', () => {
        const formId = document.getElementById('modalConfirmDelete').dataset.targetForm;
        if (formId) document.getElementById(formId).submit();
    });
}

// ============================================
// LIVE GRADE PREVIEW (letter + pass/fail)
// ============================================
function computeLetter(g) {
    if (g >= 95) return 'A+';
    if (g >= 90) return 'A';
    if (g >= 87) return 'A-';
    if (g >= 83) return 'B+';
    if (g >= 80) return 'B';
    if (g >= 77) return 'B-';
    if (g >= 73) return 'C+';
    if (g >= 70) return 'C';
    if (g >= 67) return 'C-';
    if (g >= 60) return 'D';
    return 'F';
}

function updateGradePreview(inputId, previewId) {
    const input   = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    if (!input || !preview) return;
    const g       = parseFloat(input.value);
    if (isNaN(g) || g < 0 || g > 100) {
        preview.innerHTML = '<span class="text-muted">Enter 0–100</span>';
        return;
    }
    const letter  = computeLetter(g);
    const passed  = g >= 60;
    const cls     = passed ? 'badge-pass' : 'badge-fail';
    const remarks = passed ? 'Passed' : 'Failed';
    preview.innerHTML = `
        <span class="badge badge-grade">${letter}</span>
        <span class="badge ${cls}" style="margin-left:6px">${remarks}</span>`;
}

// Wire up grade inputs
['add_grade_value', 'edit_grade_value'].forEach(id => {
    const el = document.getElementById(id);
    if (el) {
        el.addEventListener('input', () => {
            const previewId = id === 'add_grade_value' ? 'add_grade_preview' : 'edit_grade_preview';
            updateGradePreview(id, previewId);
        });
    }
});

// ============================================
// SEARCH FILTER (client-side table filter)
// ============================================
function initTableSearch(inputId, tableId) {
    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);
    if (!input || !table) return;

    input.addEventListener('input', () => {
        const q = input.value.toLowerCase();
        table.querySelectorAll('tbody tr').forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(q) ? '' : 'none';
        });
    });
}

// Initialize table searches
initTableSearch('studentSearch', 'studentsTable');
initTableSearch('courseSearch',  'coursesTable');
initTableSearch('gradeSearch',   'gradesTable');

// ============================================
// AUTO-DISMISS FLASH ALERTS
// ============================================
document.querySelectorAll('.alert[data-autohide]').forEach(alert => {
    setTimeout(() => {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    }, 4000);
});

// ============================================
// TOAST NOTIFICATIONS (programmatic)
// ============================================
function showToast(message, type = 'success') {
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `<span>${type === 'success' ? '✅' : '❌'}</span> ${message}`;
    container.appendChild(toast);
    requestAnimationFrame(() => toast.classList.add('show'));
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3500);
}