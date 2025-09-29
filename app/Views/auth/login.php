<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="<?= base_url('css/login.css') ?>">
  
</head>
<body>
  <div class="login-container">
    <img src="<?= base_url('images/clearpaylogo.png') ?>" alt="Logo" class="logo">
    <h3>ClearPay</h3>
    <h2>Welcome Back</h2>
    <p class="description">Please sign in to your account</p>
    
    <div class="error-message" id="errorMessage"></div>
    
    <form action="/login" method="post" autocomplete="off">
        <!-- Username field -->
        <input type="text" name="username" placeholder="Username" autocomplete="username" required>

      <!-- Password field with toggle -->
      <div class="password-container">
        <input type="password" id="password" name="password" placeholder="Password" autocomplete="new-password" required>

        <!-- Eye open icon -->
        <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
          <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
          <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
        </svg>

        <!-- Eye closed icon -->
        <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16" style="display: none;">
          <path d="m10.79 12.912-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7.029 7.029 0 0 0 2.79-.588zM5.21 3.088A7.028 7.028 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474L5.21 3.089z"/>
          <path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829l-2.83-2.829zm4.95.708-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829zm3.171 6-12-12 .708-.708 12 12-.708.708z"/>
        </svg>
      </div>

      <!-- Buttons -->
      <div class="button-group">
        <button type="submit" class="btn-primary">Sign In</button>
      </div>
    </form>
    
    <!-- Links Section -->
    <div class="links-section">
      <a href="/forgot-password">Forgot Password?</a>
      <a href="/register">Create Account</a>
    </div>
  </div>

  <!-- Clear form on page load -->
  <script>
    // Clear form fields on page load to prevent auto-fill
    window.addEventListener('load', function() {
      const form = document.querySelector('form');
      if (form) {
        form.reset();
      }
      
      // Clear specific fields
      const usernameField = document.querySelector('input[name="username"]');
      const passwordField = document.querySelector('input[name="password"]');
      
      if (usernameField) usernameField.value = '';
      if (passwordField) passwordField.value = '';
    });
  </script>

  <!-- External JS -->
  <script src="<?= base_url('js/login.js') ?>"></script>
</body>
</html>
