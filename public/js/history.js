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
 * Create payment history modal - FIXED SCROLLABLE VERSION
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
    
    // Add CSS with proper scrolling
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
                background-color: rgba(0,0,0,0.5);
                overflow: auto; /* Allow background scrolling */
            }
            .modal-content {
                background-color: #fefefe;
                margin: 2% auto;
                padding: 0;
                border: 1px solid #888;
                width: 90%;
                max-width: 700px;
                border-radius: 12px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.3);
                max-height: 95vh; /* Limit height to viewport */
                display: flex;
                flex-direction: column;
                overflow: hidden; /* Hide overflow on container */
            }
            .payment-history-container {
                display: flex;
                flex-direction: column;
                height: 100%;
                max-height: 95vh;
            }
            .modal-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 20px;
                border-bottom: 2px solid #eee;
                background: linear-gradient(45deg, #667eea, #764ba2);
                color: white;
                border-radius: 12px 12px 0 0;
                flex-shrink: 0; /* Don't shrink header */
            }
            .modal-header h4 {
                margin: 0;
                font-size: 1.3em;
            }
            #paymentHistoryContent {
                flex: 1;
                overflow-y: auto; /* Enable vertical scrolling */
                overflow-x: hidden;
                padding: 20px;
                max-height: calc(95vh - 80px); /* Account for header */
            }
            .loading {
                text-align: center;
                padding: 40px 20px;
            }
            .close {
                background: none;
                border: none;
                color: white;
                font-size: 28px;
                font-weight: bold;
                cursor: pointer;
                padding: 5px;
                border-radius: 50%;
                width: 40px;
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: background-color 0.2s;
            }
            .close:hover {
                background-color: rgba(255,255,255,0.1);
            }
            .payment-summary {
                background: #f8f9fa;
                padding: 20px;
                border-radius: 12px;
                margin-bottom: 25px;
                border: 1px solid #e9ecef;
            }
            .summary-details {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                gap: 20px;
            }
            .total-section {
                text-align: center;
                padding: 15px;
                background: white;
                border-radius: 8px;
                border: 1px solid #e9ecef;
            }
            .total-section h5 {
                margin: 0 0 8px 0;
                color: #666;
                font-size: 0.9em;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            .total-section .amount {
                font-size: 1.4em;
                font-weight: bold;
                color: #28a745;
                margin: 0;
            }
            .payment-history-list {
                margin-top: 10px;
            }
            .payment-history-list h5 {
                color: #333;
                margin-bottom: 20px;
                padding-bottom: 10px;
                border-bottom: 2px solid #e9ecef;
                font-size: 1.1em;
            }
            .payment-record {
                border: 1px solid #e9ecef;
                border-radius: 12px;
                padding: 20px;
                margin-bottom: 15px;
                background: white;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                transition: box-shadow 0.2s;
            }
            .payment-record:hover {
                box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            }
            .payment-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 15px;
                padding-bottom: 10px;
                border-bottom: 1px solid #f0f0f0;
            }
            .payment-date {
                color: #666;
                font-size: 0.95em;
                display: flex;
                align-items: center;
                gap: 8px;
            }
            .payment-amount {
                font-weight: bold;
                color: #28a745;
                font-size: 1.2em;
            }
            .payment-details {
                display: grid;
                gap: 10px;
            }
            .payment-details p {
                margin: 0;
                font-size: 0.95em;
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 8px 0;
            }
            .payment-details strong {
                color: #495057;
                min-width: 100px;
            }
            .verification-code {
                font-family: 'Courier New', monospace;
                background: #f8f9fa;
                padding: 4px 8px;
                border-radius: 6px;
                font-size: 0.9em;
                border: 1px solid #e9ecef;
            }
            .status-badge {
                display: inline-block;
                padding: 6px 12px;
                border-radius: 20px;
                font-size: 0.8em;
                font-weight: bold;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            .fully-paid {
                background: #d4edda;
                color: #155724;
                border: 1px solid #c3e6cb;
            }
            .partial {
                background: #fff3cd;
                color: #856404;
                border: 1px solid #ffeaa7;
            }
            .qr-section {
                text-align: center;
                margin-top: 15px;
                padding: 15px;
                background: #f8f9fa;
                border-radius: 8px;
            }
            .qr-section img {
                border: 2px solid #ddd;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            .qr-section button {
                margin-top: 12px;
                padding: 8px 16px;
                background: #007bff;
                color: white;
                border: none;
                border-radius: 6px;
                cursor: pointer;
                font-size: 0.9em;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                transition: background-color 0.2s;
            }
            .qr-section button:hover {
                background: #0056b3;
            }
            
            /* Mobile responsiveness */
            @media (max-width: 768px) {
                .modal-content {
                    width: 95%;
                    margin: 1% auto;
                    max-height: 98vh;
                }
                .summary-details {
                    grid-template-columns: 1fr;
                }
                .payment-header {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 10px;
                }
                .payment-details p {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 5px;
                }
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
                    <h5>Total Paid</h5>
                    <p class="amount">₱${parseFloat(summary.total_paid || 0).toFixed(2)}</p>
                </div>
                <div class="total-section">
                    <h5>Remaining Balance</h5>
                    <p class="amount" style="color: ${summary.remaining_balance <= 0 ? '#28a745' : '#dc3545'}">₱${parseFloat(summary.remaining_balance || 0).toFixed(2)}</p>
                </div>
                <div class="total-section">
                    <h5>Payment Status</h5>
                    <p class="status-badge ${summary.remaining_balance <= 0 ? 'fully-paid' : 'partial'}">
                        ${summary.remaining_balance <= 0 ? 'FULLY PAID' : 'PARTIAL PAYMENT'}
                    </p>
                </div>
            </div>
        </div>

        <div class="payment-history-list">
            <h5><i class="fas fa-history"></i> Payment Transactions (${payments.length})</h5>
            ${payments.map(payment => `
                <div class="payment-record">
                    <div class="payment-header">
                        <div class="payment-date">
                            <i class="fas fa-calendar-alt"></i>
                            ${new Date(payment.payment_date || payment.created_at).toLocaleString()}
                        </div>
                        <div class="payment-amount">₱${parseFloat(payment.amount_paid || 0).toFixed(2)}</div>
                    </div>
                    <div class="payment-details">
                        <p><strong>Method:</strong> <span>${(payment.payment_method || 'N/A').toUpperCase()}</span></p>
                        <p><strong>Status:</strong> <span>${(payment.payment_status || 'N/A').toUpperCase()}</span></p>
                        <p><strong>Verification:</strong> <span class="verification-code">${payment.verification_code || 'N/A'}</span></p>
                        ${payment.qr_receipt_path ? `
                            <div class="qr-section">
                                <img src="/writable/uploads/${payment.qr_receipt_path}" 
                                     alt="Payment QR" 
                                     style="width: 150px; height: 150px;">
                                <br>
                                <button onclick="downloadQR('${payment.qr_receipt_path}')">
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
    
    // Scroll to top of modal content
    content.scrollTop = 0;
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
