<?php
session_start();
date_default_timezone_set('Asia/Manila');

require_once('../includes/db.php');

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $otp = trim($_POST['otp']);

    // ✅ Check OTP details
    $stmt = $conn->prepare("SELECT otp_code, otp_expiry, otp_purpose FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($dbOtp, $dbExpiry, $purpose);

    if ($stmt->fetch()) {
        $stmt->close(); // ✅ CLOSE BEFORE OTHER QUERIES

        if (new DateTime() > new DateTime($dbExpiry)) {
            $error = "❌ OTP expired.";
        } elseif ($otp !== $dbOtp) {
            $error = "❌ Incorrect OTP.";
        } else {
            // ✅ Clear OTP fields
            $clearOtp = $conn->prepare("UPDATE users SET otp_code = NULL, otp_expiry = NULL, otp_purpose = NULL WHERE email = ?");
            $clearOtp->bind_param("s", $email);
            $clearOtp->execute();
            $clearOtp->close();

            if ($purpose === 'register') {
                // ✅ Approve account
                $approve = $conn->prepare("UPDATE users SET status = 'approved' WHERE email = ?");
                $approve->bind_param("s", $email);
                $approve->execute();
                $approve->close();

                $success = "✅ Account verified! You can now login.";
            } elseif ($purpose === 'reset') {
                // ✅ Redirect to reset password
                header("Location: reset_password.php?email=" . urlencode($email));
                exit;
            }
        }
    } else {
        $stmt->close();
        $error = "❌ No pending OTP found for this email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Verify OTP | LearnIT</title>
    <link rel="stylesheet" href="../assets/css/register.css">
</head>

<body>
    <div class="overlay"></div>
    <div class="register-box">
        <h2>Verify OTP</h2>

        <?php if ($error): ?>
            <div class="message error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="message success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($_GET['email'] ?? '') ?>" required>
            <input type="text" name="otp" placeholder="Enter OTP" required>
            <button type="submit" style="
                    background-color: #4CAF50;
                    color: white;
                    padding: 12px 20px;
                    border: none;
                    border-radius: 8px;
                    cursor: pointer;
                    font-size: 16px;
                    transition: background-color 0.3s;
                ">
                Verify OTP
            </button>

        </form>

        <p>Already verified? <a href="../index.php">Login here</a></p>
    </div>
</body>

</html>