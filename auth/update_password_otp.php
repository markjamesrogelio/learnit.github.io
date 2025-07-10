<?php
require_once '../includes/db.php';

if (isset($_POST['email'], $_POST['password'], $_POST['confirm'])) {
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $confirm = $_POST['confirm'];

    if ($pass !== $confirm) {
        die("Passwords do not match. <a href='reset_password_form.php?email=" . urlencode($email) . "'>Try again</a>");
    }

    $hashed = password_hash($pass, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("UPDATE users SET password=?, otp_code=NULL, otp_expiry=NULL WHERE email=?");
    $stmt->bind_param("ss", $hashed, $email);
    $stmt->execute();

    header("Location: reset_password_otp.php?reset=success");
    exit();
    }
?>
