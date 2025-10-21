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
          <button class="btn btn-outline" onclick="window.history.back()">
            <i class="fas fa-arrow-left"></i>
            Back to Students
          </button>
          <button class="btn btn-primary" onclick="exportStudentData()">
            <i class="fas fa-download"></i>
            Export Data
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
                  <div class="transaction-card">
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
                    <div class="transaction-actions">
                      <button class="btn-text" onclick="viewTransaction(<?= $payment['id'] ?>)">
                        <i class="fas fa-eye"></i>
                        View Details
                      </button>
                      <button class="btn-text" onclick="printReceipt(<?= $payment['id'] ?>)">
                        <i class="fas fa-print"></i>
                        Print Receipt
                      </button>
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
      transition: var(--transition-fast);
    }

    .transaction-card:hover {
      border-color: var(--primary-color);
      box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
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

    .transaction-actions {
      display: flex;
      gap: 1rem;
      padding-top: 1rem;
      border-top: 1px solid var(--border-color);
    }

    .btn-text {
      background: none;
      border: none;
      color: var(--primary-color);
      cursor: pointer;
      font-size: 0.875rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.5rem 0;
      transition: var(--transition-fast);
    }

    .btn-text:hover {
      color: var(--primary-hover);
      text-decoration: underline;
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
      // Implement export functionality for this student
      console.log('Export student data');
    }

    function viewTransaction(transactionId) {
      console.log('View transaction:', transactionId);
      // Implement transaction details view
    }

    function printReceipt(transactionId) {
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
  </script>
  
  <!-- Dashboard JavaScript -->
  <script src="<?= base_url('js/dashboard.js') ?>"></script>
</body>
</html>