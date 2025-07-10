<?php
session_start();
require_once('../includes/db.php');

// Ensure the user is a student
if ($_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit();
}

// Fetch student details (e.g., first name, course, and section)
$student_id = $_SESSION['user_id'];
$student_query = "SELECT first_name, course, section FROM users WHERE id = ?";
$stmt = $conn->prepare($student_query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$stmt->bind_result($first_name, $course, $section);
$stmt->fetch();
$stmt->close();

// Fetch assigned exams based on course and section
$exams_query = "
    SELECT e.id, e.title
    FROM exams e
    JOIN course_year_section_exam cye ON e.id = cye.exam_id
    WHERE cye.course = ? AND cye.section_name = ?
";
$exams_stmt = $conn->prepare($exams_query);
$exams_stmt->bind_param("ss", $course, $section);
$exams_stmt->execute();
$exams_result = $exams_stmt->get_result();
$exams_stmt->close();

// Fetch assigned quizzes based on course and section
$quizzes_query = "
    SELECT q.id, q.title
    FROM quizzes q
    JOIN course_year_section_quiz cysq ON q.id = cysq.quiz_id
    WHERE cysq.course = ? AND cysq.section_name = ?
";
$quizzes_stmt = $conn->prepare($quizzes_query);
$quizzes_stmt->bind_param("ss", $course, $section);
$quizzes_stmt->execute();
$quizzes_result = $quizzes_stmt->get_result();
$quizzes_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Assessment | LearnIT</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/student_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .exam-card, .quiz-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            cursor: pointer;
            text-align: center;
            overflow: hidden;
            position: relative;
            width: 220px; /* Shrunk the width of the card */
            margin: 0 auto;
        }

        .exam-card:hover, .quiz-card:hover {
            transform: scale(1.05);
        }

        .exam-card .card-cover, .quiz-card .card-cover {
            height: 150px; /* Reduced the height of the cover image */
            background-size: cover;
            background-position: center;
        }

        .exam-title, .quiz-title {
            padding: 10px;
            font-weight: bold;
            color: #fff;
            background-color: rgba(0, 0, 0, 0.6);
            border-radius: 0 0 8px 8px;
            font-size: 1rem; /* Reduced font size */
        }

        .home-exam-grid, .home-quiz-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); /* Adjusted grid size */
            gap: 20px;
        }

        .home-exam-grid, .home-quiz-grid p {
            text-align: center;
        }
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
                <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>"><i class="fas fa-home"></i> Home</a>
                <a href="modules.php" class="<?= basename($_SERVER['PHP_SELF']) === 'modules.php' ? 'active' : '' ?>"><i class="fas fa-book"></i> Module</a>
                <a href="views.php" class="<?= basename($_SERVER['PHP_SELF']) === 'views.php' ? 'active' : '' ?>"><i class="fas fa-play-circle"></i> Views</a>
                <a href="assessment.php" class="<?= basename($_SERVER['PHP_SELF']) === 'assessment.php' ? 'active' : '' ?>"><i class="fas fa-file-alt"></i> Assessment</a>
                <a href="profile.php" class="<?= basename($_SERVER['PHP_SELF']) === 'profile.php' ? 'active' : '' ?>"><i class="fas fa-user"></i> Profile</a>
                <a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Log Out</a>
            </nav>
        </aside>

        <main class="main-content">
            <h2>Welcome, <?= htmlspecialchars($first_name) ?>!</h2>

            <!-- Display current exams -->
            <section class="exams-section">
                <h3>📝 Current Exams</h3>
                <div class="home-exam-grid">
                    <?php if ($exams_result->num_rows > 0): ?>
                        <?php while ($exam = $exams_result->fetch_assoc()): ?>
                            <div class="exam-card">
                                <div class="card-cover" style="background-image: url('../assets/images/module_book.png');"></div>
                                <a href="answer_exam.php?exam_id=<?= $exam['id'] ?>" class="exam-link">
                                    <div class="exam-title"><?= htmlspecialchars($exam['title']) ?></div>
                                </a>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No exams available.</p>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Display current quizzes -->
            <section class="quizzes-section">
                <h3>📝 Current Quizzes</h3>
                <div class="home-quiz-grid">
                    <?php if ($quizzes_result->num_rows > 0): ?>
                        <?php while ($quiz = $quizzes_result->fetch_assoc()): ?>
                            <div class="quiz-card">
                                <div class="card-cover" style="background-image: url('../assets/images/module_book.png');"></div>
                                <a href="answer_quiz.php?quiz_id=<?= $quiz['id'] ?>" class="quiz-link">
                                    <div class="quiz-title"><?= htmlspecialchars($quiz['title']) ?></div>
                                </a>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No quizzes available.</p>
                    <?php endif; ?>
                </div>
            </section>

        </main>
    </div>
</body>
</html>
