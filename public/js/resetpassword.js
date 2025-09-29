// Password toggle functionality
    function togglePassword(inputId, eyeOpenId, eyeClosedId) {
      const input = document.getElementById(inputId);
      const eyeOpen = document.getElementById(eyeOpenId);
      const eyeClosed = document.getElementById(eyeClosedId);
      
      if (input.type === 'password') {
        input.type = 'text';
        eyeOpen.style.display = 'none';
        eyeClosed.style.display = 'block';
      } else {
        input.type = 'password';
        eyeOpen.style.display = 'block';
        eyeClosed.style.display = 'none';
      }
    }
    
    document.getElementById('eyeOpen1').addEventListener('click', () => togglePassword('password', 'eyeOpen1', 'eyeClosed1'));
    document.getElementById('eyeClosed1').addEventListener('click', () => togglePassword('password', 'eyeOpen1', 'eyeClosed1'));
    document.getElementById('eyeOpen2').addEventListener('click', () => togglePassword('confirmPassword', 'eyeOpen2', 'eyeClosed2'));
    document.getElementById('eyeClosed2').addEventListener('click', () => togglePassword('confirmPassword', 'eyeOpen2', 'eyeClosed2'));

    // Form submission
    document.getElementById('resetPasswordForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      
      const password = document.getElementById('password').value;
      const confirmPassword = document.getElementById('confirmPassword').value;
      const token = document.querySelector('input[name="token"]').value;
      const submitBtn = document.getElementById('submitBtn');
      const successMsg = document.getElementById('successMessage');
      const errorMsg = document.getElementById('errorMessage');
      
      // Hide previous messages
      successMsg.style.display = 'none';
      errorMsg.style.display = 'none';
      
      // Validation
      if (!password || !confirmPassword) {
        showError('Please fill in all fields.');
        return;
      }
      
      // Password complexity validation
      if (password.length < 8) {
        showError('Password must be at least 8 characters long.');
        return;
      }

      if (!/[A-Z]/.test(password)) {
        showError('Password must contain at least one uppercase letter.');
        return;
      }

      if (!/[a-z]/.test(password)) {
        showError('Password must contain at least one lowercase letter.');
        return;
      }

      if (!/[0-9]/.test(password)) {
        showError('Password must contain at least one number.');
        return;
      }

      if (!/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\?]/.test(password)) {
        showError('Password must contain at least one special character.');
        return;
      }
      
      if (password !== confirmPassword) {
        showError('Passwords do not match.');
        return;
      }
      
      // Show loading state
      submitBtn.classList.add('loading');
      submitBtn.textContent = 'Resetting...';
      
      try {
        const formData = new FormData();
        formData.append('token', token);
        formData.append('password', password);
        formData.append('confirmPassword', confirmPassword);
        
        const response = await fetch('/reset-password', {
          method: 'POST',
          body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
          showSuccess(result.message);
          // Redirect to login after 2 seconds
          setTimeout(() => {
            window.location.href = '/';
          }, 2000);
        } else {
          showError(result.message);
        }
        
      } catch (error) {
        console.error('Error:', error);
        showError('An error occurred. Please try again.');
      } finally {
        // Reset button state
        submitBtn.classList.remove('loading');
        submitBtn.textContent = 'Reset Password';
      }
    });
    
    function showSuccess(message) {
      const successMsg = document.getElementById('successMessage');
      successMsg.textContent = message;
      successMsg.style.display = 'block';
    }
    
    function showError(message) {
      const errorMsg = document.getElementById('errorMessage');
      errorMsg.textContent = message;
      errorMsg.style.display = 'block';
    }
    
    // Clear form fields on page load to prevent auto-fill
    window.addEventListener('load', function() {
      const form = document.getElementById('resetPasswordForm');
      if (form) {
        // Clear password fields only (keep the token)
        const passwordField = document.getElementById('password');
        const confirmPasswordField = document.getElementById('confirmPassword');
        
        if (passwordField) passwordField.value = '';
        if (confirmPasswordField) confirmPasswordField.value = '';
      }
    });