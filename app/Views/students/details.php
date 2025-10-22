<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Details - <?= esc($student['student_name']) ?> - ClearPay Admin</title>
  <link rel="stylesheet" href="<?= base_url('css/dashboard.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
  <!-- Main App Container -->
  <div class="app-container">
    
    <!-- Sidebar Navigation -->
    <aside class="sidebar">
      <div class="sidebar-header">
        <div class="app-logo">
          <div class="logo-icon">
            <i class="fas fa-credit-card"></i>
          </div>
          <h2 class="app-name">ClearPay</h2>
        </div>
        <button class="sidebar-toggle" id="sidebarToggle">
          <i class="fas fa-bars"></i>
        </button>
      </div>
      
      <nav class="sidebar-nav">
        <ul class="nav-list">
          <li class="nav-item">
            <a href="<?= base_url('dashboard') ?>" class="nav-link">
              <i class="fas fa-home"></i>
              <span>Dashboard</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= base_url('payments') ?>" class="nav-link">
              <i class="fas fa-credit-card"></i>
              <span>Payments</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= base_url('contributions') ?>" class="nav-link">
              <i class="fas fa-hand-holding-usd"></i>
              <span>Contributions</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= base_url('payments/partial') ?>" class="nav-link">
              <i class="fas fa-clock"></i>
              <span>Partial Payments</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= base_url('payments/history') ?>" class="nav-link">
              <i class="fas fa-history"></i>
              <span>Payment History</span>
            </a>
          </li>
          <li class="nav-divider"></li>
          <li class="nav-item">
            <a href="<?= base_url('analytics') ?>" class="nav-link">
              <i class="fas fa-chart-bar"></i>
              <span>Analytics</span>
            </a>
          </li>
          <li class="nav-item active">
            <a href="<?= base_url('students') ?>" class="nav-link">
              <i class="fas fa-users"></i>
              <span>Students</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= base_url('profile') ?>" class="nav-link">
              <i class="fas fa-cog"></i>
              <span>Settings</span>
            </a>
          </li>
        </ul>
      </nav>
      
      <div class="sidebar-footer">
        <div class="user-profile">
          <div class="profile-avatar">
            <i class="fas fa-user"></i>
          </div>
          <div class="profile-info">
            <h4><?= esc(session()->get('name') ? explode(' ', session()->get('name'))[0] : 'Admin') ?></h4>
            <p>Administrator</p>
          </div>
          <button class="profile-menu-btn" onclick="toggleProfileMenu()">
            <i class="fas fa-ellipsis-vertical"></i>
          </button>
        </div>
      </div>
    </aside>

    <!-- Main Content Area -->
    <main class="main-content">
      
      <!-- Header -->
      <header class="header">
        <div class="header-left">
          <div class="breadcrumb">
            <a href="<?= base_url('students') ?>" class="breadcrumb-link">Students</a>
            <i class="fas fa-chevron-right"></i>
            <span class="breadcrumb-current"><?= esc($student['student_name']) ?></span>
          </div>
          <h1 class="page-title">Student Details</h1>
          <p class="page-subtitle">Payment history and transaction records</p>
        </div>
        
        <div class="header-right">
          <a href="<?= base_url('students') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Back to Students
          </a>
          <button class="btn btn-primary" onclick="exportStudentData()">
            <i class="fas fa-file-pdf"></i>
            Export PDF Report
          </button>
        </div>
      </header>

      <!-- Dashboard Content -->
      <div class="dashboard-content">
        
        <!-- Student Profile Card -->
        <div class="card student-profile-card">
          <div class="card-content">
            <div class="student-profile-header">
              <div class="student-avatar-large">
                <i class="fas fa-user"></i>
              </div>
              <div class="student-basic-info">
                <h2><?= esc($student['student_name']) ?></h2>
                <p class="student-id">ID: <?= esc($student['student_id']) ?></p>
                <div class="student-status">
                  <span class="status-badge active">
                    <i class="fas fa-check-circle"></i>
                    Active Student
                  </span>
                </div>
              </div>
              <div class="student-actions">
                <button class="btn btn-outline btn-sm" onclick="editStudent()">
                  <i class="fas fa-edit"></i>
                  Edit
                </button>
                <button class="btn btn-primary btn-sm" onclick="addPayment()">
                  <i class="fas fa-plus"></i>
                  Add Payment
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid" style="margin-bottom: 2rem;">
          <div class="stat-card primary">
            <div class="stat-content">
              <div class="stat-header">
                <h3>Total Paid</h3>
                <div class="stat-icon">
                  <i class="fas fa-dollar-sign"></i>
                </div>
              </div>
              <div class="stat-value">₱<?= number_format($student['total_paid'], 2) ?></div>
              <div class="stat-footer">
                <span class="stat-change">
                  <i class="fas fa-wallet"></i>
                  All time total
                </span>
              </div>
            </div>
          </div>
          
          <div class="stat-card success">
            <div class="stat-content">
              <div class="stat-header">
                <h3>Total Payments</h3>
                <div class="stat-icon">
                  <i class="fas fa-receipt"></i>
                </div>
              </div>
              <div class="stat-value"><?= $student['total_payments'] ?></div>
              <div class="stat-footer">
                <span class="stat-change">
                  <i class="fas fa-credit-card"></i>
                  Transaction count
                </span>
              </div>
            </div>
          </div>
          
          <div class="stat-card info">
            <div class="stat-content">
              <div class="stat-header">
                <h3>Contributions</h3>
                <div class="stat-icon">
                  <i class="fas fa-hand-holding-usd"></i>
                </div>
              </div>
              <div class="stat-value"><?= $student['contributions_count'] ?></div>
              <div class="stat-footer">
                <span class="stat-change">
                  <i class="fas fa-chart-line"></i>
                  Different campaigns
                </span>
              </div>
            </div>
          </div>
          
          <div class="stat-card warning">
            <div class="stat-content">
              <div class="stat-header">
                <h3>Average Payment</h3>
                <div class="stat-icon">
                  <i class="fas fa-calculator"></i>
                </div>
              </div>
              <div class="stat-value">₱<?= number_format($student['total_paid'] / max($student['total_payments'], 1), 2) ?></div>
              <div class="stat-footer">
                <span class="stat-change">
                  <i class="fas fa-chart-bar"></i>
                  Per transaction
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Student Timeline -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">
              <i class="fas fa-clock"></i>
              Payment Timeline
            </h3>
            <div class="card-actions">
              <div class="timeline-period">
                <span>From <?= date('M j, Y', strtotime($student['first_payment'])) ?> to <?= date('M j, Y', strtotime($student['last_payment'])) ?></span>
              </div>
            </div>
          </div>
          <div class="card-content">
            <?php if (count($payments) > 0): ?>
            <div class="transaction-timeline">
              <?php foreach ($payments as $index => $payment): ?>
              <div class="timeline-item">
                <div class="timeline-marker">
                  <div class="timeline-dot <?= strtolower($payment['payment_status']) ?>">
                    <i class="fas fa-<?= $payment['payment_status'] === 'completed' ? 'check' : ($payment['payment_status'] === 'partial' ? 'clock' : 'credit-card') ?>"></i>
                  </div>
                  <?php if ($index < count($payments) - 1): ?>
                  <div class="timeline-line"></div>
                  <?php endif; ?>
                </div>
                <div class="timeline-content">
                  <div class="transaction-card clickable-card" onclick="viewTransaction(<?= $payment['id'] ?>)">
                    <div class="transaction-header">
                      <div class="transaction-info">
                        <h4 class="transaction-title"><?= esc($payment['contribution_title'] ?? 'Payment') ?></h4>
                        <p class="transaction-meta">
                          <span class="transaction-id">TXN-<?= str_pad($payment['id'], 6, '0', STR_PAD_LEFT) ?></span>
                          <span class="transaction-date"><?= date('M j, Y g:i A', strtotime($payment['created_at'])) ?></span>
                        </p>
                      </div>
                      <div class="transaction-amount">
                        <span class="amount-value">₱<?= number_format($payment['amount_paid'], 2) ?></span>
                        <span class="payment-status status-<?= strtolower($payment['payment_status']) ?>">
                          <?= ucfirst($payment['payment_status']) ?>
                        </span>
                      </div>
                    </div>
                    <div class="transaction-details">
                      <div class="detail-item">
                        <span class="detail-label">Payment Method:</span>
                        <span class="detail-value">
                          <i class="fas fa-<?= $payment['payment_method'] === 'cash' ? 'money-bill-wave' : 'credit-card' ?>"></i>
                          <?= ucfirst($payment['payment_method']) ?>
                        </span>
                      </div>
                      <?php if (isset($payment['reference_number']) && !empty($payment['reference_number'])): ?>
                      <div class="detail-item">
                        <span class="detail-label">Reference:</span>
                        <span class="detail-value"><?= esc($payment['reference_number']) ?></span>
                      </div>
                      <?php endif; ?>
                      <?php if (isset($payment['notes']) && !empty($payment['notes'])): ?>
                      <div class="detail-item">
                        <span class="detail-label">Notes:</span>
                        <span class="detail-value"><?= esc($payment['notes']) ?></span>
                      </div>
                      <?php endif; ?>
                    </div>
                    <div class="card-hover-indicator">
                      <i class="fas fa-eye"></i>
                      <span>Click to view details</span>
                    </div>
                  </div>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state">
              <div class="empty-icon">
                <i class="fas fa-receipt"></i>
              </div>
              <h4>No Payment Records</h4>
              <p>This student hasn't made any payments yet.</p>
              <button class="btn btn-primary" onclick="addPayment()">
                <i class="fas fa-plus"></i>
                Add First Payment
              </button>
            </div>
            <?php endif; ?>
          </div>
        </div>

      </div>

    </main>
  </div>

  <style>
    .breadcrumb {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      margin-bottom: 0.5rem;
      font-size: 0.875rem;
    }

    .breadcrumb-link {
      color: var(--primary-color);
      text-decoration: none;
    }

    .breadcrumb-link:hover {
      text-decoration: underline;
    }

    .breadcrumb-current {
      color: var(--text-secondary);
    }

    .student-profile-card {
      margin-bottom: 2rem;
    }

    .student-profile-header {
      display: flex;
      align-items: center;
      gap: 2rem;
    }

    .student-avatar-large {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 2rem;
    }

    .student-basic-info {
      flex: 1;
    }

    .student-basic-info h2 {
      margin: 0 0 0.5rem 0;
      font-size: 1.5rem;
      color: var(--text-primary);
    }

    .student-id {
      font-family: 'Courier New', monospace;
      color: var(--text-secondary);
      margin: 0 0 1rem 0;
      font-size: 0.875rem;
    }

    .status-badge {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.5rem 1rem;
      border-radius: var(--radius-lg);
      font-size: 0.875rem;
      font-weight: 600;
    }

    .status-badge.active {
      background: rgba(16, 185, 129, 0.1);
      color: var(--success-color);
    }

    .student-actions {
      display: flex;
      gap: 1rem;
    }

    .timeline-period {
      font-size: 0.875rem;
      color: var(--text-secondary);
    }

    .transaction-timeline {
      padding: 1rem 0;
    }

    .timeline-item {
      display: flex;
      gap: 1.5rem;
      margin-bottom: 2rem;
    }

    .timeline-item:last-child {
      margin-bottom: 0;
    }

    .timeline-marker {
      display: flex;
      flex-direction: column;
      align-items: center;
      position: relative;
    }

    .timeline-dot {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 0.875rem;
      z-index: 2;
    }

    .timeline-dot.completed {
      background: var(--success-color);
    }

    .timeline-dot.partial {
      background: var(--warning-color);
    }

    .timeline-dot.pending {
      background: var(--info-color);
    }

    .timeline-line {
      width: 2px;
      height: 60px;
      background: var(--border-color);
      margin-top: 0.5rem;
    }

    .timeline-content {
      flex: 1;
    }

    .transaction-card {
      background: var(--bg-white);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-lg);
      padding: 1.5rem;
      transition: all var(--transition-fast);
      position: relative;
    }

    .transaction-card.clickable-card {
      cursor: pointer;
      user-select: none;
    }

    .transaction-card.clickable-card:hover {
      border-color: var(--primary-color);
      box-shadow: 0 8px 25px rgba(59, 130, 246, 0.15);
      transform: translateY(-3px) scale(1.02);
      background: linear-gradient(145deg, var(--bg-white), var(--bg-secondary));
    }

    .transaction-card.clickable-card:active {
      transform: translateY(-1px) scale(1.01);
      box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
      transition: all 0.1s ease;
    }

    .card-hover-indicator {
      position: absolute;
      top: 1rem;
      right: 1rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      color: var(--text-tertiary);
      font-size: 0.75rem;
      font-weight: 500;
      opacity: 0;
      transition: all 0.2s ease;
      background: rgba(255, 255, 255, 0.95);
      padding: 0.5rem 1rem;
      border-radius: var(--radius-md);
      backdrop-filter: blur(8px);
      border: 1px solid var(--border-color);
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      z-index: 10;
    }

    .transaction-card.clickable-card:hover .card-hover-indicator {
      opacity: 1;
      color: var(--primary-color);
      border-color: var(--primary-color);
      background: rgba(59, 130, 246, 0.05);
      transform: translateY(-2px);
    }

    .card-hover-indicator i {
      font-size: 0.875rem;
    }

    .transaction-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 1rem;
    }

    .transaction-title {
      margin: 0 0 0.5rem 0;
      font-size: 1.1rem;
      color: var(--text-primary);
    }

    .transaction-meta {
      margin: 0;
      font-size: 0.875rem;
      color: var(--text-secondary);
      display: flex;
      gap: 1rem;
    }

    .transaction-id {
      font-family: 'Courier New', monospace;
      background: var(--bg-secondary);
      padding: 0.25rem 0.5rem;
      border-radius: var(--radius-sm);
    }

    .transaction-amount {
      text-align: right;
    }

    .amount-value {
      display: block;
      font-size: 1.25rem;
      font-weight: 700;
      color: var(--success-color);
      margin-bottom: 0.5rem;
    }

    .payment-status {
      display: inline-block;
      padding: 0.25rem 0.75rem;
      border-radius: var(--radius-sm);
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
    }

    .status-completed {
      background: rgba(16, 185, 129, 0.1);
      color: var(--success-color);
    }

    .status-partial {
      background: rgba(245, 158, 11, 0.1);
      color: var(--warning-color);
    }

    .status-pending {
      background: rgba(59, 130, 246, 0.1);
      color: var(--info-color);
    }

    .transaction-details {
      margin-bottom: 1rem;
    }

    .detail-item {
      display: flex;
      justify-content: space-between;
      margin-bottom: 0.5rem;
    }

    .detail-label {
      font-weight: 500;
      color: var(--text-secondary);
    }

    .detail-value {
      color: var(--text-primary);
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }



    /* Modal Styles */
    .modal-overlay {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.7);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 2000;
      backdrop-filter: blur(4px);
    }

    .modal-container {
      background: var(--bg-primary);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow-xl);
      max-height: 90vh;
      overflow-y: auto;
      animation: modalSlideIn 0.3s ease-out;
    }

    @keyframes modalSlideIn {
      from {
        opacity: 0;
        transform: translateY(-20px) scale(0.95);
      }
      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    .btn-icon {
      background: none;
      border: none;
      cursor: pointer;
      padding: 0.5rem;
      border-radius: var(--radius-md);
      transition: background-color var(--transition-fast);
    }

    .btn-icon:hover {
      background: var(--bg-secondary);
    }

    .btn-secondary {
      background: var(--bg-tertiary);
      color: var(--text-primary);
      border: 1px solid var(--border-color);
      padding: 0.75rem 1.5rem;
      border-radius: var(--radius-md);
      cursor: pointer;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      transition: all var(--transition-fast);
    }

    .btn-secondary:hover:not(:disabled) {
      background: var(--bg-secondary);
      border-color: var(--primary-color);
      transform: translateY(-1px);
    }

    .btn-primary {
      background: var(--primary-color);
      color: white;
      border: none;
      padding: 0.75rem 1.5rem;
      border-radius: var(--radius-md);
      cursor: pointer;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      transition: all var(--transition-fast);
    }

    .btn-primary:hover:not(:disabled) {
      background: var(--primary-hover);
      transform: translateY(-1px);
      box-shadow: var(--shadow-md);
    }

    @media (max-width: 768px) {
      .student-profile-header {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
      }

      .student-actions {
        width: 100%;
        justify-content: center;
      }

      .transaction-header {
        flex-direction: column;
        gap: 1rem;
      }

      .transaction-amount {
        text-align: left;
      }

      .detail-item {
        flex-direction: column;
        gap: 0.25rem;
      }

      .modal-container {
        width: 95%;
        margin: 1rem;
      }
    }
  </style>

  <script>
    function editStudent() {
      console.log('Edit student functionality');
      // Implement edit student functionality
    }

    function addPayment() {
      window.location.href = '<?= base_url('payments') ?>?student_id=<?= urlencode($student['student_id']) ?>';
    }

    function exportStudentData() {
      console.log('Export student data as PDF');
      
      // Show loading state
      const exportBtn = document.querySelector('.btn.btn-primary');
      if (exportBtn) {
        const originalText = exportBtn.innerHTML;
        exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating PDF...';
        exportBtn.disabled = true;
        
        // Reset button after a delay
        setTimeout(() => {
          exportBtn.innerHTML = originalText;
          exportBtn.disabled = false;
        }, 4000);
      }
      
      // Trigger PDF download
      const studentId = '<?= esc($student['student_id']) ?>';
      const exportUrl = '<?= base_url('students/export') ?>/' + encodeURIComponent(studentId);
      
      console.log('PDF Export URL:', exportUrl);
      
      // Use window.location.href for direct download
      window.location.href = exportUrl;
    }

    function viewTransaction(transactionId) {
      // Find the payment data from the payments array
      const payments = <?= json_encode($payments) ?>;
      const paymentData = payments.find(p => p.id == transactionId);
      
      if (!paymentData) {
        alert('Payment details not found.');
        return;
      }
      
      // Show payment details modal similar to dashboard
      viewPaymentDetails(paymentData);
    }
    
    function viewPaymentDetails(paymentData) {
      // Create modal to show payment details
      const modal = document.createElement('div');
      modal.className = 'modal-overlay';
      modal.id = 'paymentDetailsModal';
      modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2000;
        backdrop-filter: blur(4px);
      `;
      
      modal.innerHTML = `
        <div class="modal-container" style="background: var(--bg-primary); border-radius: var(--radius-lg); box-shadow: var(--shadow-xl); max-height: 90vh; overflow-y: auto; width: 90%; max-width: 600px;">
          <div class="card-header" style="padding: 1.5rem; border-bottom: 1px solid var(--border-color);">
            <div>
              <h3 style="margin: 0; color: var(--text-primary);"><i class="fas fa-receipt"></i> Payment Receipt</h3>
              <p style="margin: 0.5rem 0 0 0; color: var(--text-secondary);">Detailed payment information and QR receipt</p>
            </div>
            <div class="card-actions">
              <button type="button" class="btn-icon" onclick="closePaymentModal()" style="background: none; border: none; font-size: 1.2rem; cursor: pointer; color: var(--text-secondary);">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
          
          <div style="padding: 1.5rem;">
            <!-- Student Information -->
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; padding: 1rem; background: var(--bg-secondary); border-radius: var(--radius-md);">
              <div style="width: 60px; height: 60px; background: linear-gradient(135deg, var(--primary-color), var(--info-color)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem;">
                <i class="fas fa-user"></i>
              </div>
              <div style="flex: 1;">
                <h4 style="margin: 0; font-size: 1.25rem; color: var(--text-primary);"><?= esc($student['student_name']) ?></h4>
                <p style="margin: 0.25rem 0; color: var(--text-secondary);">ID: <?= esc($student['student_id']) ?></p>
              </div>
            </div>

            <!-- Payment Summary -->
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
              <div style="text-align: center; padding: 1rem; background: var(--success-light, #f0fff4); border-radius: var(--radius-md); border: 1px solid var(--success-color, #38a169);">
                <div style="color: var(--success-color, #38a169); font-size: 1.1rem; font-weight: 700; margin-bottom: 0.25rem;">₱${parseFloat(paymentData.amount_paid || 0).toFixed(2)}</div>
                <div style="color: var(--success-color, #38a169); font-size: 0.8rem; text-transform: uppercase;">Amount Paid</div>
              </div>
              <div style="text-align: center; padding: 1rem; background: var(--info-light, #ebf8ff); border-radius: var(--radius-md); border: 1px solid var(--info-color, #3182ce);">
                <div style="color: var(--info-color, #3182ce); font-size: 1.1rem; font-weight: 700; margin-bottom: 0.25rem;">₱${parseFloat(paymentData.remaining_balance || 0).toFixed(2)}</div>
                <div style="color: var(--info-color, #3182ce); font-size: 0.8rem; text-transform: uppercase;">Remaining</div>
              </div>
              <div style="text-align: center; padding: 1rem; background: var(--warning-light, #fffbeb); border-radius: var(--radius-md); border: 1px solid var(--warning-color, #d69e2e);">
                <div style="color: var(--warning-color, #d69e2e); font-size: 1.1rem; font-weight: 700; margin-bottom: 0.25rem; text-transform: uppercase;">${paymentData.payment_status || 'Unknown'}</div>
                <div style="color: var(--warning-color, #d69e2e); font-size: 0.8rem; text-transform: uppercase;">Status</div>
              </div>
            </div>

            <!-- Payment Details -->
            <div style="background: var(--bg-primary); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.5rem; margin-bottom: 1.5rem;">
              <h5 style="margin-bottom: 1rem; color: var(--text-primary); font-size: 1rem; font-weight: 600;">Transaction Details</h5>
              
              <div style="border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1rem; background: var(--bg-secondary);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                  <span style="font-weight: 600; color: var(--text-primary);">${new Date(paymentData.payment_date || paymentData.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</span>
                  <span style="font-weight: 700; color: var(--success-color); font-size: 1.1rem;">₱${parseFloat(paymentData.amount_paid || 0).toFixed(2)}</span>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; font-size: 0.9rem;">
                  <div><strong>Method:</strong> ${(paymentData.payment_method || 'cash').toUpperCase()}</div>
                  <div><strong>Reference:</strong> ${paymentData.reference_number || 'N/A'}</div>
                  <div><strong>Contribution:</strong> ${paymentData.contribution_title || 'General Payment'}</div>
                  <div><strong>Transaction ID:</strong> TXN-${String(paymentData.id).padStart(6, '0')}</div>
                </div>
                ${paymentData.notes ? `<div style="margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px solid var(--border-color);"><strong>Notes:</strong> ${paymentData.notes}</div>` : ''}
              </div>
            </div>

            <!-- QR Code Section -->
            <div style="text-align: center; background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.5rem;">
              <h5 style="margin-bottom: 1rem; color: var(--text-primary); font-size: 1rem; font-weight: 600;">QR Receipt Code</h5>
              <div style="margin-bottom: 1rem; display: flex; justify-content: center; align-items: center; min-height: 200px;">
                <div id="qrCodeContainer" style="background: white; padding: 20px; border-radius: 8px; border: 2px solid var(--border-color);">
                  <!-- QR Code will be generated here -->
                  <div style="width: 150px; height: 150px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: #999;">
                    <i class="fas fa-qrcode"></i>
                  </div>
                </div>
              </div>
              <div style="display: flex; gap: 1rem; justify-content: center;">
                ${paymentData.qr_receipt_path && paymentData.qr_receipt_path.trim() !== '' ? `
                  <button class="btn-primary" onclick="downloadQRReceipt(${paymentData.id})" style="padding: 0.75rem 1.5rem; background: var(--primary-color); color: white; border: none; border-radius: var(--radius-md); cursor: pointer; font-weight: 600;">
                    <i class="fas fa-download"></i>
                    Download QR Receipt
                  </button>
                ` : `
                  <button class="btn-secondary" disabled style="padding: 0.75rem 1.5rem; background: var(--text-tertiary); color: white; border: none; border-radius: var(--radius-md); cursor: not-allowed; font-weight: 600; opacity: 0.6;">
                    <i class="fas fa-exclamation-circle"></i>
                    QR Receipt Not Available
                  </button>
                `}
                <button class="btn-secondary" onclick="printReceiptModal()" style="padding: 0.75rem 1.5rem; background: var(--bg-tertiary); color: var(--text-primary); border: 1px solid var(--border-color); border-radius: var(--radius-md); cursor: pointer; font-weight: 600;">
                  <i class="fas fa-print"></i>
                  Print Receipt
                </button>
              </div>
            </div>
          </div>
        </div>
      `;
      
      document.body.appendChild(modal);
      
      // Add click outside to close
      modal.addEventListener('click', function(e) {
        if (e.target === modal) {
          closePaymentModal();
        }
      });

      // Generate QR code if available
      generateQRForPayment(paymentData);
    }

    function closePaymentModal() {
      const modal = document.getElementById('paymentDetailsModal');
      if (modal) {
        modal.remove();
      }
    }

    function downloadQRReceipt(paymentId) {
      if (!paymentId) {
        alert('Payment ID is required for downloading QR receipt.');
        return;
      }
      
      // Show loading state
      const button = event.target.closest('button');
      const originalContent = button.innerHTML;
      button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Downloading...';
      button.disabled = true;
      
      // Create a temporary link to download the QR receipt
      const downloadUrl = '<?= base_url('payments/downloadReceipt/') ?>' + paymentId;
      const link = document.createElement('a');
      link.href = downloadUrl;
      link.target = '_blank';
      link.download = 'qr_receipt_' + paymentId + '.png';
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      
      // Restore button state after a short delay
      setTimeout(() => {
        if (button) {
          button.innerHTML = originalContent;
          button.disabled = false;
        }
      }, 1500);
    }

    function generateQRForPayment(paymentData) {
      const qrContainer = document.getElementById('qrCodeContainer');
      if (!qrContainer) return;

      // Clear container
      qrContainer.innerHTML = '';

      // Check if payment has an existing QR receipt
      if (paymentData.qr_receipt_path && paymentData.qr_receipt_path.trim() !== '') {
        // Display the existing QR code image
        const qrImage = document.createElement('img');
        qrImage.src = '<?= base_url('payments/downloadReceipt/') ?>' + paymentData.id;
        qrImage.style.cssText = `
          width: 150px;
          height: 150px;
          object-fit: contain;
          border-radius: 8px;
          border: 1px solid var(--border-color);
          background: white;
        `;
        qrImage.alt = 'QR Receipt for Payment #' + paymentData.id;
        
        // Handle image load error
        qrImage.onerror = function() {
          qrContainer.innerHTML = `
            <div style="width: 150px; height: 150px; background: #f8f9fa; display: flex; flex-direction: column; align-items: center; justify-content: center; border: 2px dashed #dee2e6; border-radius: 8px;">
              <i class="fas fa-exclamation-triangle" style="font-size: 2rem; color: #dc3545; margin-bottom: 0.5rem;"></i>
              <span style="font-size: 0.8rem; color: #6c757d; text-align: center;">QR Image Not Found</span>
            </div>
          `;
        };
        
        qrContainer.appendChild(qrImage);
      } else {
        // Fallback if no QR available
        qrContainer.innerHTML = `
          <div style="width: 150px; height: 150px; background: #f8f9fa; display: flex; flex-direction: column; align-items: center; justify-content: center; border: 2px dashed #dee2e6; border-radius: 8px;">
            <i class="fas fa-qrcode" style="font-size: 3rem; color: #6c757d; margin-bottom: 0.5rem;"></i>
            <span style="font-size: 0.8rem; color: #6c757d; text-align: center;">Payment #${paymentData.id}</span>
          </div>
        `;
      }
    }

    function printReceiptModal() {
      window.print();
    }

    function printReceipt(transactionId) {
      // Open the specific payment receipt for printing
      window.open('<?= base_url('payments/receipt') ?>/' + transactionId, '_blank');
    }

    function toggleProfileMenu() {
      console.log('Profile menu toggled');
    }

    // Sidebar functionality
    const sidebar = document.querySelector('.sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mainContent = document.querySelector('.main-content');

    function toggleSidebar() {
      sidebar.classList.toggle('collapsed');
      mainContent.classList.toggle('expanded');
    }

    sidebarToggle?.addEventListener('click', toggleSidebar);

    // Modal functionality
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        const modal = document.getElementById('paymentDetailsModal');
        if (modal) {
          closePaymentModal();
        }
      }
    });
  </script>
  
  <!-- External Libraries -->
  <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
  
  <!-- Dashboard JavaScript -->
  <script src="<?= base_url('js/dashboard.js') ?>"></script>
</body>
</html>