/**
 * Payment History Page JavaScript - Uses same modal as contribution_details
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Payment History page loaded');
    
    // Initialize modal functionality
    initializeModal();
    
    // Initialize receipt buttons  
    initializeReceiptButtons();
});

/**
 * Initialize modal functionality
 */
function initializeModal() {
    const modal = document.getElementById('paymentDetailsModal');
    
    if (!modal) {
        console.log('Modal not found');
        return;
    }
    
    console.log('Modal initialized successfully');
    
    // Close button functionality
    const closeBtns = modal.querySelectorAll('.close-btn, [onclick*="closePaymentModal"]');
    closeBtns.forEach(closeBtn => {
        closeBtn.addEventListener('click', function() {
            closePaymentModal();
        });
    });
    
    // Close on overlay click
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closePaymentModal();
        }
    });
    
    // Close with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.style.display === 'block') {
            closePaymentModal();
        }
    });
}

/**
 * Initialize receipt buttons
 */
function initializeReceiptButtons() {
    const receiptButtons = document.querySelectorAll('.view-receipt');
    
    console.log('Found receipt buttons:', receiptButtons.length);
    
    receiptButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('Receipt button clicked');
            
            const paymentDataStr = this.getAttribute('data-payment');
            
            if (!paymentDataStr) {
                console.error('No payment data found on button');
                return;
            }
            
            try {
                const paymentData = JSON.parse(paymentDataStr);
                console.log('Payment data:', paymentData);
                
                // Use the same function as contribution_details.js
                showStudentPaymentHistory(paymentData.contribution_id, paymentData.student_id);
            } catch (error) {
                console.error('Error parsing payment data:', error);
                alert('Error loading payment details');
            }
        });
    });
}

/**
 * Show student payment history - SAME AS contribution_details.js
 */
function showStudentPaymentHistory(contributionId, studentId) {
    console.log('Showing payment history for:', contributionId, studentId);
    
    fetch(`/payments/studentPaymentHistory/${contributionId}/${studentId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                displayPaymentHistoryModal(data);
            } else {
                throw new Error(data.message || 'Failed to load payment history');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading payment history: ' + error.message);
        });
}

/**
 * Create payment history modal - SAME AS contribution_details.js
 */
function createPaymentHistoryModal() {
    const modal = document.createElement('div');
    modal.id = 'paymentHistoryModal';
    modal.className = 'modal';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="payment-history-container">
                <div class="modal-header">
                    <h4>Payment History</h4>
                    <button type="button" class="close" onclick="closePaymentModal()">&times;</button>
                </div>
                <div id="paymentHistoryContent">
                    <div class="loading">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p>Loading payment history...</p>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Add CSS if not already present
    if (!document.getElementById('payment-modal-styles')) {
        const styles = document.createElement('style');
        styles.id = 'payment-modal-styles';
        styles.textContent = `
            .modal {
                display: none;
                position: fixed;
                z-index: 1000;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0,0,0,0.4);
            }
            .modal-content {
                background-color: #fefefe;
                margin: 15% auto;
                padding: 20px;
                border: 1px solid #888;
                width: 80%;
                max-width: 600px;
                border-radius: 8px;
            }
            .loading {
                text-align: center;
                padding: 20px;
            }
            .close {
                float: right;
                font-size: 28px;
                font-weight: bold;
                cursor: pointer;
            }
            .close:hover {
                color: #000;
            }
            .modal-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                border-bottom: 2px solid #eee;
                padding-bottom: 15px;
                margin-bottom: 20px;
            }
            .modal-title {
                font-size: 1.5em;
                margin: 0;
                color: #333;
            }
            .payment-history-container {
                padding: 20px;
                max-width: 600px;
                margin: 0 auto;
            }
            .summary-box {
                display: grid;
                gap: 15px;
            }
            .summary-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .payment-item {
                border-bottom: 1px solid #eee;
                padding: 10px 0;
            }
            .payment-info {
                display: flex;
                justify-content: space-between;
                margin-bottom: 5px;
            }
            .payment-summary {
                background: #f8f9fa;
                padding: 15px;
                border-radius: 8px;
                margin-bottom: 20px;
            }
            .total-section {
                margin-bottom: 15px;
            }
            .total-section h5 {
                margin: 0 0 5px 0;
                color: #666;
            }
            .total-section .amount {
                font-size: 1.2em;
                font-weight: bold;
                color: #28a745;
                margin: 0;
            }
            .payment-record {
                border: 1px solid #e9ecef;
                border-radius: 8px;
                padding: 15px;
                margin-bottom: 10px;
                background: white;
            }
            .payment-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 10px;
            }
            .payment-date {
                color: #666;
                font-size: 0.9em;
            }
            .payment-amount {
                font-weight: bold;
                color: #28a745;
            }
            .payment-details p {
                margin: 5px 0;
                font-size: 0.9em;
            }
            .verification-code {
                font-family: monospace;
                background: #f8f9fa;
                padding: 2px 6px;
                border-radius: 4px;
            }
            .status-badge {
                display: inline-block;
                padding: 4px 8px;
                border-radius: 4px;
                font-size: 0.8em;
                font-weight: bold;
                text-transform: uppercase;
            }
            .fully-paid {
                background: #d4edda;
                color: #155724;
            }
            .partial {
                background: #fff3cd;
                color: #856404;
            }
        `;
        document.head.appendChild(styles);
    }

    document.body.appendChild(modal);
    return modal;
}

/**
 * Display payment history modal - SAME AS contribution_details.js
 */
function displayPaymentHistoryModal(data) {
    if (!data || !data.payments) {
        console.error('Invalid payment data received');
        return;
    }

    let modal = document.getElementById('paymentHistoryModal');
    if (!modal) {
        modal = createPaymentHistoryModal();
    }

    const content = modal.querySelector('#paymentHistoryContent');
    const payments = data.payments;
    const summary = data.summary || {};

    let html = `
        <div class="payment-summary">
            <div class="summary-details">
                <div class="total-section">
                    <h5>Total Paid:</h5>
                    <p class="amount">₱${parseFloat(summary.total_paid || 0).toFixed(2)}</p>
                </div>
                <div class="total-section">
                    <h5>Remaining Balance:</h5>
                    <p class="amount">₱${parseFloat(summary.remaining_balance || 0).toFixed(2)}</p>
                </div>
                <div class="total-section">
                    <h5>Payment Status:</h5>
                    <p class="status-badge ${summary.remaining_balance <= 0 ? 'fully-paid' : 'partial'}">
                        ${summary.remaining_balance <= 0 ? 'FULLY PAID' : 'PARTIAL PAYMENT'}
                    </p>
                </div>
            </div>
        </div>

        <div class="payment-history-list">
            <h5>Payment Transactions (${payments.length})</h5>
            ${payments.map(payment => `
                <div class="payment-record">
                    <div class="payment-header">
                        <div class="payment-date">
                            <i class="fas fa-calendar"></i>
                            ${new Date(payment.payment_date).toLocaleString()}
                        </div>
                        <div class="payment-amount">₱${parseFloat(payment.amount_paid || 0).toFixed(2)}</div>
                    </div>
                    <div class="payment-details">
                        <p><strong>Method:</strong> ${(payment.payment_method || '').toUpperCase()}</p>
                        <p><strong>Status:</strong> ${(payment.payment_status || '').toUpperCase()}</p>
                        <p><strong>Verification:</strong> <span class="verification-code">${payment.verification_code || 'N/A'}</span></p>
                        ${payment.qr_receipt_path ? `
                            <div class="qr-section" style="text-align: center; margin-top: 10px;">
                                <img src="/writable/uploads/${payment.qr_receipt_path}" 
                                     alt="Payment QR" 
                                     style="width: 150px; height: 150px; border: 1px solid #ddd; border-radius: 4px;">
                                <br>
                                <button onclick="downloadQR('${payment.qr_receipt_path}')" 
                                        style="margin-top: 10px; padding: 5px 10px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
                                    <i class="fas fa-download"></i> Download QR
                                </button>
                            </div>
                        ` : ''}
                    </div>
                </div>
            `).join('')}
        </div>
    `;

    content.innerHTML = html;
    modal.style.display = 'block';
}

/**
 * Close modal functions - SAME AS contribution_details.js
 */
function closePaymentModal() {
    const modal = document.getElementById('paymentHistoryModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

/**
 * Download QR function - SAME AS contribution_details.js
 */
function downloadQR(qrPath) {
    if (!qrPath) {
        console.error('No QR path provided');
        return;
    }

    const filename = `payment_qr_${Date.now()}.png`;
    fetch(`/writable/uploads/${qrPath}`)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.blob();
        })
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        })
        .catch(error => {
            console.error('Error downloading QR:', error);
            alert('Error downloading QR code');
        });
}

// Make functions globally available
window.closePaymentModal = closePaymentModal;
window.downloadQR = downloadQR;
window.showStudentPaymentHistory = showStudentPaymentHistory;

// Close modal when clicking outside
window.addEventListener('click', function(event) {
    const modal = document.getElementById('paymentHistoryModal');
    if (event.target === modal) {
        closePaymentModal();
    }
});
