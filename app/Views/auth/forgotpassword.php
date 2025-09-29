<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password</title>
  <link rel="stylesheet" href="<?= base_url('css/forgotpassword.css') ?>">
</head>
<body>
  <div class="forgot-password-container">
    <div class="icon">🔐</div>
    <h2>Forgot Password</h2>
    <p class="description">
      Enter your email address and we'll send you a link to reset your password.
    </p>

    <div id="successMessage" class="success-message">
      Password reset link has been sent to your email address. Please check your inbox.
    </div>

    <div id="errorMessage" class="error-message">
      Error message will appear here.
    </div>

    <form id="forgotPasswordForm">
      <input 
        type="email" 
        id="email" 
        name="email" 
        placeholder="Enter your email address" 
        required
      >
      
      <div class="button-group">
        <button type="submit" id="submitBtn" class="btn-primary">
          Send Reset Link
        </button>
        <button type="button" onclick="location.href='/'" class="btn-secondary">
          Back to Login
        </button>
      </div>
    </form>

    <p>
      <a href="/register" class="back-link">Don't have an account? Sign up</a>
    </p>
  </div>

  <script src="<?= base_url('js/forgotpassword.js') ?>"></script>
</body>
</html>