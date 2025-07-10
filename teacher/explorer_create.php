<?php
require_once('../includes/db.php');

header('Content-Type: application/json');

$name = $_POST['name'] ?? '';
$parent_id = intval($_POST['parent_id'] ?? 0);
$section_id = intval($_POST['section_id'] ?? 0);

if (empty($name) || $section_id === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Missing folder name or section.']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO explorer_items (name, type, parent_id, section_id, created_at) VALUES (?, 'folder', ?, ?, NOW())");
$stmt->bind_param("sii", $name, $parent_id, $section_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to create folder.']);
}
?>
