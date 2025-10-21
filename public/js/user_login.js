document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('userLoginForm');
    const loginBtn = document.getElementById('loginBtn');
    const errorMessage = document.getElementById('errorMessage');
    
    // Clear form on page load
    if (loginForm) {
        loginForm.reset();
    }
    
    // Handle form submission
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const studentName = document.getElementById('studentName').value.trim();
            const studentId = document.getElementById('studentId').value.trim();
            
            // Validation
            if (!studentName || !studentId) {
                showError('Please fill in all fields');
                return;
            }
            
            // Show loading state
            setLoadingState(true);
            hideError();
            
            try {
                const formData = new FormData();
                formData.append('student_name', studentName);
                formData.append('student_id', studentId);
                
                const response = await fetch('/user/login', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showSuccess(result.message);
                    setTimeout(() => {
                        window.location.href = result.redirect;
                    }, 1000);
                } else {
                    showError(result.message);
                }
                
            } catch (error) {
                console.error('Login error:', error);
                showError('Connection error. Please try again.');
            } finally {
                setLoadingState(false);
            }
        });
    }
    
    /**
     * Show error message
     */
    function showError(message) {
        if (errorMessage) {
            errorMessage.textContent = message;
            errorMessage.style.display = 'block';
            errorMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
    
    /**
     * Hide error message
     */
    function hideError() {
        if (errorMessage) {
            errorMessage.style.display = 'none';
        }
    }
    
    /**
     * Show success message
     */
    function showSuccess(message) {
        if (errorMessage) {
            errorMessage.textContent = message;
            errorMessage.style.display = 'block';
            errorMessage.style.background = 'rgba(16, 185, 129, 0.1)';
            errorMessage.style.color = '#059669';
            errorMessage.style.borderColor = 'rgba(16, 185, 129, 0.2)';
        }
    }
    
    /**
     * Set loading state
     */
    function setLoadingState(isLoading) {
        if (loginBtn) {
            if (isLoading) {
                loginBtn.classList.add('loading');
                loginBtn.disabled = true;
            } else {
                loginBtn.classList.remove('loading');
                loginBtn.disabled = false;
            }
        }
    }
    
    // Input formatting and validation
    const studentIdInput = document.getElementById('studentId');
    if (studentIdInput) {
        studentIdInput.addEventListener('input', function(e) {
            // Convert to uppercase for consistency
            e.target.value = e.target.value.toUpperCase();
        });
    }
    
    const studentNameInput = document.getElementById('studentName');
    if (studentNameInput) {
        studentNameInput.addEventListener('input', function(e) {
            // Capitalize first letter of each word
            e.target.value = e.target.value.replace(/\b\w/g, l => l.toUpperCase());
        });
    }
});