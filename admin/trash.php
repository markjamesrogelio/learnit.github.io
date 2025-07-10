<?php
require_once('../includes/db.php');

$trashed_items = $conn->query("SELECT * FROM explorer_items WHERE is_deleted = 1 ORDER BY updated_at DESC");
?>

<link rel="stylesheet" href="../assets/css/explorer.css">

<div class="explorer-wrapper">
    <div class="explorer-header">
        <h2>🗑️ Trash</h2>
        <div class="explorer-controls">
            <form method="GET" style="display: inline;">
                <input type="text" name="search" placeholder="Search Trash..." required>
                <button type="submit" class="btn">🔍</button>
                <a href="dashboard.php?section=trash" class="btn">Clear</a>
            </form>
        </div>
    </div>

    <div class="breadcrumbs">
        <a href="dashboard.php?section=explorer">← Back to Explorer</a>
    </div>

    <table class="explorer-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Type</th>
                <th>Deleted On</th>
                <th>Restore</th>
                <th>Permanent Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($item = $trashed_items->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= ucfirst($item['type']) ?></td>
                    <td><?= date('Y-m-d H:i', strtotime($item['updated_at'])) ?></td>
                    <td><a href="trash_restore.php?id=<?= $item['id'] ?>" class="btn btn-edit">Restore</a></td>
                    <td><a href="trash_delete.php?id=<?= $item['id'] ?>" class="btn btn-delete" onclick="return confirm('This will permanently delete the item. Continue?')">Delete</a></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
