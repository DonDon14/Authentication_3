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
                  <span class="amount">₱<?= number_format($payment['amount_paid'], 2) ?></span>
                  <span class="status-badge status-<?= esc($payment['payment_status']) ?>"><?= ucfirst(esc($payment['payment_status'])) ?></span>
                </div>
              </div>
              <div class="record-details">
                <div class="payment-info">
                  <p class="payment-type"><?= esc($payment['payment_type'] ?? 'General Payment') ?></p>
                  <p class="payment-date"><?= date('M j, Y', strtotime($payment['created_at'])) ?></p>
                  
                  <?php if (!empty($payment['qr_receipt_path'])): ?>
                     
                  <?php else: ?>
                      <p class="qr-reference">QR: Not available</p>
                  <?php endif; ?>
                </div>
              </div>

              <div class="record-actions">
                <button class="action-btn view-receipt" 
                        data-payment='<?= htmlspecialchars(json_encode($payment)) ?>'>
                  View Receipt
                </button>
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

  <!-- FIXED MODAL STRUCTURE -->
  <div id="paymentDetailsModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999;">
    <div style="background: white; margin: 5% auto; padding: 0; width: 90%; max-width: 600px; border-radius: 12px; max-height: 80vh; overflow-y: auto; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
      
      <!-- Modal Header -->
      <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px; border-bottom: 1px solid #eee; background: linear-gradient(45deg, #667eea, #764ba2); color: white; border-radius: 12px 12px 0 0;">
        <h3 style="margin: 0;"><i class="fas fa-receipt"></i> Payment Receipt</h3>
        <button onclick="closePaymentModal()" class="close-btn" style="background: none; border: none; color: white; font-size: 24px; cursor: pointer; padding: 5px; border-radius: 5px;">
          <i class="fas fa-times"></i>
        </button>
      </div>
      
      <!-- Modal Body -->
      <div id="paymentDetailsContent" style="padding: 20px;">
        <div class="payment-summary-card">
          <div class="payment-info-grid" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div class="payment-info-item" style="text-align: center; padding: 15px; background: #f8f9fa; border-radius: 8px;">
              <span class="payment-info-label" style="display: block; font-size: 0.9em; color: #666; margin-bottom: 5px;">Total Paid</span>
              <span class="payment-info-value" id="modalTotalPaid" style="font-size: 1.2em; font-weight: bold; color: #28a745;">-</span>
            </div>
            <div class="payment-info-item" style="text-align: center; padding: 15px; background: #f8f9fa; border-radius: 8px;">
              <span class="payment-info-label" style="display: block; font-size: 0.9em; color: #666; margin-bottom: 5px;">Remaining Balance</span>
              <span class="payment-info-value" id="modalRemainingBalance" style="font-size: 1.2em; font-weight: bold; color: #dc3545;">-</span>
            </div>
            <div class="payment-info-item" style="text-align: center; padding: 15px; background: #f8f9fa; border-radius: 8px;">
              <span class="payment-info-label" style="display: block; font-size: 0.9em; color: #666; margin-bottom: 5px;">Status</span>
              <span class="payment-status-tag" id="modalPaymentStatus" style="display: inline-flex; align-items: center; gap: 5px; font-size: 0.9em; font-weight: bold; color: #28a745;">
                <i class="fas fa-check-circle"></i> <span id="statusText">-</span>
              </span>
            </div>
          </div>
        </div>

        <div class="qr-section" style="text-align: center; margin-top: 20px;">
          <div class="qr-code-img" id="modalQrCode" style="margin-bottom: 15px;">
            <!-- QR Code image will be inserted here -->
          </div>
          <button class="download-qr-btn" onclick="downloadQR()" style="background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
            <i class="fas fa-download"></i>
            Download QR Code
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- External JS -->
  <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
  <script src="<?= base_url('js/history.js') ?>"></script>
</body>
</html>