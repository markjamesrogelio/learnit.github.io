<?php
session_start();
require_once('../includes/db.php');

if ($_SESSION['role'] !== 'teacher') {
    header("Location: ../index.php");
    exit();
}

$folder_id = $_GET['folder_id'] ?? null;
if (!$folder_id) {
    echo "Folder not found.";
    exit();
}

$teacher_id = $_SESSION['user_id'];

// Fetch folder info
$stmt = $conn->prepare("SELECT * FROM module_folders WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $folder_id, $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$folder = $result->fetch_assoc();
$stmt->close();

if (!$folder) {
    echo "Folder not found or you do not have access to it.";
    exit();
}

// Sanitize folder name
$clean_folder_name = preg_replace('/[^A-Za-z0-9_\-]/', '_', $folder['folder_name']);

// Handle multiple deletion
if (isset($_POST['delete_selected']) && isset($_POST['selected_modules'])) {
    foreach ($_POST['selected_modules'] as $delete_id) {
        $stmt = $conn->prepare("SELECT file_path FROM modules WHERE module_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $delete_id, $teacher_id);
        $stmt->execute();
        $stmt->bind_result($path);
        $stmt->fetch();
        $stmt->close();

        if ($path && file_exists($_SERVER['DOCUMENT_ROOT'] . '/learnit/' . $path)) {
            unlink($_SERVER['DOCUMENT_ROOT'] . '/learnit/' . $path);
        }

        $stmt = $conn->prepare("DELETE FROM modules WHERE module_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $delete_id, $teacher_id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['module_files'])) {
    $prefix = $_POST['file_name'] ?? '';
    $files = $_FILES['module_files'];
    $count = count($files['name']);

    $relative_folder = 'uploads/modules/' . $clean_folder_name;
    $absolute_folder = $_SERVER['DOCUMENT_ROOT'] . '/learnit/' . $relative_folder;

    if (!file_exists($absolute_folder)) {
        mkdir($absolute_folder, 0777, true);
    }

    for ($i = 0; $i < $count; $i++) {
        $file_name = $prefix ? $prefix . ' - ' . basename($files['name'][$i]) : basename($files['name'][$i]);
        $file_type = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $tmp_name = $files['tmp_name'][$i];

        $relative_file_path = $relative_folder . '/' . $file_name;
        $absolute_file_path = $absolute_folder . '/' . $file_name;

        $counter = 1;
        $original_file_name = $file_name;
        while (file_exists($absolute_file_path)) {
            $file_name = pathinfo($original_file_name, PATHINFO_FILENAME) . "($counter)." . $file_type;
            $relative_file_path = $relative_folder . '/' . $file_name;
            $absolute_file_path = $absolute_folder . '/' . $file_name;
            $counter++;
        }

        if (move_uploaded_file($tmp_name, $absolute_file_path)) {
            $stmt = $conn->prepare("INSERT INTO modules (module_name, module_folder, module_type, user_id, file_path, folder_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssdsd", $file_name, $relative_folder, $file_type, $teacher_id, $relative_file_path, $folder_id);
            $stmt->execute();
            $stmt->close();
        }
    }
    echo '<script>window.location.reload();</script>';
    exit();
}

// Fetch existing modules
$modules = [];
$stmt = $conn->prepare("SELECT module_id, module_name, module_type, file_path FROM modules WHERE folder_id = ?");
$stmt->bind_param("i", $folder_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $modules[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload to Folder</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            display: flex;
            justify-content: center;
            padding: 40px;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            width: 100%;
            max-width: 700px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        h2 { margin-bottom: 20px; }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .drop-zone {
            border: 2px dashed #007bff;
            border-radius: 6px;
            padding: 30px;
            text-align: center;
            background-color: #f1f8ff;
            cursor: pointer;
            transition: 0.3s;
            margin-bottom: 20px;
        }
        .drop-zone:hover { background-color: #e2f0ff; }
        .drop-zone.dragover { background-color: #d0e7ff; border-color: #0056b3; }
        button {
            background: #007bff;
            color: white;
            padding: 12px;
            width: 100%;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover { background: #0056b3; }
        ul { list-style: none; padding: 0; }
        li {
            background: #f9f9f9;
            padding: 10px 15px;
            border-radius: 6px;
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            align-items: center;
        }
        li.selected { background-color: #dbeeff; }
        li .actions { display: flex; gap: 10px; }
        #file-list div { margin-bottom: 8px; }
        #progress-bar-container { margin: 15px 0; display: none; }
        #progress-bar {
            height: 10px;
            background: #007bff;
            border-radius: 5px;
            width: 0%;
        }
        #upload-spinner {
            display: none;
            text-align: center;
            margin-top: 15px;
        }
        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #ddd;
            border-top: 4px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        #preview-modal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0,0,0,0.7);
            z-index: 999;
            justify-content: center;
            align-items: center;
        }

        #preview-box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            max-width: 90%;
            max-height: 90%;
            overflow: auto;
            position: relative;
        }

        /* Floating close button on top of modal screen */
        #modal-close-btn {
        position: fixed;
        top: 20px;
        right: 30px;
        background: transparent;
        color: #ffffff;
        border: none;
        font-size: 28px;
        cursor: pointer;
        z-index: 1000;
        padding: 0;
        line-height: 1;
    }
    #modal-close-btn:hover {
        color: #ff6b6b;
    }


    </style>
</head>
<body>
<div class="container">
    <h2>Upload to: <?= htmlspecialchars($folder['folder_name']) ?></h2>
    <form method="POST" enctype="multipart/form-data" id="upload-form">
        <input type="text" name="file_name" placeholder="File name prefix (optional)">
        <div class="drop-zone" id="drop-zone">
            <span>Drop files here or click to select</span>
            <input type="file" name="module_files[]" id="file-input" multiple hidden>
        </div>
        <div id="file-list"></div>
        <div id="progress-bar-container">
            <div style="background:#e0e0e0; border-radius:5px;">
                <div id="progress-bar"></div>
            </div>
        </div>
        <button type="submit">Upload Files</button>
        <div id="upload-spinner">
            <div class="spinner"></div>
            <p style="margin-top: 10px; color: #555;">Uploading files, please wait...</p>
        </div>
    </form>

    <?php if (count($modules) > 0): ?>
        <h3 style="margin-top: 30px;">Uploaded Files</h3>
        <form method="POST" onsubmit="return confirm('Delete selected files?');">
            <label><input type="checkbox" id="select-all" style="margin-right: 8px;"> Select All</label>
            <ul>
                <?php foreach ($modules as $mod):
                    $icon = '📄';
                    if ($mod['module_type'] === 'pdf') $icon = '📄';
                    elseif ($mod['module_type'] === 'docx') $icon = '📘';
                    elseif ($mod['module_type'] === 'pptx') $icon = '📊';
                ?>
                <li>
                    <label style="display: flex; align-items: center; flex: 1;">
                        <input type="checkbox" name="selected_modules[]" value="<?= $mod['module_id'] ?>" class="file-checkbox" style="margin-right: 10px;">
                        <span><?= $icon ?> <?= htmlspecialchars($mod['module_name']) ?></span>
                    </label>
                    <div class="actions">
                        <a href="#" onclick="previewFile('<?= $mod['module_type'] ?>', '<?= '/learnit/' . $mod['file_path'] ?>')" style="color: #007bff;">👁️</a>
                        <a href="/learnit/<?= $mod['file_path'] ?>" download style="color: green;">⬇️</a>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
            <button type="submit" name="delete_selected" style="background-color: #dc3545; margin-top: 10px;">🗑️ Delete Selected</button>
        </form>
    <?php else: ?>
        <p>No files uploaded yet.</p>
    <?php endif; ?>

    <div style="margin-top: 30px;">
        <button onclick="window.location.href='http://localhost/learnit/teacher/dashboard.php?section=module'" 
                style="background-color: #28a745; color: white;">
            ⬅ Return to Module Dashboard
        </button>
    </div>
</div>
<!-- Preview Modal -->
<div id="preview-modal">
    <button onclick="closePreview()" id="modal-close-btn">✖</button>
    <div id="preview-box">
        <div id="preview-content"></div>
    </div>
</div>
<script>
const dropZone = document.getElementById('drop-zone');
const fileInput = document.getElementById('file-input');
const fileList = document.getElementById('file-list');
const uploadForm = document.getElementById('upload-form');
const progressBarContainer = document.getElementById('progress-bar-container');
const progressBar = document.getElementById('progress-bar');
const selectAllCheckbox = document.getElementById('select-all');

dropZone.addEventListener('click', () => fileInput.click());
dropZone.addEventListener('dragover', e => {
    e.preventDefault();
    dropZone.classList.add('dragover');
});
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.classList.remove('dragover');
    fileInput.files = e.dataTransfer.files;
    displayFiles(fileInput.files);
});
fileInput.addEventListener('change', () => displayFiles(fileInput.files));

function displayFiles(files) {
    fileList.innerHTML = '';
    for (let file of files) {
        fileList.innerHTML += `<div>📄 ${file.name}</div>`;
    }
}

uploadForm.addEventListener('submit', function(e) {
    e.preventDefault();

    const files = fileInput.files;
    if (!files || files.length === 0) {
        alert("Please select at least one file to upload.");
        return;
    }

    document.getElementById('upload-spinner').style.display = 'block';
    const formData = new FormData(uploadForm);
    const xhr = new XMLHttpRequest();

    xhr.open("POST", "", true);
    xhr.upload.addEventListener("progress", function(e) {
        if (e.lengthComputable) {
            const percent = (e.loaded / e.total) * 100;
            progressBarContainer.style.display = 'block';
            progressBar.style.width = percent + "%";
        }
    });

    xhr.onload = function () {
        if (xhr.status === 200) {
            setTimeout(() => window.location.reload(), 800);
        }
    };

    xhr.send(formData);
});

selectAllCheckbox?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.file-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = this.checked;
        cb.closest('li').classList.toggle('selected', this.checked);
    });
});

document.querySelectorAll('.file-checkbox').forEach(cb => {
    cb.addEventListener('change', function() {
        this.closest('li').classList.toggle('selected', this.checked);
    });
});

function previewFile(type, path) {
    const modal = document.getElementById('preview-modal');
    const content = document.getElementById('preview-content');
    content.innerHTML = ''; // clear previous

    if (type === 'pdf') {
        content.innerHTML = `<iframe src="${path}" style="width:80vw; height:80vh;" frameborder="0"></iframe>`;
    } else if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(type)) {
        content.innerHTML = `<img src="${path}" style="max-width:100%; max-height:80vh; border-radius:10px;">`;
    } else {
        content.innerHTML = `<p style="color:#333; font-size:16px;">Preview not available for this file type.</p>`;
    }

    modal.style.display = 'flex';
}

function closePreview() {
    document.getElementById('preview-modal').style.display = 'none';
    document.getElementById('preview-content').innerHTML = '';
}
</script>
</body>
</html>
