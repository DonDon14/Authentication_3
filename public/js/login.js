const password = document.getElementById('password');
const eyeOpen = document.getElementById('eyeOpen');
const eyeClosed = document.getElementById('eyeClosed');

function togglePassword() {
  if (password.type === 'password') {
    password.type = 'text';
    eyeOpen.style.display = 'none';
    eyeClosed.style.display = 'block';
  } else {
    password.type = 'password';
    eyeOpen.style.display = 'block';
    eyeClosed.style.display = 'none';
  }
}
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


eyeOpen.addEventListener('click', togglePassword);
eyeClosed.addEventListener('click', togglePassword);

// Handle login form submission
document.addEventListener('DOMContentLoaded', function() {
  const loginForm = document.querySelector('form');
  
  if (loginForm) {
    loginForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      
      console.log('Login form submitted!');
      
      const username = document.querySelector('input[name="username"]').value.trim();
      const password = document.querySelector('input[name="password"]').value.trim();
      
      console.log('Username:', username);
      console.log('Password length:', password.length);
      
      if (!username || !password) {
        alert('Please fill in all fields');
        return;
      }
      
      const formData = new FormData();
      formData.append('username', username);
      formData.append('password', password);
      
      try {
        console.log('Sending login request...');
        const response = await fetch('/login', {
          method: 'POST',
          body: formData
        });
        
        console.log('Response status:', response.status);
        const result = await response.json();
        console.log('Server response:', result);
        
        if (result.success) {
          alert(result.message);
          window.location.href = result.redirect;
        } else {
          alert(result.message);
        }
      } catch (error) {
        console.error('Login error:', error);
        alert('An error occurred during login');
      }
    });
  }
});
