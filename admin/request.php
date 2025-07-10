<?php
$pending = $conn->query("SELECT id, id_number, first_name, last_name, email, role FROM users WHERE status = 'pending'");
?>

<link rel="stylesheet" href="../assets/css/register.css">
<link rel="stylesheet" href="../assets/css/admin_tables.css">

<style>
    .styled-container {
        background: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        margin: 30px auto;
        max-width: 1000px;
        font-family: 'Segoe UI', sans-serif;
    }

    .styled-container h2 {
        margin-bottom: 20px;
        color: #2c3e50;
        font-size: 24px;
        font-weight: 600;
    }

    .user-table table {
        width: 100%;
        border-collapse: collapse;
        font-size: 15px;
    }

    .user-table th,
    .user-table td {
        border: 1px solid #ddd;
        padding: 12px 15px;
        text-align: left;
    }

    .user-table th {
        background-color: #f0f0f0;
        color: #333;
        font-weight: 600;
    }

    .user-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .user-table tr:hover {
        background-color: #f1f1f1;
    }

    .accept-btn,
    .reject-btn {
        padding: 8px 14px;
        margin: 0 2px;
        border: none;
        border-radius: 5px;
        color: #fff;
        cursor: pointer;
        font-size: 14px;
        transition: background-color 0.2s ease;
    }

    .accept-btn {
        background-color: #27ae60;
    }

    .accept-btn:hover {
        background-color: #219150;
    }

    .reject-btn {
        background-color: #e74c3c;
    }

    .reject-btn:hover {
        background-color: #c0392b;
    }
</style>

<div class="styled-container">
    <h2>Pending User Requests</h2>
    <div class="user-table">
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $pending->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id_number']) ?></td>
                        <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= ucfirst($row['role']) ?></td>
                        <td>
                            <button class="accept-btn" data-id="<?= $row['id'] ?>">Accept</button>
                            <button class="reject-btn" data-id="<?= $row['id'] ?>">Reject</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
