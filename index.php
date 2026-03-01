<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Grading System</title>
    <style>
        /* ============================================
           BASE STYLES
           ============================================ */
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #eef2f7;
            color: #333;
            min-height: 100vh;
        }

        /* ---- Header ---- */
        header {
            background: #1a3c6e;
            color: white;
            padding: 14px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        header .brand { display: flex; align-items: center; gap: 10px; }
        header h1 { font-size: 1.3rem; }
        header .user-info { display: flex; align-items: center; gap: 14px; font-size: 0.9rem; }
        header .role-badge {
            background: rgba(255,255,255,0.2);
            padding: 3px 12px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .role-badge.instructor { background: #e67e22; }
        .role-badge.student    { background: #27ae60; }

        /* ---- Navigation Tabs ---- */
        nav {
            background: #fff;
            padding: 0 28px;
            border-bottom: 2px solid #dce3ee;
            display: flex;
            gap: 2px;
        }
        nav button {
            padding: 12px 20px;
            border: none;
            background: none;
            cursor: pointer;
            font-size: 0.9rem;
            color: #666;
            border-bottom: 3px solid transparent;
            transition: all 0.2s;
        }
        nav button:hover { color: #1a3c6e; }
        nav button.active { color: #1a3c6e; font-weight: 700; border-bottom-color: #1a3c6e; }

        /* ---- Main Content ---- */
        main { padding: 24px 28px; max-width: 1150px; margin: 0 auto; }

        /* ---- Panels ---- */
        .tab-panel { display: none; }
        .tab-panel.active { display: block; }

        h2 { color: #1a3c6e; margin-bottom: 16px; font-size: 1.15rem; }
        h3 { color: #34495e; margin-bottom: 10px; font-size: 1rem; }

        /* ---- Cards ---- */
        .card {
            background: #fff;
            border-radius: 10px;
            padding: 20px 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            margin-bottom: 20px;
        }

        /* ---- Forms ---- */
        .form-row { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 14px; }
        .form-group { display: flex; flex-direction: column; gap: 5px; flex: 1; min-width: 150px; }
        label { font-size: 0.82rem; color: #666; font-weight: 600; }
        input, select {
            padding: 9px 12px;
            border: 1.5px solid #ccd6e0;
            border-radius: 7px;
            font-size: 0.93rem;
            transition: border 0.2s;
        }
        input:focus, select:focus { outline: none; border-color: #1a3c6e; }

        /* ---- Buttons ---- */
        .btn {
            padding: 9px 20px; border: none; border-radius: 7px;
            cursor: pointer; font-size: 0.9rem; font-weight: 600;
            transition: background 0.2s, transform 0.1s;
        }
        .btn:active { transform: scale(0.97); }
        .btn-primary { background: #1a3c6e; color: white; }
        .btn-primary:hover { background: #14305a; }
        .btn-success { background: #27ae60; color: white; }
        .btn-success:hover { background: #219150; }
        .btn-warning { background: #e67e22; color: white; }
        .btn-warning:hover { background: #ca6f1e; }
        .btn-danger  { background: #e74c3c; color: white; font-size: 0.8rem; padding: 5px 11px; }
        .btn-danger:hover { background: #c0392b; }
        .btn-sm { padding: 5px 12px; font-size: 0.82rem; }
        .btn-outline {
            background: transparent; border: 1.5px solid #1a3c6e;
            color: #1a3c6e; padding: 8px 18px;
        }
        .btn-outline:hover { background: #1a3c6e; color: white; }

        /* ---- Tables ---- */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
        thead tr { background: #1a3c6e; color: white; }
        th, td { padding: 10px 14px; text-align: left; }
        tbody tr:nth-child(even) { background: #f7f9fc; }
        tbody tr:hover { background: #eaf0fb; }

        /* ---- Badges ---- */
        .badge {
            padding: 3px 10px; border-radius: 12px;
            font-size: 0.78rem; font-weight: 700;
        }
        .badge-pass   { background: #d4edda; color: #155724; }
        .badge-fail   { background: #f8d7da; color: #721c24; }
        .badge-grade  { background: #cce5ff; color: #004085; }
        .badge-instructor { background: #fdebd0; color: #784212; }
        .badge-student    { background: #d5f5e3; color: #1e8449; }

        /* ---- Stats Row ---- */
        .stats-row { display: flex; gap: 14px; margin-bottom: 20px; flex-wrap: wrap; }
        .stat-card {
            flex: 1; min-width: 120px; background: #fff;
            border-radius: 10px; padding: 16px 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07); text-align: center;
        }
        .stat-card .num { font-size: 2rem; font-weight: 700; color: #1a3c6e; }
        .stat-card .lbl { font-size: 0.78rem; color: #999; margin-top: 3px; }

        /* ---- Filter bar ---- */
        .filter-bar { display: flex; gap: 12px; align-items: flex-end; margin-bottom: 16px; flex-wrap: wrap; }

        /* ---- Grade preview ---- */
        #grade-preview { margin: 10px 0; font-size: 0.9rem; color: #555; }

        /* ---- Toast ---- */
        #toast {
            position: fixed; bottom: 24px; right: 24px;
            padding: 12px 22px; border-radius: 8px;
            color: white; font-weight: 600; font-size: 0.9rem;
            opacity: 0; transition: opacity 0.3s; z-index: 9999;
            max-width: 320px;
        }
        #toast.show { opacity: 1; }
        #toast.success { background: #27ae60; }
        #toast.error   { background: #e74c3c; }
        #toast.info    { background: #2980b9; }

        /* ============================================
           LOGIN PAGE STYLES
           ============================================ */
        #login-page {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #1a3c6e 0%, #2980b9 100%);
        }
        .login-box {
            background: white;
            border-radius: 14px;
            padding: 40px 44px;
            width: 380px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.25);
            text-align: center;
        }
        .login-box .logo { font-size: 3rem; margin-bottom: 8px; }
        .login-box h2 { color: #1a3c6e; margin-bottom: 6px; font-size: 1.4rem; }
        .login-box p  { color: #888; font-size: 0.85rem; margin-bottom: 24px; }
        .login-box .form-group { text-align: left; margin-bottom: 14px; }
        .login-box input { width: 100%; }
        .login-box .btn-primary { width: 100%; padding: 11px; font-size: 1rem; margin-top: 6px; }
        .login-error { color: #e74c3c; font-size: 0.85rem; margin-top: 10px; min-height: 20px; }

        /* Demo credentials helper */
        .demo-creds {
            margin-top: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 12px 16px;
            text-align: left;
            font-size: 0.8rem;
            color: #555;
        }
        .demo-creds strong { color: #333; display: block; margin-bottom: 6px; }
        .demo-creds code {
            background: #e9ecef; padding: 1px 5px;
            border-radius: 3px; font-family: monospace;
        }

        /* ============================================
           APP SHELL (shown after login)
           ============================================ */
        #app { display: none; }

        /* Instructor-only elements hidden for students */
        .instructor-only { display: none; }

        /* ---- Responsive ---- */
        @media (max-width: 600px) {
            main { padding: 14px; }
            .login-box { width: 94%; padding: 28px 20px; }
            nav button { padding: 10px 10px; font-size: 0.82rem; }
        }
    </style>
</head>
<body>

<!-- ============================================
     LOGIN PAGE
     Shown to users who are not logged in
     ============================================ -->
<div id="login-page">
    <div class="login-box">
        <div class="logo">🎓</div>
        <h2>Grading System</h2>
        <p>Sign in to access your portal</p>

        <div class="form-group">
            <label>Username</label>
            <input type="text" id="login-username" placeholder="Enter your username" autocomplete="username">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" id="login-password" placeholder="Enter your password" autocomplete="current-password">
        </div>
        <button class="btn btn-primary" onclick="doLogin()">🔑 Sign In</button>
        <div class="login-error" id="login-error"></div>

        <!-- Demo credentials shown on the login page for convenience -->
        <div class="demo-creds">
            <strong>🧪 Demo Accounts (password: <code>password</code>)</strong>
            <div>👨‍🏫 Instructor: <code>instructor</code></div>
            <div>👩‍🎓 Student: <code>maria.santos</code></div>
            <div>👨‍🎓 Student: <code>juan.delacruz</code></div>
            <div>👩‍🎓 Student: <code>ana.reyes</code></div>
            <div>👨‍🎓 Student: <code>carlo.mendoza</code></div>
            <div>👩‍🎓 Student: <code>jasmine.v</code></div>
        </div>
    </div>
</div>


<!-- ============================================
     APP SHELL — shown after successful login
     ============================================ -->
<div id="app">

    <!-- Header -->
    <header>
        <div class="brand">
            <span>🎓</span>
            <h1>Student Grading System</h1>
        </div>
        <div class="user-info">
            <span id="header-username"></span>
            <span class="role-badge" id="header-role"></span>
            <button class="btn btn-outline" style="color:white;border-color:rgba(255,255,255,0.5);padding:6px 14px;font-size:0.82rem;" onclick="doLogout()">🚪 Logout</button>
        </div>
    </header>

    <!-- Navigation — tabs change based on role -->
    <nav id="main-nav"></nav>

    <!-- Content panels -->
    <main>

        <!-- =============================================
             INSTRUCTOR PANELS
             ============================================= -->

        <!-- Panel: View All Grades -->
        <div id="tab-all-grades" class="tab-panel instructor-only">
            <h2>📋 All Grade Records</h2>

            <!-- Stats -->
            <div class="stats-row">
                <div class="stat-card"><div class="num" id="stat-total">-</div><div class="lbl">Total Records</div></div>
                <div class="stat-card"><div class="num" id="stat-avg">-</div><div class="lbl">Class Average</div></div>
                <div class="stat-card"><div class="num" id="stat-pass">-</div><div class="lbl">Passed</div></div>
                <div class="stat-card"><div class="num" id="stat-fail">-</div><div class="lbl">Failed</div></div>
            </div>

            <!-- Filter -->
            <div class="filter-bar">
                <div class="form-group" style="max-width:280px;">
                    <label>Filter by Student</label>
                    <select id="filter-student" onchange="loadAllGrades()">
                        <option value="">-- All Students --</option>
                    </select>
                </div>
                <button class="btn btn-primary btn-sm" onclick="loadAllGrades()">🔄 Refresh</button>
            </div>

            <div class="card" style="padding:0;overflow:hidden;">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Student ID</th><th>Student Name</th>
                                <th>Course ID</th><th>Course Name</th>
                                <th>Grade</th><th>Letter</th><th>Remarks</th><th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="all-grades-tbody">
                            <tr><td colspan="8" style="text-align:center;padding:20px;color:#aaa">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Panel: Add Grade -->
        <div id="tab-add-grade" class="tab-panel instructor-only">
            <h2>➕ Add / Update Grade</h2>
            <div class="card">
                <div class="form-row">
                    <div class="form-group">
                        <label>Student *</label>
                        <select id="grade-student">
                            <option value="">-- Select Student --</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Course *</label>
                        <select id="grade-course">
                            <option value="">-- Select Course --</option>
                        </select>
                    </div>
                    <div class="form-group" style="max-width:160px;">
                        <label>Grade (0–100) *</label>
                        <input type="number" id="grade-value" min="0" max="100" step="0.01" placeholder="85.50">
                    </div>
                </div>
                <div id="grade-preview">Enter a grade to preview the letter grade.</div>
                <br>
                <button class="btn btn-success" onclick="saveGrade()">💾 Save Grade</button>
            </div>

            <!-- Grading scale reference -->
            <div class="card">
                <h3>📊 Grading Scale</h3>
                <div class="table-wrap">
                    <table>
                        <thead><tr><th>Range</th><th>Letter</th><th>Remarks</th></tr></thead>
                        <tbody>
                            <tr><td>95–100</td><td><span class="badge badge-grade">A+</span></td><td><span class="badge badge-pass">Passed</span></td></tr>
                            <tr><td>90–94</td><td><span class="badge badge-grade">A</span></td><td><span class="badge badge-pass">Passed</span></td></tr>
                            <tr><td>87–89</td><td><span class="badge badge-grade">A-</span></td><td><span class="badge badge-pass">Passed</span></td></tr>
                            <tr><td>83–86</td><td><span class="badge badge-grade">B+</span></td><td><span class="badge badge-pass">Passed</span></td></tr>
                            <tr><td>80–82</td><td><span class="badge badge-grade">B</span></td><td><span class="badge badge-pass">Passed</span></td></tr>
                            <tr><td>77–79</td><td><span class="badge badge-grade">B-</span></td><td><span class="badge badge-pass">Passed</span></td></tr>
                            <tr><td>70–76</td><td><span class="badge badge-grade">C / C+</span></td><td><span class="badge badge-pass">Passed</span></td></tr>
                            <tr><td>60–69</td><td><span class="badge badge-grade">C- / D</span></td><td><span class="badge badge-pass">Passed</span></td></tr>
                            <tr><td>0–59</td><td><span class="badge badge-grade">F</span></td><td><span class="badge badge-fail">Failed</span></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Panel: Manage Students -->
        <div id="tab-students" class="tab-panel instructor-only">
            <h2>👥 Manage Students</h2>

            <!-- Add Student Form -->
            <div class="card">
                <h3>➕ Add New Student</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label>Student ID * (e.g. STU006)</label>
                        <input type="text" id="new-sid" placeholder="STU006" style="text-transform:uppercase">
                    </div>
                    <div class="form-group">
                        <label>First Name *</label>
                        <input type="text" id="new-fname" placeholder="Maria">
                    </div>
                    <div class="form-group">
                        <label>Last Name *</label>
                        <input type="text" id="new-lname" placeholder="Santos">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" id="new-email" placeholder="student@school.edu">
                    </div>
                    <div class="form-group">
                        <label>Login Username *</label>
                        <input type="text" id="new-username" placeholder="maria.santos">
                    </div>
                    <div class="form-group">
                        <label>Login Password *</label>
                        <input type="text" id="new-password" placeholder="Set initial password">
                    </div>
                </div>
                <button class="btn btn-success" onclick="addStudent()">👤 Add Student</button>
            </div>

            <!-- Students table -->
            <div class="card" style="padding:0;overflow:hidden;">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Student ID</th><th>First Name</th>
                                <th>Last Name</th><th>Email</th><th>Date Added</th><th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="students-tbody">
                            <tr><td colspan="6" style="text-align:center;padding:20px;color:#aaa">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Panel: Manage Courses -->
        <div id="tab-courses" class="tab-panel instructor-only">
            <h2>📚 Manage Courses</h2>

            <!-- Add Course Form -->
            <div class="card">
                <h3>➕ Add New Course</h3>
                <div class="form-row">
                    <div class="form-group" style="max-width:160px;">
                        <label>Course ID * (e.g. CS102)</label>
                        <input type="text" id="new-cid" placeholder="CS102" style="text-transform:uppercase">
                    </div>
                    <div class="form-group">
                        <label>Course Name *</label>
                        <input type="text" id="new-cname" placeholder="Introduction to Programming">
                    </div>
                    <div class="form-group" style="max-width:100px;">
                        <label>Units *</label>
                        <input type="number" id="new-units" value="3" min="1" max="6">
                    </div>
                </div>
                <button class="btn btn-success" onclick="addCourse()">📚 Add Course</button>
            </div>

            <!-- Courses table -->
            <div class="card" style="padding:0;overflow:hidden;">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr><th>Course ID</th><th>Course Name</th><th>Units</th><th>Action</th></tr>
                        </thead>
                        <tbody id="courses-tbody">
                            <tr><td colspan="4" style="text-align:center;padding:20px;color:#aaa">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <!-- =============================================
             STUDENT PORTAL PANEL
             Only visible when role = student
             ============================================= -->
        <div id="tab-my-grades" class="tab-panel">
            <h2>📋 My Grades</h2>

            <!-- Student's personal summary -->
            <div class="stats-row">
                <div class="stat-card"><div class="num" id="my-stat-total">-</div><div class="lbl">Subjects Taken</div></div>
                <div class="stat-card"><div class="num" id="my-stat-avg">-</div><div class="lbl">My Average</div></div>
                <div class="stat-card"><div class="num" id="my-stat-pass">-</div><div class="lbl">Passed</div></div>
                <div class="stat-card"><div class="num" id="my-stat-fail">-</div><div class="lbl">Failed</div></div>
            </div>

            <div class="card" style="padding:0;overflow:hidden;">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Course ID</th><th>Course Name</th>
                                <th>Units</th><th>Grade</th><th>Letter</th><th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody id="my-grades-tbody">
                            <tr><td colspan="6" style="text-align:center;padding:20px;color:#aaa">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </main>
</div><!-- #app -->

<!-- Toast notification -->
<div id="toast"></div>


<!-- ============================================
     JAVASCRIPT
     ============================================ -->
<script>

// ---- Current logged-in user info (set after login) ----
let currentUser = null;

// ============================================
// UTILITY FUNCTIONS
// ============================================

// Show a toast notification
function showToast(msg, type = 'success') {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = 'show ' + type;
    setTimeout(() => t.className = '', 3200);
}

// Letter grade computation (mirrors PHP logic)
function getLetterGrade(g) {
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

// Tab switcher — shows the correct panel
function showTab(name) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('nav button').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + name).classList.add('active');

    // Mark the clicked nav button active
    document.querySelectorAll('nav button').forEach(b => {
        if (b.dataset.tab === name) b.classList.add('active');
    });

    // Load data for the tab
    if (name === 'all-grades') loadAllGrades();
    if (name === 'students')   loadStudents();
    if (name === 'courses')    loadCourses();
    if (name === 'my-grades')  loadMyGrades();
}

// ============================================
// AUTH FUNCTIONS
// ============================================

// Handle login form submission
async function doLogin() {
    const username = document.getElementById('login-username').value.trim();
    const password = document.getElementById('login-password').value.trim();
    const errEl    = document.getElementById('login-error');

    errEl.textContent = '';
    if (!username || !password) { errEl.textContent = 'Please enter username and password.'; return; }

    const fd = new FormData();
    fd.append('action',   'login');
    fd.append('username', username);
    fd.append('password', password);

    const res  = await fetch('auth.php', { method: 'POST', body: fd });
    const data = await res.json();

    if (data.success) {
        currentUser = data;
        showApp(data.role, username);
    } else {
        errEl.textContent = data.message;
    }
}

// Allow pressing Enter key on password field to login
document.getElementById('login-password').addEventListener('keydown', e => {
    if (e.key === 'Enter') doLogin();
});

// Handle logout
async function doLogout() {
    await fetch('auth.php?action=logout');
    currentUser = null;
    document.getElementById('app').style.display = 'none';
    document.getElementById('login-page').style.display = 'flex';
    document.getElementById('login-username').value = '';
    document.getElementById('login-password').value = '';
}

// ============================================
// SHOW APP based on role
// ============================================
function showApp(role, username) {
    // Hide login, show app
    document.getElementById('login-page').style.display = 'none';
    document.getElementById('app').style.display = 'block';

    // Update header
    document.getElementById('header-username').textContent = '👤 ' + username;
    const roleBadge = document.getElementById('header-role');
    roleBadge.textContent  = role;
    roleBadge.className    = 'role-badge ' + role;

    // Build navigation tabs based on role
    const nav = document.getElementById('main-nav');
    if (role === 'instructor') {
        nav.innerHTML = `
            <button data-tab="all-grades" onclick="showTab('all-grades')">📋 All Grades</button>
            <button data-tab="add-grade"  onclick="showTab('add-grade')">➕ Add Grade</button>
            <button data-tab="students"   onclick="showTab('students')">👥 Students</button>
            <button data-tab="courses"    onclick="showTab('courses')">📚 Courses</button>
        `;
        // Load dropdowns for the add-grade form
        loadStudentOptions();
        loadCourseOptions();
        // Show the first tab
        showTab('all-grades');

    } else {
        // Student sees only their own portal
        nav.innerHTML = `
            <button data-tab="my-grades" onclick="showTab('my-grades')">📋 My Grades</button>
        `;
        showTab('my-grades');
    }
}

// ============================================
// DATA LOADING FUNCTIONS
// ============================================

// ---- Load all grades (instructor) ----
async function loadAllGrades() {
    const studentId = document.getElementById('filter-student')?.value || '';
    const url = 'api.php?action=get_grades' + (studentId ? '&student_id=' + studentId : '');
    const res  = await fetch(url);
    const grades = await res.json();

    const tbody = document.getElementById('all-grades-tbody');
    if (!grades.length) {
        tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:20px;color:#aaa">No records found.</td></tr>';
        updateStats([]); return;
    }

    tbody.innerHTML = grades.map(g => `
        <tr>
            <td>${g.student_id}</td>
            <td>${g.first_name} ${g.last_name}</td>
            <td>${g.course_id}</td>
            <td>${g.course_name}</td>
            <td><strong>${parseFloat(g.grade).toFixed(2)}</strong></td>
            <td><span class="badge badge-grade">${g.letter}</span></td>
            <td><span class="badge ${g.remarks === 'Passed' ? 'badge-pass' : 'badge-fail'}">${g.remarks}</span></td>
            <td><button class="btn btn-danger" onclick="deleteGrade(${g.id})">🗑</button></td>
        </tr>
    `).join('');

    updateStats(grades);
}

// Update summary stat cards on the grades tab
function updateStats(grades) {
    if (!grades.length) {
        ['stat-total','stat-avg','stat-pass','stat-fail'].forEach(id => document.getElementById(id).textContent = '0');
        return;
    }
    const avg    = grades.reduce((s, g) => s + parseFloat(g.grade), 0) / grades.length;
    const passed = grades.filter(g => g.remarks === 'Passed').length;
    document.getElementById('stat-total').textContent = grades.length;
    document.getElementById('stat-avg').textContent   = avg.toFixed(1);
    document.getElementById('stat-pass').textContent  = passed;
    document.getElementById('stat-fail').textContent  = grades.length - passed;
}

// ---- Load student's own grades ----
async function loadMyGrades() {
    const res    = await fetch('api.php?action=get_grades');
    const grades = await res.json();
    const tbody  = document.getElementById('my-grades-tbody');

    if (!grades.length) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:20px;color:#aaa">No grade records yet.</td></tr>';
        ['my-stat-total','my-stat-avg','my-stat-pass','my-stat-fail'].forEach(id => document.getElementById(id).textContent = '0');
        return;
    }

    tbody.innerHTML = grades.map(g => `
        <tr>
            <td>${g.course_id}</td>
            <td>${g.course_name}</td>
            <td>${g.units}</td>
            <td><strong>${parseFloat(g.grade).toFixed(2)}</strong></td>
            <td><span class="badge badge-grade">${g.letter}</span></td>
            <td><span class="badge ${g.remarks === 'Passed' ? 'badge-pass' : 'badge-fail'}">${g.remarks}</span></td>
        </tr>
    `).join('');

    // Student stats
    const avg    = grades.reduce((s, g) => s + parseFloat(g.grade), 0) / grades.length;
    const passed = grades.filter(g => g.remarks === 'Passed').length;
    document.getElementById('my-stat-total').textContent = grades.length;
    document.getElementById('my-stat-avg').textContent   = avg.toFixed(1);
    document.getElementById('my-stat-pass').textContent  = passed;
    document.getElementById('my-stat-fail').textContent  = grades.length - passed;
}

// ---- Populate student dropdowns ----
async function loadStudentOptions() {
    const res      = await fetch('api.php?action=get_students');
    const students = await res.json();

    const filter = document.getElementById('filter-student');
    if (filter) {
        filter.innerHTML = '<option value="">-- All Students --</option>';
        students.forEach(s => filter.innerHTML += `<option value="${s.student_id}">${s.student_id} – ${s.first_name} ${s.last_name}</option>`);
    }

    const sel = document.getElementById('grade-student');
    if (sel) {
        sel.innerHTML = '<option value="">-- Select Student --</option>';
        students.forEach(s => sel.innerHTML += `<option value="${s.student_id}">${s.student_id} – ${s.first_name} ${s.last_name}</option>`);
    }
}

// ---- Populate course dropdown ----
async function loadCourseOptions() {
    const res     = await fetch('api.php?action=get_courses');
    const courses = await res.json();
    const sel     = document.getElementById('grade-course');
    if (sel) {
        sel.innerHTML = '<option value="">-- Select Course --</option>';
        courses.forEach(c => sel.innerHTML += `<option value="${c.course_id}">${c.course_id} – ${c.course_name}</option>`);
    }
}

// ---- Load students table ----
async function loadStudents() {
    const res      = await fetch('api.php?action=get_students');
    const students = await res.json();
    const tbody    = document.getElementById('students-tbody');

    if (!students.length) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:20px;color:#aaa">No students found.</td></tr>'; return;
    }

    tbody.innerHTML = students.map(s => `
        <tr>
            <td>${s.student_id}</td>
            <td>${s.first_name}</td>
            <td>${s.last_name}</td>
            <td>${s.email}</td>
            <td>${new Date(s.created_at).toLocaleDateString()}</td>
            <td>
                <button class="btn btn-danger" onclick="deleteStudent('${s.student_id}', '${s.first_name} ${s.last_name}')">🗑 Delete</button>
            </td>
        </tr>
    `).join('');
}

// ---- Load courses table ----
async function loadCourses() {
    const res     = await fetch('api.php?action=get_courses');
    const courses = await res.json();
    const tbody   = document.getElementById('courses-tbody');

    if (!courses.length) {
        tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:20px;color:#aaa">No courses found.</td></tr>'; return;
    }

    tbody.innerHTML = courses.map(c => `
        <tr>
            <td>${c.course_id}</td>
            <td>${c.course_name}</td>
            <td>${c.units}</td>
            <td><button class="btn btn-danger" onclick="deleteCourse('${c.course_id}')">🗑 Delete</button></td>
        </tr>
    `).join('');
}

// ============================================
// ACTION FUNCTIONS (POST to api.php)
// ============================================

// ---- Save a grade ----
async function saveGrade() {
    const student_id = document.getElementById('grade-student').value;
    const course_id  = document.getElementById('grade-course').value;
    const grade      = document.getElementById('grade-value').value;

    if (!student_id || !course_id || grade === '') { showToast('Please fill in all fields.', 'error'); return; }
    if (grade < 0 || grade > 100) { showToast('Grade must be 0–100.', 'error'); return; }

    const fd = new FormData();
    fd.append('action', 'save_grade');
    fd.append('student_id', student_id);
    fd.append('course_id',  course_id);
    fd.append('grade',      grade);

    const res  = await fetch('api.php', { method: 'POST', body: fd });
    const data = await res.json();

    if (data.success) {
        showToast(`✅ ${data.message} → ${data.letter} (${data.remarks})`);
        document.getElementById('grade-value').value   = '';
        document.getElementById('grade-preview').textContent = 'Enter a grade to preview the letter grade.';
    } else {
        showToast('❌ ' + data.message, 'error');
    }
}

// ---- Delete a grade ----
async function deleteGrade(id) {
    if (!confirm('Delete this grade record?')) return;
    const fd = new FormData();
    fd.append('action', 'delete_grade');
    fd.append('id', id);
    const res  = await fetch('api.php', { method: 'POST', body: fd });
    const data = await res.json();
    if (data.success) { showToast('🗑 Deleted.'); loadAllGrades(); }
    else showToast('❌ ' + data.message, 'error');
}

// ---- Add student ----
async function addStudent() {
    const fd = new FormData();
    fd.append('action',     'add_student');
    fd.append('student_id', document.getElementById('new-sid').value.trim().toUpperCase());
    fd.append('first_name', document.getElementById('new-fname').value.trim());
    fd.append('last_name',  document.getElementById('new-lname').value.trim());
    fd.append('email',      document.getElementById('new-email').value.trim());
    fd.append('username',   document.getElementById('new-username').value.trim());
    fd.append('password',   document.getElementById('new-password').value.trim());

    const res  = await fetch('api.php', { method: 'POST', body: fd });
    const data = await res.json();
    if (data.success) {
        showToast('✅ ' + data.message);
        // Clear fields
        ['new-sid','new-fname','new-lname','new-email','new-username','new-password'].forEach(id => document.getElementById(id).value = '');
        loadStudents();
        loadStudentOptions();
    } else {
        showToast('❌ ' + data.message, 'error');
    }
}

// ---- Delete student ----
async function deleteStudent(id, name) {
    if (!confirm(`Delete student "${name}" and ALL their grades and login account?`)) return;
    const fd = new FormData();
    fd.append('action', 'delete_student');
    fd.append('student_id', id);
    const res  = await fetch('api.php', { method: 'POST', body: fd });
    const data = await res.json();
    if (data.success) { showToast('🗑 Student deleted.'); loadStudents(); loadStudentOptions(); }
    else showToast('❌ ' + data.message, 'error');
}

// ---- Add course ----
async function addCourse() {
    const fd = new FormData();
    fd.append('action',      'add_course');
    fd.append('course_id',   document.getElementById('new-cid').value.trim().toUpperCase());
    fd.append('course_name', document.getElementById('new-cname').value.trim());
    fd.append('units',       document.getElementById('new-units').value);

    const res  = await fetch('api.php', { method: 'POST', body: fd });
    const data = await res.json();
    if (data.success) {
        showToast('✅ ' + data.message);
        document.getElementById('new-cid').value = '';
        document.getElementById('new-cname').value = '';
        loadCourses();
        loadCourseOptions();
    } else {
        showToast('❌ ' + data.message, 'error');
    }
}

// ---- Delete course ----
async function deleteCourse(id) {
    if (!confirm(`Delete course "${id}"? All related grades will also be removed.`)) return;
    const fd = new FormData();
    fd.append('action', 'delete_course');
    fd.append('course_id', id);
    const res  = await fetch('api.php', { method: 'POST', body: fd });
    const data = await res.json();
    if (data.success) { showToast('🗑 Course deleted.'); loadCourses(); loadCourseOptions(); }
    else showToast('❌ ' + data.message, 'error');
}

// ============================================
// LIVE GRADE PREVIEW (on Add Grade form)
// ============================================
document.getElementById('grade-value').addEventListener('input', function() {
    const g       = parseFloat(this.value);
    const preview = document.getElementById('grade-preview');
    if (isNaN(g) || g < 0 || g > 100) {
        preview.textContent = 'Enter a valid grade (0–100).'; return;
    }
    const letter    = getLetterGrade(g);
    const remarks   = g >= 60 ? 'Passed' : 'Failed';
    const badgeCls  = remarks === 'Passed' ? 'badge-pass' : 'badge-fail';
    preview.innerHTML = `Preview: <strong>${g.toFixed(2)}</strong> → 
        <span class="badge badge-grade">${letter}</span> 
        <span class="badge ${badgeCls}">${remarks}</span>`;
});

// ============================================
// ON PAGE LOAD: check if session already exists
// (handles browser refresh without re-login)
// ============================================
(async () => {
    const res  = await fetch('auth.php?action=check');
    const data = await res.json();
    if (data.logged_in) {
        currentUser = data;
        showApp(data.role, data.username);
    }
})();

</script>
</body>
</html>