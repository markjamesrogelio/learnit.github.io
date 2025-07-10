<?php
session_start();
require_once('../includes/db.php');

if ($_SESSION['role'] !== 'teacher') {
    header("Location: ../index.php");
    exit();
}

$module_id = $_GET['id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $module_name = $_POST['module_name'];
    $stmt = $conn->prepare("UPDATE modules SET module_name = ? WHERE module_id = ? AND user_id = ?");
    $stmt->bind_param("sii", $module_name, $module_id, $_SESSION['user_id']);
    if ($stmt->execute()) {
        header("Location: dashboard.php?section=modules&msg=updated");
        exit();
    } else {
        echo "❌ Error: " . $conn->error;
    }
}

$stmt = $conn->prepare("SELECT * FROM modules WHERE module_id = ? AND user_id = ?");
$stmt->bind_param("ii", $module_id, $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($module_name);
if ($stmt->fetch()) {
    $stmt->close();
} else {
    echo "Module not found or you don't have permission to edit this module.";
    exit();
}
?>

<h2>Edit Module</h2>
<form method="POST">
    <label>Module Name:</label><br>
    <input type="text" name="module_name" value="<?= htmlspecialchars($module_name) ?>" required><br><br>

    <button type="submit">Update Module</button>
</form>
