/**
 * Dashboard JavaScript
 * Handles dropdown menus, notifications, and other interactive elements
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeDashboard();
    // Initialize settings after dashboard
    initializeSettings();
    initializeTheme();
    initializeSettingsModal();
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
        // Don't close if clicking on modal or settings items
        if (e.target.closest('#settingsModal') || e.target.closest('.modal')) {
            return;
        }
        
        // Close dropdowns when clicking outside
        if (!e.target.closest('.user-dropdown-wrapper')) {
            closeUserDropdown();
        }
        
        if (!e.target.closest('.notifications-wrapper')) {
            closeNotifications();
        }
    });
    
    // Close dropdowns on escape key (but not if modal is open)
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('settingsModal');
            if (!modal || modal.style.display !== 'block') {
                closeUserDropdown();
                closeNotifications();
            }
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
    if (button && dropdown) {
        button.classList.remove('active');
        dropdown.classList.remove('show');
        
        // Remove animation
        dropdown.style.animation = '';
    }
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
 * Show QR Code Verification Modal with Improved UI
 */
function showVerificationModal() {
    const modalHTML = `
        <div id="verificationModal" class="verification-modal" style="
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.85);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            backdrop-filter: blur(5px);
            animation: fadeIn 0.3s ease-out;
        ">
            <div class="verification-modal-content" style="
                background: linear-gradient(145deg, #ffffff, #f8fafc);
                border-radius: 20px;
                padding: 40px;
                max-width: 650px;
                width: 90%;
                text-align: center;
                box-shadow: 0 25px 50px rgba(0,0,0,0.15), 0 0 0 1px rgba(255,255,255,0.3);
                position: relative;
                transform: translateY(0);
                animation: slideUp 0.4s ease-out;
            ">
                <!-- Close Button -->
                <button onclick="closeVerificationModal()" style="
                    position: absolute;
                    top: 15px;
                    right: 15px;
                    background: #f1f5f9;
                    border: none;
                    width: 40px;
                    height: 40px;
                    border-radius: 50%;
                    cursor: pointer;
                    color: #64748b;
                    font-size: 18px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    transition: all 0.2s ease;
                    z-index: 10;
                " onmouseover="this.style.background='#e2e8f0'; this.style.color='#334155';" onmouseout="this.style.background='#f1f5f9'; this.style.color='#64748b';">
                    <i class="fas fa-times"></i>
                </button>
                
                <div class="verification-header" style="margin-bottom: 35px;">
                    <div style="
                        background: linear-gradient(135deg, #667eea, #764ba2);
                        width: 80px;
                        height: 80px;
                        border-radius: 50%;
                        margin: 0 auto 20px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
                    ">
                        <i class="fas fa-qrcode" style="color: white; font-size: 32px;"></i>
                    </div>
                    <h2 style="
                        color: #1e293b; 
                        margin: 0 0 10px 0; 
                        font-size: 28px;
                        font-weight: 700;
                        letter-spacing: -0.5px;
                    ">
                        Verify Payment Receipt
                    </h2>
                    <p style="
                        color: #64748b; 
                        margin: 0; 
                        font-size: 16px;
                        line-height: 1.5;
                    ">
                        Scan or upload a QR code receipt to verify payment authenticity
                    </p>
                </div>
                
                <!-- Verification Methods -->
                <div class="verification-methods" style="
                    display: grid; 
                    grid-template-columns: 1fr 1fr; 
                    gap: 15px; 
                    margin-bottom: 30px;
                ">
                    <button id="scanVerifyBtn" class="verify-method-btn" style="
                        background: linear-gradient(135deg, #667eea, #764ba2);
                        color: white;
                        padding: 20px;
                        border: none;
                        border-radius: 15px;
                        cursor: pointer;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        gap: 12px;
                        font-size: 16px;
                        font-weight: 600;
                        transition: all 0.3s ease;
                        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
                        border: 2px solid transparent;
                    " onmouseover="
                        this.style.transform='translateY(-2px)';
                        this.style.boxShadow='0 12px 35px rgba(102, 126, 234, 0.4)';
                    " onmouseout="
                        this.style.transform='translateY(0)';
                        this.style.boxShadow='0 8px 25px rgba(102, 126, 234, 0.3)';
                    ">
                        <i class="fas fa-camera" style="font-size: 24px;"></i>
                        <span>Scan QR Code</span>
                        <small style="font-size: 12px; opacity: 0.8; font-weight: 400;">Use camera to scan</small>
                    </button>
                    
                    <button id="uploadVerifyBtn" class="verify-method-btn" style="
                        background: linear-gradient(135deg, #10b981, #059669);
                        color: white;
                        padding: 20px;
                        border: none;
                        border-radius: 15px;
                        cursor: pointer;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        gap: 12px;
                        font-size: 16px;
                        font-weight: 600;
                        transition: all 0.3s ease;
                        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
                        border: 2px solid transparent;
                    " onmouseover="
                        this.style.transform='translateY(-2px)';
                        this.style.boxShadow='0 12px 35px rgba(16, 185, 129, 0.4)';
                    " onmouseout="
                        this.style.transform='translateY(0)';
                        this.style.boxShadow='0 8px 25px rgba(16, 185, 129, 0.3)';
                    ">
                        <i class="fas fa-upload" style="font-size: 24px;"></i>
                        <span>Upload Image</span>
                        <small style="font-size: 12px; opacity: 0.8; font-weight: 400;">Choose from device</small>
                    </button>
                </div>
                
                <!-- Divider -->
                <div style="
                    position: relative;
                    margin: 30px 0;
                    text-align: center;
                ">
                    <div style="
                        position: absolute;
                        top: 50%;
                        left: 0;
                        right: 0;
                        height: 1px;
                        background: linear-gradient(90deg, transparent, #e2e8f0, transparent);
                    "></div>
                    <span style="
                        background: linear-gradient(145deg, #ffffff, #f8fafc);
                        padding: 0 20px;
                        color: #64748b;
                        font-size: 14px;
                        font-weight: 500;
                    ">OR</span>
                </div>
                
                <!-- Manual Verification Section -->
                <div class="manual-verify-section" style="
                    background: linear-gradient(145deg, #f8fafc, #f1f5f9);
                    padding: 25px;
                    border-radius: 15px;
                    margin-bottom: 25px;
                    border: 1px solid #e2e8f0;
                ">
                    <h4 style="
                        margin: 0 0 15px 0; 
                        color: #334155;
                        font-size: 18px;
                        font-weight: 600;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        gap: 8px;
                    ">
                        <i class="fas fa-keyboard" style="color: #667eea; font-size: 16px;"></i>
                        Enter QR Code Data Manually
                    </h4>
                    <p style="
                        color: #64748b;
                        font-size: 14px;
                        margin: 0 0 20px 0;
                        line-height: 1.4;
                    ">
                        If you have the QR code data as text, paste it in the field below
                    </p>
                    
                    <div style="display: flex; gap: 12px; align-items: stretch;">
                        <div style="flex: 1; position: relative;">
                            <input type="text" id="manualVerifyInput" placeholder="Paste or type QR code data here..." style="
                                width: 100%;
                                padding: 16px 20px;
                                border: 2px solid #e2e8f0;
                                border-radius: 12px;
                                font-size: 15px;
                                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                                background: white;
                                color: #334155;
                                transition: all 0.2s ease;
                                box-sizing: border-box;
                                outline: none;
                            " onfocus="
                                this.style.borderColor='#667eea';
                                this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.1)';
                            " onblur="
                                this.style.borderColor='#e2e8f0';
                                this.style.boxShadow='none';
                            ">
                            <div style="
                                position: absolute;
                                right: 12px;
                                top: 50%;
                                transform: translateY(-50%);
                                color: #94a3b8;
                                font-size: 14px;
                                pointer-events: none;
                            ">
                                <i class="fas fa-paste"></i>
                            </div>
                        </div>
                        <button id="verifyManualBtn" style="
                            background: linear-gradient(135deg, #f59e0b, #d97706);
                            color: white;
                            padding: 16px 24px;
                            border: none;
                            border-radius: 12px;
                            cursor: pointer;
                            font-weight: 600;
                            font-size: 15px;
                            transition: all 0.2s ease;
                            display: flex;
                            align-items: center;
                            gap: 8px;
                            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
                            white-space: nowrap;
                        " onmouseover="
                            this.style.transform='translateY(-1px)';
                            this.style.boxShadow='0 6px 20px rgba(245, 158, 11, 0.4)';
                        " onmouseout="
                            this.style.transform='translateY(0)';
                            this.style.boxShadow='0 4px 15px rgba(245, 158, 11, 0.3)';
                        ">
                            <i class="fas fa-search"></i>
                            Verify
                        </button>
                    </div>
                </div>
                
                <!-- Verification Result -->
                <div id="verificationResult" style="display: none; margin-bottom: 20px;"></div>
                
                <!-- Footer -->
                <div style="
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding-top: 20px;
                    border-top: 1px solid #e2e8f0;
                ">
                    <div style="
                        color: #64748b;
                        font-size: 13px;
                        display: flex;
                        align-items: center;
                        gap: 6px;
                    ">
                        <i class="fas fa-shield-alt" style="color: #10b981;"></i>
                        <span>Secure verification system</span>
                    </div>
                    
                    <button onclick="closeVerificationModal()" style="
                        background: linear-gradient(135deg, #6b7280, #4b5563);
                        color: white;
                        padding: 12px 24px;
                        border: none;
                        border-radius: 10px;
                        cursor: pointer;
                        font-weight: 500;
                        transition: all 0.2s ease;
                        display: flex;
                        align-items: center;
                        gap: 8px;
                    " onmouseover="
                        this.style.background='linear-gradient(135deg, #4b5563, #374151)';
                    " onmouseout="
                        this.style.background='linear-gradient(135deg, #6b7280, #4b5563)';
                    ">
                        <i class="fas fa-times"></i>
                        Close
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Hidden file input for image upload -->
        <input type="file" id="verifyFileInput" accept="image/*,image/png,image/jpeg,image/jpg" style="display: none;">
        
        <!-- QR Scanner Canvas -->
        <canvas id="verifyQrCanvas" style="display: none;"></canvas>
        
        <style>
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            
            @keyframes slideUp {
                from { 
                    opacity: 0;
                    transform: translateY(30px) scale(0.95);
                }
                to { 
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }
            
            @media (max-width: 768px) {
                .verification-modal-content {
                    padding: 25px !important;
                    margin: 20px !important;
                    width: calc(100% - 40px) !important;
                }
                
                .verification-methods {
                    grid-template-columns: 1fr !important;
                    gap: 12px !important;
                }
                
                .manual-verify-section > div:last-child {
                    flex-direction: column !important;
                    gap: 12px !important;
                }
                
                .manual-verify-section input {
                    font-size: 16px !important; /* Prevents zoom on iOS */
                }
            }
        </style>
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
    const scanBtn = document.getElementById('scanVerifyBtn'); // Add this line
    const fileInput = document.getElementById('verifyFileInput');
    const manualVerifyBtn = document.getElementById('verifyManualBtn');
    const manualInput = document.getElementById('manualVerifyInput');
    
    // Scan QR Code functionality - ADD THIS SECTION
    scanBtn?.addEventListener('click', () => {
        startQRScanner();
    });
    
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
                'X-Requested-With': 'XMLHttpRequest'
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

/**
 * Start QR Scanner for verification
 */
async function startQRScanner() {
    try {
        showVerificationLoading('Starting camera...');
        
        // Request camera access
        const stream = await navigator.mediaDevices.getUserMedia({
            video: { 
                facingMode: 'environment', // Use back camera if available
                width: { ideal: 1280 },
                height: { ideal: 720 }
            }
        });
        
        // Create video element for scanning
        const videoElement = document.createElement('video');
        videoElement.style.cssText = `
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 300px;
            height: 300px;
            border-radius: 15px;
            z-index: 10000;
            background: #000;
        `;
        videoElement.setAttribute('playsinline', true);
        videoElement.setAttribute('autoplay', true);
        
        // Create scanner overlay
        const scannerOverlay = document.createElement('div');
        scannerOverlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            z-index: 9999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        `;
        
        // Add close button to scanner
        const closeButton = document.createElement('button');
        closeButton.innerHTML = '<i class="fas fa-times"></i> Close Scanner';
        closeButton.style.cssText = `
            position: absolute;
            top: 20px;
            right: 20px;
            background: #ef4444;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            z-index: 10001;
        `;
        
        closeButton.onclick = () => {
            stream.getTracks().forEach(track => track.stop());
            scannerOverlay.remove();
            hideVerificationResult();
        };
        
        // Add instructions
        const instructions = document.createElement('div');
        instructions.innerHTML = `
            <h3 style="color: white; margin-bottom: 10px;">Position QR Code in Camera View</h3>
            <p style="color: #ccc; margin-bottom: 20px;">Hold the QR code steady in front of the camera</p>
        `;
        instructions.style.textAlign = 'center';
        
        scannerOverlay.appendChild(closeButton);
        scannerOverlay.appendChild(instructions);
        scannerOverlay.appendChild(videoElement);
        document.body.appendChild(scannerOverlay);
        
        // Start video
        videoElement.srcObject = stream;
        
        // Create canvas for QR detection
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');
        
        videoElement.addEventListener('loadedmetadata', () => {
            canvas.width = videoElement.videoWidth;
            canvas.height = videoElement.videoHeight;
            scanForQR();
        });
        
        let scanning = true;
        
        function scanForQR() {
            if (!scanning) return;
            
            if (videoElement.readyState === videoElement.HAVE_ENOUGH_DATA) {
                context.drawImage(videoElement, 0, 0, canvas.width, canvas.height);
                const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                
                if (typeof jsQR !== 'undefined') {
                    const code = jsQR(imageData.data, imageData.width, imageData.height);
                    
                    if (code) {
                        scanning = false;
                        stream.getTracks().forEach(track => track.stop());
                        scannerOverlay.remove();
                        verifyPaymentQR(code.data);
                        return;
                    }
                }
            }
            
            requestAnimationFrame(scanForQR);
        }
        
        hideVerificationResult();
        
    } catch (error) {
        console.error('Camera access error:', error);
        showVerificationError('Camera access denied. Please allow camera permission and try again.');
    }
}

/**
 * Hide verification result
 */
function hideVerificationResult() {
    const resultDiv = document.getElementById('verificationResult');
    if (resultDiv) {
        resultDiv.style.display = 'none';
        resultDiv.innerHTML = '';
    }
}

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
 * Global variables for settings
 */
let currentTheme = localStorage.getItem('theme') || 'light';

/**
 * Initialize settings functionality - FIXED VERSION
 */
function initializeSettings() {
    console.log('Initializing settings...');
    
    // Use event delegation to handle dynamically added elements
    document.addEventListener('click', function(e) {
        const settingsItem = e.target.closest('.dropdown-item[data-setting]');
        
        if (settingsItem) {
            e.preventDefault();
            e.stopPropagation();
            
            const setting = settingsItem.getAttribute('data-setting');
            console.log('Settings item clicked:', setting);
            
            if (setting) {
                // Close user dropdown first
                closeUserDropdown();
                
                // Small delay to ensure dropdown closes smoothly
                setTimeout(() => {
                    openSettingsModal(setting);
                }, 100);
            }
        }
    });
    
    console.log('Settings initialized');
}

/**
 * Initialize theme system
 */
function initializeTheme() {
    if (currentTheme === 'dark') {
        document.body.classList.add('dark-theme');
    }
}

/**
 * Initialize settings modal
 */
function initializeSettingsModal() {
    const modal = document.getElementById('settingsModal');
    
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeSettingsModal();
            }
        });
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal && modal.style.display === 'block') {
                closeSettingsModal();
            }
        });
    }
}

/**
 * Open settings modal
 */
function openSettingsModal(settingType) {
    console.log('Opening settings modal for:', settingType);
    
    const modal = document.getElementById('settingsModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalBody = document.getElementById('modalBody');
    
    if (!modal || !modalTitle || !modalBody) {
        console.error('Modal elements not found');
        return;
    }
    
    switch (settingType) {
        case 'profile':
            modalTitle.textContent = 'Profile Information';
            modalBody.innerHTML = getProfileContent();
            break;
        case 'password':
            modalTitle.textContent = 'Change Password';
            modalBody.innerHTML = getPasswordContent();
            break;
        case 'notifications':
            modalTitle.textContent = 'Notification Settings';
            modalBody.innerHTML = getNotificationsContent();
            break;
        case 'payment':
            modalTitle.textContent = 'Payment Preferences';
            modalBody.innerHTML = getPaymentContent();
            break;
        case 'theme':
            modalTitle.textContent = 'Theme Settings';
            modalBody.innerHTML = getThemeContent();
            break;
        case 'help':
            modalTitle.textContent = 'Help & Support';
            modalBody.innerHTML = getHelpContent();
            break;
        default:
            console.error('Unknown setting type:', settingType);
            return;
    }
    
    modal.style.display = 'block';
    initializeModalContent(settingType);
    console.log('Modal opened successfully');
}

/**
 * Close settings modal
 */
function closeSettingsModal() {
    console.log('Closing settings modal');
    const modal = document.getElementById('settingsModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

/**
 * Toggle theme
 */
function toggleTheme() {
    const body = document.body;
    const toggleSwitch = document.querySelector('.toggle-switch');
    
    if (currentTheme === 'light') {
        currentTheme = 'dark';
        body.classList.add('dark-theme');
        if (toggleSwitch) toggleSwitch.classList.add('active');
    } else {
        currentTheme = 'light';
        body.classList.remove('dark-theme');
        if (toggleSwitch) toggleSwitch.classList.remove('active');
    }
    
    localStorage.setItem('theme', currentTheme);
}

// Content functions
function getProfileContent() {
    return `
        <form id="profileForm" onsubmit="updateProfile(event)">
            <div class="form-group">
                <label for="firstName">First Name</label>
                <input type="text" id="firstName" name="firstName" required>
            </div>
            <div class="form-group">
                <label for="lastName">Last Name</label>
                <input type="text" id="lastName" name="lastName" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Profile
            </button>
        </form>
    `;
}

function getPasswordContent() {
    return `
        <form id="passwordForm" onsubmit="changePassword(event)">
            <div class="form-group">
                <label for="currentPassword">Current Password</label>
                <input type="password" id="currentPassword" name="currentPassword" required>
            </div>
            <div class="form-group">
                <label for="newPassword">New Password</label>
                <input type="password" id="newPassword" name="newPassword" required minlength="6">
            </div>
            <div class="form-group">
                <label for="confirmPassword">Confirm New Password</label>
                <input type="password" id="confirmPassword" name="confirmPassword" required>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-lock"></i> Change Password
            </button>
        </form>
    `;
}

function getNotificationsContent() {
    return `
        <form id="notificationsForm" onsubmit="updateNotifications(event)">
            <div class="form-group">
                <label>
                    <input type="checkbox" id="emailNotifications" checked>
                    Email Notifications
                </label>
                <small style="display: block; color: #666; margin-top: 5px;">
                    Receive payment confirmations and reminders via email
                </small>
            </div>
            <div class="form-group">
                <label>
                    <input type="checkbox" id="smsNotifications">
                    SMS Alerts
                </label>
                <small style="display: block; color: #666; margin-top: 5px;">
                    Receive important updates via SMS
                </small>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-bell"></i> Save Notification Settings
            </button>
        </form>
    `;
}

function getPaymentContent() {
    return `
        <form id="paymentForm" onsubmit="updatePaymentPreferences(event)">
            <div class="form-group">
                <label for="defaultPaymentMethod">Default Payment Method</label>
                <select id="defaultPaymentMethod">
                    <option value="">Select Payment Method</option>
                    <option value="gcash">GCash</option>
                    <option value="paymaya">PayMaya</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="cash">Cash</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-credit-card"></i> Save Payment Preferences
            </button>
        </form>
    `;
}

function getThemeContent() {
    return `
        <div class="theme-settings">
            <div class="theme-toggle">
                <span>Dark Mode</span>
                <div class="toggle-switch ${currentTheme === 'dark' ? 'active' : ''}" onclick="toggleTheme()">
                    <div class="toggle-slider"></div>
                </div>
            </div>
            <p style="color: #666; font-size: 0.9em; margin-top: 10px;">
                Toggle between light and dark theme for better viewing experience.
            </p>
            <button onclick="saveThemeSettings()" class="btn btn-primary">
                <i class="fas fa-palette"></i> Save Settings
            </button>
        </div>
    `;
}

function getHelpContent() {
    return `
        <div class="help-content">
            <h4><i class="fas fa-question-circle"></i> Need Help?</h4>
            <p>Contact support for assistance with your account.</p>
            <a href="mailto:support@example.com" class="btn btn-primary">
                <i class="fas fa-envelope"></i> Email Support
            </a>
        </div>
    `;
}

// Utility functions
function initializeModalContent(settingType) {
    console.log('Initializing modal content for:', settingType);
}

function updateProfile(event) {
    event.preventDefault();
    showNotification('Profile updated successfully!', 'success');
    closeSettingsModal();
}

function changePassword(event) {
    event.preventDefault();
    showNotification('Password changed successfully!', 'success');
    closeSettingsModal();
}

function updateNotifications(event) {
    event.preventDefault();
    showNotification('Notification settings updated!', 'success');
    closeSettingsModal();
}

function updatePaymentPreferences(event) {
    event.preventDefault();
    showNotification('Payment preferences updated!', 'success');
    closeSettingsModal();
}

function saveThemeSettings() {
    showNotification('Theme settings saved!', 'success');
    closeSettingsModal();
}

// Make functions globally available
window.closeSettingsModal = closeSettingsModal;
window.toggleTheme = toggleTheme;
window.updateProfile = updateProfile;
window.changePassword = changePassword;
window.updateNotifications = updateNotifications;
window.updatePaymentPreferences = updatePaymentPreferences;
window.saveThemeSettings = saveThemeSettings;

/**
 * Legacy function for logout confirmation
 */
function confirmLogout() {
    return confirm('Are you sure you want to logout?');
}