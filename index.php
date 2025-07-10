<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>LearnIT | Welcome</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>
  <div class="landing">
    <div class="overlay" id="pageOverlay"></div>
    <div class="login-button">
      <button onclick="toggleLoginModal()">Login</button>
    </div>
    <div class="content">
      <h1>Learn<span>IT</span></h1>
      <p>Learning. Educational. Activities. Resources and Networking. for Integrated Teaching.</p>
    </div>
    <div class="footer">
      <img src="assets/images/yanga.png" alt="DYCI Logo">
    </div>
  </div>

  <!-- LOGIN MODAL -->
  <div class="login-modal" id="loginModal">
    <div class="login-box">
      <span class="close-btn" onclick="toggleLoginModal()">&times;</span>
      <h2>Login</h2>
      <div id="errorContainer"></div>
      <form method="POST" action="auth/login.php">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" class="login-submit">Login</button>
      </form>
      <div class="login-footer">
        <p>Don't have an account? <a href="auth/register.php">Register</a></p>
        <p><a href="#" onclick="openModal('forgotModal')">Forgot Password?</a></p>
      </div>
    </div>
  </div>

  <!-- FORGOT PASSWORD MODAL -->
  <div id="forgotModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal('forgotModal')">&times;</span>
      <h3>Forgot Password</h3>
      <form action="auth/send_reset.php" method="POST">
        <label for="email">Enter your email:</label>
        <input type="email" name="email" required>
        <button type="submit">Send OTP</button>
      </form>
    </div>
  </div>

  <!-- OTP MODAL -->
  <div id="otpModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal('otpModal')">&times;</span>
      <h3>Enter OTP</h3>
      <form action="auth/verify_otp_action.php" method="POST">
        <input type="hidden" name="email" value="<?= $_GET['email'] ?? '' ?>">
        <label for="otp">Enter OTP:</label>
        <input type="text" name="otp" required>
        <button type="submit">Verify OTP</button>
      </form>
    </div>
  </div>

  <!-- RESET PASSWORD MODAL -->
  <div id="resetModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal('resetModal')">&times;</span>
      <h3>Reset Your Password</h3>
      <form action="auth/update_password.php" method="POST">
        <input type="hidden" name="email" value="<?= $_GET['email'] ?? '' ?>">
        <input type="password" name="password" placeholder="New Password" required>
        <input type="password" name="confirm" placeholder="Confirm Password" required>
        <button type="submit">Reset Password</button>
      </form>
    </div>
  </div>

  <?php if (isset($_GET['reset']) && $_GET['reset'] === 'success'): ?>
    <div class="overlay-toast success">
      ✅ Your password has been successfully changed. Redirecting to login...
    </div>
    <script>
      setTimeout(() => {
        window.location.href = 'index.php';
      }, 4000);
    </script>
  <?php endif; ?>

  <script>
    function toggleLoginModal() {
      const modal = document.getElementById('loginModal');
      const overlay = document.getElementById('pageOverlay');
      modal.classList.toggle('show');
      if (overlay) overlay.style.display = modal.classList.contains('show') ? 'block' : 'none';
    }

    function openModal(id) {
      document.getElementById(id).style.display = 'block';
    }

    function closeModal(id) {
      document.getElementById(id).style.display = 'none';
    }

    function displayError(errorType) {
      const errorContainer = document.getElementById('errorContainer');
      let errorMessage = '';
      switch (errorType) {
        case 'notfound': errorMessage = 'No account found with that email.'; break;
        case 'invalid': errorMessage = 'Incorrect password.'; break;
        case 'approval': errorMessage = 'Your account is still pending approval.'; break;
      }
      if (errorMessage) {
        errorContainer.innerHTML = `<div class="error-message">${errorMessage}</div>`;
      }
    }

    const urlParams = new URLSearchParams(window.location.search);
    const step = urlParams.get('step');
    if (urlParams.has('error')) {
      toggleLoginModal();
      displayError(urlParams.get('error'));
    }
    if (step === 'otp') openModal('otpModal');
    if (step === 'reset') openModal('resetModal');

    document.getElementById('pageOverlay').addEventListener('click', toggleLoginModal);
    document.querySelector('.login-box').addEventListener('click', function(e) {
      e.stopPropagation();
    });

    window.onclick = function(event) {
      ['forgotModal', 'otpModal', 'resetModal'].forEach(id => {
        const modal = document.getElementById(id);
        if (event.target === modal) modal.style.display = 'none';
      });
    }
  </script>
</body>
</html>
