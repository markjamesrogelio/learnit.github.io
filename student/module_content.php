<?php
session_start();
require_once('../includes/db.php');

// Ensure the user is a student
if ($_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit();
}
error_reporting(0);

$folder_id = isset($_GET['folder_id']) ? $_GET['folder_id'] : null;
if (!$folder_id) {
    echo "Invalid folder ID.";
    exit();
}

// Fetch folder name
$module_query = "SELECT folder_name FROM module_folders WHERE id = ?";
$module_stmt = $conn->prepare($module_query);
$module_stmt->bind_param("i", $folder_id);
$module_stmt->execute();
$module_stmt->bind_result($module_name);
$module_stmt->fetch();
$module_stmt->close();

// Handle Add Module form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['folder_code'])) {
    $folder_code = trim($_POST['folder_code']);
    
    // Check if folder_code exists
    $check_query = "SELECT id FROM module_folders WHERE folder_code = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("s", $folder_code);
    $check_stmt->execute();
    $check_stmt->bind_result($new_folder_id);
    if ($check_stmt->fetch()) {
        // Here you could link this folder to the student or show its content
        // For now, just redirect to its content
        header("Location: module_content.php?folder_id=" . $new_folder_id);
        exit();
    } else {
        $message = 'Invalid folder code. Please try again.';
    }
    $check_stmt->close();
}

// Fetch files
$files_query = "SELECT module_name, file_path FROM modules WHERE folder_id = ?";
$files_stmt = $conn->prepare($files_query);
$files_stmt->bind_param("i", $folder_id);
$files_stmt->execute();
$files_result = $files_stmt->get_result();
$files_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Module Content | LearnIT</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/student_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .files-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-top: 1rem;
        }
        .file-card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 12px;
            padding: 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            text-align: center;
            transition: transform 0.2s;
        }
        .file-card:hover {
            transform: translateY(-4px);
        }
        .file-title {
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .file-buttons {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        .btn {
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            font-size: 0.9rem;
            text-decoration: none;
            color: #fff;
            display: inline-block;
        }
        .view-btn {
            background-color: #3498db;
        }
        .download-btn {
            background-color: #2ecc71;
        }
        .view-btn:hover {
            background-color: #2980b9;
        }
        .download-btn:hover {
            background-color: #27ae60;
        }
        .top-buttons {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
        }
        .back-btn {
            background-color: #7f8c8d;
            color: #fff;
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            text-decoration: none;
        }
        .add-module-form {
            margin-top: 1rem;
            background: #f9f9f9;
            padding: 1rem;
            border-radius: 8px;
        }
        .add-module-form input {
            padding: 0.4rem;
            width: 200px;
            margin-right: 0.5rem;
        }
        .add-module-form button {
            padding: 0.4rem 0.8rem;
            background: #8e44ad;
            color: #fff;
            border: none;
            border-radius: 6px;
        }
        .add-module-form button:hover {
            background: #732d91;
        }
        .message {
            color: red;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="profile-section">
                <img src="../assets/images/profile.png" alt="Profile" class="profile-pic">
                <h4><?= htmlspecialchars($_SESSION['first_name']) ?></h4>
            </div>
            <nav class="nav-links">
                <a href="dashboard.php"><i class="fas fa-home"></i> Home</a>
                <a href="modules.php"><i class="fas fa-book"></i> Module</a>
                <a href="views.php"><i class="fas fa-play-circle"></i> Views</a>
                <a href="assessment.php"><i class="fas fa-file-alt"></i> Assessment</a>
                <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                <a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Log Out</a>
            </nav>
        </aside>

        <main class="main-content">
            <div class="top-buttons">
                <a href="modules.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back</a>               
            </div>

            <h2>Module: <?= htmlspecialchars($module_name) ?></h2>

            <section class="files-section">
                <h3>📁 Files in this Module</h3>
                <div class="files-grid">
                    <?php if ($files_result->num_rows > 0): ?>
                        <?php while ($file = $files_result->fetch_assoc()): ?>
                            <div class="file-card">
                                <div class="file-title"><?= htmlspecialchars($file['module_name']) ?></div>
                                <div class="file-buttons">
                                    <a href="../<?= htmlspecialchars($file['file_path']) ?>" target="_blank" class="btn view-btn">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="../<?= htmlspecialchars($file['file_path']) ?>" download class="btn download-btn">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No files uploaded for this module.</p>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
