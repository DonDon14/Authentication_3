document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing dropdown functionality');
    
    // Get elements
    const profileBtn = document.querySelector('#profileBtn');
    const userDropdown = document.querySelector('#userDropdown');

    if (!profileBtn) console.error('Profile button not found');
    if (!userDropdown) console.error('User dropdown not found');

    if (profileBtn && userDropdown) {
        console.log('Found all required elements');

        // Ensure dropdown is hidden initially
        userDropdown.style.display = 'none';

        profileBtn.addEventListener('click', function(event) {
            event.preventDefault();
            event.stopPropagation();
            
            // Toggle visibility
            const currentDisplay = window.getComputedStyle(userDropdown).display;
            if (currentDisplay === 'none') {
                userDropdown.style.display = 'block';
                console.log('Opening dropdown - current display:', userDropdown.style.display);
            } else {
                userDropdown.style.display = 'none';
                console.log('Closing dropdown - current display:', userDropdown.style.display);
            }
        });

        // Handle clicking outside
        document.addEventListener('click', function(event) {
            const isClickInside = profileBtn.contains(event.target) || userDropdown.contains(event.target);
            
            if (!isClickInside && userDropdown.style.display === 'block') {
                userDropdown.style.display = 'none';
                console.log('Clicked outside, closing dropdown');
            }
        });

        console.log('Event listeners attached');
    }
});
