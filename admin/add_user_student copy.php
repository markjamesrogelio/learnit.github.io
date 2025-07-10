<?php
require_once('../includes/db.php');
if (session_status() === PHP_SESSION_NONE) session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['role'] === 'student') {
    $id_number   = trim($_POST['id_number'] ?? '');
    $first_name  = trim($_POST['first_name'] ?? '');
    $middle_name = trim($_POST['middle_name'] ?? '');
    $last_name   = trim($_POST['last_name'] ?? '');
    $suffix_name = trim($_POST['suffix_name'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $password    = $_POST['password'] ?? '';
    $confirm     = $_POST['confirm_password'] ?? '';
    $department_id = intval($_POST['department_id'] ?? 0);
    $course      = trim($_POST['course'] ?? '');
    $year        = trim($_POST['year'] ?? '');
    $section     = trim($_POST['section'] ?? '');
    $sex         = trim($_POST['sex'] ?? '');

    // Validation
    if (!$id_number || !$first_name || !$last_name || !$email || !$password || !$confirm || !$department_id || !$course || !$year || !$section || !$sex) {
        $_SESSION['error'] = "❌ Please fill all required fields.";
        header("Location: dashboard.php?section=add");
        exit();
    }

    if ($password !== $confirm) {
        $_SESSION['error'] = "❌ Passwords do not match.";
        header("Location: dashboard.php?section=add");
        exit();
    }

    // Duplicate check
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        $_SESSION['error'] = "❌ Email already exists.";
        header("Location: dashboard.php?section=add");
        exit();
    }

    $hashed = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (id_number, first_name, middle_name, last_name, suffix_name, email, password, department_id, course, year, section, sex, role, status)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'student', 'approved')");
    $stmt->bind_param("ssssssssssss", $id_number, $first_name, $middle_name, $last_name, $suffix_name, $email, $hashed, $department_id, $course, $year, $section, $sex);

    if ($stmt->execute()) {
        $_SESSION['success'] = "✅ Student";
    } else {
        $_SESSION['error'] = "❌ Error saving student: " . $stmt->error;
    }

    header("Location: dashboard.php?section=add");
    exit();
}
