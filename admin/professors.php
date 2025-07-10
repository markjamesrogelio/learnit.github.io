<?php
require_once('../includes/db.php');
if (session_status() === PHP_SESSION_NONE) session_start();

// Handle Delete
if (isset($_POST['delete_id'])) {
    $stmt = $conn->prepare("DELETE FROM users WHERE id_number = ?");
    $stmt->bind_param("s", $_POST['delete_id']);
    $stmt->execute();
    $_SESSION['success'] = "✅ Teacher deleted successfully!";
    header("Location: dashboard.php?section=professors");
    exit();
}

// Handle Update
if (isset($_POST['update_teacher'])) {
    $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, department_id = ? WHERE id_number = ?");
    $stmt->bind_param("ssis", $_POST['first_name'], $_POST['last_name'], $_POST['department_id'], $_POST['id_number']);
    $stmt->execute();
    $_SESSION['success'] = "✅ Teacher updated successfully!";
    header("Location: dashboard.php?section=professors");
    exit();
}

// Fetch departments
$deptRes = $conn->query("SELECT * FROM departments");
$departments = $deptRes->fetch_all(MYSQLI_ASSOC);

// Fetch teachers
$teachers = $conn->query("
    SELECT u.id_number, u.first_name, u.last_name, d.name AS department, u.department_id 
    FROM users u 
    LEFT JOIN departments d ON u.department_id = d.id 
    WHERE u.role = 'teacher' AND u.status = 'approved'
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

    .modal {
        display: none;
        position: fixed;
        z-index: 999;
        left: 0; top: 0;
        width: 100%; height: 100%;
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
        top: 10px; right: 15px;
        font-size: 22px;
        cursor: pointer;
    }

    .user-table th, .user-table td {
        padding: 12px 15px;
    }

    .edit-btn {
        background-color: #2c3e50;
        color: #fff;
        padding: 6px 12px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .delete-btn {
        background-color: #e74c3c;
        color: #fff;
        padding: 6px 12px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .edit-btn:hover { background-color: #34495e; }
    .delete-btn:hover { background-color: #c0392b; }
</style>

<?php if (isset($_SESSION['success'])): ?>
    <div style="color: green; text-align: center; font-weight: bold;"><?= $_SESSION['success'] ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<div class="styled-container">
    <h2>Professor List</h2>
    <div class="user-table">
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Department</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $teachers->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id_number']) ?></td>
                        <td><?= htmlspecialchars($row['first_name']) ?></td>
                        <td><?= htmlspecialchars($row['last_name']) ?></td>
                        <td><?= htmlspecialchars($row['department'] ?? 'N/A') ?></td>
                        <td>
                            <button class="edit-btn" onclick="openEditModal(
                                '<?= $row['id_number'] ?>',
                                '<?= addslashes($row['first_name']) ?>',
                                '<?= addslashes($row['last_name']) ?>',
                                '<?= $row['department_id'] ?>'
                            )">Edit</button>
                        </td>
                        <td>
                            <form method="POST" onsubmit="return confirm('Are you sure?')">
                                <input type="hidden" name="delete_id" value="<?= $row['id_number'] ?>">
                                <button class="delete-btn" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('editModal')">&times;</span>
        <form method="POST">
            <h3>Edit Teacher</h3>
            <input type="hidden" name="update_teacher" value="1">
            <input type="hidden" name="id_number" id="edit_id_number">
            <input type="text" name="first_name" id="edit_first_name" placeholder="First Name" required>
            <input type="text" name="last_name" id="edit_last_name" placeholder="Last Name" required>
            <select name="department_id" id="edit_department" required>
                <option value="">Select Department</option>
                <?php foreach ($departments as $dept): ?>
                    <option value="<?= $dept['id'] ?>"><?= htmlspecialchars($dept['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Update</button>
        </form>
    </div>
</div>

<script>
    function openEditModal(id, first, last, deptId) {
        document.getElementById('edit_id_number').value = id;
        document.getElementById('edit_first_name').value = first;
        document.getElementById('edit_last_name').value = last;
        document.getElementById('edit_department').value = deptId;
        document.getElementById('editModal').style.display = 'block';
    }

    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }
</script>
