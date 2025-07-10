<?php
session_start();
require_once('../includes/db.php');

if ($_SESSION['role'] !== 'teacher') {
    header("Location: ../index.php");
    exit();
}

$module_id = $_GET['id'];
$stmt = $conn->prepare("DELETE FROM modules WHERE module_id = ? AND user_id = ?");
$stmt->bind_param("ii", $module_id, $_SESSION['user_id']);
if ($stmt->execute()) {
    header("Location: dashboard.php?section=modules&msg=deleted");
    exit();
} else {
    echo "❌ Error: " . $conn->error;
}
?>
