<?php
require_once('../includes/db.php');
if (session_status() === PHP_SESSION_NONE) session_start();

// Fetch departments and courses
$deptQuery = $conn->query("SELECT * FROM departments");
$departments = $deptQuery->fetch_all(MYSQLI_ASSOC);

$courseQuery = $conn->query("SELECT d.name AS department, c.name AS course FROM courses c JOIN departments d ON c.department_id = d.id ORDER BY d.name, c.name");
$coursesByDept = [];
while ($row = $courseQuery->fetch_assoc()) {
    $coursesByDept[$row['department']][] = $row['course'];
}
?>

<link rel="stylesheet" href="../assets/css/register.css">
<style>
    .role-selection {
        text-align: center;
        margin-bottom: 30px;
    }
    .role-box {
        display: inline-block;
        margin: 0 20px;
        cursor: pointer;
        transition: transform 0.2s;
    }
    .role-box:hover {
        transform: scale(1.05);
    }
    .student-only {
        display: none;
    }
    .notification {
        background-color: #2ecc71;
        color: white;
        padding: 10px;
        margin-bottom: 15px;
        text-align: center;
        border-radius: 6px;
    }
    .notification.error {
        background-color: #e74c3c;
    }
</style>

<div class="register-box">
    <h2>Create an account for …</h2>

    <div class="role-selection">
        <div class="role-box" onclick="selectRole('teacher')">
            <img src="../assets/images/teacher.png" alt="Teacher" width="100">
            <div>Teacher</div>
        </div>
        <div class="role-box" onclick="selectRole('student')">
            <img src="../assets/images/student.png" alt="Student" width="100">
            <div>Student</div>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="notification"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php elseif (isset($_SESSION['error'])): ?>
        <div class="notification error"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form method="POST" id="mainForm">
        <input type="hidden" name="role" id="roleInput" value="">

        <div style="display: flex; gap: 10px;">
            <input type="text" name="id_number" placeholder="ID Number" required>
            <input type="text" name="first_name" placeholder="First Name" required>
        </div>

        <div style="display: flex; gap: 10px;">
            <input type="text" name="middle_name" placeholder="Middle Name">
            <input type="text" name="last_name" placeholder="Last Name" required>
        </div>

        <div style="display: flex; gap: 10px;">
            <input type="text" name="suffix_name" placeholder="Suffix Name">
            <input type="email" name="email" placeholder="Email Address" required>
        </div>

        <div style="display: flex; gap: 10px;">
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        </div>

        <select name="department_id" required>
            <option value="">Select Department</option>
            <?php foreach ($departments as $dept): ?>
                <option value="<?= $dept['id'] ?>" data-name="<?= htmlspecialchars($dept['name']) ?>">
                    <?= htmlspecialchars($dept['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <div class="student-only">
            <select name="course" id="courseSelect">
                <option value="">Select Course</option>
            </select>

            <select name="year" id="yearSelect" onchange="populateSections(this.value)">
                <option value="">Select Year</option>
                <option value="1st">1st</option>
                <option value="2nd">2nd</option>
                <option value="3rd">3rd</option>
                <option value="4th">4th</option>
            </select>

            <select name="section" id="sectionSelect">
                <option value="">Select Section</option>
            </select>
        </div>

        <select name="sex" required>
            <option value="">Select Sex</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Others">Others</option>
        </select>

        <button type="submit">Create Account</button>
    </form>
</div>

<script>
    const form = document.getElementById('mainForm');
    const roleInput = document.getElementById('roleInput');
    const studentFields = document.querySelector('.student-only');
    const courseSelect = document.getElementById('courseSelect');
    const sectionSelect = document.getElementById('sectionSelect');
    const coursesByDept = <?= json_encode($coursesByDept) ?>;
    const yearSections = {
        '1st': ['1A', '1B', '1C'],
        '2nd': ['2A', '2B', '2C'],
        '3rd': ['3A', '3B', '3C'],
        '4th': ['4A', '4B', '4C']
    };

    function selectRole(role) {
        roleInput.value = role;
        if (role === 'student') {
            studentFields.style.display = 'block';
            form.action = 'add_user_student.php';
        } else {
            studentFields.style.display = 'none';
            form.action = 'add_user_teacher.php';
        }
    }

    function populateSections(year) {
        sectionSelect.innerHTML = '<option value="">Select Section</option>';
        if (yearSections[year]) {
            yearSections[year].forEach(section => {
                const opt = document.createElement('option');
                opt.value = section;
                opt.textContent = section;
                sectionSelect.appendChild(opt);
            });
        }
    }

    document.querySelector('[name="department_id"]').addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const deptName = selectedOption.dataset.name;

        courseSelect.innerHTML = '<option value="">Select Course</option>';
        if (coursesByDept[deptName]) {
            coursesByDept[deptName].forEach(course => {
                const opt = document.createElement('option');
                opt.value = course;
                opt.textContent = course;
                courseSelect.appendChild(opt);
            });
        }
    });
</script>
