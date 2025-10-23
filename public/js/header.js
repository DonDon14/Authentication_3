// Header functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize header functionality
    initializeHeader();
    
    // Add click handlers for buttons
    const notificationBtn = document.getElementById('notificationBtn');
    const userMenuBtn = document.getElementById('userMenuBtn');
    
    if (notificationBtn) {
        notificationBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleNotifications();
        });
    }
    
    if (userMenuBtn) {
        userMenuBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleUserMenu();
        });
    }
});

function initializeHeader() {
    // Auto-refresh notifications periodically
    setInterval(refreshNotifications, 30000);
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        const notificationDropdown = document.getElementById('notificationDropdown');
        const userDropdown = document.getElementById('userDropdown');
        
        if (!event.target.closest('.notification-center') && !event.target.closest('#notificationDropdown')) {
            notificationDropdown?.classList.remove('active');
        }
        
        if (!event.target.closest('.user-menu') && !event.target.closest('#userDropdown')) {
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
    
    async function refreshNotifications() {
        try {
            const response = await fetch('/notifications/get');
            if (!response.ok) throw new Error('Failed to fetch notifications');
            
            const data = await response.json();
            const container = document.querySelector('.notification-list');
            const countElement = document.querySelector('.notification-count');
            
            if (container && data.notifications) {
                // Update notifications list
                container.innerHTML = data.notifications.map(notification => `
                    <a href="${notification.link || '#'}" class="notification-item ${notification.read ? '' : 'unread'}">
                        <div class="notification-content">
                            <div class="notification-title">${notification.title}</div>
                            <div class="notification-message">${notification.message}</div>
                            <div class="notification-time">${notification.time}</div>
                        </div>
                    </a>
                `).join('');
                
                // Update unread count
                if (countElement && data.unreadCount !== undefined) {
                    countElement.textContent = data.unreadCount;
                }
            }
        } catch (error) {
            console.error('Error refreshing notifications:', error);
        }
    }
