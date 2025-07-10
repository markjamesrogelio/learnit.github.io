<?php
require_once('../includes/db.php');

// Fetch archive entries grouped by type
$archives = $conn->query("SELECT * FROM archive");
?>

<link rel="stylesheet" href="../assets/css/register.css">
<style>
    .styled-container {
        background: #fff;
        padding: 30px;
        border-radius: 10px;
        max-width: 1100px;
        margin: 30px auto;
        box-shadow: 0 0 10px rgba(0,0,0,0.08);
    }

    .archive-filter {
        margin-bottom: 20px;
    }

    .archive-filter select {
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #ccc;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        padding: 12px;
        border: 1px solid #ddd;
    }

    th {
        background-color: #f4f4f4;
    }
</style>

<div class="styled-container">
    <h2>Archived Items</h2>

    <div class="archive-filter">
        <label for="filter">Filter by Type:</label>
        <select id="filter" onchange="filterArchives()">
            <option value="all">All</option>
            <option value="department">Department</option>
            <option value="course">Course</option>
            <option value="section">Section</option>
        </select>
    </div>

    <table id="archiveTable">
        <thead>
            <tr>
                <th>Name</th>
                <th>Type</th>
                <th>Date Archived</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $archives->fetch_assoc()): ?>
                <tr data-type="<?= htmlspecialchars($row['type']) ?>">
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= ucfirst($row['type']) ?></td>
                    <td><?= htmlspecialchars($row['date_archived']) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
    function filterArchives() {
        const filter = document.getElementById('filter').value;
        const rows = document.querySelectorAll('#archiveTable tbody tr');
        rows.forEach(row => {
            const type = row.getAttribute('data-type');
            row.style.display = (filter === 'all' || filter === type) ? '' : 'none';
        });
    }
</script>
