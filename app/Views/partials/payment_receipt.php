<?php
/**
 * Payment Receipt Partial
 * This partial displays a detailed payment receipt with QR code
 * 
 * Required variables:
 * - $payment: array containing payment details
 *   - student_name: string
 *   - student_id: string
 *   - payment_date: datetime
 *   - amount_paid: float
 *   - remaining: float
 *   - payment_type: string
 *   - payment_method: string
 *   - transaction_id: string
 *   - verification_status: string
 *   - qr_code: string (path to QR code image)
 */

// Set default values if $payment is not provided
if (!isset($payment)) {
    $payment = [
        'student_name' => '',
        'student_id' => '',
        'payment_date' => date('Y-m-d H:i:s'),
        'amount_paid' => 0,
        'remaining' => 0,
        'payment_type' => '',
        'payment_method' => '',
        'transaction_id' => '',
        'verification_status' => 'Not verified',
        'qr_code' => ''
    ];
}
?>
<div class="payment-receipt">
  <div class="receipt-header">
    <h3 class="receipt-title">
      <i class="fas fa-receipt"></i>
      Payment Receipt
    </h3>
    <p class="receipt-subtitle">Detailed payment information and QR code</p>
  </div>

  <!-- Student Information -->
  <div class="student-info">
    <div class="avatar-section">
      <div class="student-avatar">
        <i class="fas fa-user-graduate"></i>
      </div>
    </div>
    <div class="info-section">
      <h4><?= esc($payment['student_name'] ?: 'Loading...') ?></h4>
      <p>ID: <?= esc($payment['student_id'] ?: 'Loading...') ?></p>
      <small><?= $payment['payment_date'] ? date('m/d/Y, h:i:s A', strtotime($payment['payment_date'])) : 'Loading...' ?></small>
    </div>
  </div>

  <!-- Payment Summary -->
  <div class="payment-summary">
    <div class="amount-box amount-paid">
      <span class="label">AMOUNT PAID</span>
      <span class="value">₱<?= number_format($payment['amount_paid'] ?? 0, 2) ?></span>
    </div>
    <div class="amount-box amount-remaining">
      <span class="label">REMAINING</span>
      <span class="value">₱<?= number_format($payment['remaining'] ?? 0, 2) ?></span>
    </div>
    <div class="amount-box payment-status">
      <span class="label">STATUS</span>
      <span class="value status-<?= ($payment['remaining'] ?? 0) <= 0 ? 'paid' : 'partial' ?>">
        <?= ($payment['remaining'] ?? 0) <= 0 ? '<i class="fa fa-check-circle"></i> Fully Paid' : '<i class="fa fa-hourglass-half"></i> Partially Paid' ?>
      </span>
    </div>
  </div>

  <!-- Transaction Details -->
  <div class="transaction-details">
    <h5>Transaction Details</h5>
    <div class="details-grid">
      <div class="detail-item">
        <span class="label">Payment Type:</span>
        <span class="value"><?= esc($payment['payment_type'] ?? 'Loading...') ?></span>
      </div>
      <div class="detail-item">
        <span class="label">Receipt Number:</span>
        <span class="value"><?= esc($payment['receipt_number'] ?? 'N/A') ?></span>
      </div>
    </div>
  </div>

  <!-- QR Receipt Code and Actions -->
  <?php if (isset($payment['qr_code']) && !empty($payment['qr_code'])): ?>
  <div class="qr-section">
    <h5>QR Receipt Code</h5>
    <div class="qr-container">
      <img src="<?= base_url('writable/uploads/' . $payment['qr_code']) ?>" 
           alt="Payment QR Code" 
           class="qr-code" 
           id="qrCodeImage">
    </div>
  </div>
  <?php endif; ?>
  
  <div class="receipt-actions">
    <button onclick="PaymentReceipt.printReceipt()" class="btn btn-secondary btn-sm">
      <i class="fas fa-print"></i> Print Receipt
    </button>
    <?php if (isset($payment['qr_code']) && !empty($payment['qr_code'])): ?>
    <button onclick="PaymentReceipt.downloadQR()" class="btn btn-primary btn-sm">
      <i class="fas fa-download"></i> Download QR
    </button>
    <?php endif; ?>
  </div>

  <div class="receipt-footer">
    <button onclick="PaymentReceipt.closeModal()" class="close-btn">
      Close
    </button>
  </div>
</div>

<!-- Receipt JavaScript Module -->
<script>
const PaymentReceipt = {
    // Initialize the receipt functionality
    init: function(options = {}) {
        this.payment = options.payment || {};
        this.modalId = options.modalId || 'paymentDetailsModal';
        this.downloadUrl = options.downloadUrl || '';
        this.onClose = options.onClose || null;
        
        // Bind close button if modal exists
        const modal = document.getElementById(this.modalId);
        if (modal) {
            // Close on backdrop click
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    this.closeModal();
                }
            });
            
            // Close on ESC key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && modal.style.display === 'flex') {
                    this.closeModal();
                }
            });
        }
    },

    // Download QR code
    downloadQR: function() {
        const qrImage = document.getElementById('qrCodeImage');
        if (!qrImage || !qrImage.src) {
            alert('QR code image not available.');
            return;
        }

        // Create temporary link and trigger download
        const link = document.createElement('a');
        link.href = qrImage.src;
        link.download = `payment_receipt_qr_${Date.now()}.png`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    },

    // Print receipt
    printReceipt: function() {
        window.print();
    },

    // Close modal
    closeModal: function() {
        const modal = document.getElementById(this.modalId);
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
            
            // Call onClose callback if provided
            if (typeof this.onClose === 'function') {
                this.onClose();
            }
        }
    },

    // Show receipt in modal
    show: function(paymentId) {
        const modal = document.getElementById(this.modalId);
        if (!modal) return;

        // Fetch and display receipt
        fetch(window.location.origin + '/payments/renderReceiptPartial/' + paymentId)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    modal.querySelector('.modal-content').innerHTML = data.html;
                    modal.style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                    
                    // Initialize receipt with the new payment data
                    this.init({ payment: data.payment });
                } else {
                    console.error('Error rendering receipt:', data.message);
                    alert('Error loading receipt: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error fetching receipt:', error);
                alert('Error loading receipt. Please try again.');
            });
    }
};
</script>

<style>
/* Add styles for receipt actions */
.receipt-actions {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
    margin-top: 1rem;
}

.btn {
    padding: 0.5rem 1rem;
    border-radius: var(--radius-md);
    font-size: 0.875rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
}


.btn-primary {
    background: var(--primary-color);
    color: white;
}

.btn-primary:hover {
    background: blue;
}

.btn-secondary {
    background: var(--bg-secondary);
    color: var(--text-primary);
}

.btn-secondary:hover {
    background: lightgrey;
}

.btn-tertiary {
    background: transparent;
    color: var(--text-secondary);
    border: 1px solid var(--border-color);
}

.btn-tertiary:hover {
    background: var(--bg-secondary);
    color: var(--text-primary);
}

.btn i {
    font-size: 1rem;
}

/* Print styles */
@media print {
    @page {
        margin: 0;
        size: A4;
    }
    
    html, body {
        margin: 0 !important;
        padding: 0 !important;
        background: white !important;
        overflow: hidden !important;
        height: 100% !important;
        width: 100% !important;
    }

    /* Hide everything except the receipt */
    body > *:not(.payment-receipt):not(.modal-overlay):not(.modal-container):not(.modal-content) {
        display: none !important;
    }

    /* Reset modal styles for printing */
    .modal-overlay {
        position: static !important;
        background: none !important;
        overflow: visible !important;
        height: auto !important;
        display: block !important;
    }

    .modal-container {
        position: static !important;
        transform: none !important;
        max-height: none !important;
        overflow: visible !important;
        box-shadow: none !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .modal-content {
        overflow: visible !important;
        padding: 0 !important;
    }

    /* Style the receipt for print */
    .payment-receipt {
        display: block !important;
        position: relative !important;
        background: white !important;
        color: black !important;
        padding: 20px !important;
        margin: 0 auto !important;
        max-width: 210mm !important; /* A4 width */
        width: 100% !important;
        box-shadow: none !important;
        overflow: visible !important;
        float: none !important;
        page-break-after: always;
    }

    /* Hide UI elements */
    .receipt-actions, 
    button,
    .btn {
        display: none !important;
    }

    /* Ensure proper rendering of borders and backgrounds */
    * {
        -webkit-print-color-adjust: exact !important;
        color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    /* Fix any scrollbar issues */
    ::-webkit-scrollbar {
        display: none !important;
    }
}

/* Original styles below */
.payment-receipt {
  background: var(--bg-primary);
  border-radius: var(--radius-lg);
  padding: 1.5rem;
}

.receipt-header {
  margin-bottom: 1.5rem;
  text-align: center;
}

.receipt-title {
  font-size: 1.25rem;
  font-weight: 600;
  margin: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.receipt-subtitle {
  color: var(--text-secondary);
  margin: 0.5rem 0 0;
  font-size: 0.875rem;
}

.student-info {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid var(--border-color);
}

.student-avatar {
  width: 60px;
  height: 60px;
  background: var(--primary-color);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
}

.info-section h4 {
  margin: 0;
  font-size: 1.2rem;
  color: var(--text-primary);
}

.info-section p {
  margin: 0.25rem 0;
  color: var(--text-secondary);
  font-size: 0.9rem;
}

.info-section small {
  color: var(--text-tertiary);
  font-size: 0.8rem;
}

.payment-summary {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.amount-box {
  background: var(--bg-secondary);
  padding: 1rem;
  border-radius: var(--radius-md);
  text-align: center;
}

.amount-box .label {
  font-size: 0.75rem;
  color: var(--text-secondary);
  display: block;
  margin-bottom: 0.5rem;
}

.amount-box .value {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--text-primary);
}

.amount-paid .value {
  color: var(--success-color);
}

.status-paid {
  color: var(--success-color);
}

.status-partial {
  color: var(--warning-color);
}

.transaction-details {
  margin-bottom: 1.5rem;
}

.transaction-details h5 {
  margin: 0 0 1rem;
  color: var(--text-primary);
  font-size: 1rem;
}

.details-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
}

.detail-item {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.detail-item .label {
  font-size: 0.875rem;
  color: var(--text-secondary);
}

.detail-item .value {
  font-size: 0.9375rem;
  color: var(--text-primary);
  font-weight: 500;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
  display: none;
  align-items: center;
  justify-content: center;
  z-index: 2000;
  backdrop-filter: blur(4px);
}

.modal-container {
  background: var(--bg-primary);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-xl);
  width: 90%;
  max-width: 600px;
  max-height: 90vh;
  margin: 2rem;
  position: relative;
  overflow-y: auto;
  scrollbar-width: thin;
  scrollbar-color: var(--primary-color) transparent;
}

/* Scrollbar Styles */
.modal-container::-webkit-scrollbar {
  width: 6px;
  background: transparent;
}

.modal-container::-webkit-scrollbar-track {
  background: transparent;
  margin: 4px 0;
}

.modal-container::-webkit-scrollbar-thumb {
  background-color: var(--primary-color);
  border-radius: 20px;
  transition: opacity 0.3s;
  opacity: 0;
}

.modal-container:hover::-webkit-scrollbar-thumb {
  opacity: 1;
}

.modal-content {
  padding: 1.5rem;
}

.qr-section {
  text-align: center;
  background: var(--bg-secondary);
  border-radius: var(--radius-md);
  padding: 1.5rem;
  margin-top: 1.5rem;
}

.qr-section h5 {
  margin: 0 0 1rem;
  color: var(--text-primary);
  font-size: 1rem;
  font-weight: 600;
}

.qr-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
  background: var(--bg-primary);
  padding: 1rem;
  border-radius: var(--radius-md);
}

.qr-code {
  max-width: 180px;
  height: auto;
  border: 2px solid var(--border-color);
  border-radius: var(--radius-md);
  padding: 0.5rem;
  background: white;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.text-warning {
  color: var(--warning-color);
}

.text-success {
  color: var(--success-color);
}

.receipt-footer {
  display: flex;
  justify-content: flex-end;
  margin-top: 1.5rem;
  padding-top: 1rem;
  border-top: 1px solid var(--border-color);
}

.close-btn {
  padding: 0.5rem 1.5rem;
  font-size: 0.875rem;
  background: var(--bg-tertiary);
  color: var(--text-secondary);
  border: 1px solid var(--border-color);
  border-radius: var(--radius-md);
  transition: all 0.2s;
}

.close-btn:hover {
  background: lightcoral;
  color: white;
}

@media print {
  .receipt-footer {
    display: none !important;
  }
}
</style>