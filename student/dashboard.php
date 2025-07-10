<?php
session_start();
require_once('../includes/db.php');

// Ensure the user is a student
if ($_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit();
}
error_reporting(0);

// Fetch student details (e.g., first name, course, and section)
$student_id = $_SESSION['user_id'];
$student_query = "SELECT first_name, course, section FROM users WHERE id = ?";
$stmt = $conn->prepare($student_query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$stmt->bind_result($first_name, $course, $section);
$stmt->fetch();
$stmt->close();


// Fetch assigned modules based on student_modules
$modules_query = "
    SELECT m.folder_name
    FROM module_folders m
    INNER JOIN student_modules sm ON sm.module_folder_id = m.id
    WHERE sm.student_id = ?
";
$modules_stmt = $conn->prepare($modules_query);
$modules_stmt->bind_param("i", $student_id);
$modules_stmt->execute();
$modules_result = $modules_stmt->get_result();
$modules_stmt->close();


// Fetch assigned videos based on course (updated to use 'views' table)
$videos_query = "
    SELECT v.id, v.title, v.video_path AS link
    FROM views v
    WHERE v.course = ?
";
$videos_stmt = $conn->prepare($videos_query);
$videos_stmt->bind_param("s", $course);
$videos_stmt->execute();
$videos_result = $videos_stmt->get_result();
$videos_stmt->close();

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

// Fetch assigned quizzes based on course and section (updated query for section_name)
$quizzes_query = "
    SELECT q.id, q.title
    FROM quizzes q
    JOIN course_year_section_quiz cysq ON q.id = cysq.quiz_id
    WHERE cysq.course = ? AND cysq.section_name = ?
";
$quizzes_stmt = $conn->prepare($quizzes_query);
$quizzes_stmt->bind_param("ss", $course, $section); // Use section_name for filtering
$quizzes_stmt->execute();
$quizzes_result = $quizzes_stmt->get_result();
$quizzes_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Student Dashboard | LearnIT</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/student_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .video-cards-container,
        .home-exam-grid,
        .home-quiz-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }

        .video-card,
        .exam-card,
        .quiz-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            cursor: pointer;
            text-align: center;
        }

        .video-card:hover,
        .exam-card:hover,
        .quiz-card:hover {
            transform: scale(1.05);
        }

        .video-thumbnail video,
        .exam-card img,
        .quiz-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px 8px 0 0;
        }

        .video-title,
        .exam-title,
        .quiz-title {
            padding: 10px;
            font-weight: bold;
        }

        /* CSS for Exam and Quiz Card Layout */
        .exam-card,
        .quiz-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            cursor: pointer;
            text-align: center;
        }

        .exam-card:hover,
        .quiz-card:hover {
            transform: scale(1.05);
        }

        .exam-title,
        .quiz-title {
            padding: 10px;
            font-weight: bold;
            color: #062D91; /* Customize text color */
            background-color: #f0f0f0; /* Background for title */
            border-radius: 0 0 8px 8px; /* Rounded corners at the bottom */
        }
    </style>

</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="profile-section">
                <img src="../assets/images/profile.png" alt="Profile" class="profile-pic">
                <h4><?= htmlspecialchars($first_name) ?></h4> <!-- Display student's first name -->
            </div>
            <nav class="nav-links">
                <a href="dashboard.php" class="active"><i class="fas fa-home"></i> Home</a>
                <a href="modules.php"><i class="fas fa-book"></i> Module</a>
                <a href="views.php"><i class="fas fa-play-circle"></i> Views</a>
                <a href="assessment.php"><i class="fas fa-file-alt"></i> Assessment</a>
                <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                <a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Log Out</a>
            </nav>
        </aside>

        <main class="main-content">
            <h2>Welcome, <?= htmlspecialchars($first_name) ?>!</h2>

            <!-- Display assigned modules -->
            <section class="modules-section">
                <h3>📚 Recent Modules</h3>
                <div class="home-module-grid">
                    <?php if ($modules_result->num_rows > 0): ?>
                        <?php while ($row = $modules_result->fetch_assoc()): ?>
                            <div class="module-card">
                                <img src="../assets/images/module_book.png" alt="Module Icon">
                                <div class="module-title"><?= htmlspecialchars($row['folder_name']) ?></div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No modules assigned yet.</p>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Display assigned videos -->
            <section class="videos-section">
                <h3>🎥 Recent Videos</h3>
                <div class="video-cards-container">
                    <?php if ($videos_result->num_rows > 0): ?>
                        <?php while ($video = $videos_result->fetch_assoc()): ?>
                            <div class="video-card">
                                <a href="view_video.php?id=<?= htmlspecialchars($video['id']) ?>" class="video-link">
                                    <div class="video-thumbnail">
                                        <!-- Display the video thumbnail itself -->
                                        <video muted>
                                            <source src="http://localhost/learnit/uploads/videos/<?= htmlspecialchars($video['link']) ?>" type="video/mp4">
                                        </video>
                                    </div>
                                    <div class="video-title"><?= htmlspecialchars($video['title']) ?></div>
                                </a>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No videos assigned to your course yet.</p>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Display current exams -->
            <section class="exams-section">
                <h3>📝 Current Exams</h3>
                <div class="home-exam-grid">
                    <?php if ($exams_result->num_rows > 0): ?>
                        <?php while ($exam = $exams_result->fetch_assoc()): ?>
                            <div class="exam-card">
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
