<?php
require_once('../includes/db.php');
$deptQuery = $conn->query("SELECT * FROM departments");
$departments = $deptQuery->fetch_all(MYSQLI_ASSOC);
$courseQuery = $conn->query("SELECT d.name AS department, c.name AS course FROM courses c JOIN departments d ON c.department_id = d.id ORDER BY d.name, c.name");
$coursesByDept = [];
while ($row = $courseQuery->fetch_assoc()) {
    $coursesByDept[$row['department']][] = $row['course'];
}
?>

<link rel="stylesheet" href="../assets/css/admin_quick_access.css">
<style>
    .toast {
        position: fixed;
        top: 20px;
        right: 20px;
        background: #27ae60;
        color: white;
        padding: 14px 24px;
        border-radius: 6px;
        font-weight: 600;
        font-family: 'Segoe UI', sans-serif;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        z-index: 9999;
        opacity: 0;
        transform: translateY(-20px);
        animation: slideIn 0.4s ease forwards, fadeOut 0.4s ease 3.6s forwards;
    }

    .toast.error {
        background: #c0392b;
    }

    @keyframes slideIn {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeOut {
        to {
            opacity: 0;
            transform: translateY(-20px);
        }
    }
</style>


<div class="quick-access-wrapper">
    <div class="quick-access-panel">
        <h2>Quick Access</h2>
        <div class="access-buttons">
            <button onclick="showForm('student')">Add Student</button>
            <button onclick="showForm('teacher')">Add Teacher</button>
        </div>
    </div>

    <div class="form-panel">
        <!-- STUDENT FORM -->
        <div id="form-student" class="form-content">
            <h3>Add Student</h3>
            <form class="ajax-form" data-type="student" action="save_student.php" method="POST">
                <input type="text" name="id_number" placeholder="Student Number" required>
                <input type="text" name="first_name" placeholder="First Name" required>
                <input type="text" name="last_name" placeholder="Last Name" required>
                <input type="text" name="middle_name" placeholder="Middle Name">
                <input type="text" name="suffix_name" placeholder="Suffix">
                <input type="email" name="email" placeholder="Email Address" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>

                <select name="department_id" id="departmentDropdown" required>
                    <option value="">Select Department</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?= $dept['id'] ?>" data-name="<?= htmlspecialchars($dept['name']) ?>">
                            <?= htmlspecialchars($dept['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="course" id="courseSelect" required>
                    <option value="">Select Course</option>
                </select>

                <input type="text" name="section" placeholder="Section" required>
                <input type="text" name="sex" placeholder="Sex" required>
                <input type="text" name="year" placeholder="Year" required>

                <button type="submit">Save</button>
            </form>
        </div>

        <!-- TEACHER FORM -->
        <div id="form-teacher" class="form-content" style="display: none;">
            <h3>Add Teacher</h3>
            <<form class="ajax-form" data-type="teacher" action="save_teacher.php" method="POST">
                <input type="text" name="teacher_id" placeholder="Teacher Number" required>
                <input type="text" name="first_name" placeholder="First Name" required>
                <input type="text" name="last_name" placeholder="Last Name" required>
                <input type="text" name="middle_name" placeholder="Middle Name">
                <input type="text" name="suffix" placeholder="Suffix">
                <input type="email" name="email" placeholder="Email Address" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="password" name="confirm" placeholder="Confirm Password" required>

                <!-- Fix this name -->
                <select name="department_id" required>
                    <option value="">Select Department</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?= $dept['id'] ?>"><?= htmlspecialchars($dept['name']) ?></option>
                    <?php endforeach; ?>
                </select>

                <input type="text" name="sex" placeholder="Sex" required>
                <button type="submit">Save</button>
                </form>

                </form>
        </div>
    </div>
</div>

<script>
    const coursesByDept = <?= json_encode($coursesByDept) ?>;

    function updateCourseOptions() {
        const departmentDropdown = document.getElementById('departmentDropdown');
        const selectedOption = departmentDropdown.options[departmentDropdown.selectedIndex];
        const deptName = selectedOption.getAttribute('data-name');
        const courseSelect = document.getElementById('courseSelect');
        courseSelect.innerHTML = '<option value="">Select Course</option>';

        if (coursesByDept[deptName]) {
            coursesByDept[deptName].forEach(course => {
                const opt = document.createElement('option');
                opt.value = course;
                opt.textContent = course;
                courseSelect.appendChild(opt);
            });
        }
    }

    document.getElementById('departmentDropdown').addEventListener('change', updateCourseOptions);

    function showForm(type) {
        document.querySelectorAll('.form-content').forEach(f => f.style.display = 'none');
        const target = document.getElementById('form-' + type);
        if (target) target.style.display = 'block';
    }

    showForm('student');

    document.querySelectorAll('.ajax-form').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(form);
            const action = form.getAttribute('action');

            try {
                const res = await fetch(action, {
                    method: 'POST',
                    body: formData
                });
                const result = await res.json();
                showNotification(result.message, result.status === 'success' ? 'success' : 'error');
                if (result.status === 'success') form.reset();
            } catch (err) {
                showNotification("An unexpected error occurred.", "error");
            }
        });
    });

</script>