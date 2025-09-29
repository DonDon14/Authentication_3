document.getElementById('forgotPasswordForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      
      const email = document.getElementById('email').value.trim();
      const submitBtn = document.getElementById('submitBtn');
      const successMsg = document.getElementById('successMessage');
      const errorMsg = document.getElementById('errorMessage');
      
      // Hide previous messages
      successMsg.style.display = 'none';
      errorMsg.style.display = 'none';
      
      if (!email) {
        showError('Please enter your email address.');
        return;
      }
      
      // Email validation
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(email)) {
        showError('Please enter a valid email address.');
        return;
      }
      
      // Show loading state
      submitBtn.classList.add('loading');
      submitBtn.textContent = 'Sending...';
      
      try {
        const formData = new FormData();
        formData.append('email', email);
        
        const response = await fetch('/forgot-password', {
          method: 'POST',
          body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
          showSuccess(result.message);
          document.getElementById('forgotPasswordForm').style.display = 'none';
        } else {
          showError(result.message);
        }
        
      } catch (error) {
        console.error('Error:', error);
        showError('An error occurred. Please try again.');
      } finally {
        // Reset button state
        submitBtn.classList.remove('loading');
        submitBtn.textContent = 'Send Reset Link';
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