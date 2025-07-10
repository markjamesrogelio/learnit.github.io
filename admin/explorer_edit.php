<?php
require_once('../includes/db.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    exit;
}

$id = intval($_POST['id']);
$name = $_POST['name'];
$color = $_POST['color'] ?? '#87cefa';
$description = $_POST['description'] ?? '';
$start = $_POST['start_date'] ?? null;
$end = $_POST['end_date'] ?? null;
$availability = $_POST['availability'] ?? 'Published';

$stmt = $conn->prepare("UPDATE explorer_items SET name=?, color=?, description=?, start_date=?, end_date=?, availability=? WHERE id=?");
$stmt->bind_param("ssssssi", $name, $color, $description, $start, $end, $availability, $id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => $stmt->error]);
}
