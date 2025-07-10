<?php
session_start();
require_once('../includes/db.php');

// Ensure the user is a student
if ($_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit();
}

// Fetch student details (e.g., first name, course)
$student_id = $_SESSION['user_id'];
$student_query = "SELECT first_name, course FROM users WHERE id = ?";
$stmt = $conn->prepare($student_query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$stmt->bind_result($first_name, $course);
$stmt->fetch();
$stmt->close();

// Handle search query
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Handle category filter
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';

// Categories list
$categories = [
    'Science & Technology', 'Educational', 'Musical', 'Achievements', 
    'Culture, Arts, & Sports', 'Mathematics', 'Information Communication Technology',
    'Character Values Formation', 'Graduation', 'Academic Resources', 
    'Social Sciences', 'Events', 'The New Normal', 'Anniversary', 
    'Robotics', 'History', 'Business and Management', 'Trivia', 'Entertainment'
];

// Build the query based on search and category
$videos_query = "
    SELECT v.id, v.title, v.video_path AS link, v.category
    FROM views v
    WHERE v.course = ?
";

// Add search filter to query if search term is provided
if ($search) {
    $videos_query .= " AND v.title LIKE ?";
    $search = "%$search%";
}

// Add category filter to query if a category is selected
if ($category_filter) {
    $videos_query .= " AND v.category = ?";
}

// Prepare and execute the query
$videos_stmt = $conn->prepare($videos_query);
if ($search && $category_filter) {
    $videos_stmt->bind_param("sss", $course, $search, $category_filter);
} elseif ($search) {
    $videos_stmt->bind_param("ss", $course, $search);
} elseif ($category_filter) {
    $videos_stmt->bind_param("ss", $course, $category_filter);
} else {
    $videos_stmt->bind_param("s", $course);
}

$videos_stmt->execute();
$videos_result = $videos_stmt->get_result();
$videos_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Video Views | LearnIT</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/student_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .video-cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }

        .video-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            cursor: pointer;
            text-align: center;
        }

        .video-card:hover {
            transform: scale(1.05);
        }

        .video-thumbnail video {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px 8px 0 0;
        }

        .video-title {
            padding: 10px;
            font-weight: bold;
        }

        .filter-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .search-bar {
            width: 60%;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .category-select {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .filter-button {
            padding: 8px 15px;
            background-color: #062D91;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .filter-button:hover {
            background-color: #042a5b;
        }

        /* Highlight active navigation link */
        .active {
            background-color: #062D91;
            color: white;
        }

        .filter-message {
            margin-top: 20px;
            font-size: 16px;
            color: #333;
            font-weight: bold;
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
                <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>"><i class="fas fa-home"></i> Home</a>
                <a href="modules.php" class="<?= basename($_SERVER['PHP_SELF']) === 'modules.php' ? 'active' : '' ?>"><i class="fas fa-book"></i> Module</a>
                <a href="views.php" class="<?= basename($_SERVER['PHP_SELF']) === 'views.php' ? 'active' : '' ?>"><i class="fas fa-play-circle"></i> Views</a>
                <a href="assessment.php" class="<?= basename($_SERVER['PHP_SELF']) === 'assessment.php' ? 'active' : '' ?>"><i class="fas fa-file-alt"></i> Assessment</a>
                <a href="profile.php" class="<?= basename($_SERVER['PHP_SELF']) === 'profile.php' ? 'active' : '' ?>"><i class="fas fa-user"></i> Profile</a>
                <a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Log Out</a>
            </nav>
        </aside>

        <main class="main-content">
            <h2>🎥 Assigned Videos</h2>

            <!-- Search and filter section -->
            <div class="filter-container">
                <form method="get" action="views.php" style="width: 100%; display: flex; justify-content: space-between;">
                    <input type="text" class="search-bar" name="search" placeholder="Search by title..." value="<?= htmlspecialchars($search) ?>">
                    <select name="category" class="category-select">
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category ?>" <?= $category === $category_filter ? 'selected' : '' ?>><?= $category ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="filter-button">Filter</button>
                </form>
            </div>

            <!-- Filter message -->
            <div class="filter-message">
                <?php if ($search && $category_filter): ?>
                    <p>Showing results for <strong>"<?= htmlspecialchars($search) ?>"</strong> in the <strong>"<?= htmlspecialchars($category_filter) ?>"</strong> category.</p>
                <?php elseif ($search): ?>
                    <p>Showing results for <strong>"<?= htmlspecialchars($search) ?>"</strong>.</p>
                <?php elseif ($category_filter): ?>
                    <p>Showing results for the <strong>"<?= htmlspecialchars($category_filter) ?>"</strong> category.</p>
                <?php else: ?>
                    <p>Showing all videos for your course.</p>
                <?php endif; ?>
            </div>

            <!-- Display videos -->
            <div class="video-cards-container">
                <?php if ($videos_result->num_rows > 0): ?>
                    <?php while ($video = $videos_result->fetch_assoc()): ?>
                        <div class="video-card">
                            <a href="view_video.php?id=<?= htmlspecialchars($video['id']) ?>" class="video-link">
                                <div class="video-thumbnail">
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
        </main>
    </div>
</body>
</html>
