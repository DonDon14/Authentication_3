// Header functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize header functionality
    initializeHeader();
});

function initializeHeader() {
    // Auto-refresh notifications periodically
    setInterval(refreshNotifications, 30000);
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        const notificationDropdown = document.getElementById('notificationDropdown');
        const userDropdown = document.getElementById('userDropdown');
        
        if (!event.target.closest('.notification-center')) {
            notificationDropdown?.classList.remove('active');
        }
        
        if (!event.target.closest('.user-menu')) {
            userDropdown?.classList.remove('active');
        }
    });
}
    
    // Handle notifications toggle
    window.toggleNotifications = function() {
        const dropdown = document.getElementById('notificationDropdown');
        const userDropdown = document.getElementById('userDropdown');
        
        // Close user dropdown if open
        if (userDropdown?.classList.contains('active')) {
            userDropdown.classList.remove('active');
        }
        
        dropdown?.classList.toggle('active');
    };
    
    // Handle user menu toggle
    window.toggleUserMenu = function() {
        const dropdown = document.getElementById('userDropdown');
        const notificationDropdown = document.getElementById('notificationDropdown');
        
        // Close notification dropdown if open
        if (notificationDropdown?.classList.contains('active')) {
            notificationDropdown.classList.remove('active');
        }
        
        dropdown?.classList.toggle('active');
    };
    
    // Handle mark all as read
    const markReadBtn = document.querySelector('.mark-read-btn');
    if (markReadBtn) {
        markReadBtn.addEventListener('click', function() {
            const unreadItems = document.querySelectorAll('.notification-item.unread');
            unreadItems.forEach(item => item.classList.remove('unread'));
            
            // Update notification count
            const countElement = document.querySelector('.notification-count');
            if (countElement) {
                countElement.textContent = '0';
            }
        });
    }
    
    function refreshNotifications() {
        // Add AJAX call to refresh notifications data
        console.log('Refreshing notifications...');
        // Implement actual refresh logic here
    }
