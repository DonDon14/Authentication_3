/**
 * Profile Page JavaScript
 * Handles profile form submission, validation, and edit mode functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeProfile();
});

function initializeProfile() {
    console.log('Profile page initialized successfully');
    
    // Initialize form submission handlers
    initializeFormHandlers();
    
    // Initialize edit mode functionality
    initializeEditMode();
    
    // Initialize other functionality
    initializeDropdowns();
    initializeNotifications();
}

/**
 * Initialize form handlers
 */
function initializeFormHandlers() {
    // Personal info form
    const personalForm = document.getElementById('personalInfoForm');
    if (personalForm) {
        personalForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitPersonalForm();
        });
    }
    
    // Security form
    const securityForm = document.getElementById('securityForm');
    if (securityForm) {
        securityForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitSecurityForm();
        });
    }
}

/**
 * Global functions for edit mode
 */
window.toggleEditMode = function(section) {
    console.log('toggleEditMode called with:', section);
    const form = document.getElementById(section === 'personal' ? 'personalInfoForm' : 'securityForm');
    const inputs = form.querySelectorAll('input:not(#profilePictureInput)');
    const actions = document.getElementById(section + 'Actions');
    const editBtn = form.closest('.card').querySelector('.btn-secondary');
    
    if (!form || !actions || !editBtn) {
        console.error('Required elements not found for section:', section);
        return;
    }
    
    const isCurrentlyReadOnly = inputs[0].readOnly;
    
    inputs.forEach(input => {
        input.readOnly = !isCurrentlyReadOnly;
        if (!isCurrentlyReadOnly) {
            input.classList.add('editable');
        } else {
            input.classList.remove('editable');
        }
    });
    
    // Handle profile picture upload button for personal section
    if (section === 'personal') {
        const uploadBtn = document.getElementById('profilePictureUploadBtn');
        if (uploadBtn) {
            if (isCurrentlyReadOnly) {
                uploadBtn.style.display = 'block';
            } else {
                uploadBtn.style.display = 'none';
            }
        }
    }
    
    if (isCurrentlyReadOnly) {
        actions.style.display = 'flex';
        editBtn.innerHTML = '<i class="fas fa-times"></i> Cancel';
        editBtn.onclick = () => cancelEdit(section);
    } else {
        actions.style.display = 'none';
        editBtn.innerHTML = '<i class="fas fa-edit"></i> Edit';
        editBtn.onclick = () => toggleEditMode(section);
    }
};

window.cancelEdit = function(section) {
    const form = document.getElementById(section === 'personal' ? 'personalInfoForm' : 'securityForm');
    const inputs = form.querySelectorAll('input:not(#profilePictureInput)');
    const actions = document.getElementById(section + 'Actions');
    const editBtn = form.closest('.card').querySelector('.btn-secondary');
    
    inputs.forEach(input => {
        input.readOnly = true;
        input.classList.remove('editable');
    });
    
    if (section === 'personal') {
        const uploadBtn = document.getElementById('profilePictureUploadBtn');
        if (uploadBtn) {
            uploadBtn.style.display = 'none';
        }
    }
    
    actions.style.display = 'none';
    editBtn.innerHTML = '<i class="fas fa-edit"></i> Edit';
    editBtn.onclick = () => toggleEditMode(section);
    
    location.reload();
};

/**
 * Profile picture functions
 */
window.triggerFileInput = function() {
    const fileInput = document.getElementById('profilePictureInput');
    if (fileInput) {
        fileInput.click();
    }
};

window.handleProfilePictureChange = function(event) {
    const file = event.target.files[0];
    if (!file) return;

    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (!allowedTypes.includes(file.type)) {
        showNotification('Please select a valid image file (JPG, PNG, or GIF)', 'error');
        return;
    }

    const maxSize = 2 * 1024 * 1024;
    if (file.size > maxSize) {
        showNotification('File size must be less than 2MB', 'error');
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        const preview = document.getElementById('profilePicturePreview');
        const placeholder = document.getElementById('profilePlaceholder');
        let existingImg = document.getElementById('profileImage');
        
        if (existingImg) {
            existingImg.src = e.target.result;
        } else {
            const img = document.createElement('img');
            img.id = 'profileImage';
            img.src = e.target.result;
            img.alt = 'Profile Picture';
            
            if (placeholder) {
                placeholder.style.display = 'none';
            }
            
            preview.appendChild(img);
        }
    };
    reader.readAsDataURL(file);

    uploadProfilePicture(file);
};

function uploadProfilePicture(file) {
    console.log('uploadProfilePicture called with file:', file);
    const formData = new FormData();
    formData.append('profile_picture', file);

    showNotification('Uploading profile picture...', 'info');

    fetch('/profile/upload-picture', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('Upload response:', response);
        return response.json();
    })
    .then(data => {
        console.log('Upload data:', data);
        if (data.success) {
            showNotification('Profile picture updated successfully!', 'success');
            // Use test route for now to avoid 500 errors
            const filename = data.profile_picture.split('/').pop(); // Get just the filename
            const imageUrl = `/Authentication_3/test-profile-picture/${filename}`;
            updateAllProfilePictures(imageUrl);
            
            // Notify other pages about the profile picture update
            localStorage.setItem('profilePictureUpdated', imageUrl);
            setTimeout(() => localStorage.removeItem('profilePictureUpdated'), 1000);
        } else {
            showNotification(data.message || 'Failed to upload profile picture', 'error');
        }
    })
    .catch(error => {
        console.error('Upload error:', error);
        showNotification('Error uploading profile picture', 'error');
    });
}

function updateAllProfilePictures(imageUrl) {
    console.log('Updating all profile pictures with URL:', imageUrl);
    
    const profileImage = document.getElementById('profileImage');
    const profilePlaceholder = document.getElementById('profilePlaceholder');
    const profilePreview = document.getElementById('profilePicturePreview');
    
    if (profilePlaceholder && profilePreview) {
        profilePreview.innerHTML = `<img src="${imageUrl}" alt="Profile Picture" id="profileImage">`;
    } else if (profileImage) {
        profileImage.src = imageUrl;
    }

    const sidebarAvatar = document.querySelector('.sidebar-footer .profile-avatar');
    if (sidebarAvatar) {
        sidebarAvatar.innerHTML = `<img src="${imageUrl}" alt="Profile Picture">`;
    }

    const headerAvatar = document.querySelector('.header .user-avatar');
    if (headerAvatar) {
        headerAvatar.innerHTML = `<img src="${imageUrl}" alt="Profile Picture">`;
    }

    const otherProfileImages = document.querySelectorAll('.profile-avatar img, .user-avatar img');
    otherProfileImages.forEach(img => {
        if (img.id !== 'profileImage') {
            img.src = imageUrl;
        }
    });
}

/**
 * Submit personal information form
 */
function submitPersonalForm() {
    const form = document.getElementById('personalInfoForm');
    const formData = new FormData(form);
    
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
    submitBtn.disabled = true;
    
    fetch('/auth/updateProfile', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Personal information updated successfully!', 'success');
            cancelEdit('personal');
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showNotification(data.message || 'Error updating personal information', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error updating personal information', 'error');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

/**
 * Submit security form
 */
function submitSecurityForm() {
    const form = document.getElementById('securityForm');
    const formData = new FormData(form);
    
    const newPassword = formData.get('new_password');
    const confirmPassword = formData.get('confirm_password');
    
    if (newPassword !== confirmPassword) {
        showNotification('Passwords do not match!', 'error');
        return;
    }
    
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
    submitBtn.disabled = true;
    
    fetch('/auth/updateProfile', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Password updated successfully!', 'success');
            cancelEdit('security');
            form.reset();
        } else {
            showNotification(data.message || 'Failed to update password', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while updating password', 'error');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

/**
 * Initialize dropdowns and other UI elements
 */
function initializeDropdowns() {
    // Notification dropdown toggle
    window.toggleNotifications = function() {
        const dropdown = document.getElementById('notificationDropdown');
        if (dropdown) {
            dropdown.classList.toggle('active');
        }
    };
    
    // User menu dropdown toggle
    window.toggleUserMenu = function() {
        const dropdown = document.getElementById('userDropdown');
        if (dropdown) {
            dropdown.classList.toggle('active');
        }
    };
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        const notificationBtn = document.querySelector('.notification-btn');
        const notificationDropdown = document.getElementById('notificationDropdown');
        const userMenuBtn = document.querySelector('.user-menu-btn');
        const userDropdown = document.getElementById('userDropdown');
        
        if (notificationDropdown && !notificationBtn.contains(event.target) && !notificationDropdown.contains(event.target)) {
            notificationDropdown.classList.remove('active');
        }
        
        if (userDropdown && !userMenuBtn.contains(event.target) && !userDropdown.contains(event.target)) {
            userDropdown.classList.remove('active');
        }
    });
}

/**
 * Initialize notifications
 */
function initializeNotifications() {
    // Toast functions
    window.showToast = function(toastId, message) {
        const toast = document.getElementById(toastId);
        if (toast) {
            const messageElement = toast.querySelector('.toast-message');
            if (messageElement && message) {
                messageElement.textContent = message;
            }
            toast.classList.add('show');
            
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }
    };
    
    window.hideToast = function(toastId) {
        const toast = document.getElementById(toastId);
        if (toast) {
            toast.classList.remove('show');
        }
    };
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

// Add CSS for toast notifications if not already present
if (!document.querySelector('#profile-toast-styles')) {
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

    const styleSheet = document.createElement('style');
    styleSheet.id = 'profile-toast-styles';
    styleSheet.textContent = additionalStyles;
    document.head.appendChild(styleSheet);
}