<?php
require_once('../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id        = trim($_POST['id_number']);
    $first     = trim($_POST['first_name']);
    $last      = trim($_POST['last_name']);
    $middle    = trim($_POST['middle_name'] ?? '');
    $suffix    = trim($_POST['suffix_name'] ?? '');
    $email     = trim($_POST['email']);
    $password  = $_POST['password'];
    $confirm   = $_POST['confirm_password'];
    $department_id = intval($_POST['department_id']);
    $course    = trim($_POST['course']);
    $section   = trim($_POST['section']);
    $year      = trim($_POST['year']);
    $sex       = trim($_POST['sex']);

    if ($password !== $confirm) {
        echo json_encode(["status" => "error", "message" => "Passwords do not match."]);
        exit;
    }

    // 🔒 Check if email already exists
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo json_encode([
            "status" => "error",
            "message" => "Email is already taken. Please use a different one."
        ]);
        exit;
    }

    // ✅ Proceed to insert
    $hashed = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (
        id_number, first_name, last_name, middle_name, suffix_name, email, password,
        department_id, course, section, year, sex, role, status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'student', 'approved')");

    $stmt->bind_param("ssssssssssss", $id, $first, $last, $middle, $suffix, $email, $hashed,
        $department_id, $course, $section, $year, $sex);

    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success",
            "message" => "Student account created successfully!"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Database error: " . $stmt->error
        ]);
    }
}
?>
