<?php
require_once('../includes/db.php');

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $id = $_POST['id'];
    $first = trim($_POST['first_name']);
    $last = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $role = trim($_POST['role']);

    $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, role = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $first, $last, $email, $role, $id);
    $stmt->execute();
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $id = $_POST['user_id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

// Fetch approved users
$users = $conn->query("SELECT id, id_number, first_name, last_name, email, role FROM users WHERE status = 'approved'");
?>

<link rel="stylesheet" href="../assets/css/register.css">
<link rel="stylesheet" href="../assets/css/admin_tables.css">

<style>
    .styled-container {
        background: #ffffff;
        padding: 40px;
        border-radius: 16px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        margin: 40px auto;
        max-width: 1100px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .styled-container h2 {
        margin-bottom: 25px;
        color: #2c3e50;
        font-size: 1.8rem;
        text-align: center;
    }

    .user-table table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 10px;
    }

    .user-table thead tr {
        background-color: #f5f6fa;
    }

    .user-table th, .user-table td {
        padding: 14px 20px;
        text-align: left;
        font-size: 14px;
    }

    .user-table td {
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
    }

    .edit-btn {
        padding: 8px 16px;
        background: linear-gradient(to right, #4facfe, #00f2fe);
        color: white;
        border: none;
        border-radius: 30px;
        font-size: 14px;
        cursor: pointer;
        margin-right: 5px;
    }

    .edit-btn:hover {
        background: linear-gradient(to right, #00f2fe, #4facfe);
    }

    .delete-btn {
        padding: 8px 16px;
        background: #e74c3c;
        color: white;
        border: none;
        border-radius: 30px;
        font-size: 14px;
        cursor: pointer;
    }

    .delete-btn:hover {
        background: #c0392b;
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
        margin: 8% auto;
        padding: 30px;
        border-radius: 12px;
        width: 400px;
        position: relative;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }

    .modal-content input,
    .modal-content select {
        width: 100%;
        padding: 10px 14px;
        margin: 10px 0;
        border-radius: 8px;
        border: 1px solid #ccc;
    }

    .modal-content button {
        background: #27ae60;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        margin-top: 10px;
        cursor: pointer;
    }

    .modal-content .close {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 24px;
        cursor: pointer;
        color: #555;
    }
</style>

<div class="styled-container">
    <h2>Approved Accounts</h2>
    <div class="user-table">
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id_number']) ?></td>
                        <td><?= htmlspecialchars($row['first_name']) ?></td>
                        <td><?= htmlspecialchars($row['last_name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= ucfirst($row['role']) ?></td>
                        <td>
                            <button class="edit-btn" onclick="openEditModal(
                                '<?= $row['id'] ?>',
                                '<?= htmlspecialchars($row['first_name'], ENT_QUOTES) ?>',
                                '<?= htmlspecialchars($row['last_name'], ENT_QUOTES) ?>',
                                '<?= htmlspecialchars($row['email'], ENT_QUOTES) ?>',
                                '<?= $row['role'] ?>'
                            )">Edit</button>

                            <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this user?');">
                                <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                                <button type="submit" name="delete_user" class="delete-btn">Delete</button>
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
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h3>Edit User</h3>
        <form method="POST">
            <input type="hidden" name="id" id="editId">
            <input type="text" name="first_name" id="editFirst" required>
            <input type="text" name="last_name" id="editLast" required>
            <input type="email" name="email" id="editEmail" required>
            <select name="role" id="editRole" required>
                <option value="student">Student</option>
                <option value="teacher">Teacher</option>
                <option value="admin">Admin</option>
            </select>
            <button type="submit" name="update_user">Save Changes</button>
        </form>
    </div>
</div>

<script>
    function openEditModal(id, first, last, email, role) {
        document.getElementById('editId').value = id;
        document.getElementById('editFirst').value = first;
        document.getElementById('editLast').value = last;
        document.getElementById('editEmail').value = email;
        document.getElementById('editRole').value = role;
        document.getElementById('editModal').style.display = 'block';
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    window.onclick = function(event) {
        const modal = document.getElementById('editModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
</script>
