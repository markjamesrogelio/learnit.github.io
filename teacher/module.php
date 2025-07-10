<?php
session_start();
require_once('../includes/db.php');

if ($_SESSION['role'] !== 'teacher') {
    header("Location: ../index.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];
$folders = [];

$courses_query = "SELECT id, name FROM courses";
$courses_result = $conn->query($courses_query);

$sql = "SELECT * FROM module_folders WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $folders[] = $row;
}
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_folder'])) {
    $folder_name = $_POST['folder_name'];
    $course = $_POST['course'];
    $year = $_POST['year'];
    $sections = isset($_POST['sections']) ? $_POST['sections'] : [];
    $user_id = $_SESSION['user_id'];
    $module_folder = 'uploads/modules/' . $folder_name;

    $check_folder_query = "SELECT * FROM module_folders WHERE folder_name = ? AND course = ? AND user_id = ?";
    $check_stmt = $conn->prepare($check_folder_query);
    $check_stmt->bind_param("ssi", $folder_name, $course, $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "❌ Folder already exists. Please choose a different name.";
    } else {
        if (!is_dir($module_folder)) {
            mkdir($module_folder, 0777, true);
        }

        // Generate folder code like "2AS-DFE-42R"
function generateFolderCode($length = 9) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[random_int(0, strlen($characters) - 1)];
    }
    return substr($code, 0, 3) . '-' . substr($code, 3, 3) . '-' . substr($code, 6, 3);
}

$folder_code = generateFolderCode();

$stmt = $conn->prepare("INSERT INTO module_folders (folder_name, course, year, sections, user_id, folder_path, folder_code) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $folder_name, $course, $year, implode(',', $sections), $user_id, $module_folder, $folder_code);

        if ($stmt->execute()) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "❌ DB Error: " . $conn->error;
        }
        $stmt->close();
    }
}
// Handle folder deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_folder'])) {
    $folder_id = intval($_POST['folder_id']);

    // Optional: fetch the folder path first to delete files (if needed)
    $get_path_stmt = $conn->prepare("SELECT folder_path FROM module_folders WHERE id = ? AND user_id = ?");
    $get_path_stmt->bind_param("ii", $folder_id, $teacher_id);
    $get_path_stmt->execute();
    $path_result = $get_path_stmt->get_result();
    $folder_path_row = $path_result->fetch_assoc();
    $get_path_stmt->close();

    // Delete from database
    $del_stmt = $conn->prepare("DELETE FROM module_folders WHERE id = ? AND user_id = ?");
    $del_stmt->bind_param("ii", $folder_id, $teacher_id);
    if ($del_stmt->execute()) {
        // Optional: delete folder files too (if needed)
        if ($folder_path_row && is_dir($folder_path_row['folder_path'])) {
            array_map('unlink', glob($folder_path_row['folder_path'] . "/*"));
            rmdir($folder_path_row['folder_path']);
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "❌ Failed to delete folder.";
    }
    $del_stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Module Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #7f7fd5, #86a8e7, #91eae4);
            color: #333;
        }

        header {
            text-align: center;
            padding: 40px 20px 20px;
            color: #4a4ae3;
        }

        header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            font-weight: 700;
        }

        header p {
            color: #444;
            font-size: 1.1em;
        }

        .create-btn {
            display: block;
            margin: 20px auto;
            padding: 10px 25px;
            background: linear-gradient(to right, #667eea, #764ba2);
            border: none;
            color: white;
            font-size: 1em;
            border-radius: 10px;
            cursor: pointer;
            transition: 0.3s ease;
        }

        .create-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .folder-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin-top: 20px;
        }

        .folder-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            width: 300px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            animation: fadeIn 0.7s ease-in;
            transition: transform 0.3s;
        }

        .folder-card:hover {
            transform: translateY(-5px);
        }

        .folder-card h3 {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            font-size: 1.3em;
            font-weight: 600;
        }

        .folder-card h3::before {
            content: "📁";
            font-size: 1.5em;
            margin-right: 10px;
        }

        .folder-info p {
            margin: 5px 0;
        }

        .folder-info span {
            font-weight: bold;
        }

        .card-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }

        .card-actions button {
            border: none;
            padding: 8px 15px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            font-size: 0.9em;
        }

        .upload-btn {
            background-color: #28a745;
            color: white;
        }

        .view-btn {
            background-color: #007bff;
            color: white;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .modal-bg {
            position: fixed;
            top: 0; left: 0;
            width: 100vw; height: 100vh;
            background: rgba(0, 0, 0, 0.4);
            display: none;
            z-index: 999;
        }

        #create-folder-form {
            background: #ffffff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            margin: auto;
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
            animation: popIn 0.3s ease-out;
        }

        #create-folder-form h3 {
            margin-bottom: 20px;
            font-size: 1.4em;
        }

        #create-folder-form label {
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        #create-folder-form input[type="text"],
        #create-folder-form select {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        #create-folder-form .btn {
            margin-top: 20px;
            padding: 10px 20px;
            background: #4a4ae3;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
        }

        #create-folder-form .btn:hover {
            background: #3737d1;
        }

        #create-folder-form .close {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 28px;
            color: #888;
            cursor: pointer;
        }

        #create-folder-form .close:hover {
            color: #333;
        }
        .delete-btn {
        background-color: #dc3545;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 6px;
        font-size: 0.9em;
        cursor: pointer;
        margin-top: 10px;
        width: 100%;
    }

    .delete-btn:hover {
        background-color: #c82333;
    }


        @keyframes popIn {
            from {
                opacity: 0;
                transform: translate(-50%, -45%);
            }
            to {
                opacity: 1;
                transform: translate(-50%, -50%);
            }
        }

        .sections-container {
            margin-top: 10px;
            max-height: 120px;
            overflow-y: auto;
            border: 1px solid #ccc;
            background-color: #f7f7f7;
            border-radius: 8px;
            padding: 10px 15px;
            font-size: 14px;
        }

        .sections-container label {
            display: inline-block;
            margin: 5px 10px 5px 0;
        }

        .sections-container input[type="checkbox"] {
            margin-right: 5px;
        }

        .sections-container::-webkit-scrollbar {
            width: 8px;
        }

        .sections-container::-webkit-scrollbar-thumb {
            background-color: #bbb;
            border-radius: 4px;
        }

        .sections-container::-webkit-scrollbar-track {
            background-color: #eee;
        }
    </style>
</head>
<body>

<header>
    <h1>Module Dashboard</h1>
    <p>Manage your course modules and files</p>
</header>

<button class="create-btn" onclick="toggleFolderForm()">+ Create New Folder</button>

<div class="modal-bg" id="modal-bg" onclick="closeModal()"></div>

<form method="POST" id="create-folder-form">
    <span class="close" onclick="closeModal()">&times;</span>
    <h3>Create New Folder</h3>

    <label>Folder Name:</label>
    <input type="text" name="folder_name" required>

    <label>Course:</label>
    <select name="course" required>
        <option value="">Select Course</option>
        <?php while ($course = $courses_result->fetch_assoc()): ?>
            <option value="<?= htmlspecialchars($course['name']) ?>"><?= htmlspecialchars($course['name']) ?></option>
        <?php endwhile; ?>
    </select>

    <label>Year:</label>
    <select name="year" required>
        <option value="">Select Year</option>
        <option value="1st Year">1st Year</option>
        <option value="2nd Year">2nd Year</option>
        <option value="3rd Year">3rd Year</option>
        <option value="4th Year">4th Year</option>
    </select>

    <label>Sections:</label>
    <div id="sections-container" class="sections-container"></div>

    <button type="submit" name="create_folder" class="btn">Create Folder</button>
</form>

<?php if (count($folders) > 0): ?>
    <div class="folder-grid">
        <?php foreach ($folders as $folder): ?>
            <div class="folder-card">
                <h3><?= htmlspecialchars($folder['folder_name']) ?> Module</h3>
                <div class="folder-info">
                    <p>Course: <span><?= htmlspecialchars($folder['course']) ?></span></p>
                    <p>Year: <span><?= htmlspecialchars($folder['year']) ?></span></p>
                    <p>Sections: <span><?= htmlspecialchars($folder['sections']) ?></span></p>
                    <p>Code: <span style="color: #4a4ae3; font-weight: bold;"><?= htmlspecialchars($folder['folder_code']) ?></span></p>
                </div>
                <div class="card-actions">
                    <button class="upload-btn" onclick="window.location.href='module_upload.php?folder_id=<?= $folder['id'] ?>'">Open Folder</button>                    
                </div>
                <form method="POST" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this folder?');">
                    <input type="hidden" name="folder_id" value="<?= $folder['id'] ?>">
                    <button type="submit" name="delete_folder" class="delete-btn">Delete Folder</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p style="text-align: center;">No folders found. Please create a new folder.</p>
<?php endif; ?>

<script>
    function toggleFolderForm() {
        const form = document.getElementById("create-folder-form");
        const modalBg = document.getElementById("modal-bg");
        form.style.display = 'block';
        modalBg.style.display = 'block';
    }

    function closeModal() {
        const form = document.getElementById("create-folder-form");
        const modalBg = document.getElementById("modal-bg");
        form.style.display = 'none';
        modalBg.style.display = 'none';
    }

    document.querySelector('select[name="year"]').addEventListener('change', function () {
        const year = this.value;
        const sectionsContainer = document.getElementById('sections-container');
        let sectionsHtml = '';

        if (year === '1st Year') {
            sectionsHtml = `<label><input type="checkbox" name="sections[]" value="1A">1A</label>
                            <label><input type="checkbox" name="sections[]" value="1B">1B</label>
                            <label><input type="checkbox" name="sections[]" value="1C">1C</label>
                            <label><input type="checkbox" name="sections[]" value="1D">1D</label>`;
        } else if (year === '2nd Year') {
            sectionsHtml = `<label><input type="checkbox" name="sections[]" value="2A">2A</label>
                            <label><input type="checkbox" name="sections[]" value="2B">2B</label>
                            <label><input type="checkbox" name="sections[]" value="2C">2C</label>
                            <label><input type="checkbox" name="sections[]" value="2D">2D</label>`;
        } else if (year === '3rd Year') {
            sectionsHtml = `<label><input type="checkbox" name="sections[]" value="3A">3A</label>
                            <label><input type="checkbox" name="sections[]" value="3B">3B</label>
                            <label><input type="checkbox" name="sections[]" value="3C">3C</label>
                            <label><input type="checkbox" name="sections[]" value="3D">3D</label>`;
        } else if (year === '4th Year') {
            sectionsHtml = `<label><input type="checkbox" name="sections[]" value="4A">4A</label>
                            <label><input type="checkbox" name="sections[]" value="4B">4B</label>
                            <label><input type="checkbox" name="sections[]" value="4C">4C</label>
                            <label><input type="checkbox" name="sections[]" value="4D">4D</label>`;
        }

        sectionsContainer.innerHTML = sectionsHtml;
    });
</script>
</body>
</html>
