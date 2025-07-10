<?php
session_start();
require_once('../includes/db.php');
require_once('../includes/auth.php');

// Ensure the user is a teacher
if ($_SESSION['role'] !== 'teacher') {
    header("Location: ../index.php");
    exit();
}

error_reporting(0);


$section = $_GET['section'] ?? 'home';
$action = $_GET['action'] ?? 'list'; 
$teacher_id = $_SESSION['user_id'];

// Fetch teacher name dynamically
$teacherName = 'Teacher'; 
$stmt = $conn->prepare("SELECT first_name, last_name FROM users WHERE id = ? AND role = 'teacher'");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($firstName, $lastName);
if ($stmt->fetch()) {
    $teacherName = htmlspecialchars($firstName . ' ' . $lastName);
}
$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Teacher Dashboard | LearnIT</title>
    <link rel="stylesheet" href="../assets/css/admin_sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .main-content {
            background-color: transparent;
        }
    </style>
</head>

<body>
    <div class="admin-layout" style="background: url('../assets/images/admin-bg.jpg') no-repeat center center fixed; background-size: cover; position: relative;">
        <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.3); pointer-events: none; z-index: -1;"></div>
        <aside class="sidebar">
            <div class="logo">Learn<span>IT</span></div>
            <ul class="nav-section">
                <li class="section-title">TEACHER</li>
                <li><a href="dashboard.php?section=views" <?= $section === 'views' ? 'class="active"' : '' ?>><i class="fas fa-play-circle"></i> Views</a></li>
                <li><a href="dashboard.php?section=module" <?= $section === 'module' ? 'class="active"' : '' ?>><i class="fas fa-folder-open"></i> Modules</a></li>
                <li><a href="dashboard.php?section=quiz" <?= $section === 'quiz' ? 'class="active"' : '' ?>><i class="fas fa-question-circle"></i> Quiz</a></li>
                <li><a href="dashboard.php?section=exam" <?= $section === 'exam' ? 'class="active"' : '' ?>><i class="fas fa-file-alt"></i> Exam</a></li>
            </ul>
        </aside>

        <header class="topbar">
            <div class="admin-label">TEACHER</div>
            <div class="admin-info" style="position: relative;">
                <i class="fas fa-user-circle"></i>
                <button onclick="toggleLogoutDropdown()" style="background: none; border: none; color: white; font-weight: bold; cursor: pointer;">
                    <?= $teacherName ?> ▼
                </button>
                <div id="logoutDropdown" style="display: none; position: absolute; top: 100%; right: 0; background: white; box-shadow: 0 2px 6px rgba(0,0,0,0.2); z-index: 999;">
                    <a href="../auth/logout.php" style="display: block; padding: 10px 15px; text-decoration: none; color: #333;">Logout</a>
                </div>
            </div>
        </header>

        <main class="main-content">
            <div class="admin-content">
                <?php
                switch ($section) {
                    case 'views':
                        include('views.php');
                        break;
                    case 'module':
                        include('module.php');
                        break;
                    case 'quiz':
                        if ($action === 'list') {
                            include('quiz.php');
                        } elseif ($action === 'create') {
                            include('quiz_create.php');
                        } elseif ($action === 'edit' && isset($_GET['id'])) {
                            include('quiz_edit.php');
                        } elseif ($action === 'delete' && isset($_GET['id'])) {
                            $delete_id = intval($_GET['id']);
                            $stmt = $conn->prepare("DELETE FROM quizzes WHERE id = ? AND teacher_id = ?");
                            $stmt->bind_param("ii", $delete_id, $teacher_id);
                            $stmt->execute();
                            header("Location: dashboard.php?section=quiz&action=list&msg=deleted");
                            exit();
                        } elseif ($action === 'questions_list' && isset($_GET['quiz_id'])) {
                            include('quiz_questions.php'); 
                        } elseif (($action === 'questions_add' || $action === 'questions_edit') && isset($_GET['quiz_id'])) {
                            include('quiz_question_form.php'); 
                        } elseif ($action === 'questions_delete' && isset($_GET['quiz_id']) && isset($_GET['question_id'])) {
                            // Delete question logic
                            $quiz_id = intval($_GET['quiz_id']);
                            $question_id = intval($_GET['question_id']);
                            if ($quiz_id > 0 && $question_id > 0) {
                                $del_stmt = $conn->prepare("DELETE FROM quiz_questions WHERE id = ? AND quiz_id = ?");
                                $del_stmt->bind_param("ii", $question_id, $quiz_id);
                                $del_stmt->execute();
                                $del_stmt->close();
                                header("Location: dashboard.php?section=quiz&action=questions_list&quiz_id=$quiz_id&msg=deleted");
                                exit();
                            }
                        } else {
                            echo "<p>Invalid quiz action.</p>";
                        }
                        break;
                    case 'exam':
                        if ($action === 'list') {
                            include('exam.php');
                        } elseif ($action === 'create') {
                            include('exam_create.php');
                        } elseif ($action === 'edit' && isset($_GET['id'])) {
                            include('exam_edit.php');
                        } elseif ($action === 'delete' && isset($_GET['id'])) {
                            $delete_id = intval($_GET['id']);
                            $stmt = $conn->prepare("DELETE FROM exams WHERE id = ? AND teacher_id = ?");
                            $stmt->bind_param("ii", $delete_id, $teacher_id);
                            $stmt->execute();
                            header("Location: dashboard.php?section=exam&action=list&msg=deleted");
                            exit();
                        } elseif ($action === 'questions_list' && isset($_GET['exam_id'])) {
                            include('exam_questions.php'); 
                        } elseif (($action === 'questions_add' || $action === 'questions_edit') && isset($_GET['exam_id'])) {
                            include('exam_question_form.php'); 
                        } elseif ($action === 'questions_delete' && isset($_GET['exam_id']) && isset($_GET['question_id'])) {
                            // Delete question logic
                            $exam_id = intval($_GET['exam_id']);
                            $question_id = intval($_GET['question_id']);
                            if ($exam_id > 0 && $question_id > 0) {
                                $del_stmt = $conn->prepare("DELETE FROM exam_questions WHERE id = ? AND exam_id = ?");
                                $del_stmt->bind_param("ii", $question_id, $exam_id);
                                $del_stmt->execute();
                                $del_stmt->close();
                                header("Location: dashboard.php?section=exam&action=questions_list&exam_id=$exam_id&msg=deleted");
                                exit();
                            }
                        } else {
                            echo "<p>Invalid exam action.</p>";
                        }
                        break;
                    default:
                        echo "<h2>Welcome to your Teacher Dashboard</h2>";
                        break;
                }
                ?>
            </div>
        </main>
    </div>

    <script>
        function toggleLogoutDropdown() {
            const dropdown = document.getElementById("logoutDropdown");
            dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
        }

        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById("logoutDropdown");
            const button = event.target.closest('.admin-info');
            if (!button) {
                dropdown.style.display = "none";
            }
        });
    </script>
</body>

</html>
