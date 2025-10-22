// Initialize dropdowns when the DOM is loaded - only if not already initialized
if (!window.dropdownsInitialized) {
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Initializing dropdowns from dropdown.js...');
        initializeHeaderDropdowns();
    });
}

// Track initialization
window.dropdownsInitialized = true;

// Header dropdown initialization function
function initializeHeaderDropdowns() {
    console.log('Setting up header dropdowns...');
    
    // Get all required elements
    const notificationBtn = document.getElementById('notificationBtn');
    const userMenuBtn = document.getElementById('userMenuBtn');
    const notificationDropdown = document.getElementById('notificationDropdown');
    const userDropdown = document.getElementById('userDropdown');
    
    console.log('Found elements:', {
        notificationBtn: !!notificationBtn,
        userMenuBtn: !!userMenuBtn,
        notificationDropdown: !!notificationDropdown,
        userDropdown: !!userDropdown
    });

    // Setup notification button
    if (notificationBtn && notificationDropdown) {
        // Remove old event listeners
        const newNotificationBtn = notificationBtn.cloneNode(true);
        notificationBtn.parentNode.replaceChild(newNotificationBtn, notificationBtn);
        
        // Add new event listener
        newNotificationBtn.addEventListener('click', function(e) {
            console.log('Notification button clicked');
            e.preventDefault();
            e.stopPropagation();
            
            // Close all other dropdowns first
            if (userDropdown) {
                userDropdown.classList.remove('show');
                userMenuBtn.classList.remove('active');
            }
            
            // Toggle notification dropdown
            notificationBtn.classList.toggle('active');
            notificationDropdown.classList.toggle('show');
        });
    }
    
    // Setup user menu button
    if (userMenuBtn && userDropdown) {
        // Remove old event listeners
        const newUserMenuBtn = userMenuBtn.cloneNode(true);
        userMenuBtn.parentNode.replaceChild(newUserMenuBtn, userMenuBtn);
        
        // Add new event listener
        newUserMenuBtn.addEventListener('click', function(e) {
            console.log('User menu button clicked');
            e.preventDefault();
            e.stopPropagation();
            
            // Close all other dropdowns first
            if (notificationDropdown) {
                notificationDropdown.classList.remove('show');
                notificationBtn.classList.remove('active');
            }
            
            // Toggle user dropdown
            userMenuBtn.classList.toggle('active');
            userDropdown.classList.toggle('show');
        });
    }
    
    // Add click handler for closing dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.notification-dropdown') && 
            !e.target.closest('.user-dropdown') && 
            !e.target.closest('#notificationBtn') && 
            !e.target.closest('#userMenuBtn')) {
            
            // Close all dropdowns
            if (notificationDropdown) {
                notificationDropdown.classList.remove('show');
                notificationBtn?.classList.remove('active');
            }
            if (userDropdown) {
                userDropdown.classList.remove('show');
                userMenuBtn?.classList.remove('active');
            }
        }
    });

    // Add escape key handler
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (notificationDropdown) {
                notificationDropdown.classList.remove('show');
                notificationBtn?.classList.remove('active');
            }
            if (userDropdown) {
                userDropdown.classList.remove('show');
                userMenuBtn?.classList.remove('active');
            }
        }
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