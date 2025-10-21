document.addEventListener('DOMContentLoaded', function() {
    console.log('User dashboard loaded');
    
    initializeUserMenu();
    initializePaymentItems();
    initializeModal();
});

/**
 * Initialize user menu functionality
 */
function initializeUserMenu() {
    // Close menu when clicking outside
    document.addEventListener('click', function(e) {
        const userMenu = document.getElementById('userMenu');
        const profileBtn = document.querySelector('.profile-btn');
        
        if (userMenu && !userMenu.contains(e.target) && !profileBtn.contains(e.target)) {
            userMenu.classList.remove('show');
        }
    });
}

/**
 * Toggle user menu
 */
function toggleUserMenu() {
    const userMenu = document.getElementById('userMenu');
    if (userMenu) {
        userMenu.classList.toggle('show');
    }
}

/**
 * Initialize payment item interactions
 */
function initializePaymentItems() {
    const paymentItems = document.querySelectorAll('.payment-item');
    
    paymentItems.forEach(item => {
        item.addEventListener('click', function() {
            const paymentId = this.getAttribute('data-payment-id');
            if (paymentId) {
                viewPaymentDetails(paymentId);
            }
        });
    });
}

/**
 * View payment details in modal
 */
function viewPaymentDetails(paymentId) {
    console.log('Viewing payment details for ID:', paymentId);
    
    // Show loading in modal
    showModal();
    const modalContent = document.getElementById('paymentModalContent');
    
    if (modalContent) {
        modalContent.innerHTML = `
            <div style="text-align: center; padding: 40px;">
                <i class="fas fa-spinner fa-spin" style="font-size: 2em; color: #667eea; margin-bottom: 15px;"></i>
                <p>Loading payment details...</p>
            </div>
        `;
    }
    
    // Fetch payment details
    fetch(`/user/payment-details/${paymentId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch payment details');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                displayPaymentDetails(data.payment);
            } else {
                throw new Error(data.message || 'Failed to load payment details');
            }
        })
        .catch(error => {
            console.error('Error loading payment details:', error);
            if (modalContent) {
                modalContent.innerHTML = `
                    <div style="text-align: center; padding: 40px; color: #ef4444;">
                        <i class="fas fa-exclamation-triangle" style="font-size: 2em; margin-bottom: 15px;"></i>
                        <p>Error loading payment details</p>
                        <button class="btn-primary" onclick="closeModal()" style="margin-top: 15px;">Close</button>
                    </div>
                `;
            }
        });
}

/**
 * Display payment details in modal
 */
function displayPaymentDetails(payment) {
    const modalContent = document.getElementById('paymentModalContent');
    
    if (!modalContent) return;
    
    const html = `
        <div class="payment-details">
            <div class="payment-summary" style="background: linear-gradient(45deg, #f8fafc, #f1f5f9); padding: 20px; border-radius: 12px; margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h4 style="margin: 0; color: #1f2937;">${payment.contribution_title || 'General Payment'}</h4>
                    <span class="status status-${payment.payment_status}" style="font-size: 0.8em;">
                        ${payment.payment_status.charAt(0).toUpperCase() + payment.payment_status.slice(1)}
                    </span>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <p style="font-size: 0.85em; color: #6b7280; margin-bottom: 5px;">Amount Paid</p>
                        <p style="font-size: 1.5em; font-weight: 700; color: #1f2937; margin: 0;">₱${parseFloat(payment.amount_paid).toFixed(2)}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.85em; color: #6b7280; margin-bottom: 5px;">Payment Date</p>
                        <p style="font-size: 1em; font-weight: 600; color: #1f2937; margin: 0;">${formatDate(payment.payment_date)}</p>
                    </div>
                </div>
            </div>
            
            <div class="payment-info-grid" style="display: grid; gap: 15px;">
                <div class="info-row" style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f1f5f9;">
                    <span style="font-weight: 600; color: #6b7280;">Payment Method:</span>
                    <span style="color: #1f2937;">${formatPaymentMethod(payment.payment_method)}</span>
                </div>
                
                <div class="info-row" style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f1f5f9;">
                    <span style="font-weight: 600; color: #6b7280;">Verification Code:</span>
                    <span style="color: #1f2937; font-family: monospace; background: #f1f5f9; padding: 4px 8px; border-radius: 4px;">${payment.verification_code || 'N/A'}</span>
                </div>
                
                <div class="info-row" style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f1f5f9;">
                    <span style="font-weight: 600; color: #6b7280;">Transaction ID:</span>
                    <span style="color: #1f2937; font-family: monospace;">#${payment.id}</span>
                </div>
                
                ${payment.qr_receipt_path ? `
                    <div class="qr-section" style="text-align: center; margin-top: 20px;">
                        <p style="font-weight: 600; color: #6b7280; margin-bottom: 10px;">QR Receipt</p>
                        <img src="/writable/uploads/${payment.qr_receipt_path}" 
                             alt="QR Receipt" 
                             style="width: 200px; height: 200px; border: 2px solid #e5e7eb; border-radius: 12px; margin-bottom: 15px;">
                        <br>
                        <button class="btn-primary" onclick="downloadQR('${payment.qr_receipt_path}')" 
                                style="padding: 8px 16px; font-size: 0.9em;">
                            <i class="fas fa-download"></i> Download QR
                        </button>
                    </div>
                ` : ''}
            </div>
        </div>
    `;
    
    modalContent.innerHTML = html;
}

/**
 * Check payment status for a contribution
 */
function checkPaymentStatus(contributionId) {
    console.log('Checking payment status for contribution:', contributionId);
    
    // Show loading toast
    showNotification('Checking your payment status...', 'info');
    
    fetch(`/user/check-payment-status/${contributionId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            // Update UI with payment status
            updatePaymentStatusDisplay(contributionId, data.status);
        } else {
            showNotification(data.message || 'Error checking payment status', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Connection error. Please try again.', 'error');
    });
}