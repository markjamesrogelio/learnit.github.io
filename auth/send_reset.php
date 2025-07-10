<?php
date_default_timezone_set('Asia/Manila');

require_once '../includes/db.php';
require_once('../includes/PHPMailer/src/PHPMailer.php');
require_once('../includes/PHPMailer/src/SMTP.php');
require_once('../includes/PHPMailer/src/Exception.php');

use PHPMailer\PHPMailer\PHPMailer;

if (isset($_POST['email'])) {
    $email = $_POST['email'];
    $otp = rand(100000, 999999);
    $expiry = date("Y-m-d H:i:s", strtotime("+5 minutes"));
    echo "OTP expires at: " . $expiry;


    $stmt = $conn->prepare("UPDATE users SET otp_code=?, otp_expiry=? WHERE email=?");
    $stmt->bind_param("sss", $otp, $expiry, $email);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'emjaylods12@gmail.com';
        $mail->Password = 'bdwvynivkuzzhbuk';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('no-reply@learnit.com', 'LearnIT');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Your OTP Code';
        $mail->Body = "Your OTP is: <b>$otp</b><br>This expires in 5 minutes.";

        $mail->send();
        header("Location: ../index.php?step=otp&email=" . urlencode($email));
        exit;
        
    } else {
        echo "Email not found.";
    }
}
?>
