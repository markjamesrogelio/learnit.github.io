<?php
session_start();
require_once('../includes/db.php');

// Ensure the user is a student
if ($_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit();
}

// Fetch student details (e.g., first name)
$student_id = $_SESSION['user_id'];
$student_query = "SELECT first_name FROM users WHERE id = ?";
$stmt = $conn->prepare($student_query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$stmt->bind_result($first_name);
$stmt->fetch();
$stmt->close();

// Get the video ID from the URL
$video_id = $_GET['id'] ?? 0;

// Fetch video details based on the ID
$video_query = "SELECT title, video_path, category, description, created_at FROM views WHERE id = ?";
$video_stmt = $conn->prepare($video_query);
$video_stmt->bind_param("i", $video_id);
$video_stmt->execute();
$video_stmt->bind_result($title, $video_path, $category, $description, $created_at);
$video_stmt->fetch();
$video_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>View Video | LearnIT</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/student_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Add styles for the close button */
        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 24px;
            background-color: yellow;
            border: none;
            color: #000;
            cursor: pointer;
            padding: 10px;
            border-radius: 50%;
            z-index: 10;
        }
        .close-btn:hover {
            background-color: orange;
            color: #fff;
        }
        .video-container {
            position: relative;
            padding-top: 10px;
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
                <a href="dashboard.php"><i class="fas fa-home"></i> Home</a>
                <a href="modules.php"><i class="fas fa-book"></i> Module</a>
                <a href="views.php" class="active"><i class="fas fa-play-circle"></i> Views</a>
                <a href="assessment.php"><i class="fas fa-file-alt"></i> Assessment</a>
                <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                <a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Log Out</a>
            </nav>
        </aside>

        <main class="main-content">
            <h2>🎥 <?= htmlspecialchars($title) ?></h2>
            <div class="video-container">
                <!-- Close button to hide the video -->
                <button class="close-btn" onclick="closeVideo()">X</button>

                <!-- Video display -->
                <video width="100%" height="400" controls>
                    <source src="http://localhost/learnit/uploads/videos/<?= htmlspecialchars($video_path) ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
                
                <div class="video-meta">
                    <p><strong>Category:</strong> <?= htmlspecialchars($category) ?></p>
                    <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($description)) ?></p>
                    <p><strong>Uploaded on:</strong> <?= htmlspecialchars($created_at) ?></p>
                </div>
                <div class="video-actions">
                    <button id="like-btn"><i class="fas fa-heart"></i> Like</button>
                    <button id="copy-btn"><i class="fas fa-link"></i> Copy Link</button>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Close the video and return to the dashboard
        function closeVideo() {
            window.location.href = "dashboard.php"; // Redirect to dashboard
        }

        // Like button functionality (toggle class on click)
        document.getElementById('like-btn').addEventListener('click', function() {
            this.classList.toggle('liked');
        });

        // Copy link functionality
        document.getElementById('copy-btn').addEventListener('click', function() {
            const videoLink = window.location.href;
            navigator.clipboard.writeText(videoLink).then(function() {
                alert('Link copied to clipboard!');
            }, function(err) {
                alert('Failed to copy link: ' + err);
            });
        });
    </script>
</body>
</html>
