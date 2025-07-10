<?php
require_once('../includes/db.php');

$department_id = isset($_GET['department_id']) ? intval($_GET['department_id']) : null;
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : null;
$section_id = isset($_GET['section_id']) && $_GET['section_id'] !== '0' ? intval($_GET['section_id']) : null;
$parent_id = isset($_GET['parent_id']) ? intval($_GET['parent_id']) : 0;
?>

<link rel="stylesheet" href="../assets/css/explorer.css">
<style>
/* [same styles as before — keep your existing ones] */
.modal {
    display: none;
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0,0,0,0.6); z-index: 999;
    justify-content: center; align-items: center;
}
.modal-content {
    background: white; padding: 25px;
    border-radius: 10px; width: 400px;
    max-width: 90%; position: relative;
}
.modal-content input, .modal-content textarea, .modal-content select {
    width: 100%; padding: 8px; margin: 10px 0;
}
.close { position: absolute; top: 10px; right: 15px; font-size: 24px; cursor: pointer; }
</style>

<div class="explorer-wrapper">
    <h2>Explorer</h2>

<?php if (!$department_id): ?>
    <!-- Department List -->
    <h3>Select Department</h3>
    <div class="card-grid">
        <?php
        $depts = $conn->query("SELECT * FROM departments ORDER BY name ASC");
        while ($dept = $depts->fetch_assoc()):
        ?>
            <a class="card" href="?section=explorer&department_id=<?= $dept['id'] ?>">
                <h3><?= htmlspecialchars($dept['name']) ?></h3>
            </a>
        <?php endwhile; ?>
    </div>

<?php elseif ($department_id && !$course_id): ?>
    <!-- Course List -->
    <a class="btn" href="?section=explorer">← Back to Departments</a>
    <h3>Select Course</h3>
    <div class="card-grid">
        <?php
        $courses = $conn->query("SELECT * FROM courses WHERE department_id = $department_id ORDER BY name ASC");
        while ($course = $courses->fetch_assoc()):
        ?>
            <a class="card" href="?section=explorer&department_id=<?= $department_id ?>&course_id=<?= $course['id'] ?>">
                <h3><?= htmlspecialchars($course['name']) ?></h3>
            </a>
        <?php endwhile; ?>
    </div>

<?php elseif ($department_id && $course_id && !$section_id): ?>
    <!-- Section List -->
    <a class="btn" href="?section=explorer&department_id=<?= $department_id ?>">← Back to Courses</a>
    <h3>Select Section</h3>
    <div class="card-grid">
        <?php
        $sections = $conn->query("SELECT * FROM sections WHERE course_id = $course_id ORDER BY section_name ASC");
        while ($row = $sections->fetch_assoc()):
        ?>
            <a class="card" href="?section=explorer&department_id=<?= $department_id ?>&course_id=<?= $course_id ?>&section_id=<?= $row['id'] ?>">
                <h3><?= htmlspecialchars($row['section_name']) ?> (<?= htmlspecialchars($row['year_level']) ?>)</h3>
            </a>
        <?php endwhile; ?>
    </div>

<?php elseif ($department_id && $course_id && $section_id): ?>
    <!-- FOLDER + FILE EXPLORER -->
    <a class="btn" href="?section=explorer&department_id=<?= $department_id ?>&course_id=<?= $course_id ?>">← Back to Sections</a>

    <div class="explorer-controls">
        <button onclick="openModal('createFolderModal')">📁 New Folder</button>
        <button onclick="openModal('uploadFileModal')">📤 Upload File</button>
    </div>

    <div class="breadcrumbs">
        <a href="?section=explorer&department_id=<?= $department_id ?>&course_id=<?= $course_id ?>&section_id=<?= $section_id ?>">Root</a>
        <?php
        $breadcrumb = [];
        $pid = $parent_id;
        while ($pid) {
            $res = $conn->query("SELECT id, name, parent_id FROM explorer_items WHERE id = $pid");
            if ($res && $row = $res->fetch_assoc()) {
                $breadcrumb[] = "<a href='?section=explorer&department_id=$department_id&course_id=$course_id&section_id=$section_id&parent_id={$row['id']}'>" . htmlspecialchars($row['name']) . "</a>";
                $pid = $row['parent_id'];
            } else break;
        }
        echo ' &gt; ' . implode(' &gt; ', array_reverse($breadcrumb));
        ?>
    </div>

    <table class="explorer-table">
        <thead>
            <tr><th>Name</th><th>Type</th><th>Edit</th><th>Delete</th></tr>
        </thead>
        <tbody>
        <?php
        $items = $conn->query("SELECT * FROM explorer_items WHERE parent_id = $parent_id AND section_id = $section_id AND deleted_at IS NULL ORDER BY name ASC");
        while ($row = $items->fetch_assoc()):
        ?>
            <tr>
                <td>
                    <?= $row['type'] === 'folder' 
                        ? "<a href='?section=explorer&department_id=$department_id&course_id=$course_id&section_id=$section_id&parent_id={$row['id']}'>📁 " 
                        : "📄 " ?>
                    <?= htmlspecialchars($row['name']) ?><?= $row['type'] === 'folder' ? "</a>" : "" ?>
                </td>
                <td><?= $row['type'] === 'folder' ? "Folder" : pathinfo($row['name'], PATHINFO_EXTENSION) ?></td>
                <td>
                    <button class="btn" onclick="openEditModal(<?= htmlspecialchars(json_encode($row)) ?>)">Edit</button>
                </td>
                <td>
                    <button class="btn" onclick="deleteItem(<?= $row['id'] ?>)">Delete</button>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <!-- CREATE FOLDER MODAL -->
    <div id="createFolderModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('createFolderModal')">&times;</span>
            <h3>Create New Folder</h3>
            <form id="createFolderForm">
                <input type="text" name="name" placeholder="Folder name" required>
                <input type="hidden" name="parent_id" value="<?= $parent_id ?>">
                <input type="hidden" name="section_id" value="<?= $section_id ?>">
                <button type="submit" class="btn">Create</button>
            </form>
        </div>
    </div>

    <!-- UPLOAD FILE MODAL -->
    <div id="uploadFileModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('uploadFileModal')">&times;</span>
            <h3>Upload File</h3>
            <form id="uploadFileForm" enctype="multipart/form-data">
                <input type="file" name="file" required>
                <input type="text" name="description" placeholder="Description">
                <select name="availability">
                    <option value="Published">Published</option>
                    <option value="Hidden">Hidden</option>
                </select>
                <input type="hidden" name="parent_id" value="<?= $parent_id ?>">
                <input type="hidden" name="section_id" value="<?= $section_id ?>">
                <button type="submit" class="btn">Upload File</button>
            </form>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div id="editItemModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editItemModal')">&times;</span>
            <h3>Edit Item</h3>
            <form id="editItemForm">
                <input type="hidden" name="id" id="editId">
                <input type="text" name="name" id="editName" placeholder="New name" required>
                <textarea name="description" id="editDescription" placeholder="Description"></textarea>
                <select name="availability" id="editAvailability">
                    <option value="Published">Published</option>
                    <option value="Hidden">Hidden</option>
                </select>
                <button type="submit" class="btn">Save Changes</button>
            </form>
        </div>
    </div>

    <script>
    function openModal(id) {
        document.getElementById(id).style.display = 'flex';
    }
    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }

    document.getElementById('createFolderForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const res = await fetch('explorer_create.php', { method: 'POST', body: formData });
        const result = await res.json();
        alert(result.status === 'success' ? '✅ Folder created' : '❌ ' + result.message);
        if (result.status === 'success') location.reload();
    });

    document.getElementById('uploadFileForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const res = await fetch('explorer_upload.php', { method: 'POST', body: formData });
        const result = await res.json();
        alert(result.status === 'success' ? '✅ File uploaded' : '❌ ' + result.message);
        if (result.status === 'success') location.reload();
    });

    function openEditModal(row) {
        document.getElementById('editId').value = row.id;
        document.getElementById('editName').value = row.name;
        document.getElementById('editDescription').value = row.description || '';
        document.getElementById('editAvailability').value = row.availability || 'Published';
        openModal('editItemModal');
    }

    document.getElementById('editItemForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const res = await fetch('explorer_edit.php', { method: 'POST', body: formData });
        const result = await res.json();
        alert(result.status === 'success' ? '✅ Updated' : '❌ ' + result.message);
        if (result.status === 'success') location.reload();
    });

    function deleteItem(id) {
        if (!confirm('Are you sure you want to delete this item?')) return;
        fetch('explorer_delete.php?id=' + id)
            .then(res => res.json())
            .then(result => {
                alert(result.status === 'success' ? '🗑️ Deleted' : '❌ ' + result.message);
                if (result.status === 'success') location.reload();
            });
    }
    </script>

<?php endif; ?>
</div>
