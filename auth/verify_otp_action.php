<?php
require_once '../includes/db.php';
date_default_timezone_set('Asia/Manila'); // Always set this!

if (isset($_POST['email'], $_POST['otp'])) {
    $email = $_POST['email'];
    $otp = $_POST['otp'];

    // Get user by email + OTP
    $stmt = $conn->prepare("SELECT email, otp_code, otp_expiry FROM users WHERE email=? AND otp_code=?");
    $stmt->bind_param("ss", $email, $otp);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $now = new DateTime();
        $expiry = new DateTime($user['otp_expiry']);

        // Debug output (optional)
        echo "Current time: " . $now->format('Y-m-d H:i:s') . "<br>";
        echo "OTP expiry: " . $expiry->format('Y-m-d H:i:s') . "<br>";

        if ($expiry > $now) {
            // OTP is valid
            header("Location: reset_password_otp.php?email=" . urlencode($email));
            exit();
        } else {
            echo "OTP expired. <a href='verify_otp.php?email=" . urlencode($email) . "'>Try again</a>";
        }
    } else {
        echo "Invalid OTP. <a href='verify_otp.php?email=" . urlencode($email) . "'>Try again</a>";
    }
}
?>
