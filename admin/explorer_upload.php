<?php
require_once('../includes/db.php');

header('Content-Type: application/json');

$section_id = intval($_POST['section_id'] ?? 0);
$parent_id = intval($_POST['parent_id'] ?? 0);
$description = $_POST['description'] ?? '';
$availability = $_POST['availability'] ?? 'Published';

if (!isset($_FILES['file']) || $section_id === 0) {
    echo json_encode(['status' => 'error', 'message' => 'No file uploaded or section missing.']);
    exit;
}

$file = $_FILES['file'];
$upload_dir = '../uploads/';
$name = basename($file['name']);
$target_path = $upload_dir . $name;

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

if (move_uploaded_file($file['tmp_name'], $target_path)) {
    $stmt = $conn->prepare("INSERT INTO explorer_items (name, type, parent_id, section_id, description, availability, created_at) VALUES (?, 'file', ?, ?, ?, ?, NOW())");
    $stmt->bind_param("siiss", $name, $parent_id, $section_id, $description, $availability);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to move uploaded file.']);
}
?>
