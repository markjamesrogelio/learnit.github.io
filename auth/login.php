<?php
session_start();
require_once('../includes/db.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if ($row['status'] !== 'approved') {
            header("Location: ../index.php?error=approval");
            exit();
        } elseif (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $row['role'];

            // Redirect based on role
            if ($row['role'] === 'admin') {
                header("Location: ../admin/dashboard.php");
            } elseif ($row['role'] === 'student') {
                header("Location: ../student/dashboard.php");
            } elseif ($row['role'] === 'teacher') {
                header("Location: ../teacher/dashboard.php"); 
            } else {
                header("Location: ../index.php");
            }

            exit();
        } else {
            header("Location: ../index.php?error=invalid");
            exit();
        }
    } else {
        header("Location: ../index.php?error=notfound");
        exit();
    }
}
?>
