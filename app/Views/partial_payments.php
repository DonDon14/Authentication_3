<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Partial Payments</title>
  <link rel="stylesheet" href="<?= base_url('css/payments.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="payments-container">
    <div class="payments-header">
      <div class="back-button">
        <a href="<?= base_url('dashboard') ?>" class="back-btn">
          <i class="fas fa-arrow-left"></i>
        </a>
      </div>
      <div class="header-content">
        <h2>Partial Payments</h2>
        <p class="description">Click on any payment to add more installments</p>
      </div>
    </div>

    <div class="partial-payments-content">
      <?php if (!empty($partialPayments)): ?>
        <?php foreach ($partialPayments as $payment): ?>
          <div class="partial-payment-card clickable-payment-card" 
               onclick="openPaymentModal(<?= $payment['contribution_id'] ?>, '<?= esc($payment['student_id']) ?>', '<?= esc($payment['student_name']) ?>', '<?= esc($payment['contribution_title']) ?>', <?= $payment['total_amount_due'] ?>, <?= $payment['remaining_balance'] ?>)"
               data-contribution="<?= $payment['contribution_id'] ?>" 
               data-student="<?= esc($payment['student_id']) ?>">
            
            <div class="payment-card-content">
              <div class="student-info">
                <h4><?= esc($payment['student_name']) ?></h4>
                <p class="student-id">ID: <?= esc($payment['student_id']) ?></p>
                <p class="contribution-title"><?= esc($payment['contribution_title']) ?></p>
                <p class="payment-date">Last payment: <?= date('M j, Y', strtotime($payment['created_at'])) ?></p>
              </div>
              
              <div class="payment-progress">
                <?php 
                  $paidAmount = $payment['total_amount_due'] - $payment['remaining_balance'];
                  $progressPercentage = ($paidAmount / $payment['total_amount_due']) * 100;
                ?>
                <div class="progress-info">
                  <span class="paid-amount">$<?= number_format($paidAmount, 2) ?></span>
                  <span class="separator">/</span>
                  <span class="total-amount">$<?= number_format($payment['total_amount_due'], 2) ?></span>
                  <span class="percentage">(<?= number_format($progressPercentage, 1) ?>%)</span>
                </div>
                
                <div class="progress-bar">
                  <div class="progress-fill" style="width: <?= $progressPercentage ?>%"></div>
                </div>
                
                <p class="remaining-balance">
                  <i class="fas fa-exclamation-circle"></i>
                  Remaining: $<?= number_format($payment['remaining_balance'], 2) ?>
                </p>
              </div>
              
              <div class="click-indicator">
                <i class="fas fa-plus-circle"></i>
                <span>Click to Add Payment</span>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="no-partial-payments">
          <i class="fas fa-check-circle"></i>
          <h3>No Partial Payments</h3>
          <p>All students have either fully paid or haven't started payments yet.</p>
          <a href="<?= base_url('payments') ?>" class="btn-primary">
            <i class="fas fa-plus"></i>
            Record New Payment
          </a>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Payment Modal -->
  <div id="partialPaymentModal" class="payment-modal" style="display: none;">
    <div class="modal-content">
      <div class="modal-header">
        <h3 id="modalTitle">Add Payment Installment</h3>
        <button class="modal-close" onclick="closePaymentModal()">&times;</button>
      </div>
      
      <div class="modal-body">
        <div class="payment-summary">
          <div class="student-details">
            <h4 id="modalStudentName"></h4>
            <p>ID: <span id="modalStudentId"></span></p>
            <p>Contribution: <span id="modalContributionTitle"></span></p>
          </div>
          
          <div class="payment-status">
            <div class="status-row">
              <span>Total Due:</span>
              <span id="modalTotalDue">$0.00</span>
            </div>
            <div class="status-row">
              <span>Already Paid:</span>
              <span id="modalAlreadyPaid">$0.00</span>
            </div>
            <div class="status-row highlight">
              <span>Remaining Balance:</span>
              <span id="modalRemainingBalance">$0.00</span>
            </div>
          </div>
        </div>
        
        <form id="partialPaymentForm" class="payment-form">
          <input type="hidden" id="hiddenContributionId" name="contribution_id">
          <input type="hidden" id="hiddenStudentId" name="student_id">
          <input type="hidden" id="hiddenStudentName" name="student_name">
          
          <div class="form-group">
            <label for="paymentAmount">Payment Amount ($)</label>
            <input type="number" id="paymentAmount" name="amount" step="0.01" min="0.01" required>
            <small class="helper-text">Enter the amount for this installment</small>
          </div>
          
          <div class="form-group">
            <label for="paymentMethodModal">Payment Method</label>
            <select id="paymentMethodModal" name="payment_method" required>
              <option value="cash">Cash</option>
              <option value="card">Card</option>
              <option value="bank_transfer">Bank Transfer</option>
              <option value="gcash">GCash</option>
              <option value="mobile_payment">Mobile Payment</option>
            </select>
          </div>
          
          <div class="form-actions">
            <button type="button" class="btn-secondary" onclick="closePaymentModal()">Cancel</button>
            <button type="submit" class="btn-primary">
              <i class="fas fa-plus"></i>
              Record Payment
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="<?= base_url('js/payments.js') ?>"></script>
</body>
</html>