// Initialize dropdowns when the DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeHeaderDropdowns();
});

// Header dropdown initialization function
function initializeHeaderDropdowns() {
    // Remove any existing event listeners by cloning and replacing elements
    const notificationBtn = document.getElementById('notificationBtn');
    const userMenuBtn = document.getElementById('userMenuBtn');
    
    if (notificationBtn) {
        const newNotificationBtn = notificationBtn.cloneNode(true);
        notificationBtn.parentNode.replaceChild(newNotificationBtn, notificationBtn);
    }
    
    if (userMenuBtn) {
        const newUserMenuBtn = userMenuBtn.cloneNode(true);
        userMenuBtn.parentNode.replaceChild(newUserMenuBtn, userMenuBtn);
    }
    
    // Now add fresh event listeners
    const notificationDropdown = document.getElementById('notificationDropdown');
    const userDropdown = document.getElementById('userDropdown');
    
    if (newNotificationBtn && notificationDropdown) {
        newNotificationBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            notificationDropdown.classList.toggle('active');
            userDropdown?.classList.remove('active');
            document.getElementById('profileMenuDropdown')?.classList.remove('active');
        });
    }
    
    if (newUserMenuBtn && userDropdown) {
        newUserMenuBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            userDropdown.classList.toggle('active');
            notificationDropdown?.classList.remove('active');
            document.getElementById('profileMenuDropdown')?.classList.remove('active');
        });
    }
    
    // Remove any existing click handlers on document
    const newBody = document.body.cloneNode(true);
    document.body.parentNode.replaceChild(newBody, document.body);
    
    // Add fresh click handler for closing dropdowns
    document.addEventListener('click', function(e) {
        const dropdowns = document.querySelectorAll('.notification-dropdown, .user-dropdown, .profile-menu-dropdown');
        dropdowns.forEach(dropdown => {
            if (!dropdown.contains(e.target) && 
                !e.target.closest('#notificationBtn') && 
                !e.target.closest('#userMenuBtn') && 
                !e.target.closest('#profileMenuBtn')) {
                dropdown.classList.remove('active');
            }
        });
    });

    // Profile picture update listener
    window.addEventListener('storage', function(e) {
        if (e.key === 'profilePictureUpdated') {
            const sidebarAvatar = document.querySelector('.sidebar-footer .profile-avatar');
            if (sidebarAvatar && e.newValue) {
                sidebarAvatar.innerHTML = `<img src="${e.newValue}" alt="Profile Picture">`;
            }
            
            const headerAvatar = document.querySelector('.user-menu .user-avatar');
            if (headerAvatar && e.newValue) {
                headerAvatar.innerHTML = `<img src="${e.newValue}" alt="Profile Picture">`;
            }
        }
    });
}