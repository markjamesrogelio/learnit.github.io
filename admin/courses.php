<?php
require_once('../includes/db.php');

// Fetch departments
$departments = $conn->query("SELECT * FROM departments");

// Add Course
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_course'])) {
    $course = $_POST['course'];
    $dept_id = $_POST['department_id'];

    $stmt = $conn->prepare("INSERT INTO courses (name, department_id) VALUES (?, ?)");
    $stmt->bind_param("si", $course, $dept_id);
    $stmt->execute();
}

// Fetch Courses (with department names)
$courses = $conn->query("
    SELECT c.id, c.name, c.date_created, d.name AS department 
    FROM courses c 
    JOIN departments d ON c.department_id = d.id
    ORDER BY c.date_created DESC
");
?>

<link rel="stylesheet" href="../assets/css/register.css">
<style>
    .styled-container {
        background: #fff;
        padding: 30px;
        border-radius: 10px;
        margin: 30px auto;
        max-width: 1100px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.08);
    }

    .form-group {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }

    .form-group select, .form-group input {
        flex: 1;
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #ccc;
    }

    .form-group button {
        padding: 10px 20px;
        background: #f1c40f;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .user-table table {
        width: 100%;
        border-collapse: collapse;
    }

    .user-table th,
    .user-table td {
        padding: 12px 15px;
        border: 1px solid #ddd;
        text-align: left;
    }

    .user-table th {
        background-color: #f4f4f4;
    }

    .edit-btn,
    .archive-btn {
        padding: 6px 12px;
        border: none;
        border-radius: 4px;
        color: #fff;
        cursor: pointer;
    }

    .edit-btn {
        background-color: #2c3e50;
    }

    .archive-btn {
        background-color: #e67e22;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.6);
    }

    .modal-content {
        background: #fff;
        margin: 10% auto;
        padding: 30px;
        border-radius: 10px;
        width: 400px;
        position: relative;
    }

    .modal-content input, .modal-content select {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
    }

    .modal-content button {
        background: #3344cc;
        color: #fff;
        border: none;
        padding: 10px;
        cursor: pointer;
    }

    .modal-content .close {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 22px;
        cursor: pointer;
    }
</style>

<div class="styled-container">
    <h2>Manage Courses</h2>

    <!-- Add Course Form -->
    <form method="POST">
        <div class="form-group">
            <select name="department_id" required>
                <option value="">Select Department</option>
                <?php while ($dept = $departments->fetch_assoc()): ?>
                    <option value="<?= $dept['id'] ?>"><?= htmlspecialchars($dept['name']) ?></option>
                <?php endwhile; ?>
            </select>
            <input type="text" name="course" placeholder="Course Name (e.g. BSIT)" required>
            <button type="submit" name="add_course">Add Course</button>
        </div>
    </form>

    <!-- Edit Modal -->
    <div id="editCourseModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editCourseModal')">&times;</span>
            <h3>Edit Course</h3>
            <form method="POST" action="update_course.php">
                <input type="hidden" name="id" id="edit_id">
                <input type="text" name="name" id="edit_name" placeholder="Course Name" required>
                <button type="submit">Update Course</button>
            </form>
        </div>
    </div>

    <!-- Course Table -->
    <div class="user-table">
        <table>
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Department</th>
                    <th>Date Created</th>
                    <th>Edit</th>
                    <th>Archive</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $courses->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['department']) ?></td>
                        <td><?= htmlspecialchars($row['date_created']) ?></td>
                        <td>
                            <button class="edit-btn" onclick="openEditModal('<?= $row['id'] ?>', '<?= addslashes($row['name']) ?>')">Edit</button>
                        </td>
                        <td>
                            <form method="POST" action="archive.php" style="display:inline;">
                                <input type="hidden" name="type" value="course">
                                <input type="hidden" name="reference_id" value="<?= $row['id'] ?>">
                                <input type="hidden" name="name" value="<?= htmlspecialchars($row['name']) ?>">
                                <button type="submit" class="archive-btn">Archive</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function openEditModal(id, name) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_name').value = name;
        document.getElementById('editCourseModal').style.display = 'block';
    }

    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }
</script>
