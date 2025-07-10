<?php
require_once('../includes/db.php');

// Handle add section POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course = $_POST['course'];
    $section_name = $_POST['section_name'];
    $year_level = $_POST['year_level'];

    $stmt = $conn->prepare("INSERT INTO sections (course, section_name, year_level) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $course, $section_name, $year_level);
    $stmt->execute();
}

// Fetch existing sections
$sections = $conn->query("SELECT * FROM sections ORDER BY date_created DESC");

// Fetch available courses
$courseQuery = $conn->query("SELECT name FROM courses ORDER BY name ASC");
$courseOptions = [];
while ($row = $courseQuery->fetch_assoc()) {
    $courseOptions[] = $row['name'];
}
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

    .styled-container h2 {
        margin-bottom: 20px;
        color: #333;
    }

    .form-group {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }

    .form-group input,
    .form-group select {
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

    .modal-content input,
    .modal-content select {
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
    <h2>Manage Sections</h2>

    <form method="POST">
        <div class="form-group">
            <select name="course" required>
                <option value="">Select Course</option>
                <?php foreach ($courseOptions as $course): ?>
                    <option value="<?= htmlspecialchars($course) ?>"><?= htmlspecialchars($course) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="section_name" placeholder="Section (e.g. BSIT-3A)" required>
            <select name="year_level" required>
                <option value="">Year Level</option>
                <option value="1st">1st</option>
                <option value="2nd">2nd</option>
                <option value="3rd">3rd</option>
                <option value="4th">4th</option>
            </select>
            <button type="submit">Add Section</button>
        </div>
    </form>

    <!-- Edit Section Modal -->
    <div id="editSectionModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editSectionModal')">&times;</span>
            <h3>Edit Section</h3>
            <form method="POST" action="update_section.php">
                <input type="hidden" name="id" id="edit_id">

                <select name="course" id="edit_course" required>
                    <option value="">Select Course</option>
                    <?php foreach ($courseOptions as $course): ?>
                        <option value="<?= htmlspecialchars($course) ?>"><?= htmlspecialchars($course) ?></option>
                    <?php endforeach; ?>
                </select>

                <input type="text" name="section_name" id="edit_section" placeholder="Section Name" required>

                <select name="year_level" id="edit_year" required>
                    <option value="">Select Year</option>
                    <option value="1st">1st</option>
                    <option value="2nd">2nd</option>
                    <option value="3rd">3rd</option>
                    <option value="4th">4th</option>
                </select>

                <button type="submit">Update Section</button>
            </form>
        </div>
    </div>

    <div class="user-table">
        <table>
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Section</th>
                    <th>Year</th>
                    <th>Date Created</th>
                    <th>Edit</th>
                    <th>Archive</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $sections->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['course']) ?></td>
                        <td><?= htmlspecialchars($row['section_name']) ?></td>
                        <td><?= htmlspecialchars($row['year_level']) ?></td>
                        <td><?= htmlspecialchars($row['date_created']) ?></td>
                        <td>
                            <button class="edit-btn" onclick="openEditModal(
        <?= $row['id'] ?>,
        '<?= addslashes($row['course']) ?>',
        '<?= addslashes($row['section_name']) ?>',
        '<?= $row['year_level'] ?>'
    )">Edit</button>
                        </td>
                        <td>
                            <form method="POST" action="archive.php" style="display:inline;">
                                <input type="hidden" name="type" value="section">
                                <input type="hidden" name="reference_id" value="<?= $row['id'] ?>">
                                <input type="hidden" name="name" value="<?= htmlspecialchars($row['course'] . ' - ' . $row['section_name']) ?>">
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
    function openEditModal(id, course, section, year) {
        document.getElementById('edit_id').value = id;

        const courseDropdown = document.getElementById('edit_course');
        [...courseDropdown.options].forEach(opt => {
            opt.selected = opt.value === course;
        });

        document.getElementById('edit_section').value = section;

        const yearDropdown = document.getElementById('edit_year');
        [...yearDropdown.options].forEach(opt => {
            opt.selected = opt.value === year;
        });

        document.getElementById('editSectionModal').style.display = 'block';
    }

    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }
</script>