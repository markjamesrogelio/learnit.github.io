<?php
session_start();
require_once('../includes/db.php');
require_once('../includes/auth.php');

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$success = $error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST['title']);
    $folder = trim($_POST['folder']);
    $course = $_POST['course'];
    $year = $_POST['year'];
    $section = $_POST['section'];

    $stmt = $conn->prepare("INSERT INTO modules (title, folder, course, year, section) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $title, $folder, $course, $year, $section);

    if ($stmt->execute()) {
        $success = "Module added successfully!";
    } else {
        $error = "Something went wrong.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Module | LearnIT Admin</title>
    <link rel="stylesheet" href="../assets/css/admin_module.css">
</head>

<body>
    <div class="container">
        <h2>Add New Module</h2>

        <?php if ($success): ?>
            <p class="success"><?= $success ?></p>
        <?php elseif ($error): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Module Title</label>
            <input type="text" name="title" required>

            <label>Folder / Subject Name</label>
            <input type="text" name="folder" required>

            <label>Course</label>
            <input type="text" name="course" required>

            <label>Year</label>
            <input type="text" name="year" required>

            <label>Section</label>
            <input type="text" name="section" required>

            <button type="submit">Add Module</button>
        </form>
    </div>
</body>

</html>