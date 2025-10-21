<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Dashboard - ClearPay</title>
  <link rel="stylesheet" href="<?= base_url('css/user_dashboard.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="dashboard-container">
    <!-- Header -->
    <div class="dashboard-header">
      <div class="header-content">
        <div class="welcome-section">
          <h1>Welcome Back!</h1>
          <h2><?= esc($student_name) ?></h2>
          <p class="student-id">Student ID: <?= esc($student_id) ?></p>
        </div>
        
        <div class="header-actions">
          <button class="profile-btn" onclick="toggleUserMenu()">
            <div class="avatar">
              <i class="fas fa-user"></i>
            </div>
            <span class="name-short"><?= esc(explode(' ', $student_name)[0]) ?></span>
            <i class="fas fa-chevron-down"></i>
          </button>
          
          <!-- User Menu Dropdown -->
          <div class="user-menu" id="userMenu">
            <div class="menu-header">
              <div class="avatar-large">
                <i class="fas fa-user"></i>
              </div>
              <div class="user-info">
                <h4><?= esc($student_name) ?></h4>
                <p><?= esc($student_id) ?></p>
              </div>
            </div>
            <div class="menu-items">
              <a href="<?= base_url('user/payment-history') ?>" class="menu-item">
                <i class="fas fa-history"></i>
                <span>Payment History</span>
              </a>
              <a href="<?= base_url('user/profile') ?>" class="menu-item">
                <i class="fas fa-user-edit"></i>
                <span>Profile</span>
              </a>
              <div class="menu-divider"></div>
              <a href="<?= base_url('user/logout') ?>" class="menu-item logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-section">
      <div class="stats-grid">
        <div class="stat-card stat-primary">
          <div class="stat-icon">
            <i class="fas fa-peso-sign"></i>
          </div>
          <div class="stat-content">
            <h3>Total Paid</h3>
            <p class="stat-value">₱<?= number_format($stats['total_paid'], 2) ?></p>
          </div>
        </div>

        <div class="stat-card stat-success">
          <div class="stat-icon">
            <i class="fas fa-check-circle"></i>
          </div>
          <div class="stat-content">
            <h3>Completed</h3>
            <p class="stat-value"><?= $stats['completed_payments'] ?></p>
          </div>
        </div>

        <div class="stat-card stat-warning">
          <div class="stat-icon">
            <i class="fas fa-clock"></i>
          </div>
          <div class="stat-content">
            <h3>Pending</h3>
            <p class="stat-value"><?= $stats['pending_payments'] ?></p>
          </div>
        </div>

        <div class="stat-card stat-info">
          <div class="stat-icon">
            <i class="fas fa-receipt"></i>
          </div>
          <div class="stat-content">
            <h3>Total Payments</h3>
            <p class="stat-value"><?= $stats['total_payments'] ?></p>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <!-- Recent Payments -->
      <div class="section recent-payments">
        <div class="section-header">
          <h3><i class="fas fa-clock"></i> Recent Payments</h3>
          <a href="<?= base_url('user/payment-history') ?>" class="view-all-btn">
            View All <i class="fas fa-arrow-right"></i>
          </a>
        </div>

        <div class="payments-list">
          <?php if (!empty($recent_payments)): ?>
            <?php foreach ($recent_payments as $payment): ?>
              <div class="payment-item" onclick="viewPaymentDetails('<?= $payment['id'] ?>')">
                <div class="payment-info">
                  <div class="payment-details">
                    <h4><?= esc($payment['contribution_title'] ?? 'General Payment') ?></h4>
                    <p class="payment-date">
                      <i class="fas fa-calendar"></i>
                      <?= date('M j, Y - g:i A', strtotime($payment['payment_date'])) ?>
                    </p>
                    <p class="payment-method">
                      <i class="fas fa-credit-card"></i>
                      <?= ucfirst(str_replace('_', ' ', $payment['payment_method'])) ?>
                    </p>
                  </div>
                  <div class="payment-amount">
                    <span class="amount">₱<?= number_format($payment['amount_paid'], 2) ?></span>
                    <span class="status status-<?= $payment['payment_status'] ?>">
                      <?= ucfirst($payment['payment_status']) ?>
                    </span>
                  </div>
                </div>
                <div class="payment-actions">
                  <i class="fas fa-chevron-right"></i>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="no-payments">
              <i class="fas fa-receipt"></i>
              <h4>No Recent Payments</h4>
              <p>Your payment activities will appear here</p>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Available Contributions -->
      <div class="section available-contributions">
        <div class="section-header">
          <h3><i class="fas fa-hand-holding-usd"></i> Available Payment Types</h3>
        </div>

        <div class="contributions-grid">
          <?php if (!empty($active_contributions)): ?>
            <?php foreach ($active_contributions as $contribution): ?>
              <div class="contribution-card">
                <div class="contribution-header">
                  <h4><?= esc($contribution['title']) ?></h4>
                  <span class="contribution-amount">₱<?= number_format($contribution['amount'], 2) ?></span>
                </div>
                <div class="contribution-body">
                  <p class="contribution-description"><?= esc($contribution['description']) ?></p>
                  <div class="contribution-meta">
                    <span class="category"><?= esc($contribution['category']) ?></span>
                    <span class="amount">
                      <i class="fas fa-dollar-sign"></i>
                      Amount: $<?= number_format($contribution['amount'], 2) ?>
                    </span>
                  </div>
                </div>
                <div class="contribution-actions">
                  <button class="btn-primary" onclick="checkPaymentStatus('<?= $contribution['id'] ?>')">
                    <i class="fas fa-search"></i>
                    Check Status
                  </button>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="no-contributions">
              <i class="fas fa-info-circle"></i>
              <p>No payment types are currently available</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Bottom Navigation -->
    <nav class="bottom-nav">
      <a href="<?= base_url('user/dashboard') ?>" class="nav-item active">
        <i class="fas fa-home"></i>
        <span>Home</span>
      </a>
      <a href="<?= base_url('user/payment-history') ?>" class="nav-item">
        <i class="fas fa-history"></i>
        <span>History</span>
      </a>
      <a href="<?= base_url('user/profile') ?>" class="nav-item">
        <i class="fas fa-user"></i>
        <span>Profile</span>
      </a>
      <a href="<?= base_url('user/help') ?>" class="nav-item">
        <i class="fas fa-question-circle"></i>
        <span>Help</span>
      </a>
    </nav>
  </div>

  <!-- Payment Details Modal -->
  <div id="paymentModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Payment Details</h3>
        <button class="close-btn" onclick="closeModal()">&times;</button>
      </div>
      <div class="modal-body" id="paymentModalContent">
        <!-- Payment details will be loaded here -->
      </div>
    </div>
  </div>

  <script src="<?= base_url('js/user_dashboard.js') ?>"></script>
</body>
</html>