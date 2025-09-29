<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up</title>
  <link rel="stylesheet" href="<?= base_url('css/register.css') ?>">

</head>
<body>
  <div class="register-container">
    <h2>Create Account</h2>
    <p class="description">Join us today! Please fill in your information</p>
    
    <div class="success-message" id="successMessage"></div>
    <div class="error-message" id="errorMessage"></div>
    
    <form id="registerForm" action="/register" method="post" autocomplete="off">

        <input type="text" id="fullname" name="fullname" placeholder="Full Name" autocomplete="name" required>
        <input type="text" id="username" name="username" placeholder="Username" autocomplete="username" required>
        <input type="email" id="email" name="email" placeholder="Email" autocomplete="email" required>

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
      
      <!-- Password feedback -->
      <div id="passwordFeedback" style="font-size: 12px; margin-bottom: 15px; text-align: left; line-height: 1.4;"></div>
      <div class="confirm-password-container">
        <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm Password" autocomplete="new-password" required>

        <!-- Eye open icon -->
        <svg id="confirmEyeOpen" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
          <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
          <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
        </svg>

        <!-- Eye closed icon -->
        <svg id="confirmEyeClosed" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16" style="display: none;">
          <path d="m10.79 12.912-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7.029 7.029 0 0 0 2.79-.588zM5.21 3.088A7.028 7.028 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474L5.21 3.089z"/>
          <path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829l-2.83-2.829zm4.95.708-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829zm3.171 6-12-12 .708-.708 12 12-.708.708z"/>
        </svg>
      </div>
      
      <!-- Password match feedback -->
      <div id="passwordMatchFeedback" style="font-size: 12px; margin-bottom: 20px; text-align: left;"></div>

      <!-- Buttons -->
      <div class="button-group">
        <button type="submit" class="btn-primary">Create Account</button>
      </div>
    </form>
    
    <!-- Links Section -->
    <div class="links-section">
      <span>Already have an account? <a href="/">Sign In</a></span>
    </div>
  </div>

  <!-- Clear form on page load -->
  <script>
    // Clear form fields on page load to prevent auto-fill
    window.addEventListener('load', function() {
      const form = document.getElementById('registerForm');
      if (form) {
        form.reset();
      }
      
      // Clear specific fields
      const fields = ['fullname', 'username', 'email', 'password', 'confirmPassword'];
      fields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) field.value = '';
      });
      
      // Clear feedback areas
      const passwordFeedback = document.getElementById('passwordFeedback');
      const passwordMatchFeedback = document.getElementById('passwordMatchFeedback');
      if (passwordFeedback) passwordFeedback.innerHTML = '';
      if (passwordMatchFeedback) passwordMatchFeedback.innerHTML = '';
    });
  </script>

  <!-- External JS -->
  <script src="<?= base_url('js/register.js') ?>"></script>
</body>
</html>
