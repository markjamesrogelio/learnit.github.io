<?php
require_once('../includes/db.php');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id > 0) {
    $stmt = $conn->prepare("UPDATE explorer_items SET is_deleted = 0, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: dashboard.php?section=trash");
exit();
