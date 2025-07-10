<?php
require_once('../includes/db.php');

// Add new department
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_department'])) {
    $dept_name = trim($_POST['department_name']);
    if (!empty($dept_name)) {
        $stmt = $conn->prepare("INSERT INTO departments (name) VALUES (?)");
        $stmt->bind_param("s", $dept_name);
        $stmt->execute();
    }
}

// Update department
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_department'])) {
    $id = $_POST['edit_id'];
    $name = $_POST['edit_name'];
    $stmt = $conn->prepare("UPDATE departments SET name = ? WHERE id = ?");
    $stmt->bind_param("si", $name, $id);
    $stmt->execute();
}

// Archive (delete) department
if (isset($_GET['archive'])) {
    $id = $_GET['archive'];
    $conn->query("DELETE FROM departments WHERE id = $id");
}

// Fetch all departments
$departments = $conn->query("SELECT * FROM departments ORDER BY name ASC");
?>

<link rel="stylesheet" href="../assets/css/register.css">
<style>
    .styled-container {
        background: #fff;
        padding: 30px;
        border-radius: 10px;
        margin: 30px auto;
        max-width: 800px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.08);
    }

    .form-group {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }

    .form-group input {
        flex: 1;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
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
    }

    .user-table th {
        background-color: #f4f4f4;
        font-weight: bold;
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
        background-color: #3498db;
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
        padding: 20px;
        border-radius: 8px;
        width: 400px;
        position: relative;
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
    <h2>Manage Departments</h2>

    <form method="POST">
        <div class="form-group">
            <input type="text" name="department_name" placeholder="Department Name" required>
            <button type="submit" name="add_department">Add Department</button>
        </div>
    </form>

    <div class="user-table">
        <table>
            <thead>
                <tr>
                    <th>Department</th>
                    <th>Edit</th>
                    <th>Archive</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $departments->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td>
                            <button class="edit-btn" onclick="openEditModal('<?= $row['id'] ?>', '<?= addslashes($row['name']) ?>')">Edit</button>
                        </td>
                        <td>
                            <form method="POST" action="archive.php" style="display:inline;">
                                <input type="hidden" name="type" value="department">
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

<!-- Edit Modal -->
<div class="modal" id="editModal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3>Edit Department</h3>
        <form method="POST">
            <input type="hidden" name="edit_id" id="edit_id">
            <input type="text" name="edit_name" id="edit_name" placeholder="Department Name" required>
            <button type="submit" name="update_department">Update</button>
        </form>
    </div>
</div>

<script>
    function openEditModal(id, name) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_name').value = name;
        document.getElementById('editModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('editModal').style.display = 'none';
    }
</script>