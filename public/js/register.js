const password = document.getElementById('password');
const confirmPassword = document.getElementById('confirmPassword');

const eyeOpen = document.getElementById('eyeOpen');
const eyeClosed = document.getElementById('eyeClosed');

const confirmEyeOpen = document.getElementById('confirmEyeOpen');
const confirmEyeClosed = document.getElementById('confirmEyeClosed');

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

// Password validation function
function validatePassword(password) {
  const errors = [];
  
  if (password.length < 8) {
    errors.push('• At least 8 characters');
  }
  
  if (!/[A-Z]/.test(password)) {
    errors.push('• At least one uppercase letter (A-Z)');
  }
  
  if (!/[a-z]/.test(password)) {
    errors.push('• At least one lowercase letter (a-z)');
  }
  
  if (!/[0-9]/.test(password)) {
    errors.push('• At least one number (0-9)');
  }
  
  if (!/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\?]/.test(password)) {
    errors.push('• At least one special character (!@#$%^&*...)');
  }
  
  return errors;
}

// Real-time password validation feedback
function updatePasswordFeedback() {
  const passwordValue = password.value;
  const passwordFeedback = document.getElementById('passwordFeedback');
  
  if (!passwordFeedback) return;
  
  if (passwordValue.length === 0) {
    passwordFeedback.innerHTML = '';
    return;
  }
  
  const errors = validatePassword(passwordValue);
  
  if (errors.length === 0) {
    passwordFeedback.innerHTML = '<span style="color: #28a745;">✓ Password meets all requirements</span>';
  } else {
    passwordFeedback.innerHTML = '<span style="color: #dc3545;">Password requirements:</span><br>' + errors.join('<br>');
  }
}

// Real-time password match validation
function updatePasswordMatchFeedback() {
  const passwordValue = password.value;
  const confirmPasswordValue = confirmPassword.value;
  const matchFeedback = document.getElementById('passwordMatchFeedback');
  
  if (!matchFeedback || confirmPasswordValue.length === 0) {
    if (matchFeedback) matchFeedback.innerHTML = '';
    return;
  }
  
  if (passwordValue === confirmPasswordValue) {
    matchFeedback.innerHTML = '<span style="color: #28a745;">✓ Passwords match</span>';
  } else {
    matchFeedback.innerHTML = '<span style="color: #dc3545;">✗ Passwords do not match</span>';
  }
}

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
function toggleConfirmPassword() {
  if (confirmPassword.type === 'password') {
    confirmPassword.type = 'text';
    confirmEyeOpen.style.display = 'none';
    confirmEyeClosed.style.display = 'block';
  } else {
    confirmPassword.type = 'password';
    confirmEyeOpen.style.display = 'block';
    confirmEyeClosed.style.display = 'none';
  }
}

eyeOpen.addEventListener('click', togglePassword);
eyeClosed.addEventListener('click', togglePassword);

confirmEyeOpen.addEventListener('click', toggleConfirmPassword);
confirmEyeClosed.addEventListener('click', toggleConfirmPassword);

// Add event listeners for real-time validation
password.addEventListener('input', updatePasswordFeedback);
confirmPassword.addEventListener('input', updatePasswordMatchFeedback);
password.addEventListener('input', updatePasswordMatchFeedback);

// Form submission with basic validation
document.getElementById('registerForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  
  console.log("Form submit event triggered!");

  const username = document.getElementById('username').value;
  const passwordValue = document.getElementById('password').value;
  const confirmPasswordValue = document.getElementById('confirmPassword').value;
  const fullname = document.getElementById('fullname').value;
  const email = document.getElementById('email').value;

  // More detailed logging
  console.log("Raw values:");
  console.log("Username:", `'${username}'`, "Length:", username.length);
  console.log("Password:", `'${passwordValue}'`, "Length:", passwordValue.length);
  console.log("Confirm Password:", `'${confirmPasswordValue}'`, "Length:", confirmPasswordValue.length);
  console.log("Fullname:", `'${fullname}'`, "Length:", fullname.length);
  console.log("Email:", `'${email}'`, "Length:", email.length);

  // Trim values
  const usernameTrimmed = username.trim();
  const passwordTrimmed = passwordValue.trim();
  const confirmPasswordTrimmed = confirmPasswordValue.trim();
  const fullnameTrimmed = fullname.trim();
  const emailTrimmed = email.trim();

  console.log("Trimmed values:");
  console.log("Username:", `'${usernameTrimmed}'`, "Length:", usernameTrimmed.length);
  console.log("Password:", `'${passwordTrimmed}'`, "Length:", passwordTrimmed.length);
  console.log("Confirm Password:", `'${confirmPasswordTrimmed}'`, "Length:", confirmPasswordTrimmed.length);

  // Check for empty fields
  if (!fullnameTrimmed || !usernameTrimmed || !emailTrimmed || !passwordTrimmed || !confirmPasswordTrimmed) {
    alert('All fields are required!');
    return;
  }

  // Check password match
  if (passwordTrimmed !== confirmPasswordTrimmed) {
    console.log("Password mismatch detected!");
    console.log("Password chars:", [...passwordTrimmed]);
    console.log("Confirm chars:", [...confirmPasswordTrimmed]);
    alert('Passwords do not match!');
    return;
  }

  // Check password complexity
  const passwordErrors = validatePassword(passwordTrimmed);
  if (passwordErrors.length > 0) {
    alert('Password requirements:\n' + passwordErrors.join('\n'));
    return;
  }

  const formData = new FormData();
  formData.append('fullname', fullnameTrimmed);
  formData.append('username', usernameTrimmed);
  formData.append('email', emailTrimmed);
  formData.append('password', passwordTrimmed);
  formData.append('confirmPassword', confirmPasswordTrimmed);

  console.log('Sending FormData with values:');
  for (let [key, value] of formData.entries()) {
    console.log(key + ':', value);
  }

  const response = await fetch('/register', {
    method: 'POST',
    body: formData
  });

  const result = await response.json();
  
  if (result.success) {
    alert(result.message);
    // Redirect to login page after successful registration
    window.location.href = result.redirect;
  } else {
    alert(result.message);
  }
});