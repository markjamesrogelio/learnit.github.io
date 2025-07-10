<?php
require_once('../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? '';
    $reference_id = $_POST['reference_id'] ?? '';
    $name = $_POST['name'] ?? '';

    // Basic validation
    if (empty($type) || empty($reference_id) || empty($name)) {
        die("Missing required data.");
    }

    // Insert into archive table
    $stmt = $conn->prepare("INSERT INTO archive (reference_id, type, name, date_archived) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iss", $reference_id, $type, $name);

    if ($stmt->execute()) {
        // Optionally, you may delete from the original table here:
        switch ($type) {
            case 'section':
                $conn->query("DELETE FROM sections WHERE id = $reference_id");
                break;
            case 'course':
                $conn->query("DELETE FROM courses WHERE id = $reference_id");
                break;
            case 'department':
                $conn->query("DELETE FROM departments WHERE id = $reference_id");
                break;
        }

        header("Location: dashboard.php?section={$type}s"); // Redirect to the appropriate section
        exit();
    } else {
        echo "Failed to archive.";
    }
} else {
    echo "Invalid request.";
}
?>
