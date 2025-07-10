<?php
require_once('../includes/db.php');

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'], $_POST['status'])) {
    $id = intval($_POST['id']);
    $status = $_POST['status'];

    if (in_array($status, ['approved', 'rejected'])) {
        $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        if ($stmt->execute()) {
            echo "User status updated.";
        } else {
            echo "Update failed.";
        }
    } else {
        echo "Invalid status.";
    }
} else {
    echo "Invalid request.";
}
?>
