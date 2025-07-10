<?php
require_once('../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $video_id = $_POST['id'];

    // Delete the video from the database
    $stmt = $conn->prepare("DELETE FROM views WHERE id = ?");
    $stmt->bind_param("i", $video_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?>
