/**
 * Dashboard JavaScript
 * Handles dropdown menus, notifications, and other interactive elements
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeDashboard();
});

function initializeDashboard() {
    console.log('Initializing dashboard...');
    
    // Initialize dropdown menus
    initializeUserDropdown();
    initializeNotifications();
    
    // Initialize verify button
    initializeVerifyButton();
    
    // Initialize click outside handlers
    initializeClickOutsideHandlers();
    
    console.log('Dashboard initialized successfully');
}

/**
 * Initialize user dropdown functionality
 */
function initializeUserDropdown() {
    const userDropdownBtn = document.getElementById('userDropdownBtn');
    const userDropdownMenu = document.getElementById('userDropdownMenu');
    
    console.log('User dropdown button:', userDropdownBtn);
    console.log('User dropdown menu:', userDropdownMenu);
    
    if (userDropdownBtn && userDropdownMenu) {
        console.log('Adding click listener to user dropdown button');
        userDropdownBtn.addEventListener('click', function(e) {
            console.log('User dropdown clicked');
            e.preventDefault();
            e.stopPropagation();
            
            // Close notifications if open
            closeNotifications();
            
            // Toggle user dropdown
            toggleDropdown(userDropdownBtn, userDropdownMenu);
        });
    } else {
        console.log('User dropdown elements not found!');
    }
}

/**
 * Initialize notifications functionality
 */
function initializeNotifications() {
    const notificationsBtn = document.getElementById('notificationsBtn');
    const notificationsDropdown = document.getElementById('notificationsDropdown');
    const markAllReadBtn = document.querySelector('.mark-all-read');
    
    console.log('Notifications button:', notificationsBtn);
    console.log('Notifications dropdown:', notificationsDropdown);
    
    if (notificationsBtn && notificationsDropdown) {
        console.log('Adding click listener to notifications button');
        notificationsBtn.addEventListener('click', function(e) {
            console.log('Notifications clicked');
            e.preventDefault();
            e.stopPropagation();
            
            // Close user dropdown if open
            closeUserDropdown();
            
            // Toggle notifications dropdown
            toggleDropdown(notificationsBtn, notificationsDropdown);
        });
    } else {
        console.log('Notifications elements not found!');
    }
    
    // Mark all notifications as read
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function(e) {
            e.preventDefault();
            markAllNotificationsAsRead();
        });
    }
    
    // Individual notification click handlers
    const notificationItems = document.querySelectorAll('.notification-item');
    notificationItems.forEach(item => {
        item.addEventListener('click', function() {
            markNotificationAsRead(this);
        });
    });
}

/**
 * Initialize click outside handlers to close dropdowns
 */
function initializeClickOutsideHandlers() {
    document.addEventListener('click', function(e) {
        // Close dropdowns when clicking outside
        if (!e.target.closest('.user-dropdown-wrapper')) {
            closeUserDropdown();
        }
        
        if (!e.target.closest('.notifications-wrapper')) {
            closeNotifications();
        }
    });
    
    // Close dropdowns on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeUserDropdown();
            closeNotifications();
        }
    });
}

/**
 * Toggle dropdown visibility
 */
function toggleDropdown(button, dropdown) {
    console.log('Toggling dropdown');
    const isOpen = dropdown.classList.contains('show');
    console.log('Dropdown is open:', isOpen);
    
    if (isOpen) {
        closeDropdown(button, dropdown);
    } else {
        openDropdown(button, dropdown);
    }
}

/**
 * Open dropdown
 */
function openDropdown(button, dropdown) {
    console.log('Opening dropdown');
    button.classList.add('active');
    dropdown.classList.add('show');
    
    // Add animation class
    dropdown.style.animation = 'slideInDown 0.3s ease-out';
    console.log('Dropdown opened, classes:', dropdown.className);
}

/**
 * Close dropdown
 */
function closeDropdown(button, dropdown) {
    button.classList.remove('active');
    dropdown.classList.remove('show');
    
    // Remove animation
    dropdown.style.animation = '';
}

/**
 * Close user dropdown specifically
 */
function closeUserDropdown() {
    const userDropdownBtn = document.getElementById('userDropdownBtn');
    const userDropdownMenu = document.getElementById('userDropdownMenu');
    
    if (userDropdownBtn && userDropdownMenu) {
        closeDropdown(userDropdownBtn, userDropdownMenu);
    }
}

/**
 * Close notifications specifically
 */
function closeNotifications() {
    const notificationsBtn = document.getElementById('notificationsBtn');
    const notificationsDropdown = document.getElementById('notificationsDropdown');
    
    if (notificationsBtn && notificationsDropdown) {
        closeDropdown(notificationsBtn, notificationsDropdown);
    }
}

/**
 * Mark all notifications as read
 */
function markAllNotificationsAsRead() {
    const unreadItems = document.querySelectorAll('.notification-item.unread');
    
    unreadItems.forEach(item => {
        item.classList.remove('unread');
    });
    
    // Update badge count
    updateNotificationBadge();
    
    // Show success message
    showNotification('All notifications marked as read', 'success');
}

/**
 * Mark single notification as read
 */
function markNotificationAsRead(notificationItem) {
    if (notificationItem.classList.contains('unread')) {
        notificationItem.classList.remove('unread');
        
        // Update badge count
        updateNotificationBadge();
    }
}

/**
 * Update notification badge count
 */
function updateNotificationBadge() {
    const unreadCount = document.querySelectorAll('.notification-item.unread').length;
    const badge = document.querySelector('.notification-badge');
    
    if (badge) {
        if (unreadCount > 0) {
            badge.textContent = unreadCount;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    }
}

/**
 * Show notification message
 */
function showNotification(message, type = 'info') {
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
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

/**
 * Initialize Verify Button for QR Code Verification
 */
function initializeVerifyButton() {
    const verifyBtn = document.querySelector('.btn-success');
    
    if (verifyBtn) {
        verifyBtn.addEventListener('click', function() {
            showVerificationModal();
        });
    }
}

/**
 * Show QR Code Verification Modal
 */
function showVerificationModal() {
    const modalHTML = `
        <div id="verificationModal" class="verification-modal" style="
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        ">
            <div class="verification-modal-content" style="
                background: white;
                border-radius: 15px;
                padding: 30px;
                max-width: 600px;
                width: 90%;
                text-align: center;
                box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            ">
                <div class="verification-header">
                    <h2 style="color: #667eea; margin-bottom: 10px;">
                        <i class="fas fa-qrcode"></i> Verify Payment Receipt
                    </h2>
                    <p style="color: #666; margin-bottom: 30px;">Scan or upload a QR code receipt to verify payment</p>
                </div>
                
                <div class="verification-methods" style="display: flex; gap: 15px; margin-bottom: 20px; justify-content: center;">
                    <button id="scanVerifyBtn" class="verify-method-btn" style="
                        background: linear-gradient(45deg, #667eea, #764ba2);
                        color: white;
                        padding: 15px 25px;
                        border: none;
                        border-radius: 10px;
                        cursor: pointer;
                        display: flex;
                        align-items: center;
                        gap: 10px;
                        font-size: 16px;
                        transition: all 0.3s ease;
                    ">
                        <i class="fas fa-camera"></i> Scan QR Code
                    </button>
                    <button id="uploadVerifyBtn" class="verify-method-btn" style="
                        background: linear-gradient(45d, #28a745, #20c997);
                        color: white;
                        padding: 15px 25px;
                        border: none;
                        border-radius: 10px;
                        cursor: pointer;
                        display: flex;
                        align-items: center;
                        gap: 10px;
                        font-size: 16px;
                        transition: all 0.3s ease;
                    ">
                        <i class="fas fa-upload"></i> Upload Image
                    </button>
                </div>
                
                <div class="manual-verify-section" style="margin-bottom: 20px;">
                    <h4 style="margin-bottom: 15px; color: #333;">Or Enter QR Code Data Manually:</h4>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <input type="text" id="manualVerifyInput" placeholder="Paste QR code data here..." style="
                            flex: 1;
                            padding: 12px;
                            border: 2px solid #e1e5e9;
                            border-radius: 8px;
                            font-size: 14px;
                        ">
                        <button id="verifyManualBtn" style="
                            background: #ffc107;
                            color: #212529;
                            padding: 12px 20px;
                            border: none;
                            border-radius: 8px;
                            cursor: pointer;
                            font-weight: 600;
                        ">
                            Verify
                        </button>
                    </div>
                </div>
                
                <div id="verificationResult" style="display: none; margin-bottom: 20px;"></div>
                
                <div style="text-align: right;">
                    <button onclick="closeVerificationModal()" style="
                        background: #6c757d;
                        color: white;
                        padding: 12px 20px;
                        border: none;
                        border-radius: 8px;
                        cursor: pointer;
                    ">
                        Close
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Hidden file input for image upload -->
        <input type="file" id="verifyFileInput" accept="image/*" style="display: none;">
        
        <!-- QR Scanner Canvas -->
        <canvas id="verifyQrCanvas" style="display: none;"></canvas>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Initialize modal functionality
    initializeVerificationModal();
    
    // Prevent body scroll
    document.body.style.overflow = 'hidden';
}

/**
 * Initialize Verification Modal Functionality
 */
function initializeVerificationModal() {
    const uploadBtn = document.getElementById('uploadVerifyBtn');
    const fileInput = document.getElementById('verifyFileInput');
    const manualVerifyBtn = document.getElementById('verifyManualBtn');
    const manualInput = document.getElementById('manualVerifyInput');
    
    // Upload functionality
    uploadBtn?.addEventListener('click', () => {
        fileInput.click();
    });
    
    fileInput?.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            processVerificationImage(file);
        }
    });
    
    // Manual verification
    manualVerifyBtn?.addEventListener('click', () => {
        const qrData = manualInput.value.trim();
        if (qrData) {
            verifyPaymentQR(qrData);
        } else {
            showVerificationError('Please enter QR code data first.');
        }
    });
    
    // Allow Enter key in manual input
    manualInput?.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            manualVerifyBtn.click();
        }
    });
}

/**
 * Process uploaded image for QR code verification
 */
function processVerificationImage(file) {
    const reader = new FileReader();
    
    reader.onload = function(e) {
        const img = new Image();
        
        img.onload = function() {
            const canvas = document.getElementById('verifyQrCanvas');
            const context = canvas.getContext('2d');
            
            canvas.width = img.width;
            canvas.height = img.height;
            
            context.drawImage(img, 0, 0);
            
            const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
            
            // Scan for QR code using jsQR library
            if (typeof jsQR !== 'undefined') {
                const code = jsQR(imageData.data, imageData.width, imageData.height);
                
                if (code) {
                    verifyPaymentQR(code.data);
                } else {
                    showVerificationError('No QR code found in the uploaded image.');
                }
            } else {
                showVerificationError('QR scanner not available. Please enter data manually.');
            }
        };
        
        img.src = e.target.result;
    };
    
    reader.readAsDataURL(file);
}

/**
 * Verify payment using QR code data
 */
async function verifyPaymentQR(qrData) {
    try {
        showVerificationLoading('Verifying payment...');
        
        const baseUrl = window.location.pathname.includes('Authentication_3') ? '/Authentication_3' : '';
        const response = await fetch(`${baseUrl}/payments/verifyPayment`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `qr_data=${encodeURIComponent(qrData)}`
        });
        
        const result = await response.json();
        
        if (result.success) {
            showVerificationSuccess(result.payment_data, result.message);
        } else {
            showVerificationError(result.message || 'Payment verification failed.');
        }
    } catch (error) {
        console.error('Verification error:', error);
        showVerificationError('Error connecting to server. Please try again.');
    }
}

// Verification display functions are now in verification-functions.js

/**
 * Close Verification Modal
 */
function closeVerificationModal() {
    const modal = document.getElementById('verificationModal');
    if (modal) {
        modal.remove();
        document.body.style.overflow = '';
    }
}

/**
 * Legacy function for logout confirmation
 */
function confirmLogout() {
    return confirm('Are you sure you want to logout?');
}