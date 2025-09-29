/**
 * Profile Page JavaScript
 * Handles profile form submission and validation
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeProfile();
});

function initializeProfile() {
    // Initialize form submission
    initializeProfileForm();
    
    // Initialize photo change functionality
    initializePhotoChange();
    
    console.log('Profile page initialized successfully');
}

/**
 * Initialize profile form functionality
 */
function initializeProfileForm() {
    const profileForm = document.getElementById('profileForm');
    
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitProfileForm();
        });
    }
}

/**
 * Initialize photo change functionality
 */
function initializePhotoChange() {
    const changePhotoBtn = document.querySelector('.change-photo-btn');
    
    if (changePhotoBtn) {
        changePhotoBtn.addEventListener('click', function() {
            // Create hidden file input
            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.accept = 'image/*';
            fileInput.style.display = 'none';
            
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    handlePhotoChange(file);
                }
            });
            
            document.body.appendChild(fileInput);
            fileInput.click();
            document.body.removeChild(fileInput);
        });
    }
}

/**
 * Handle photo change
 */
function handlePhotoChange(file) {
    if (file.size > 5 * 1024 * 1024) { // 5MB limit
        showNotification('File size must be less than 5MB', 'error');
        return;
    }
    
    const reader = new FileReader();
    reader.onload = function(e) {
        const profileAvatar = document.querySelector('.profile-avatar');
        profileAvatar.innerHTML = `<img src="${e.target.result}" alt="Profile" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;
        
        showNotification('Photo updated successfully! Remember to save changes.', 'success');
    };
    
    reader.readAsDataURL(file);
}

/**
 * Submit profile form
 */
function submitProfileForm() {
    const form = document.getElementById('profileForm');
    const formData = new FormData(form);
    
    // Validate form
    if (!validateProfileForm(formData)) {
        return;
    }
    
    // Show loading state
    const submitBtn = form.querySelector('.btn-primary');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Saving...';
    submitBtn.disabled = true;
    
    // Convert FormData to URLSearchParams for proper submission
    const data = new URLSearchParams();
    for (let [key, value] of formData.entries()) {
        data.append(key, value);
    }
    
    // Submit form
    fetch('/profile/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: data
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            
            // Update page title if name changed
            const newName = formData.get('full_name');
            if (newName) {
                document.title = `Profile - ${newName}`;
            }
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while updating profile', 'error');
    })
    .finally(() => {
        // Reset button
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
}

/**
 * Validate profile form
 */
function validateProfileForm(formData) {
    const fullName = formData.get('full_name');
    const username = formData.get('username');
    const email = formData.get('email');
    const newPassword = formData.get('new_password');
    const confirmPassword = formData.get('confirm_password');
    const currentPassword = formData.get('current_password');
    
    // Required fields validation
    if (!fullName || !username || !email) {
        showNotification('Please fill in all required fields', 'error');
        return false;
    }
    
    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        showNotification('Please enter a valid email address', 'error');
        return false;
    }
    
    // Password validation (if changing password)
    if (newPassword) {
        if (!currentPassword) {
            showNotification('Current password is required to set a new password', 'error');
            return false;
        }
        
        if (newPassword.length < 8) {
            showNotification('New password must be at least 8 characters long', 'error');
            return false;
        }
        
        if (newPassword !== confirmPassword) {
            showNotification('New password and confirmation do not match', 'error');
            return false;
        }
    }
    
    return true;
}

/**
 * Show notification message
 */
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.toast-notification');
    existingNotifications.forEach(notification => notification.remove());
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `toast-notification toast-${type}`;
    notification.textContent = message;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Show with animation
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    // Auto remove after 4 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 4000);
}

// Add CSS for toast notifications
const additionalStyles = `
    .toast-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        z-index: 1001;
        transform: translateX(400px);
        transition: transform 0.3s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        max-width: 350px;
        word-wrap: break-word;
    }
    
    .toast-notification.show {
        transform: translateX(0);
    }
    
    .toast-success {
        background: linear-gradient(135deg, #10b981, #059669);
    }
    
    .toast-error {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }
    
    .toast-info {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
    }
`;

// Inject additional styles
const styleSheet = document.createElement('style');
styleSheet.textContent = additionalStyles;
document.head.appendChild(styleSheet);