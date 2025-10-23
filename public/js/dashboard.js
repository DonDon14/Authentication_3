/**
 * Dashboard JavaScript
 * Handles dropdown menus, notifications, sidebar toggle, and other interactive elements
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
    
    // Initialize verify button
    initializeVerifyButton();
    
    console.log('Dashboard initialized successfully');
}

// Sidebar and header functionality has been moved to main.js and header-components.js
// This is kept for compatibility but functionality is handled by those files

// Header functionality has been moved to header-components.js
// This space intentionally left empty

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
window.showVerificationModal = function() {
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
window.closeVerificationModal = function() {
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
        case 'announcements':
            modalTitle.textContent = 'Manage Announcements';
            modalBody.innerHTML = getAnnouncementsContent();
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

/**
 * Get announcements management content
 */
function getAnnouncementsContent() {
    return `
        <div class="announcements-manager">
            <!-- Add New Announcement Button -->
            <div class="announcement-header" style="
                display: flex; 
                justify-content: space-between; 
                align-items: center; 
                margin-bottom: 25px;
                padding-bottom: 15px;
                border-bottom: 2px solid #e9ecef;
            ">
                <h4 style="margin: 0; color: #333; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-bullhorn" style="color: #667eea;"></i>
                    Announcements Management
                </h4>
                <button onclick="showAddAnnouncementForm()" class="btn btn-primary" style="
                    background: linear-gradient(45deg, #10b981, #059669);
                    border: none;
                    padding: 10px 20px;
                    border-radius: 8px;
                    color: white;
                    cursor: pointer;
                    font-weight: 600;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    transition: all 0.2s ease;
                ">
                    <i class="fas fa-plus"></i>
                    Add New
                </button>
            </div>

            <!-- Add/Edit Form (Initially Hidden) -->
            <div id="announcementForm" style="display: none; margin-bottom: 25px;">
                <div style="
                    background: linear-gradient(145deg, #f8fafc, #f1f5f9);
                    padding: 25px;
                    border-radius: 12px;
                    border: 1px solid #e2e8f0;
                ">
                    <h5 id="formTitle" style="
                        margin: 0 0 20px 0; 
                        color: #334155;
                        font-size: 18px;
                        font-weight: 600;
                        display: flex;
                        align-items: center;
                        gap: 8px;
                    ">
                        <i class="fas fa-edit" style="color: #667eea;"></i>
                        Add New Announcement
                    </h5>
                    
                    <form id="announcementFormElement" onsubmit="saveAnnouncement(event)">
                        <input type="hidden" id="announcementId" name="id">
                        
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label for="announcementTitle" style="
                                display: block; 
                                margin-bottom: 8px; 
                                font-weight: 600; 
                                color: #374151;
                            ">Title</label>
                            <input type="text" id="announcementTitle" name="title" required style="
                                width: 100%;
                                padding: 12px 15px;
                                border: 2px solid #e5e7eb;
                                border-radius: 8px;
                                font-size: 15px;
                                transition: border-color 0.2s ease;
                                box-sizing: border-box;
                            ">
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label for="announcementContent" style="
                                display: block; 
                                margin-bottom: 8px; 
                                font-weight: 600; 
                                color: #374151;
                            ">Content</label>
                            <textarea id="announcementContent" name="content" rows="4" required style="
                                width: 100%;
                                padding: 12px 15px;
                                border: 2px solid #e7e9eb;
                                border-radius: 8px;
                                font-size: 15px;
                                font-family: inherit;
                                transition: border-color 0.2s ease;
                                resize: vertical;
                                box-sizing: border-box;
                            "></textarea>
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label for="announcementType" style="
                                display: block; 
                                margin-bottom: 8px; 
                                font-weight: 600; 
                                color: #374151;
                            ">Type</label>
                            <select id="announcementType" name="type" required style="
                                width: 100%;
                                padding: 12px 15px;
                                border: 2px solid #e7e9eb;
                                border-radius: 8px;
                                font-size: 15px;
                                transition: border-color 0.2s ease;
                                box-sizing: border-box;
                            ">
                                <option value="">Select Type</option>
                                <option value="general">General</option>
                                <option value="payment">Payment</option>
                                <option value="urgent">Urgent</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 25px;">
                            <label style="
                                display: flex;
                                align-items: center;
                                gap: 10px;
                                font-weight: 600;
                                color: #374151;
                                cursor: pointer;
                            ">
                                <input type="checkbox" id="announcementActive" name="active" checked style="
                                    width: 18px;
                                    height: 18px;
                                    accent-color: #667eea;
                                ">
                                Active (Visible to users)
                            </label>
                        </div>
                        
                        <div style="display: flex; gap: 10px; justify-content: flex-end;">
                            <button type="button" onclick="cancelAnnouncementForm()" style="
                                background: #6b7280;
                                color: white;
                                border: none;
                                padding: 12px 20px;
                                border-radius: 8px;
                                cursor: pointer;
                                font-weight: 500;
                                display: flex;
                                align-items: center;
                                gap: 8px;
                                transition: background-color 0.2s ease;
                            ">
                                <i class="fas fa-times"></i>
                                Cancel
                            </button>
                            <button type="submit" style="
                                background: linear-gradient(45deg, #667eea, #764ba2);
                                color: white;
                                border: none;
                                padding: 12px 20px;
                                border-radius: 8px;
                                cursor: pointer;
                                font-weight: 600;
                                display: flex;
                                align-items: center;
                                gap: 8px;
                                transition: all 0.2s ease;
                            ">
                                <i class="fas fa-save"></i>
                                Save Announcement
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Announcements List -->
            <div id="announcementsList">
                <div style="
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 15px;
                ">
                    <h5 style="margin: 0; color: #334155; font-weight: 600;">
                        Current Announcements
                    </h5>
                    <button onclick="loadAnnouncements()" style="
                        background: none;
                        border: 1px solid #d1d5db;
                        padding: 6px 12px;
                        border-radius: 6px;
                        cursor: pointer;
                        color: #6b7280;
                        font-size: 14px;
                        display: flex;
                        align-items: center;
                        gap: 6px;
                        transition: all 0.2s ease;
                    ">
                        <i class="fas fa-sync-alt"></i>
                        Refresh
                    </button>
                </div>
                
                <div id="announcementsContainer" style="
                    max-height: 400px;
                    overflow-y: auto;
                    border: 1px solid #e5e7eb;
                    border-radius: 8px;
                    background: white;
                ">
                    <div style="
                        padding: 40px 20px;
                        text-align: center;
                        color: #6b7280;
                    ">
                        <i class="fas fa-spinner fa-spin" style="font-size: 24px; margin-bottom: 10px;"></i>
                        <p>Loading announcements...</p>
                    </div>
                </div>
            </div>
        </div>
    `;
}

/**
 * Initialize modal content - ADD ANNOUNCEMENTS CASE
 */
function initializeModalContent(settingType) {
    console.log('Initializing modal content for:', settingType);
    
    if (settingType === 'announcements') {
        loadAnnouncements();
    }
}

/**
 * ANNOUNCEMENTS MANAGEMENT FUNCTIONS
 */

// Global variable to store announcements
let announcements = [];
let editingAnnouncementId = null;

/**
 * Load announcements from server
 */
async function loadAnnouncements() {
    try {
        const container = document.getElementById('announcementsContainer');
        if (!container) return;
        
        // Show loading
        container.innerHTML = `
            <div style="padding: 40px 20px; text-align: center; color: #6b7280;">
                <i class="fas fa-spinner fa-spin" style="font-size: 24px; margin-bottom: 10px;"></i>
                <p>Loading announcements...</p>
            </div>
        `;
        
        // For now, we'll use mock data. In a real app, you'd fetch from your server:
        // const response = await fetch('/api/announcements');
        // const data = await response.json();
        
        // Mock data - replace this with actual API call
        setTimeout(() => {
            const mockAnnouncements = [
                {
                    id: 1,
                    title: "System Maintenance Notice",
                    content: "The payment system will be under maintenance on Friday, 10 PM to 2 AM. Please complete your payments before this time.",
                    type: "maintenance",
                    active: true,
                    created_at: new Date().toISOString()
                },
                {
                    id: 2,
                    title: "New Payment Method Available",
                    content: "You can now pay using PayMaya in addition to GCash and bank transfers.",
                    type: "payment",
                    active: true,
                    created_at: new Date(Date.now() - 86400000).toISOString()
                }
            ];
            
            announcements = mockAnnouncements;
            displayAnnouncements(announcements);
        }, 1000);
        
    } catch (error) {
        console.error('Error loading announcements:', error);
        const container = document.getElementById('announcementsContainer');
        if (container) {
            container.innerHTML = `
                <div style="padding: 40px 20px; text-align: center; color: #ef4444;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 24px; margin-bottom: 10px;"></i>
                    <p>Error loading announcements</p>
                </div>
            `;
        }
    }
}

/**
 * Display announcements in the list
 */
function displayAnnouncements(announcementsList) {
    const container = document.getElementById('announcementsContainer');
    if (!container) return;
    
    if (announcementsList.length === 0) {
        container.innerHTML = `
            <div style="padding: 40px 20px; text-align: center; color: #6b7280;">
                <i class="fas fa-bullhorn" style="font-size: 24px; margin-bottom: 10px; opacity: 0.5;"></i>
                <p>No announcements found</p>
                <small>Click "Add New" to create your first announcement</small>
            </div>
        `;
        return;
    }
    
    const typeColors = {
        general: '#3b82f6',
        payment: '#10b981',
        urgent: '#ef4444',
        maintenance: '#f59e0b'
    };
    
    container.innerHTML = announcementsList.map(announcement => `
        <div class="announcement-item" style="
            border-bottom: 1px solid #f3f4f6;
            padding: 20px;
            transition: background-color 0.2s ease;
        " onmouseover="this.style.backgroundColor='#f9fafb'" onmouseout="this.style.backgroundColor='white'">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                <div style="flex: 1;">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                        <h6 style="margin: 0; color: #111827; font-weight: 600; font-size: 16px;">
                            ${announcement.title}
                        </h6>
                        <span style="
                            background: ${typeColors[announcement.type] || '#6b7280'};
                            color: white;
                            padding: 2px 8px;
                            border-radius: 12px;
                            font-size: 11px;
                            font-weight: 600;
                            text-transform: uppercase;
                            letter-spacing: 0.5px;
                        ">
                            ${announcement.type}
                        </span>
                        ${announcement.active ? 
                            '<span style="color: #10b981; font-size: 12px;"><i class="fas fa-eye"></i> Active</span>' : 
                            '<span style="color: #6b7280; font-size: 12px;"><i class="fas fa-eye-slash"></i> Inactive</span>'
                        }
                    </div>
                    <p style="
                        margin: 0 0 8px 0; 
                        color: #4b5563; 
                        line-height: 1.4;
                        font-size: 14px;
                    ">
                        ${announcement.content}
                    </p>
                    <small style="color: #9ca3af; font-size: 12px;">
                        <i class="fas fa-clock"></i>
                        Created: ${new Date(announcement.created_at).toLocaleString()}
                    </small>
                </div>
                <div style="display: flex; gap: 8px; margin-left: 15px;">
                    <button onclick="editAnnouncement(${announcement.id})" style="
                        background: #f59e0b;
                        color: white;
                        border: none;
                        padding: 8px 12px;
                        border-radius: 6px;
                        cursor: pointer;
                        font-size: 12px;
                        font-weight: 500;
                        display: flex;
                        align-items: center;
                        gap: 4px;
                        transition: background-color 0.2s ease;
                    " title="Edit Announcement">
                        <i class="fas fa-edit"></i>
                        Edit
                    </button>
                    <button onclick="deleteAnnouncement(${announcement.id})" style="
                        background: #ef4444;
                        color: white;
                        border: none;
                        padding: 8px 12px;
                        border-radius: 6px;
                        cursor: pointer;
                        font-size: 12px;
                        font-weight: 500;
                        display: flex;
                        align-items: center;
                        gap: 4px;
                        transition: background-color 0.2s ease;
                    " title="Delete Announcement">
                        <i class="fas fa-trash"></i>
                        Delete
                    </button>
                </div>
            </div>
        </div>
    `).join('');
}

/**
 * Show add announcement form
 */
function showAddAnnouncementForm() {
    editingAnnouncementId = null;
    const form = document.getElementById('announcementForm');
    const formTitle = document.getElementById('formTitle');
    const formElement = document.getElementById('announcementFormElement');
    
    if (form && formTitle && formElement) {
        formTitle.innerHTML = '<i class="fas fa-plus" style="color: #10b981;"></i> Add New Announcement';
        formElement.reset();
        document.getElementById('announcementId').value = '';
        form.style.display = 'block';
        
        // Scroll to form
        form.scrollIntoView({ behavior: 'smooth' });
    }
}

/**
 * Edit announcement
 */
function editAnnouncement(id) {
    const announcement = announcements.find(a => a.id === id);
    if (!announcement) return;
    
    editingAnnouncementId = id;
    const form = document.getElementById('announcementForm');
    const formTitle = document.getElementById('formTitle');
    
    if (form && formTitle) {
        formTitle.innerHTML = '<i class="fas fa-edit" style="color: #f59e0b;"></i> Edit Announcement';
        
        // Populate form
        document.getElementById('announcementId').value = announcement.id;
        document.getElementById('announcementTitle').value = announcement.title;
        document.getElementById('announcementContent').value = announcement.content;
        document.getElementById('announcementType').value = announcement.type;
        document.getElementById('announcementActive').checked = announcement.active;
        
        form.style.display = 'block';
        form.scrollIntoView({ behavior: 'smooth' });
    }
}

/**
 * Cancel announcement form
 */
function cancelAnnouncementForm() {
    const form = document.getElementById('announcementForm');
    if (form) {
        form.style.display = 'none';
        editingAnnouncementId = null;
    }
}

/**
 * Save announcement
 */
async function saveAnnouncement(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const announcementData = {
        id: formData.get('id') || null,
        title: formData.get('title'),
        content: formData.get('content'),
        type: formData.get('type'),
        active: formData.get('active') === 'on'
    };
    
    try {
        // Show loading
        const submitBtn = event.target.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
        submitBtn.disabled = true;
        
        // Simulate API call - replace with actual API call
        await new Promise(resolve => setTimeout(resolve, 1000));
        
        if (editingAnnouncementId) {
            // Update existing
            const index = announcements.findIndex(a => a.id === editingAnnouncementId);
            if (index !== -1) {
                announcements[index] = {
                    ...announcements[index],
                    ...announcementData,
                    id: editingAnnouncementId
                };
            }
            showNotification('Announcement updated successfully!', 'success');
        } else {
            // Add new
            const newAnnouncement = {
                ...announcementData,
                id: Date.now(), // In real app, this would come from server
                created_at: new Date().toISOString()
            };
            announcements.unshift(newAnnouncement);
            showNotification('Announcement created successfully!', 'success');
        }
        
        // Reset form and refresh list
        cancelAnnouncementForm();
        displayAnnouncements(announcements);
        
        // Restore button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        
    } catch (error) {
        console.error('Error saving announcement:', error);
        showNotification('Error saving announcement. Please try again.', 'error');
        
        // Restore button
        const submitBtn = event.target.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<i class="fas fa-save"></i> Save Announcement';
        submitBtn.disabled = false;
    }
}

/**
 * Delete announcement
 */
async function deleteAnnouncement(id) {
    if (!confirm('Are you sure you want to delete this announcement?')) {
        return;
    }
    
    try {
        // Simulate API call - replace with actual API call
        await new Promise(resolve => setTimeout(resolve, 500));
        
        announcements = announcements.filter(a => a.id !== id);
        displayAnnouncements(announcements);
        showNotification('Announcement deleted successfully!', 'success');
        
    } catch (error) {
        console.error('Error deleting announcement:', error);
        showNotification('Error deleting announcement. Please try again.', 'error');
    }
}

/**
 * Update user profile
 */
function updateProfile(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
    submitBtn.disabled = true;
    
    // Convert FormData to URLSearchParams for proper submission
    const data = new URLSearchParams();
    for (let [key, value] of formData.entries()) {
        data.append(key, value);
    }
    
    // Submit form
    fetch('/profile/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: data
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message || 'Profile updated successfully!', 'success');
            closeSettingsModal();
        } else {
            showNotification(data.message || 'Failed to update profile', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while updating profile', 'error');
    })
    .finally(() => {
        // Reset button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

/**
 * Change user password
 */
function changePassword(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    // Validate passwords match
    const newPassword = formData.get('newPassword');
    const confirmPassword = formData.get('confirmPassword');
    
    if (newPassword !== confirmPassword) {
        showNotification('New password and confirmation do not match', 'error');
        return;
    }
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
    submitBtn.disabled = true;
    
    // Convert FormData to URLSearchParams for proper submission
    const data = new URLSearchParams();
    for (let [key, value] of formData.entries()) {
        data.append(key, value);
    }
    
    // Submit form
    fetch('/profile/change-password', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: data
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message || 'Password updated successfully!', 'success');
            closeSettingsModal();
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
        // Reset button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

/**
 * Update notification settings
 */
function updateNotifications(event) {
    event.preventDefault();
    showNotification('Notification settings updated successfully!', 'success');
    closeSettingsModal();
}

/**
 * Update payment preferences
 */
function updatePaymentPreferences(event) {
    event.preventDefault();
    showNotification('Payment preferences updated successfully!', 'success');
    closeSettingsModal();
}

/**
 * Save theme settings
 */
function saveThemeSettings(event) {
    event.preventDefault();
    showNotification('Theme settings saved successfully!', 'success');
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
window.showAddAnnouncementForm = showAddAnnouncementForm;
window.editAnnouncement = editAnnouncement;
window.cancelAnnouncementForm = cancelAnnouncementForm;
window.saveAnnouncement = saveAnnouncement;
window.deleteAnnouncement = deleteAnnouncement;
window.loadAnnouncements = loadAnnouncements;

/**
 * Legacy function for logout confirmation
 */
function confirmLogout() {
    return confirm('Are you sure you want to logout?');
}