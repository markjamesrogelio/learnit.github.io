<?php
require_once('../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $cat = $_POST['category'];

    $video_name = $_FILES['video']['name'];
    $video_tmp = $_FILES['video']['tmp_name'];
    $upload_path = "../uploads/videos/" . basename($video_name);

    if (!is_dir("../uploads/videos")) {
        mkdir("../uploads/videos", 0777, true);
    }

    if (move_uploaded_file($video_tmp, $upload_path)) {
        $stmt = $conn->prepare("INSERT INTO views (title, description, category, video_path) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $title, $desc, $cat, $upload_path);

        if ($stmt->execute()) {
            echo "View added successfully.";
        } else {
            echo "Error saving view: " . $stmt->error;
        }
    } else {
        echo "Video upload failed.";
    }
}
?>
