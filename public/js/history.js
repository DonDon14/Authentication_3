/**
 * Payment History Page JavaScript - Simplified Version
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
    const modal = document.getElementById('receiptModal');
    
    if (!modal) {
        console.log('Modal not found');
        return;
    }
    
    // Close button functionality
    const closeBtn = modal.querySelector('.modal-close');
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            modal.style.display = 'none';
        });
    }
    
    // Close on overlay click
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
    
    // Close with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.style.display === 'flex') {
            modal.style.display = 'none';
        }
    });
}

/**
 * Initialize receipt buttons
 */
function initializeReceiptButtons() {
    // Find all "View Receipt" buttons - corrected class name
    const receiptButtons = document.querySelectorAll('.view-receipt');
    
    receiptButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Get payment data from the data attribute (easier approach)
            const paymentData = JSON.parse(this.getAttribute('data-payment'));
            
            // Show receipt modal with data
            showReceiptModal({
                studentName: paymentData.student_name,
                studentId: paymentData.student_id,
                paymentType: paymentData.payment_type,
                amount: '$' + parseFloat(paymentData.amount_paid).toFixed(2),
                status: paymentData.payment_status,
                date: new Date(paymentData.created_at).toLocaleDateString()
            });
        });
    });
}

/**
 * Show receipt modal with payment data
 */
function showReceiptModal(paymentData) {
    const modal = document.getElementById('receiptModal');
    
    if (!modal) return;
    
    // Populate modal fields (only if elements exist)
    const elements = {
        'receiptStudentName': paymentData.studentName,
        'receiptStudentId': paymentData.studentId,
        'receiptPaymentType': paymentData.paymentType,
        'receiptAmount': paymentData.amount,
        'receiptDate': paymentData.date,
        'receiptStatus': paymentData.status
    };
    
    // Update elements if they exist
    Object.keys(elements).forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = elements[id];
        }
    });
    
    // Generate QR code if container exists
    generateReceiptQR(paymentData);
    
    // Show modal
    modal.style.display = 'flex';
}

/**
 * Generate QR code for receipt
 */
function generateReceiptQR(paymentData) {
    const qrContainer = document.getElementById('receiptQrCode');
    
    if (!qrContainer) return;
    
    // Create QR data
    const qrData = `Payment Receipt
Student: ${paymentData.studentName}
ID: ${paymentData.studentId}  
Type: ${paymentData.paymentType}
Amount: ${paymentData.amount}
Date: ${paymentData.date}
Status: ${paymentData.status}`;
    
    // Clear previous QR code
    qrContainer.innerHTML = '';
    
    // Generate QR code if QRCode library is available
    if (typeof QRCode !== 'undefined') {
        QRCode.toCanvas(qrContainer, qrData, {
            width: 200,
            height: 200,
            margin: 2,
            color: {
                dark: '#000000',
                light: '#FFFFFF'
            }
        }, function (error) {
            if (error) {
                console.error('QR Code generation failed:', error);
                qrContainer.innerHTML = '<p style="text-align: center; color: #666;">QR Code generation failed</p>';
            }
        });
    } else {
        qrContainer.innerHTML = '<p style="text-align: center; color: #666;">QR Code library not loaded</p>';
    }
}

/**
 * Print receipt function (can be called from modal)
 */
function printReceipt() {
    window.print();
}

/**
 * Close modal function (global function for onclick handlers)
 */
function closeReceiptModal() {
    const modal = document.getElementById('receiptModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// Make functions globally available for onclick handlers
window.printReceipt = printReceipt;
window.closeReceiptModal = closeReceiptModal;
