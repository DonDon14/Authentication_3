/**
 * Payment History Page JavaScript
 * Handles search, filtering, and payment management functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeHistoryPage();
});

function initializeHistoryPage() {
    // Initialize search functionality
    initializeSearch();
    
    // Initialize filtering
    initializeFilters();
    
    // Initialize export functionality
    initializeExport();
    
    // Initialize payment actions
    initializePaymentActions();
    
    // Initialize animations
    initializeAnimations();
    
    console.log('Payment History page initialized successfully');
}

/**
 * Initialize search functionality
 */
function initializeSearch() {
    const searchInput = document.getElementById('searchInput');
    
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        filterPayments(query);
    });
}

/**
 * Initialize filter functionality
 */
function initializeFilters() {
    const statusFilter = document.getElementById('statusFilter');
    const sortBy = document.getElementById('sortBy');
    
    statusFilter.addEventListener('change', function() {
        applyStatusFilter(this.value);
    });
    
    sortBy.addEventListener('change', function() {
        applySorting(this.value);
    });
}

/**
 * Initialize export functionality
 */
function initializeExport() {
    const exportBtn = document.getElementById('exportBtn');
    
    exportBtn.addEventListener('click', function() {
        exportPaymentHistory();
    });
}

/**
 * Initialize payment actions (verify, view receipt)
 */
function initializePaymentActions() {
    // Verify payment buttons
    const verifyButtons = document.querySelectorAll('.verify-payment');
    verifyButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const paymentId = this.dataset.id;
            verifyPayment(paymentId, this);
        });
    });
    
    // View receipt buttons
    const receiptButtons = document.querySelectorAll('.view-receipt');
    receiptButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const paymentRecord = this.closest('.payment-record');
            const studentName = paymentRecord.querySelector('h4').textContent;
            viewReceipt(studentName);
        });
    });
}

/**
 * Initialize animations
 */
function initializeAnimations() {
    // Add stagger animation to payment records
    const paymentRecords = document.querySelectorAll('.payment-record');
    
    paymentRecords.forEach((record, index) => {
        record.style.animationDelay = `${index * 0.1}s`;
        record.classList.add('fade-in-up');
    });
    
    // Add hover effects
    paymentRecords.forEach(record => {
        record.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px) scale(1.01)';
        });
        
        record.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
}

/**
 * Filter payments based on search query
 */
function filterPayments(query) {
    const paymentRecords = document.querySelectorAll('.payment-record');
    let visibleCount = 0;
    
    paymentRecords.forEach(record => {
        const studentName = record.querySelector('h4').textContent.toLowerCase();
        const studentId = record.querySelector('.student-id').textContent.toLowerCase();
        const paymentType = record.querySelector('.payment-type').textContent.toLowerCase();
        const qrReference = record.querySelector('.qr-reference').textContent.toLowerCase();
        
        const isMatch = studentName.includes(query) || 
                       studentId.includes(query) || 
                       paymentType.includes(query) || 
                       qrReference.includes(query);
        
        if (isMatch || query === '') {
            record.style.display = 'block';
            visibleCount++;
        } else {
            record.style.display = 'none';
        }
    });
    
    updateRecordsCount(visibleCount);
}

/**
 * Apply status filter
 */
function applyStatusFilter(status) {
    const paymentRecords = document.querySelectorAll('.payment-record');
    let visibleCount = 0;
    
    paymentRecords.forEach(record => {
        const recordStatus = record.dataset.status;
        
        if (status === '' || recordStatus === status) {
            record.style.display = 'block';
            visibleCount++;
        } else {
            record.style.display = 'none';
        }
    });
    
    updateRecordsCount(visibleCount);
}

/**
 * Apply sorting to payment records
 */
function applySorting(sortType) {
    const recordsContainer = document.getElementById('paymentRecords');
    const records = Array.from(recordsContainer.children);
    
    records.sort((a, b) => {
        switch (sortType) {
            case 'latest':
                return new Date(b.querySelector('.payment-date').textContent) - 
                       new Date(a.querySelector('.payment-date').textContent);
            
            case 'oldest':
                return new Date(a.querySelector('.payment-date').textContent) - 
                       new Date(b.querySelector('.payment-date').textContent);
            
            case 'amount-high':
                return parseFloat(b.querySelector('.amount').textContent.replace('$', '')) - 
                       parseFloat(a.querySelector('.amount').textContent.replace('$', ''));
            
            case 'amount-low':
                return parseFloat(a.querySelector('.amount').textContent.replace('$', '')) - 
                       parseFloat(b.querySelector('.amount').textContent.replace('$', ''));
            
            default:
                return 0;
        }
    });
    
    // Clear container and re-add sorted records
    recordsContainer.innerHTML = '';
    records.forEach(record => {
        recordsContainer.appendChild(record);
    });
}

/**
 * Update records count display
 */
function updateRecordsCount(count) {
    const recordsCount = document.querySelector('.records-count');
    recordsCount.textContent = `${count} payment${count !== 1 ? 's' : ''} found`;
}

/**
 * Verify payment
 */
function verifyPayment(paymentId, button) {
    // Show loading state
    const originalText = button.textContent;
    button.textContent = 'Verifying...';
    button.disabled = true;
    
    // Simulate API call (replace with actual API endpoint)
    setTimeout(() => {
        // Update payment record status
        const paymentRecord = button.closest('.payment-record');
        const statusBadge = paymentRecord.querySelector('.status-badge');
        
        // Update status badge
        statusBadge.className = 'status-badge status-verified';
        statusBadge.textContent = 'Verified';
        
        // Update dataset
        paymentRecord.dataset.status = 'verified';
        
        // Replace verify button with view receipt button
        button.outerHTML = `
            <button class="action-btn view-receipt">
                View Receipt
            </button>
        `;
        
        // Re-initialize receipt button
        const newReceiptBtn = paymentRecord.querySelector('.view-receipt');
        newReceiptBtn.addEventListener('click', function() {
            const studentName = paymentRecord.querySelector('h4').textContent;
            viewReceipt(studentName);
        });
        
        // Update stats
        updateStats();
        
        showNotification('Payment verified successfully!', 'success');
        
    }, 1500);
}

/**
 * View receipt for payment
 */
function viewReceipt(studentName) {
    // Create modal for receipt view
    const modal = createModal({
        title: `Payment Receipt - ${studentName}`,
        content: `
            <div class="receipt-content">
                <div class="receipt-header">
                    <h4>Payment Receipt</h4>
                    <p class="receipt-date">Date: ${new Date().toLocaleDateString()}</p>
                </div>
                
                <div class="receipt-details">
                    <div class="detail-row">
                        <span class="label">Student:</span>
                        <span class="value">${studentName}</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Payment Type:</span>
                        <span class="value">Uniform Payments</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Amount:</span>
                        <span class="value">$150.00</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Status:</span>
                        <span class="value status-verified">Verified</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Reference:</span>
                        <span class="value">Q5001</span>
                    </div>
                </div>
                
                <div class="receipt-footer">
                    <p>Thank you for your payment!</p>
                </div>
            </div>
        `,
        buttons: [
            {
                text: 'Print Receipt',
                class: 'btn-primary',
                action: () => {
                    window.print();
                }
            },
            {
                text: 'Close',
                class: 'btn-secondary',
                action: () => closeModal(modal)
            }
        ]
    });
}

/**
 * Export payment history
 */
function exportPaymentHistory() {
    const exportBtn = document.getElementById('exportBtn');
    const originalHTML = exportBtn.innerHTML;
    
    // Show loading state
    exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Exporting...';
    exportBtn.disabled = true;
    
    // Simulate export process
    setTimeout(() => {
        // Create CSV data
        const csvData = generateCSVData();
        downloadCSV(csvData, 'payment-history.csv');
        
        // Reset button
        exportBtn.innerHTML = originalHTML;
        exportBtn.disabled = false;
        
        showNotification('Payment history exported successfully!', 'success');
    }, 2000);
}

/**
 * Generate CSV data for export
 */
function generateCSVData() {
    const headers = ['Student Name', 'Student ID', 'Payment Type', 'Amount', 'Status', 'Date', 'Reference'];
    const records = document.querySelectorAll('.payment-record:not([style*="display: none"])');
    
    const csvRows = [headers.join(',')];
    
    records.forEach(record => {
        const studentName = record.querySelector('h4').textContent;
        const studentId = record.querySelector('.student-id').textContent.replace('ID: ', '');
        const paymentType = record.querySelector('.payment-type').textContent;
        const amount = record.querySelector('.amount').textContent;
        const status = record.querySelector('.status-badge').textContent;
        const date = record.querySelector('.payment-date').textContent;
        const reference = record.querySelector('.qr-reference').textContent.replace('QR: ', '');
        
        const row = [studentName, studentId, paymentType, amount, status, date, reference];
        csvRows.push(row.join(','));
    });
    
    return csvRows.join('\n');
}

/**
 * Download CSV file
 */
function downloadCSV(csvData, filename) {
    const blob = new Blob([csvData], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    
    link.href = url;
    link.download = filename;
    link.click();
    
    window.URL.revokeObjectURL(url);
}

/**
 * Update statistics after payment verification
 */
function updateStats() {
    const verifiedCount = document.querySelectorAll('[data-status="verified"]').length;
    const pendingCount = document.querySelectorAll('[data-status="pending"]').length;
    
    // Update stat cards
    document.querySelector('.stat-card-success .stat-value').textContent = verifiedCount;
    document.querySelector('.stat-card-warning .stat-value').textContent = pendingCount;
}

/**
 * Create modal element
 */
function createModal({ title, content, buttons }) {
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    
    modal.innerHTML = `
        <div class="modal-container">
            <div class="modal-header">
                <h3>${title}</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                ${content}
            </div>
            <div class="modal-footer">
                ${buttons.map(btn => `
                    <button class="btn ${btn.class}" data-action="${btn.text}">
                        ${btn.text}
                    </button>
                `).join('')}
            </div>
        </div>
    `;
    
    // Add event listeners
    const closeBtn = modal.querySelector('.modal-close');
    closeBtn.addEventListener('click', () => closeModal(modal));
    
    buttons.forEach((btn, index) => {
        const btnElement = modal.querySelectorAll('.modal-footer .btn')[index];
        btnElement.addEventListener('click', btn.action);
    });
    
    // Close on overlay click
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal(modal);
        }
    });
    
    document.body.appendChild(modal);
    return modal;
}

/**
 * Close modal
 */
function closeModal(modal) {
    modal.remove();
}

/**
 * Show notification
 */
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Add CSS for modals and notifications
const additionalStyles = `
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        animation: fadeIn 0.3s ease;
    }
    
    .modal-container {
        background: white;
        border-radius: 15px;
        width: 90%;
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
        animation: slideInUp 0.3s ease;
    }
    
    .modal-header {
        padding: 20px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .modal-header h3 {
        margin: 0;
        color: #111827;
    }
    
    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #6b7280;
    }
    
    .modal-body {
        padding: 20px;
    }
    
    .modal-footer {
        padding: 20px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        gap: 10px;
        justify-content: flex-end;
    }
    
    .receipt-content {
        max-width: 400px;
        margin: 0 auto;
    }
    
    .receipt-header {
        text-align: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #667eea;
    }
    
    .receipt-header h4 {
        color: #667eea;
        margin-bottom: 5px;
    }
    
    .receipt-date {
        color: #6b7280;
        font-size: 0.9rem;
    }
    
    .receipt-details {
        margin-bottom: 20px;
    }
    
    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .detail-row .label {
        font-weight: 500;
        color: #374151;
    }
    
    .detail-row .value {
        color: #111827;
    }
    
    .receipt-footer {
        text-align: center;
        padding-top: 15px;
        border-top: 2px solid #667eea;
        color: #667eea;
        font-weight: 500;
    }
    
    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .btn-secondary {
        background: #f3f4f6;
        color: #374151;
    }
    
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        color: white;
        z-index: 1001;
        transform: translateX(400px);
        transition: transform 0.3s ease;
    }
    
    .notification.show {
        transform: translateX(0);
    }
    
    .notification-success {
        background: #10b981;
    }
    
    .notification-error {
        background: #ef4444;
    }
    
    .notification-info {
        background: #3b82f6;
    }
    
    .fade-in-up {
        animation: fadeInUp 0.6s ease-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
`;

// Inject additional styles
const styleSheet = document.createElement('style');
styleSheet.textContent = additionalStyles;
document.head.appendChild(styleSheet);
