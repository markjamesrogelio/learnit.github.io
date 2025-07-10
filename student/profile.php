<?php
ob_start(); // start output buffering

session_start();
require_once '../includes/db.php';
require_once '../passhash.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$student_id = $_SESSION['user_id'];

$message = '';
$error = '';

if (isset($_GET['success'])) {
    $message = "Changes saved successfully.";
}

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND role = 'student'");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("Student not found or user data empty. Please check your session or DB.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Profile pic upload
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_pic']['tmp_name'];
        $fileName = $_FILES['profile_pic']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileExtension, $allowedfileExtensions)) {
            $newFileName = $student_id . '.' . $fileExtension;
            $uploadFileDir = '../uploads/profile_pics/';
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            $dest_path = $uploadFileDir . $newFileName;

            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                $pic_path = 'uploads/profile_pics/' . $newFileName;
                $pic_stmt = $conn->prepare("UPDATE users SET profile_pic=? WHERE id=?");
                $pic_stmt->bind_param("si", $pic_path, $student_id);
                if (!$pic_stmt->execute()) {
                    $error = "Failed to update profile picture in database.";
                }
            } else {
                $error = "Error moving the uploaded file.";
            }
        } else {
            $error = "Upload failed. Allowed file types: " . implode(', ', $allowedfileExtensions);
        }
    }

    // Profile info update
    if (isset($_POST['save_profile'])) {
        $first_name = trim($_POST['first_name'] ?? '');
        $middle_name = trim($_POST['middle_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $suffix_name = trim($_POST['suffix_name'] ?? '');
        $course = trim($_POST['course'] ?? '');
        $year = trim($_POST['year'] ?? '');
        $sex = trim($_POST['sex'] ?? '');

        $update_stmt = $conn->prepare("UPDATE users SET first_name=?, middle_name=?, last_name=?, suffix_name=?, course=?, year=?, sex=? WHERE id=? AND role='student'");
        $update_stmt->bind_param("sssssssi", $first_name, $middle_name, $last_name, $suffix_name, $course, $year, $sex, $student_id);

        if (!$update_stmt->execute()) {
            $error = $error ? $error . " Failed to update profile." : "Failed to update profile.";
        }
    }

    // Password change
    if (isset($_POST['change_password'])) {
        $new_password = $_POST['new_password'] ?? '';
        $retype_password = $_POST['retype_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if ($new_password !== $retype_password || $new_password !== $confirm_password) {
            $error = $error ? $error . " Passwords do not match." : "Passwords do not match.";
        } elseif (strlen($new_password) < 6) {
            $error = $error ? $error . " Password must be at least 6 characters." : "Password must be at least 6 characters.";
        } else {
            $hashed = hash_password($new_password);
            $pass_stmt = $conn->prepare("UPDATE users SET password=? WHERE id=? AND role='student'");
            $pass_stmt->bind_param("si", $hashed, $student_id);
            if (!$pass_stmt->execute()) {
                $error = $error ? $error . " Failed to change password." : "Failed to change password.";
            }
        }
    }

    // After all updates, redirect if no errors to avoid form resubmission
    if (!$error) {
        header("Location: profile.php?success=1");
        exit;
    } else {
        // Refresh user data to show updated info even if errors occurred
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Profile | LearnIT</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="../assets/css/student_modules.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        .profile-form label {
            font-weight: 600;
            display: block;
            margin-bottom: 0.25rem;
        }
        .profile-form input, .profile-form select {
            width: 100%;
            padding: 0.5rem;
            margin-bottom: 1rem;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        .profile-form button {
            background-color: #007bff;
            color: white;
            padding: 0.6rem 1.2rem;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            margin-right: 1rem;
        }
        .profile-form button:hover {
            background-color: #0056b3;
        }
        .message {
            padding: 0.75rem;
            margin-bottom: 1rem;
            border-radius: 6px;
            font-weight: 600;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .profile-pic {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #007bff;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <aside class="sidebar">
        <div class="profile-section">
            <img src="../<?= htmlspecialchars($user['profile_pic'] ?: 'assets/images/profile.png') ?>" alt="Profile" class="profile-pic" />
            <h4><?= htmlspecialchars($user['first_name'] ?? '') ?></h4>
        </div>
        <nav class="nav-links">
            <a href="dashboard.php"><i class="fas fa-home"></i> Home</a>
            <a href="modules.php"><i class="fas fa-book"></i> Module</a>
            <a href="views.php"><i class="fas fa-play-circle"></i> Views</a>
            <a href="assessment.php"><i class="fas fa-file-alt"></i> Assessment</a>
            <a href="profile.php" class="active"><i class="fas fa-user"></i> Profile</a>
            <a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Log Out</a>
        </nav>
    </aside>

    <main class="main-content">
        <h2>Profile Settings</h2>

        <?php if ($message): ?>
            <div class="message success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="message error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" action="profile.php" class="profile-form" enctype="multipart/form-data" novalidate>
            <label for="profile_pic">Profile Picture</label>
            <input type="file" name="profile_pic" id="profile_pic" accept="image/*" />

            <label for="first_name">First Name</label>
            <input id="first_name" type="text" name="first_name" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" required />

            <label for="middle_name">Middle Name</label>
            <input id="middle_name" type="text" name="middle_name" value="<?= htmlspecialchars($user['middle_name'] ?? '') ?>" />

            <label for="last_name">Last Name</label>
            <input id="last_name" type="text" name="last_name" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" required />

            <label for="suffix_name">Suffix Name</label>
            <input id="suffix_name" type="text" name="suffix_name" value="<?= htmlspecialchars($user['suffix_name'] ?? '') ?>" />

            <label for="course">Course</label>
            <input id="course" type="text" name="course" value="<?= htmlspecialchars($user['course'] ?? '') ?>" required />

            <label for="year">Year</label>
            <input id="year" type="number" name="year" value="<?= htmlspecialchars($user['year'] ?? '') ?>" min="1" max="5" required />

            <label for="sex">Sex</label>
            <select id="sex" name="sex" required>
                <option value="" <?= ($user['sex'] ?? '') === '' ? 'selected' : '' ?>>Select sex</option>
                <option value="Male" <?= ($user['sex'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
                <option value="Female" <?= ($user['sex'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
                <option value="Other" <?= ($user['sex'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
            </select>

            <button type="submit" name="save_profile">Save Profile</button>

            <h3>Change Password</h3>

            <label for="new_password">Create New Password</label>
            <input id="new_password" type="password" name="new_password" autocomplete="new-password" />

            <label for="retype_password">Retype New Password</label>
            <input id="retype_password" type="password" name="retype_password" autocomplete="new-password" />

            <label for="confirm_password">Confirm New Password</label>
            <input id="confirm_password" type="password" name="confirm_password" autocomplete="new-password" />

            <button type="submit" name="change_password">Change Password</button>
        </form>
    </main>
</div>

</body>
</html>

