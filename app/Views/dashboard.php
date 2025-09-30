<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="<?= base_url('css/dashboard.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="dashboard-container">
    <!-- Header -->
    <div class="dashboard-header">
      <div class="welcome-section">
        <h2>Welcome Back</h2>
        <p class="user-name"><?= esc($name) ?></p>
        <p class="description">Here's your payment overview</p>
      </div>
      <div class="header-actions">
        <!-- Notifications Icon -->
        <div class="notifications-wrapper">
          <button class="notifications-btn" id="notificationsBtn">
            <i class="fas fa-bell"></i>
            <span class="notification-badge">3</span>
          </button>
          
          <!-- Notifications Dropdown -->
          <div class="notifications-dropdown" id="notificationsDropdown">
            <div class="notifications-header">
              <h4>Notifications</h4>
              <button class="mark-all-read">Mark all as read</button>
            </div>
            <div class="notifications-list">
              <div class="notification-item unread">
                <div class="notification-icon">
                  <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="notification-content">
                  <p class="notification-title">New Payment Received</p>
                  <p class="notification-text">John Doe submitted $150 payment</p>
                  <p class="notification-time">2 minutes ago</p>
                </div>
              </div>
              <div class="notification-item unread">
                <div class="notification-icon">
                  <i class="fas fa-check-circle"></i>
                </div>
                <div class="notification-content">
                  <p class="notification-title">Payment Verified</p>
                  <p class="notification-text">Jane Smith's payment has been verified</p>
                  <p class="notification-time">1 hour ago</p>
                </div>
              </div>
              <div class="notification-item">
                <div class="notification-icon">
                  <i class="fas fa-user-plus"></i>
                </div>
                <div class="notification-content">
                  <p class="notification-title">New User Registered</p>
                  <p class="notification-text">Mike Johnson created an account</p>
                  <p class="notification-time">3 hours ago</p>
                </div>
              </div>
            </div>
            <div class="notifications-footer">
              <a href="<?= base_url('notifications') ?>" class="view-all-btn">View All Notifications</a>
            </div>
          </div>
        </div>

        <!-- User Dropdown -->
        <div class="user-dropdown-wrapper">
          <button class="user-dropdown-btn" id="userDropdownBtn">
            <div class="user-avatar">
              <i class="fas fa-user"></i>
            </div>
            <span class="user-name-short"><?= esc(explode(' ', $name)[0]) ?></span>
            <i class="fas fa-chevron-down dropdown-arrow"></i>
          </button>
          
          <!-- Dropdown Menu -->
          <div class="user-dropdown-menu" id="userDropdownMenu">
            <div class="dropdown-header">
              <div class="user-info">
                <div class="user-avatar-large">
                  <i class="fas fa-user"></i>
                </div>
                <div class="user-details">
                  <p class="user-full-name"><?= esc($name) ?></p>
                  <p class="user-email"><?= esc($email ?? 'user@example.com') ?></p>
                </div>
              </div>
            </div>
            
            <div class="dropdown-items">
              <a href="<?= base_url('profile') ?>" class="dropdown-item">
                <i class="fas fa-user-edit"></i>
                <span>Profile</span>
              </a>
              <a href="<?= base_url('settings') ?>" class="dropdown-item">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
              </a>
              <div class="dropdown-divider"></div>
              <a href="<?= base_url('logout') ?>" class="dropdown-item logout-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Stats Section -->
    <div class="stats-grid">
      <div class="stat-card stat-card-primary">
        <div class="stat-icon">
          <i class="fas fa-peso-sign"></i>
        </div>
        <div class="stat-content">
          <p class="stat-label">Total Collections</p>
          <p class="stat-value">₱<?= number_format($stats['total_collections'], 2) ?></p>
        </div>
      </div>
      
      <div class="stat-card stat-card-success">
        <div class="stat-icon">
          <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
          <p class="stat-label">Verified</p>
          <p class="stat-value"><?= $stats['verified_count'] ?></p>
        </div>
      </div>
      
      <div class="stat-card stat-card-warning">
        <div class="stat-icon">
          <i class="fas fa-clock"></i>
        </div>
        <div class="stat-content">
          <p class="stat-label">Pending</p>
          <p class="stat-value"><?= $stats['pending_count'] ?></p>
        </div>
      </div>
      
      <div class="stat-card stat-card-info">
        <div class="stat-icon">
          <i class="fas fa-calendar-day"></i>
        </div>
        <div class="stat-content">
          <p class="stat-label">Today</p>
          <p class="stat-value"><?= $stats['today_count'] ?></p>
        </div>
      </div>
    </div>

    <!-- Main Dashboard Content -->
    <div class="dashboard-main">
      <!-- Quick Actions -->
      <div class="quick-actions">
        <h3>Quick Actions</h3>
        <div class="action-buttons">
          <button class="btn-primary" onclick="window.location.href='<?= base_url('payments') ?>'">
            <i class="fas fa-plus"></i>
            Add Payment
          </button>
          <div class="button-row">
            <button class="btn-secondary btn-success">
              <i class="fas fa-check"></i>
              Verify
            </button>
            <button class="btn-secondary btn-info" onclick="window.location.href='<?= base_url('contributions') ?>'">
              <i class="fas fa-hand-holding-usd"></i>
              Contributions
            </button>
          </div>
        </div>
      </div>

      <!-- Recent Payments -->
      <div class="recent-payments">
      <div class="section-header">
        <h3>Recent Payments</h3>
        <p class="description">Latest payment activities</p>
      </div>
      
      <div class="payments-list">
        <?php if (isset($recentPayments) && count($recentPayments) > 0): ?>
          <?php foreach ($recentPayments as $payment): ?>
            <div class="payment-item">
              <div class="payment-info">
                <div class="student-info">
                  <h4><?= esc($payment['student_name']) ?></h4>
                  <p class="payment-type"><?= esc($payment['contribution_title'] ?? 'General Payment') ?></p>
                  <p class="payment-date"><?= date('M j, Y', strtotime($payment['payment_date'])) ?></p>
                </div>
                <div class="payment-amount">
                  <span class="amount">₱<?= number_format($payment['amount_paid'], 2) ?></span>
                  <span class="status status-<?= strtolower($payment['payment_status']) ?>">
                    <?= ucfirst($payment['payment_status']) ?>
                  </span>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="no-payments">
            <div class="empty-state">
              <i class="fas fa-receipt"></i>
              <h4>No Recent Payments</h4>
              <p>No payment activities found.</p>
              <a href="<?= base_url('payments') ?>" class="btn-primary">
                <i class="fas fa-plus"></i>
                Record First Payment
              </a>
            </div>
          </div>
        <?php endif; ?>
      </div>
      
      <?php if (isset($recent_payments) && count($recent_payments) > 0): ?>
        <div class="view-all-section">
          <a href="<?= base_url('payments/history') ?>" class="view-all-btn">
            View All Payments <i class="fas fa-arrow-right"></i>
          </a>
        </div>
      <?php endif; ?>
    </div>

    <!-- Bottom Navigation -->
    <nav class="bottom-nav">
      <a href="<?= base_url('dashboard') ?>" class="nav-link active">
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
      <a href="<?= base_url('payments/history') ?>" class="nav-link">
        <i class="fas fa-clock"></i>
        <span>History</span>
      </a>
    </nav>
  </div>

  <!-- External JS -->
  <script src="https://unpkg.com/jsqr@1.4.0/dist/jsQR.js"></script>
  <script src="<?= base_url('js/verification-functions.js') ?>"></script>
  <script src="<?= base_url('js/dashboard.js') ?>"></script>
</body>
</html>
