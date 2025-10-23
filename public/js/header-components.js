document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing dropdowns functionality');
    
    // Profile dropdown elements
    const profileBtn = document.querySelector('#profileBtn');
    const userDropdown = document.querySelector('#userDropdown');

    // Notification elements
    const notificationBtn = document.querySelector('#notificationBtn');
    const notificationDropdown = document.querySelector('#notificationDropdown');
    const markAllReadBtn = document.querySelector('#markAllRead');
    const notificationCount = document.querySelector('.notification-count');

    // Initialize Profile Dropdown
    if (profileBtn && userDropdown) {
        console.log('Profile dropdown elements found');

        // Ensure dropdown is hidden initially
        userDropdown.style.display = 'none';

        profileBtn.addEventListener('click', function(event) {
            event.preventDefault();
            event.stopPropagation();
            
            // Close notification dropdown if open
            if (notificationDropdown) {
                notificationDropdown.style.display = 'none';
            }
            
            // Toggle profile dropdown
            const currentDisplay = window.getComputedStyle(userDropdown).display;
            if (currentDisplay === 'none') {
                userDropdown.style.display = 'block';
                console.log('Opening profile dropdown');
            } else {
                userDropdown.style.display = 'none';
                console.log('Closing profile dropdown');
            }
        });
    } else {
        console.error('Profile dropdown elements not found');
    }

    // Initialize Notification Dropdown
    if (notificationBtn && notificationDropdown) {
        console.log('Notification elements found');

        // Ensure dropdown is hidden initially
        notificationDropdown.style.display = 'none';

        notificationBtn.addEventListener('click', function(event) {
            event.preventDefault();
            event.stopPropagation();
            
            // Close profile dropdown if open
            if (userDropdown) {
                userDropdown.style.display = 'none';
            }
            
            // Toggle notification dropdown
            const currentDisplay = window.getComputedStyle(notificationDropdown).display;
            if (currentDisplay === 'none') {
                notificationDropdown.style.display = 'block';
                console.log('Opening notification dropdown');
            } else {
                notificationDropdown.style.display = 'none';
                console.log('Closing notification dropdown');
            }
        });

        // Handle mark as read for individual notifications
        document.querySelectorAll('.mark-read').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                event.stopPropagation();
                
                const notificationItem = this.closest('.notification-item');
                if (notificationItem) {
                    notificationItem.classList.remove('unread');
                    this.style.display = 'none';
                    
                    // Update notification count
                    const unreadCount = document.querySelectorAll('.notification-item.unread').length;
                    if (notificationCount) {
                        notificationCount.textContent = unreadCount;
                        if (unreadCount === 0) {
                            notificationCount.style.display = 'none';
                        }
                    }
                }
            });
        });

        // Handle mark all as read
        if (markAllReadBtn) {
            markAllReadBtn.addEventListener('click', function(event) {
                event.preventDefault();
                event.stopPropagation();
                
                document.querySelectorAll('.notification-item.unread').forEach(item => {
                    item.classList.remove('unread');
                    const markReadBtn = item.querySelector('.mark-read');
                    if (markReadBtn) {
                        markReadBtn.style.display = 'none';
                    }
                });
                
                if (notificationCount) {
                    notificationCount.style.display = 'none';
                }
            });
        }
    } else {
        console.error('Notification elements not found');
    }

    // Handle clicking outside of dropdowns
    document.addEventListener('click', function(event) {
        // Close profile dropdown
        if (userDropdown && !profileBtn.contains(event.target) && !userDropdown.contains(event.target)) {
            userDropdown.style.display = 'none';
        }
        
        // Close notification dropdown
        if (notificationDropdown && !notificationBtn.contains(event.target) && !notificationDropdown.contains(event.target)) {
            notificationDropdown.style.display = 'none';
        }
    });

    console.log('All event listeners attached');
});
