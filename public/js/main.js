// Main JavaScript initialization
window.addEventListener('load', function() {
    console.log('Main JS initialized');
    if (!window.sidebarInitialized) {
        initializeSidebarToggle();
        window.sidebarInitialized = true;
    }
});

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded');
    if (!window.sidebarInitialized) {
        initializeSidebarToggle();
        window.sidebarInitialized = true;
    }
});

/**
 * Enhanced Sidebar Toggle Functionality
 */
function initializeSidebarToggle() {
    console.log('Initializing sidebar toggle...');
    
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    
    if (!sidebarToggle || !sidebar) {
        console.error('Sidebar elements not found!');
        return;
    }
    
    // Get stored sidebar state
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (isCollapsed) {
        sidebar.classList.add('collapsed');
        updateToggleIcon(true);
    }
    
    // Add tooltips to nav links for collapsed state
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        const span = link.querySelector('span');
        if (span) {
            link.setAttribute('title', span.textContent.trim());
        }
    });
    
    // Handle sidebar toggle
    function toggleSidebar() {
        console.log('Toggle sidebar called');
        const isCurrentlyCollapsed = sidebar.classList.contains('collapsed');
        console.log('Currently collapsed:', isCurrentlyCollapsed);
        
        requestAnimationFrame(() => {
            sidebar.classList.toggle('collapsed');
            console.log('Sidebar classes after toggle:', sidebar.className);
            
            // Update icon and localStorage
            const newState = sidebar.classList.contains('collapsed');
            updateToggleIcon(newState);
            localStorage.setItem('sidebarCollapsed', newState.toString());
            
            // Force a layout recalculation
            sidebar.offsetHeight;
        });
    }
    
    function updateToggleIcon(isCollapsed) {
        const icon = sidebarToggle.querySelector('i');
        if (!icon) return;
        
        if (isCollapsed) {
            icon.className = 'fas fa-chevron-right';
            sidebarToggle.setAttribute('title', 'Expand Sidebar');
        } else {
            icon.className = 'fas fa-bars';
            sidebarToggle.setAttribute('title', 'Collapse Sidebar');
        }
    }
    
    // Add click event listener
    sidebarToggle.addEventListener('click', function(e) {
        console.log('Sidebar toggle clicked!');
        e.preventDefault();
        toggleSidebar();
    });
    
    // Handle ESC key on mobile
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && window.innerWidth <= 768) {
            if (!sidebar.classList.contains('collapsed')) {
                toggleSidebar();
            }
        }
    });
}