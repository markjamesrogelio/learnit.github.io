<?php
session_start();
require_once('../includes/db.php');

if ($_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit();
}

$student_id = $_SESSION['user_id'];

// Handle adding a module by folder code
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['folder_code'])) {
    $folder_code = trim($_POST['folder_code']);
    
    $check_query = "SELECT id FROM module_folders WHERE folder_code = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("s", $folder_code);
    $check_stmt->execute();
    $check_stmt->bind_result($new_folder_id);
    
    if ($check_stmt->fetch()) {
        $check_stmt->close();
        
        // Check if already added
        $exists_query = "SELECT 1 FROM student_modules WHERE student_id = ? AND module_folder_id = ?";
        $exists_stmt = $conn->prepare($exists_query);
        $exists_stmt->bind_param("ii", $student_id, $new_folder_id);
        $exists_stmt->execute();
        $exists_stmt->store_result();
        
        if ($exists_stmt->num_rows === 0) {
            // Add to student_modules
            $insert_query = "INSERT INTO student_modules (student_id, module_folder_id) VALUES (?, ?)";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bind_param("ii", $student_id, $new_folder_id);
            $insert_stmt->execute();
            $insert_stmt->close();
            $message = "Module successfully added!";
        } else {
            $message = "You already have this module.";
        }
        $exists_stmt->close();
    } else {
        $message = "Invalid folder code.";
        $check_stmt->close();
    }
}

// Fetch student info
$student_query = "SELECT first_name FROM users WHERE id = ?";
$stmt = $conn->prepare($student_query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$stmt->bind_result($first_name);
$stmt->fetch();
$stmt->close();

// Fetch assigned modules
$modules_query = "
    SELECT mf.folder_name, mf.id
    FROM module_folders mf
    INNER JOIN student_modules sm ON sm.module_folder_id = mf.id
    WHERE sm.student_id = ?
";
$modules_stmt = $conn->prepare($modules_query);
$modules_stmt->bind_param("i", $student_id);
$modules_stmt->execute();
$modules_result = $modules_stmt->get_result();
$modules_stmt->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Student Modules | LearnIT</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/student_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .modal { display: none; position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5); align-items:center; justify-content:center; }
        .modal-content { background: #fff; padding: 20px; border-radius: 8px; width: 300px; text-align: center; }
        .modal-content input { width: 100%; padding: 8px; margin: 10px 0; }
        .modal-content button { padding: 8px 16px; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="profile-section">
                <img src="../assets/images/profile.png" alt="Profile" class="profile-pic">
                <h4><?= htmlspecialchars($first_name) ?></h4>
            </div>
            <nav class="nav-links">
                <a href="dashboard.php"><i class="fas fa-home"></i> Home</a>
                <a href="modules.php" class="active"><i class="fas fa-book"></i> Module</a>
                <a href="views.php"><i class="fas fa-play-circle"></i> Views</a>
                <a href="assessment.php"><i class="fas fa-file-alt"></i> Assessment</a>
                <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                <a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Log Out</a>
            </nav>
        </aside>

        <main class="main-content">
            <h2>Welcome, <?= htmlspecialchars($first_name) ?>!</h2>
            
            <button onclick="document.getElementById('addModuleModal').style.display='flex'">➕ Add Module</button>
            <?php if ($message): ?>
                <p style="color:green;"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>
            
            <section class="modules-section">
                <h3>📚 Your Modules</h3>
                <div class="home-module-grid">
                    <?php if ($modules_result->num_rows > 0): ?>
                        <?php while ($row = $modules_result->fetch_assoc()): ?>
                            <div class="module-card">
                                <a href="module_content.php?folder_id=<?= htmlspecialchars($row['id']) ?>">
                                    <img src="../assets/images/module_book.png" alt="Module Icon">
                                    <div class="module-title"><?= htmlspecialchars($row['folder_name']) ?></div>
                                </a>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No modules assigned to you.</p>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>

    <!-- Add Module Modal -->
    <div id="addModuleModal" class="modal">
        <div class="modal-content">
            <h4>Add Module</h4>
            <form method="POST">
                <input type="text" name="folder_code" placeholder="Enter Folder Code" required>
                <button type="submit">Add</button>
                <button type="button" onclick="document.getElementById('addModuleModal').style.display='none'">Cancel</button>
            </form>
        </div>
    </div>

    <script>
        // Close modal on outside click
        window.onclick = function(event) {
            const modal = document.getElementById('addModuleModal');
            if (event.target === modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>
