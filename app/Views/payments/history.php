<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment History</title>
  <link rel="stylesheet" href="<?= base_url('css/history.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="history-container">
    <!-- Header -->
    <div class="history-header">
      <div class="welcome-section">
        <h2>Payment History</h2>
        <p class="description">View all payment records</p>
      </div>
    </div>

    <!-- Stats Section -->
    <div class="stats-grid">
      <div class="stat-card stat-card-primary">
        <div class="stat-icon">
          <i class="fas fa-peso-sign"></i>
        </div>
        <div class="stat-content">
          <p class="stat-label">Total</p>
          <p class="stat-value">₱<?= number_format($totalAmount ?? 0, 2) ?></p>
        </div>
      </div>
      
      <div class="stat-card stat-card-success">
        <div class="stat-icon">
          <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
          <p class="stat-label">Verified</p>
          <p class="stat-value"><?= $verifiedCount ?? 0 ?></p>
        </div>
      </div>
      
      <div class="stat-card stat-card-warning">
        <div class="stat-icon">
          <i class="fas fa-clock"></i>
        </div>
        <div class="stat-content">
          <p class="stat-label">Pending</p>
          <p class="stat-value"><?= $pendingCount ?? 0 ?></p>
        </div>
      </div>
      
      <div class="stat-card stat-card-info">
        <div class="stat-icon">
          <i class="fas fa-calendar-day"></i>
        </div>
        <div class="stat-content">
          <p class="stat-label">Today</p>
          <p class="stat-value"><?= $todayCount ?? 0 ?></p>
        </div>
      </div>
    </div>

    <!-- Payment Records Section -->
    <div class="records-section">
      <div class="section-header">
        <h3>Payment Records</h3>
        <p class="records-count"><?= count($payments ?? []) ?> payments found</p>
      </div>
      
      <div class="payment-records" id="paymentRecords">
        <?php if (!empty($payments)): ?>
          <?php foreach ($payments as $payment): ?>
            <div class="payment-record" data-status="<?= esc($payment['payment_status']) ?>">
              <div class="record-header">
                <div class="student-info">
                  <h4><?= esc($payment['student_name']) ?></h4>
                  <p class="student-id">ID: <?= esc($payment['student_id']) ?></p>
                </div>
                <div class="payment-amount">
                  <span class="amount">$<?= number_format($payment['amount_paid'], 2) ?></span>
                  <span class="status-badge status-<?= esc($payment['payment_status']) ?>"><?= ucfirst(esc($payment['payment_status'])) ?></span>
                </div>
              </div>
              <div class="record-details">
              <div class="payment-info">
                  <p class="payment-type"><?= esc($payment['payment_type']) ?></p>
                  <p class="payment-date"><?= date('M j, Y', strtotime($payment['created_at'])) ?></p>
                  
                  <?php if (!empty($payment['qr_receipt_path'])): ?>
                      <div class="qr-code-container">
                          <p class="qr-reference">QR Receipt:</p>
                          <img src="<?= base_url('uploads/' . esc($payment['qr_receipt_path'])) ?>" 
                              alt="QR Receipt" 
                              class="qr-code-image"
                              style="width: 100px; height: 100px; border: 1px solid #ddd;">
                      </div>
                  <?php else: ?>
                      <p class="qr-reference">QR: Not available</p>
                  <?php endif; ?>
              </div>
          </div>
                <div class="record-actions">
                  <?php if ($payment['payment_status'] === 'verified'): ?>
                    <button class="action-btn view-receipt" 
                            data-payment='<?= json_encode($payment) ?>'>
                      View Receipt
                    </button>
                  <?php else: ?>
                    <button class="action-btn verify-payment" data-id="<?= esc($payment['id']) ?>">
                      Verify Payment
                    </button>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="no-payments">
            <i class="fas fa-receipt"></i>
            <p>No payment records found</p>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Bottom Navigation -->
    <nav class="bottom-nav">
      <a href="<?= base_url('dashboard') ?>" class="nav-link">
        <i class="fas fa-home"></i>
        <span>Home</span>
      </a>
      <a href="<?= base_url('payments') ?>" class="nav-link">
        <i class="fas fa-credit-card"></i>
        <span>Payments</span>
      </a>
      <a href="<?= base_url('contributions') ?>" class="nav-link">
        <i class="fas fa-hand-holding-usd"></i>
        <span>Contributions</span>
      </a>
      <a href="<?= base_url('payments/history') ?>" class="nav-link active">
        <i class="fas fa-clock"></i>
        <span>History</span>
      </a>
    </nav>
  </div>

  <!-- Payment Receipt Modal -->
  <div class="modal-overlay" id="receiptModal" style="display: none;">
    <div class="modal-container">
        <div class="modal-header">
            <h3>Payment Receipt</h3>
            <button class="modal-close" onclick="closeReceiptModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="receipt-content">
                <div class="receipt-info">
                    <div class="info-row">
                        <span class="label">Student Name:</span>
                        <span class="value" id="receiptStudentName">-</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Student ID:</span>
                        <span class="value" id="receiptStudentId">-</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Payment Type:</span>
                        <span class="value" id="receiptPaymentType">-</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Amount:</span>
                        <span class="value" id="receiptAmount">-</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Date:</span>
                        <span class="value" id="receiptDate">-</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Status:</span>
                        <span class="value" id="receiptStatus">-</span>
                    </div>
                </div>
                
                <!-- QR Code Section -->
                <div class="qr-section">
                    <h4>Payment QR Code</h4>
                    <div id="receiptQrCode"></div>
                </div>
                
                <div class="receipt-actions">
                    <button class="btn-print" onclick="printReceipt()">
                        <i class="fas fa-print"></i> Print Receipt
                    </button>
                    <button class="btn-download" onclick="downloadReceipt()">
                        <i class="fas fa-download"></i> Download
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

  <!-- External JS -->
  <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
  <script src="<?= base_url('js/history.js') ?>"></script>
</body>
</html>