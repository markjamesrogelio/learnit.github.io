<?php
require_once('../includes/db.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input
    $id_number   = trim($_POST['teacher_id'] ?? '');
    $first       = trim($_POST['first_name'] ?? '');
    $last        = trim($_POST['last_name'] ?? '');
    $middle      = trim($_POST['middle_name'] ?? '');
    $suffix      = trim($_POST['suffix'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $password    = $_POST['password'] ?? '';
    $confirm     = $_POST['confirm'] ?? '';
    $department  = intval($_POST['department_id'] ?? 0);
    $sex         = trim($_POST['sex'] ?? '');

    // Basic validation
    if (!$id_number || !$first || !$last || !$email || !$password || !$confirm || !$department || !$sex) {
        echo json_encode([
            "status" => "error",
            "message" => "❌ Please fill in all required fields."
        ]);
        exit();
    }

    if ($password !== $confirm) {
        echo json_encode([
            "status" => "error",
            "message" => "❌ Passwords do not match."
        ]);
        exit();
    }

    // Check for duplicate email
    $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $checkEmail->store_result();

    if ($checkEmail->num_rows > 0) {
        echo json_encode([
            "status" => "error",
            "message" => "❌ Email already exists. Please use a different one."
        ]);
        exit();
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Prepare INSERT
    $stmt = $conn->prepare("INSERT INTO users (
        id_number, first_name, last_name, middle_name, suffix_name, email, password, department_id, sex, role, status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'teacher', 'approved')");

    if (!$stmt) {
        echo json_encode([
            "status" => "error",
            "message" => "❌ SQL Error: " . $conn->error
        ]);
        exit();
    }

    // Bind and execute
    $stmt->bind_param(
        "sssssssis",
        $id_number,
        $first,
        $last,
        $middle,
        $suffix,
        $email,
        $hashedPassword,
        $department,
        $sex
    );

    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success",
            "message" => "✅ Teacher account created successfully!"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "❌ Insert failed: " . $stmt->error
        ]);
    }
}
?>
