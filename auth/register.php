<?php
session_start();
date_default_timezone_set('Asia/Manila');

require_once('../includes/db.php');
// Include PHPMailer manually
require_once('../includes/PHPMailer/src/PHPMailer.php');
require_once('../includes/PHPMailer/src/SMTP.php');
require_once('../includes/PHPMailer/src/Exception.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error = '';
$success = '';

// Fetch departments and courses for the form
$deptQuery = $conn->query("SELECT * FROM departments");
$departments = $deptQuery->fetch_all(MYSQLI_ASSOC);

$courseQuery = $conn->query("SELECT d.name AS department, c.name AS course FROM courses c JOIN departments d ON c.department_id = d.id ORDER BY d.name, c.name");
$coursesByDept = [];
while ($row = $courseQuery->fetch_assoc()) {
    $coursesByDept[$row['department']][] = $row['course'];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm = trim($_POST['confirm_password']);
    // Auto-detect role based on email
    if (preg_match('/\d+@dyci\.edu\.ph$/', $email)) {
        $role = 'student';
    } else {
        $role = 'teacher';
    }


    $first = $_POST['first_name'];
    $middle = $_POST['middle_name'];
    $last = $_POST['last_name'];
    $suffix = $_POST['suffix_name'];
    $course = $_POST['course'];
    $section = $_POST['section'];
    $year = $_POST['year'];
    $sex = $_POST['sex'];
    $idnum = $_POST['id_number'];

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
            ) VALUES (?, ?, ?, 'pending', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $insert->bind_param(
                "sssssssssssssss",
                $email,
                $hashed,
                $role,
                $first,
                $middle,
                $last,
                $suffix,
                $course,
                $section,
                $year,
                $sex,
                $idnum,
                $otp_code,
                $otp_expiry,
                $otp_purpose
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

                    // ✅ Redirect to verify page
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
    <title>Register | LearnIT</title>
    <link rel="stylesheet" href="../assets/css/register.css">
</head>

<body>
    <div class="overlay"></div>
    
    <div class="register-box">
        
        <h2>Register</h2>
        
        <?php if ($error): ?>
            <div class="message error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="message success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" id="registrationForm">
            <div class="grid-3">
                <input type="text" name="first_name" placeholder="First Name" required>
                <input type="text" name="middle_name" placeholder="Middle Name">
                <input type="text" name="last_name" placeholder="Last Name" required>
            </div>

            <div class="grid-2">
                <input type="email" name="email" placeholder="DYCI Email Only" required>
                <input type="text" name="suffix_name" placeholder="Suffix Name">
            </div>

            <div class="grid-2">
                <input type="password" name="password" placeholder="Password" required minlength="6">
                <input type="password" name="confirm_password" placeholder="Confirm Password" required minlength="6">
            </div>

            <div class="grid-2">
                <select name="department" id="department" required>
                    <option value="">Select Department</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?= htmlspecialchars($dept['name']) ?>"><?= htmlspecialchars($dept['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="course" id="course" required>
                    <option value="" disabled selected>Select Course</option>
                </select>
            </div>

            <div class="grid-2">
                <select name="sex" required>
                    <option value="" disabled selected>Select Sex</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Others">Others</option>
                </select>
                <select name="year" id="year" required>
                    <option value="" disabled selected>Select Year Level</option>
                    <option value="1st">1st</option>
                    <option value="2nd">2nd</option>
                    <option value="3rd">3rd</option>
                    <option value="4th">4th</option>
                </select>
            </div>

            <div class="grid-2">
                <select name="section" id="section" required>
                    <option value="" disabled selected>Select Section</option>
                </select>
                <input type="text" name="id_number" placeholder="ID Number" required>
            </div>

            <button type="submit" class="register-submit" id="submitBtn">Register</button>
        </form>

        <p>Already have an account? <a href="../index.php">Login here</a></p>
        <p>Are you a Teacher? <a href="register_teacher.php">Register here</a></p>

    </div>

    <script>
        const departmentCourses = <?= json_encode($coursesByDept) ?>;
        const yearSections = {
            "1st": ["1A", "1B", "1C"],
            "2nd": ["2A", "2B", "2C"],
            "3rd": ["3A", "3B", "3C"],
            "4th": ["4A", "4B", "4C"]
        };

        document.getElementById('department').addEventListener('change', function() {
            const courseDropdown = document.getElementById('course');
            const selectedDept = this.value;
            courseDropdown.innerHTML = '<option value="" disabled selected>Select Course</option>';

            if (departmentCourses[selectedDept]) {
                departmentCourses[selectedDept].forEach(course => {
                    const option = document.createElement('option');
                    option.value = course;
                    option.textContent = course;
                    courseDropdown.appendChild(option);
                });
            }
        });

        document.getElementById('year').addEventListener('change', function() {
            const sectionDropdown = document.getElementById('section');
            const selectedYear = this.value;
            sectionDropdown.innerHTML = '<option value="" disabled selected>Select Section</option>';

            if (yearSections[selectedYear]) {
                yearSections[selectedYear].forEach(section => {
                    const option = document.createElement('option');
                    option.value = section;
                    option.textContent = section;
                    sectionDropdown.appendChild(option);
                });
            }
        });
    </script>
</body>

</html>