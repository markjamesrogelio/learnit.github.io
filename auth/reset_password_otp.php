<?php $email = $_GET['email'] ?? ''; ?>
<?php if (isset($_GET['reset']) && $_GET['reset'] === 'success'): ?>
  <div class="overlay-toast success">
    ✅ Your password has been successfully changed. Redirecting to login...
  </div>

  <script>
    setTimeout(() => {
      window.location.href = '../index.php';
    }, 4000);
  </script>
<?php endif; ?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reset Password</title>
  <link rel="stylesheet" href="../assets/css/reset_password_otp.css"> <!-- Link to CSS -->
</head>
<body>
  <div class="reset-container">
    <div class="reset-box">
      <h2>Reset Your Password</h2>
      <form action="update_password_otp.php" method="POST">
        <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">

        <label for="password">New Password</label>
        <input type="password" name="password" id="password" required>

        <label for="confirm">Confirm Password</label>
        <input type="password" name="confirm" id="confirm" required>

        <button type="submit">Reset Password</button>
      </form>
    </div>
  </div>
</body>
</html>
