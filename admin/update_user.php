<?php
require_once('../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $role = trim($_POST['role']);

    // Validate inputs
    if (empty($first_name) || empty($last_name) || empty($email) || empty($role)) {
        die("❌ All fields are required.");
    }

    // Optional: check if email already exists for another user
    $check = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $check->bind_param("si", $email, $id);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        die("❌ Email is already in use by another user.");
    }

    // Update query
    $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, role = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $first_name, $last_name, $email, $role, $id);

    if ($stmt->execute()) {
        header("Location: ../admin/dashboard.php?section=accounts&updated=1");
        exit;
    } else {
        echo "❌ Error: " . htmlspecialchars($stmt->error);
    }
}
?>
