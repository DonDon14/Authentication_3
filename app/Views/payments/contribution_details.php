<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($contribution['title']) ?> - Payment Details</title>
  <link rel="stylesheet" href="<?= base_url('css/contribution_details.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
</head>
<body>
  <div class="contribution-details-container">
    <!-- Header -->
    <div class="contribution-details-header">
      <div class="back-button">
        <a href="<?= base_url('contributions') ?>" class="back-btn">
          <i class="fas fa-arrow-left"></i>
        </a>
      </div>
      <div class="header-content">
        <h2><?= esc($contribution['title']) ?></h2>
        <p class="description">Payment Details & History</p>
      </div>
      <div class="header-actions">
        <a href="<?= base_url('payments?contribution=' . $contribution['id']) ?>" class="btn-primary">
          <i class="fas fa-plus"></i> Add Payment
        </a>
      </div>
    </div>

    <!-- Contribution Summary -->
    <div class="contribution-summary">
      <div class="summary-card">
        <div class="contribution-info">
          <h3><?= esc($contribution['title']) ?></h3>
          <p class="contribution-description"><?= esc($contribution['description']) ?></p>
          <div class="contribution-meta">
            <span class="amount-badge">$<?= number_format($contribution['amount'], 2) ?></span>
            <span class="category-badge"><?= esc($contribution['category']) ?></span>
            <span class="status-badge status-<?= strtolower($contribution['status']) ?>">
              <i class="fas fa-<?= $contribution['status'] === 'active' ? 'check-circle' : 'pause-circle' ?>"></i>
              <?= ucfirst($contribution['status']) ?>
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Statistics -->
    <div class="stats-grid">
      <div class="stat-card stat-primary">
        <div class="stat-icon">
          <i class="fas fa-users"></i>
        </div>
        <div class="stat-content">
          <h4><?= $stats['total_payments'] ?></h4>
          <p>Students Paid</p>
        </div>
      </div>
      
      <div class="stat-card stat-success">
        <div class="stat-icon">
          <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-content">
          <h4>₱<?= number_format($stats['total_amount'], 2) ?></h4>
          <p>Total Collected</p>
        </div>
      </div>
      
      <div class="stat-card stat-info">
        <div class="stat-icon">
          <i class="fas fa-chart-line"></i>
        </div>
        <div class="stat-content">
          <h4>₱<?= number_format($stats['average_amount'], 2) ?></h4>
          <p>Average Payment</p>
        </div>
      </div>
      
      <div class="stat-card stat-warning">
        <div class="stat-icon">
          <i class="fas fa-percentage"></i>
        </div>
        <div class="stat-content">
          <h4><?= $stats['total_payments'] > 0 ? number_format(($stats['total_amount'] / ($contribution['amount'] * $stats['total_payments'])) * 100, 1) : '0' ?>%</h4>
          <p>Payment Rate</p>
        </div>
      </div>
    </div>

    <!-- Students Who Paid Section -->
    <div class="payment-history-section">
      <div class="section-header">
        <div class="section-title-group">
          <h3><i class="fas fa-users"></i> Students Who Paid</h3>
          <p class="section-description">
            <?php if (count($payments) > 0): ?>
              <?= count($payments) ?> student<?= count($payments) > 1 ? 's have' : ' has' ?> paid for this contribution
            <?php else: ?>
              No students have paid for this contribution yet
            <?php endif; ?>
          </p>
        </div>
        
        <!-- Search Bar -->
        <?php if (count($payments) > 0): ?>
        <div class="search-section">
          <div class="search-container">
            <input type="text" id="studentSearchInput" placeholder="Search students by name or ID..." class="search-input">
            <i class="fas fa-search search-icon"></i>
          </div>
          <div class="search-stats">
            <span id="searchResults">Showing <?= count($payments) ?> of <?= count($payments) ?> students</span>
          </div>
        </div>
        <?php endif; ?>
      </div>

      <?php if (count($payments) > 0): ?>
        <div class="students-list" id="studentsList">
          <?php foreach ($payments as $payment): ?>
            <div class="student-payment-item" data-payment-id="<?= $payment['id'] ?>" 
                 data-student-name="<?= strtolower(esc($payment['student_name'])) ?>" 
                 data-student-id="<?= strtolower(esc($payment['student_id'])) ?>"
                 style="cursor: pointer; position: relative;">
              
              <!-- Invisible click overlay for modal -->
              <div class="click-overlay" onclick="showStudentPaymentHistory('<?= $payment['contribution_id'] ?>', '<?= esc($payment['student_id']) ?>')" 
                   style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 10; cursor: pointer;"></div>
              
              <div class="student-info-section">
                <div class="student-details">
                  <div class="student-avatar">
                    <i class="fas fa-user-graduate"></i>
                  </div>
                  <div class="student-data">
                    <h4 class="student-name"><?= esc($payment['student_name']) ?></h4>
                    <p class="student-id">Student ID: <?= esc($payment['student_id']) ?></p>
                    <p class="payment-date">
                      <i class="fas fa-calendar-alt"></i>
                      <?php if (isset($payment['payment_count']) && $payment['payment_count'] > 1): ?>
                        <?= $payment['payment_count'] ?> payments
                      <?php else: ?>
                        Paid on <?= date('M j, Y \a\t g:i A', strtotime($payment['payment_date'])) ?>
                      <?php endif; ?>
                    </p>
                  </div>
                </div>
                
                <div class="payment-summary">
                  <div class="payment-amount-display">
                    <span class="amount-large">
                        ₱<?= isset($payment['total_paid']) ? number_format($payment['total_paid'], 2) : '0.10' ?>
                    </span>
                    <?php if (isset($payment['total_installments']) && $payment['payment_count'] > 1): ?>
                        <small class="payment-count"><?= $payment['payment_count'] ?> installments</small>
                    <?php endif; ?>
                  </div>
                  
                  <div class="payment-details-summary">
                    <div class="payment-method-badge">
                      <i class="fas fa-<?= $payment['payment_method'] === 'cash' ? 'money-bill' : ($payment['payment_method'] === 'card' ? 'credit-card' : 'mobile-alt') ?>"></i>
                      <?= ucfirst(str_replace('_', ' ', $payment['payment_method'])) ?>
                    </div>
                    
                    <div class="payment-status-badge status-<?= strtolower($payment['payment_status']) ?>">
                      <i class="fas fa-<?= $payment['payment_status'] === 'fully_paid' ? 'check-circle' : ($payment['payment_status'] === 'partial' ? 'clock' : 'exclamation-circle') ?>"></i>
                      <?= $payment['payment_status'] === 'fully_paid' ? 'Fully Paid' : 'Partial Payment' ?>
                    </div>
                  </div>
                  
                  <!-- Partial Payment Progress (if applicable) -->
                  <?php if ($payment['payment_status'] === 'partial'): ?>
                  <div class="partial-payment-progress">
                    <?php 
                      $contributionAmount = (float)$contribution['amount'];
                      $paidAmount = (isset($payment['total_paid']) ? (float)$payment['total_paid'] : 0);
                      $percentage = $contributionAmount > 0 ? ($paidAmount / $contributionAmount) * 100 : 0;
                    ?>
                    <div class="progress-bar-small">
                      <div class="progress-fill" style="width: <?= $percentage ?>%"></div>
                    </div>
                    <small class="progress-text">
                      <?= number_format($percentage, 1) ?>% paid (₱<?= number_format($payment['remaining_balance'], 2) ?> remaining)
                    </small>
                  </div>
                  <?php endif; ?>
                </div>
                
              </div>
              
            </div>
          <?php endforeach; ?>
        </div>
        
        <!-- No Results Message (hidden by default) -->
        <div class="no-search-results" id="noSearchResults" style="display: none;">
          <div class="no-results-icon">
            <i class="fas fa-search"></i>
          </div>
          <h4>No students found</h4>
          <p>Try adjusting your search terms</p>
        </div>
        
      <?php else: ?>
        <div class="no-payments">
          <div class="no-payments-icon">
            <i class="fas fa-user-slash"></i>
          </div>
          <h3>No Students Have Paid Yet</h3>
          <p>No students have made payments for this contribution yet.</p>
          <a href="<?= base_url('payments?contribution=' . $contribution['id']) ?>" class="btn-primary">
            <i class="fas fa-plus"></i>
            Record First Payment
          </a>
        </div>
      <?php endif; ?>
    </div>

    <!-- Export Options -->
    <?php if (count($payments) > 0): ?>
    <div class="export-section">
      <h4><i class="fas fa-download"></i> Export Options</h4>
      <div class="export-buttons">
        <button class="btn-secondary" onclick="exportToCSV()">
          <i class="fas fa-file-csv"></i> Export CSV
        </button>
        <button class="btn-secondary" onclick="printReport()">
          <i class="fas fa-print"></i> Print Report
        </button>
      </div>
    </div>
    <?php endif; ?>

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
      <a href="<?= base_url('contributions') ?>" class="nav-link active">
        <i class="fas fa-hand-holding-usd"></i>
        <span>Contributions</span>
      </a>
      <a href="<?= base_url('payments/history') ?>" class="nav-link">
        <i class="fas fa-clock"></i>
        <span>History</span>
      </a>
    </nav>
</div>
  <!-- Payment Modal - Added at end -->
  <div id="paymentDetailsModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999;">
    <div style="background: white; margin: 5% auto; padding: 20px; width: 90%; max-width: 600px; border-radius: 8px; max-height: 80vh; overflow-y: auto;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
        <h3>Payment Details</h3>
        <span onclick="closePaymentModal()" style="cursor: pointer; font-size: 24px; font-weight: bold; color: #999;">&times;</span>
      </div>
      <div id="paymentDetailsContent" class="payment-history-modal">
        <div class="payment-history-header">
          <h3><i class="fas fa-receipt"></i> Payment History</h3>
          <button onclick="closePaymentModal()" class="close-btn">
            <i class="fas fa-times"></i>
          </button>
        </div>
        
        <div class="payment-history-content">
          <div class="payment-summary-card">
            <div class="payment-info-grid">
              <div class="payment-info-item">
                <span class="payment-info-label">Total Paid</span>
                <span class="payment-info-value">₱<?= number_format($payment['total_paid'], 2) ?></span>
              </div>
              <div class="payment-info-item">
                <span class="payment-info-label">Remaining Balance</span>
                <span class="payment-info-value">₱<?= number_format($payment['remaining_balance'], 2) ?></span>
              </div>
              <div class="payment-info-item">
                <span class="payment-info-label">Payment Status</span>
                <span class="payment-status-tag status-<?= strtolower($payment['payment_status']) ?>">
                  <i class="fas fa-check-circle"></i> Fully Paid
                </span>
              </div>
            </div>
          </div>

          <div class="qr-section">
            <div class="qr-code-img">
              <!-- QR Code image here -->
            </div>
            <button class="download-qr-btn">
              <i class="fas fa-download"></i>
              Download QR Code
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="<?= base_url('js/contribution_details.js') ?>"></script>

</body>
</html>