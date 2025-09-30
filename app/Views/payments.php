<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Record Payment</title>
  <link rel="stylesheet" href="<?= base_url('css/payments.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- QR Code Scanner Library -->
  <script src="https://unpkg.com/jsqr@1.4.0/dist/jsQR.js"></script>
</head>
<body>
  <div class="payments-container">
    <!-- Header -->
    <div class="payments-header">
      <div class="back-button">
        <a href="<?= base_url('dashboard') ?>" class="back-btn">
          <i class="fas fa-arrow-left"></i>
        </a>
      </div>
      <div class="header-content">
        <h2>Record Payment</h2>
        <p class="description">Add a new student payment</p>
      </div>
    </div>

    <!-- Success/Error Messages -->
    <div class="success-message" id="successMessage"></div>
    <div class="error-message" id="errorMessage"></div>

    <!-- Contribution Information (when specific contribution is selected) -->
    <?php if (isset($contribution)): ?>
    <div class="contribution-info-card">
      <div class="contribution-card-header">
        <h3><i class="fas fa-hand-holding-usd"></i> Recording Payment For</h3>
      </div>
      <div class="contribution-card-body">
        <div class="contribution-details">
          <h4><?= esc($contribution['title']) ?></h4>
          <p class="contribution-desc"><?= esc($contribution['description']) ?></p>
          <div class="contribution-meta">
            <span class="contribution-amount">Amount: ₱<?= number_format($contribution['amount'], 2) ?></span>
            <span class="contribution-category"><?= esc($contribution['category']) ?></span>
          </div>
        </div>
      </div>
      <?php if (isset($payments) && count($payments) > 0): ?>
      <div class="payment-stats">
        <div class="stat-item">
          <span class="stat-label">Paid Students:</span>
          <span class="stat-value"><?= count($payments) ?></span>
        </div>
        <div class="stat-item">
          <span class="stat-label">Total Collected:</span>
          <span class="stat-value">₱<?= number_format(array_sum(array_column($payments, 'amount_paid')), 2) ?></span>
        </div>
      </div>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Partial Payment Information -->
    <?php if (isset($mode) && $mode === 'partial_payment'): ?>
    <div class="partial-payment-info-card">
      <div class="contribution-card-header">
        <h3><i class="fas fa-clock"></i> Adding Partial Payment</h3>
      </div>
      <div class="contribution-card-body">
        <div class="contribution-details">
          <h4><?= esc($contribution['title']) ?></h4>
          <p class="contribution-desc"><?= esc($contribution['description']) ?></p>
          <div class="payment-status-info">
            <div class="status-row">
              <span class="label">Total Due:</span>
              <span class="value">$<?= number_format($payment_status['total_amount_due'], 2) ?></span>
            </div>
            <div class="status-row">
              <span class="label">Already Paid:</span>
              <span class="value">$<?= number_format($payment_status['total_paid'], 2) ?></span>
            </div>
            <div class="status-row">
              <span class="label">Remaining Balance:</span>
              <span class="value remaining-balance">$<?= number_format($payment_status['remaining_balance'], 2) ?></span>
            </div>
          </div>
          
          <?php if (count($payment_status['payments']) > 0): ?>
          <div class="payment-history">
            <h5>Payment History:</h5>
            <ul>
              <?php foreach ($payment_status['payments'] as $payment): ?>
                <li>
                  Payment <?= $payment['payment_sequence'] ?>: $<?= number_format($payment['amount_paid'], 2) ?> 
                  (<?= date('M j, Y', strtotime($payment['payment_date'])) ?>)
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <div class="qr-code-scanner">
        <h3>Scan QR Code</h3>
        <div class="qr-button-group">
          <button type="button" class="btn-primary" id="scanQRButton">
            <i class="fas fa-qrcode"></i> Scan Now
          </button>
          <button type="button" class="btn-secondary" id="uploadQRButton">
            <i class="fas fa-upload"></i> Upload QR
          </button>
        </div>
        <div class="upload-processing" id="uploadProcessing" style="display: none;">
          <div class="upload-spinner"></div>
          <p id="uploadProcessingText">Processing uploaded QR code...</p>
        </div>
        <input type="file" id="qrFileInput" accept="image/*" style="display: none;">
    </div>

    <!-- QR Scanner Modal -->
    <div id="qrScannerModal" class="qr-modal">
      <div class="qr-modal-content">
        <div class="qr-modal-header">
          <h3>Scan Student QR Code</h3>
          <button type="button" class="qr-close-btn" id="closeQRScanner">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="qr-modal-body">
          <div class="scanner-container">
            <video id="qrVideo" autoplay playsinline></video>
            <canvas id="qrCanvas" style="display: none;"></canvas>
            <div class="scanner-overlay">
              <div class="scanner-box">
                <div class="scanner-line"></div>
              </div>
            </div>
          </div>
          <div class="scanner-status">
            <p id="scannerStatus">Position the QR code within the scanner box</p>
          </div>
          <div class="processing-indicator" id="processingIndicator" style="display: none;">
            <div class="processing-spinner"></div>
            <p id="processingText">Processing QR Code...</p>
          </div>
          <div class="scanner-result" id="scannerResult" style="display: none;">
            <div class="result-info">
              <h4>Scanned Information:</h4>
              <p><strong>Student ID:</strong> <span id="scannedId"></span></p>
              <p><strong>Student Name:</strong> <span id="scannedName"></span></p>
              <p><strong>Course:</strong> <span id="scannedCourse"></span></p>
            </div>
            <div class="result-actions">
              <button type="button" class="btn-primary" id="useScannedData">Use This Data</button>
              <button type="button" class="btn-secondary" id="scanAgain">Scan Again</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Payment Form -->
    <form id="paymentForm" autocomplete="off">
      <!-- Hidden fields for contribution data -->
      <?php if (isset($contribution_id)): ?>
      <input type="hidden" id="contributionId" name="contribution_id" value="<?= $contribution_id ?>">
      <?php endif; ?>
      
      <!-- Student Search Section -->
      <div class="form-group">
        <label for="studentSearch">Search Existing Student</label>
        <div class="search-container">
          <input type="text" id="studentSearch" placeholder="Search by name or ID..." autocomplete="off">
          <i class="fas fa-search input-icon"></i>
          <div class="search-results" id="searchResults" style="display: none;"></div>
        </div>
        <p class="form-note">Search for existing students or add new student information below</p>
      </div>

      <!-- Manual QR Code Entry -->
      <div class="form-group">
        <label for="manualQR">Or Enter QR Code Data</label>
        <div class="qr-input-container">
          <input type="text" id="manualQR" placeholder="Paste or type QR code data here..." autocomplete="off">
          <button type="button" id="searchQRBtn" class="qr-search-btn">
            <i class="fas fa-search"></i>
          </button>
          <i class="fas fa-qrcode input-icon"></i>
        </div>
        <p class="form-note">Enter QR code data manually to auto-fill student information</p>
      </div>

      <div class="form-group">
        <label for="studentName">Student Name</label>
        <input type="text" id="studentName" name="student_name" placeholder="Enter student full name" required>
        <i class="fas fa-user input-icon"></i>
      </div>

      <div class="form-group">
        <label for="studentId">Student ID</label>
        <input type="text" id="studentId" name="student_id" placeholder="e.g., STU001" required>
        <i class="fas fa-id-card input-icon"></i>
      </div>

      <?php if (!isset($contribution)): ?>
      <div class="form-group">
        <label for="contributionType">
          Contribution Type 
          <a href="<?= base_url('contributions') ?>" class="manage-link">Manage</a>
        </label>
        <select id="contributionType" name="contribution_type" required>
          <option value="">Select contribution type</option>
          <?php if (isset($all_contributions) && !empty($all_contributions)): ?>
            <?php foreach ($all_contributions as $contrib): ?>
              <option value="<?= $contrib['id'] ?>" data-amount="<?= $contrib['amount'] ?>">
                <?= esc($contrib['title']) ?> - $<?= number_format($contrib['amount'], 2) ?>
              </option>
            <?php endforeach; ?>
          <?php else: ?>
            <option value="" disabled>No active contributions available</option>
          <?php endif; ?>
        </select>
        <i class="fas fa-list input-icon"></i>
      </div>
      <?php endif; ?>

      <div class="form-group">
        <label for="amount">Amount ($)</label>
        <input type="number" id="amount" name="amount" 
               value="<?= isset($contribution) ? number_format($contribution['amount'], 2, '.', '') : '' ?>" 
               placeholder="0.00" step="0.01" min="0" required
               <?= isset($contribution) ? '' : '' ?>>
        <i class="fas fa-dollar-sign input-icon"></i>
        
        <!-- Payment Type Selection -->
        <div class="payment-type-section" style="margin-top: 10px;">
          <div class="radio-group">
            <label class="radio-label">
              <input type="radio" name="payment_type" value="full" id="fullPayment" checked>
              <span class="radio-custom"></span>
              <span class="radio-text">Full Payment</span>
            </label>
            <label class="radio-label">
              <input type="radio" name="payment_type" value="partial" id="partialPayment">
              <span class="radio-custom"></span>
              <span class="radio-text">Partial Payment</span>
            </label>
          </div>
        </div>
        
        <!-- Payment Status Display -->
        <div id="paymentStatusDisplay" style="display: none; margin-top: 10px; padding: 12px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #17a2b8;">
          <div class="status-info">
            <p><strong>Payment Status:</strong> <span id="statusText"></span></p>
            <p><strong>Total Due:</strong> $<span id="totalDue">0.00</span></p>
            <p><strong>Already Paid:</strong> $<span id="totalPaid">0.00</span></p>
            <p><strong>Remaining Balance:</strong> $<span id="remainingBalance">0.00</span></p>
          </div>
          <div id="paymentHistory" style="margin-top: 10px;">
            <strong>Payment History:</strong>
            <ul id="paymentHistoryList" style="margin: 8px 0; padding-left: 20px;"></ul>
          </div>
        </div>
        
        <?php if (isset($contribution)): ?>
        <small class="field-note">Full amount: $<?= number_format($contribution['amount'], 2) ?> | You can make partial payments</small>
        <?php endif; ?>
      </div>

      <div class="form-group">
        <label for="paymentMethod">Payment Method</label>
        <select id="paymentMethod" name="payment_method" required>
          <option value="cash">Cash</option>
          <option value="card">Card</option>
          <option value="bank_transfer">Bank Transfer</option>
          <option value="mobile_payment">Mobile Payment</option>
        </select>
        <i class="fas fa-credit-card input-icon"></i>
      </div>

      <!-- Buttons -->
      <div class="form-group form-row-full">
        <div class="button-group">
          <button type="submit" class="btn-primary">
            Record Payment
          </button>
        </div>
      </div>
    </form>

    <!-- Info Note -->
    <div class="info-note">
      <div class="note-icon">
        <i class="fas fa-info-circle"></i>
      </div>
      <div class="note-content">
        <strong>Note:</strong>
        A QR receipt will be automatically generated after recording the payment. Students can use this receipt for verification purposes.
        <div class="note-footer">
          <a href="#" class="contribution-link">
            <?php 
            $contributionCount = isset($all_contributions) ? count($all_contributions) : 0;
            echo $contributionCount;
            ?> contribution<?= $contributionCount !=1 ? ' types' : ' type' ?> available
        </div>
      </div>
    </div>
  </div>

  <!-- Bottom Navigation -->
  <nav class="bottom-nav">
    <a href="<?= base_url('dashboard') ?>" class="nav-link">
      <i class="fas fa-home"></i>
      <span>Home</span>
    </a>
    <a href="<?= base_url('payments') ?>" class="nav-link active">
      <i class="fas fa-credit-card"></i>
      <span>Payments</span>
    </a>
    <a href="<?= base_url('contributions') ?>" class="nav-link">
      <i class="fas fa-hand-holding-usd"></i>
      <span>Contributions</span>
    </a>
    <a href="<?= base_url('payments/history') ?>" class="nav-link">
      <i class="fas fa-clock"></i>
      <span>History</span>
    </a>
  </nav>

  <!-- External JS -->
  <script>
    // Pass student data to JavaScript
    window.STUDENTS_DATA = <?= json_encode($all_users ?? []) ?>;
  </script>
  <script src="<?= base_url('js/payments.js') ?>"></script>
</body>
</html>
