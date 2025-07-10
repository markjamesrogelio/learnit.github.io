<?php
session_start();
date_default_timezone_set('Asia/Manila');

require_once('../includes/db.php');
require_once('../includes/PHPMailer/src/PHPMailer.php');
require_once('../includes/PHPMailer/src/SMTP.php');
require_once('../includes/PHPMailer/src/Exception.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$deptQuery = $conn->query("SELECT * FROM departments");
$departments = $deptQuery->fetch_all(MYSQLI_ASSOC);

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm = trim($_POST['confirm_password']);
    $role = 'teacher';

    $first = $_POST['first_name'];
    $middle = $_POST['middle_name'];
    $last = $_POST['last_name'];
    $suffix = $_POST['suffix_name'];
    $sex = $_POST['sex'];
    $department = $_POST['department'];

    if (!preg_match('/^[a-zA-Z0-9._%+-]+@dyci\.edu\.ph$/', $email)) {
        $error = "Only @dyci.edu.ph emails are allowed.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Email already exists.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $otp_code = rand(100000, 999999);
            $otp_expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));
            $otp_purpose = 'register';

            $insert = $conn->prepare("INSERT INTO users (
                email, password, role, status, first_name, middle_name, last_name, suffix_name,
                course, section, year, sex, id_number, otp_code, otp_expiry, otp_purpose
            ) VALUES (?, ?, ?, 'pending', ?, ?, ?, ?, NULL, NULL, NULL, ?, NULL, ?, ?, ?)");

            $insert->bind_param(
                "sssssssssss",
                $email, $hashed, $role,
                $first, $middle, $last, $suffix,
                $sex,
                $otp_code, $otp_expiry, $otp_purpose
            );

            if ($insert->execute()) {
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'easece.dyci@gmail.com';
                    $mail->Password = 'prvjjfwoklhtmvqs';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('easece.dyci@gmail.com', 'EAS-CE System');
                    $mail->addAddress($email);

                    $mail->isHTML(true);
                    $mail->Subject = 'Your LearnIT OTP Code';
                    $mail->Body = "<p>Your OTP code is: <strong>$otp_code</strong>. It expires in 10 minutes.</p>";

                    $mail->send();

                    header("Location: verify_otp.php?email=" . urlencode($email));
                    exit;
                } catch (Exception $e) {
                    $error = "Failed to send OTP email. Mailer Error: {$mail->ErrorInfo}";
                }
            } else {
                $error = "Something went wrong during registration.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Teacher Register | LearnIT</title>
    <link rel="stylesheet" href="../assets/css/register.css">
</head>

<body>
    <div class="overlay"></div>
    <div class="register-box">
        <h2>Teacher Registration</h2>

        <?php if ($error): ?>
            <div class="message error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="message success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="grid-3">
                <input type="text" name="first_name" placeholder="First Name" required>
                <input type="text" name="middle_name" placeholder="Middle Name">
                <input type="text" name="last_name" placeholder="Last Name" required>
            </div>

            <div class="grid-2">
                <input type="email" name="email" placeholder="Email" required>
                <input type="text" name="suffix_name" placeholder="Suffix Name">
            </div>

            <div class="grid-2">
                <input type="password" name="password" placeholder="Password" required minlength="6">
                <input type="password" name="confirm_password" placeholder="Confirm Password" required minlength="6">
            </div>

            <div class="grid-2">
                <select name="department" required>
                    <option value="">Select Department</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?= htmlspecialchars($dept['name']) ?>"><?= htmlspecialchars($dept['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="sex" required>
                    <option value="" disabled selected>Select Sex</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Others">Others</option>
                </select>
            </div>

            <button type="submit" class="register-submit">Register</button>
        </form>

        <p>Are you a student? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>
