<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ClearPay Dashboard - Admin Portal</title>
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
          <li class="nav-item active">
            <a href="<?= base_url('dashboard') ?>" class="nav-link">
              <i class="fas fa-home"></i>
              <span>Dashboard</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= base_url('payments') ?>" class="nav-link">
              <i class="fas fa-credit-card"></i>
              <span>Payments</span>
              <span class="nav-badge">New</span>
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
          <li class="nav-item">
            <a href="<?= base_url('students') ?>" class="nav-link">
              <i class="fas fa-users"></i>
              <span>Students</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= base_url('announcements') ?>" class="nav-link">
              <i class="fas fa-bullhorn"></i>
              <span>Announcements</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= base_url('settings') ?>" class="nav-link">
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
            <h4><?= esc(explode(' ', $name)[0]) ?></h4>
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
      
      <!-- Top Header Bar -->
      <header class="header">
        <div class="header-left">
          <h1 class="page-title">Dashboard</h1>
          <p class="page-subtitle">Welcome back, <?= esc($name) ?>! Here's your overview.</p>
        </div>
        
        <div class="header-right">
          <!-- Search Bar -->
          <div class="search-container">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Search payments, students..." class="search-input">
          </div>
          
          <!-- Notifications -->
          <div class="notification-center">
            <button class="notification-btn" onclick="toggleNotifications()">
              <i class="fas fa-bell"></i>
              <span class="notification-count">3</span>
            </button>
            
            <!-- Notification Dropdown -->
            <div class="notification-dropdown" id="notificationDropdown">
              <div class="notification-header">
                <h3>Notifications</h3>
                <button class="mark-read-btn">Mark all read</button>
              </div>
              <div class="notification-list">
                <div class="notification-item unread">
                  <div class="notification-icon success">
                    <i class="fas fa-check-circle"></i>
                  </div>
                  <div class="notification-content">
                    <h4>Payment Verified</h4>
                    <p>John Doe's payment of ₱1,000.00 has been verified</p>
                    <span class="notification-time">2 minutes ago</span>
                  </div>
                </div>
                <div class="notification-item unread">
                  <div class="notification-icon primary">
                    <i class="fas fa-dollar-sign"></i>
                  </div>
                  <div class="notification-content">
                    <h4>New Payment Received</h4>
                    <p>Jane Smith submitted uniform payment</p>
                    <span class="notification-time">1 hour ago</span>
                  </div>
                </div>
                <div class="notification-item">
                  <div class="notification-icon info">
                    <i class="fas fa-user-plus"></i>
                  </div>
                  <div class="notification-content">
                    <h4>System Update</h4>
                    <p>QR receipt system is now active</p>
                    <span class="notification-time">3 hours ago</span>
                  </div>
                </div>
              </div>
              <div class="notification-footer">
                <a href="#" class="view-all-notifications">View all notifications</a>
              </div>
            </div>
          </div>
          
          <!-- User Menu -->
          <div class="user-menu">
            <button class="user-menu-btn" onclick="toggleUserMenu()">
              <div class="user-avatar">
                <i class="fas fa-user"></i>
              </div>
              <span class="user-name"><?= esc(explode(' ', $name)[0]) ?></span>
              <i class="fas fa-chevron-down"></i>
            </button>
            
            <!-- User Dropdown -->
            <div class="user-dropdown" id="userDropdown">
              <div class="dropdown-header">
                <div class="user-info">
                  <h4><?= esc($name) ?></h4>
                  <p><?= esc($email ?? 'admin@clearpay.com') ?></p>
                </div>
              </div>
              <div class="dropdown-menu">
                <a href="<?= base_url('profile') ?>" class="dropdown-item">
                  <i class="fas fa-user"></i>
                  <span>Profile</span>
                </a>
                <a href="<?= base_url('settings') ?>" class="dropdown-item">
                  <i class="fas fa-cog"></i>
                  <span>Settings</span>
                </a>
                <a href="#" class="dropdown-item">
                  <i class="fas fa-question-circle"></i>
                  <span>Help</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="<?= base_url('logout') ?>" class="dropdown-item logout">
                  <i class="fas fa-sign-out-alt"></i>
                  <span>Logout</span>
                </a>
              </div>
            </div>
          </div>
        </div>
      </header>

      <!-- Dashboard Content -->
      <div class="dashboard-content">
        
        <!-- Statistics Cards -->
        <div class="stats-grid">
          <div class="stat-card primary">
            <div class="stat-content">
              <div class="stat-header">
                <h3>Total Collections</h3>
                <div class="stat-icon">
                  <i class="fas fa-coins"></i>
                </div>
              </div>
              <div class="stat-value">₱<?= number_format($stats['total_collections'], 2) ?></div>
              <div class="stat-footer">
                <span class="stat-change positive">
                  <i class="fas fa-arrow-up"></i>
                  +12.5%
                </span>
                <span class="stat-period">vs last month</span>
              </div>
            </div>
          </div>
          
          <div class="stat-card success">
            <div class="stat-content">
              <div class="stat-header">
                <h3>Verified Payments</h3>
                <div class="stat-icon">
                  <i class="fas fa-check-circle"></i>
                </div>
              </div>
              <div class="stat-value"><?= $stats['verified_count'] ?></div>
              <div class="stat-footer">
                <span class="stat-change positive">
                  <i class="fas fa-arrow-up"></i>
                  +8.2%
                </span>
                <span class="stat-period">this week</span>
              </div>
            </div>
          </div>
          
          <div class="stat-card warning">
            <div class="stat-content">
              <div class="stat-header">
                <h3>Pending Payments</h3>
                <div class="stat-icon">
                  <i class="fas fa-clock"></i>
                </div>
              </div>
              <div class="stat-value"><?= $stats['pending_count'] ?></div>
              <div class="stat-footer">
                <span class="stat-change neutral">
                  <i class="fas fa-minus"></i>
                  No change
                </span>
                <span class="stat-period">since yesterday</span>
              </div>
            </div>
          </div>
          
          <div class="stat-card info">
            <div class="stat-content">
              <div class="stat-header">
                <h3>Today's Payments</h3>
                <div class="stat-icon">
                  <i class="fas fa-calendar-day"></i>
                </div>
              </div>
              <div class="stat-value"><?= $stats['today_count'] ?></div>
              <div class="stat-footer">
                <span class="stat-change positive">
                  <i class="fas fa-arrow-up"></i>
                  +3 new
                </span>
                <span class="stat-period">since morning</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Main Dashboard Grid -->
        <div class="dashboard-grid">
          
          <!-- Quick Actions Panel -->
          <div class="dashboard-card">
            <div class="card-header">
              <h3>Quick Actions</h3>
              <p>Frequently used operations</p>
            </div>
            <div class="card-content">
              <div class="quick-actions-grid">
                <button class="action-btn primary" onclick="window.location.href='<?= base_url('payments') ?>'">
                  <div class="action-icon">
                    <i class="fas fa-plus"></i>
                  </div>
                  <div class="action-text">
                    <h4>Record Payment</h4>
                    <p>Add new payment record</p>
                  </div>
                </button>
                
                <button class="action-btn success" onclick="showVerificationModal()">
                  <div class="action-icon">
                    <i class="fas fa-check"></i>
                  </div>
                  <div class="action-text">
                    <h4>Verify Payments</h4>
                    <p>Scan QR codes to verify</p>
                  </div>
                </button>
                
                <button class="action-btn info" onclick="window.location.href='<?= base_url('contributions') ?>'">
                  <div class="action-icon">
                    <i class="fas fa-hand-holding-usd"></i>
                  </div>
                  <div class="action-text">
                    <h4>Manage Contributions</h4>
                    <p>Add or edit fee types</p>
                  </div>
                </button>
                
                <button class="action-btn secondary" onclick="window.location.href='<?= base_url('analytics') ?>'">
                  <div class="action-icon">
                    <i class="fas fa-chart-bar"></i>
                  </div>
                  <div class="action-text">
                    <h4>View Analytics</h4>
                    <p>System performance insights</p>
                  </div>
                </button>
                
                <button class="action-btn warning" onclick="window.location.href='<?= base_url('payments/partial') ?>'">
                  <div class="action-icon">
                    <i class="fas fa-clock"></i>
                  </div>
                  <div class="action-text">
                    <h4>Partial Payments</h4>
                    <p>View installment records</p>
                  </div>
                </button>
                
                <button class="action-btn purple" onclick="window.location.href='<?= base_url('announcements') ?>'">
                  <div class="action-icon">
                    <i class="fas fa-bullhorn"></i>
                  </div>
                  <div class="action-text">
                    <h4>Add Announcement</h4>
                    <p>Create system announcements</p>
                  </div>
                </button>
              </div>
            </div>
          </div>

          <!-- Recent Payments Panel -->
          <div class="dashboard-card">
            <div class="card-header">
              <h3>Recent Payments</h3>
              <div class="card-actions">
                <button class="btn-secondary" onclick="refreshPayments()">
                  <i class="fas fa-refresh"></i>
                </button>
                <a href="<?= base_url('payments/history') ?>" class="btn-primary">
                  View All
                </a>
              </div>
            </div>
            <div class="card-content">
              <?php if (isset($recentPayments) && count($recentPayments) > 0): ?>
                <div class="payments-list">
                  <?php foreach (array_slice($recentPayments, 0, 5) as $payment): ?>
                    <div class="payment-item">
                      <div class="payment-avatar">
                        <i class="fas fa-user"></i>
                      </div>
                      <div class="payment-details">
                        <h4><?= esc($payment['student_name']) ?></h4>
                        <p class="payment-type"><?= esc($payment['contribution_title'] ?? 'General Payment') ?></p>
                        <span class="payment-time"><?= date('M j, Y g:i A', strtotime($payment['payment_date'])) ?></span>
                      </div>
                      <div class="payment-amount">
                        <span class="amount">₱<?= number_format($payment['amount_paid'], 2) ?></span>
                        <span class="status status-<?= strtolower($payment['payment_status']) ?>">
                          <?= ucfirst($payment['payment_status']) ?>
                        </span>
                      </div>
                      <div class="payment-actions">
                        <button class="btn-icon" title="View Details">
                          <i class="fas fa-eye"></i>
                        </button>
                        <?php if ($payment['qr_receipt_path']): ?>
                          <button class="btn-icon" title="Download QR Receipt">
                            <i class="fas fa-qrcode"></i>
                          </button>
                        <?php endif; ?>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php else: ?>
                <div class="empty-state">
                  <div class="empty-icon">
                    <i class="fas fa-receipt"></i>
                  </div>
                  <h4>No Recent Payments</h4>
                  <p>No payment activities recorded yet.</p>
                  <button class="btn-primary" onclick="window.location.href='<?= base_url('payments') ?>'">
                    <i class="fas fa-plus"></i>
                    Record First Payment
                  </button>
                </div>
              <?php endif; ?>
            </div>
          </div>

          <!-- System Status Panel -->
          <div class="dashboard-card">
            <div class="card-header">
              <h3>System Status</h3>
              <span class="status-indicator online">Online</span>
            </div>
            <div class="card-content">
              <div class="status-list">
                <div class="status-item">
                  <div class="status-icon success">
                    <i class="fas fa-database"></i>
                  </div>
                  <div class="status-info">
                    <h4>Database</h4>
                    <p>Connected and operational</p>
                  </div>
                  <div class="status-badge success">Healthy</div>
                </div>
                
                <div class="status-item">
                  <div class="status-icon success">
                    <i class="fas fa-qrcode"></i>
                  </div>
                  <div class="status-info">
                    <h4>QR Generation</h4>
                    <p>GD extension active</p>
                  </div>
                  <div class="status-badge success">Active</div>
                </div>
                
                <div class="status-item">
                  <div class="status-icon warning">
                    <i class="fas fa-cloud"></i>
                  </div>
                  <div class="status-info">
                    <h4>Backup System</h4>
                    <p>Last backup: 2 hours ago</p>
                  </div>
                  <div class="status-badge warning">Scheduled</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Recent Activity Panel -->
          <div class="dashboard-card">
            <div class="card-header">
              <h3>Recent Activity</h3>
              <p>Latest system activities (<?= count($recentActivities ?? []) ?> recent activities)</p>
            </div>
            <div class="card-content">
              <div class="activity-timeline">
                <?php if (!empty($recentActivities)): ?>
                  <?php foreach ($recentActivities as $activity): ?>
                    <div class="timeline-item">
                      <div class="timeline-marker <?= $activity['color'] ?>">
                        <i class="<?= $activity['icon'] ?>"></i>
                      </div>
                      <div class="timeline-content">
                        <h4><?= esc($activity['title']) ?></h4>
                        <p><?= esc($activity['description']) ?></p>
                        <?php if (!empty($activity['user_name']) && $activity['user_name'] !== 'System'): ?>
                          <small class="activity-user">by <?= esc($activity['user_name']) ?></small>
                        <?php endif; ?>
                        <span class="timeline-time"><?= $activity['time_ago'] ?></span>
                      </div>
                    </div>
                  <?php endforeach; ?>
                <?php else: ?>
                  <div class="timeline-item">
                    <div class="timeline-marker primary">
                      <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="timeline-content">
                      <h4>Welcome to ClearPay</h4>
                      <p>No recent activities yet. Start by recording your first payment!</p>
                      <span class="timeline-time">System</span>
                    </div>
                  </div>
                <?php endif; ?>
              </div>
              
              <?php if (!empty($recentActivities) && count($recentActivities) >= 10): ?>
              <div class="activity-footer">
                <a href="#" class="view-all-link" onclick="showAllActivities()">
                  <i class="fas fa-history"></i>
                  View All Activities
                </a>
              </div>
              <?php endif; ?>
            </div>
          </div>

        </div>
      </div>
    </main>
  </div>

  <!-- Profile Menu Dropdown -->
  <div class="profile-menu-dropdown" id="profileMenuDropdown">
    <div class="dropdown-content">
      <a href="<?= base_url('profile') ?>" class="dropdown-item">
        <i class="fas fa-user"></i>
        <span>Profile</span>
      </a>
      <a href="<?= base_url('settings') ?>" class="dropdown-item">
        <i class="fas fa-cog"></i>
        <span>Settings</span>
      </a>
      <div class="dropdown-divider"></div>
      <a href="<?= base_url('logout') ?>" class="dropdown-item logout">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
      </a>
    </div>
  </div>

  <!-- JavaScript -->
  <script src="<?= base_url('js/dashboard.js') ?>"></script>
  <script>
    // Initialize dashboard functionality
    document.addEventListener('DOMContentLoaded', function() {
      initializeDashboard();
    });

    function initializeDashboard() {
      // Sidebar toggle functionality
      const sidebarToggle = document.getElementById('sidebarToggle');
      const sidebar = document.querySelector('.sidebar');
      
      sidebarToggle?.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
      });

      // Auto-refresh recent payments every 30 seconds
      setInterval(refreshPayments, 30000);

      // Initialize verification functionality
      if (typeof initializeVerifyButton === 'function') {
        initializeVerifyButton();
      }
    }

    function toggleNotifications() {
      const dropdown = document.getElementById('notificationDropdown');
      dropdown.classList.toggle('active');
    }

    function toggleUserMenu() {
      const dropdown = document.getElementById('userDropdown');
      dropdown.classList.toggle('active');
    }

    function toggleProfileMenu() {
      const dropdown = document.getElementById('profileMenuDropdown');
      dropdown.classList.toggle('active');
    }

    function refreshPayments() {
      // Add AJAX call to refresh payments data
      console.log('Refreshing payments...');
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
      const notifications = document.getElementById('notificationDropdown');
      const userMenu = document.getElementById('userDropdown');
      const profileMenu = document.getElementById('profileMenuDropdown');
      
      if (!event.target.closest('.notification-center')) {
        notifications?.classList.remove('active');
      }
      
      if (!event.target.closest('.user-menu')) {
        userMenu?.classList.remove('active');
      }
      
      if (!event.target.closest('.user-profile')) {
        profileMenu?.classList.remove('active');
      }
    });
  </script>

  <!-- Activity Management Functions -->
  <script>
    function showAllActivities() {
      // Create modal to show all activities
      const modal = document.createElement('div');
      modal.className = 'modal-overlay';
      modal.innerHTML = `
        <div class="modal-container">
          <div class="modal-header">
            <h3><i class="fas fa-history"></i> All Activities</h3>
            <button onclick="closeActivityModal()" class="close-btn">
              <i class="fas fa-times"></i>
            </button>
          </div>
          <div class="modal-content">
            <div class="loading-state">
              <i class="fas fa-spinner fa-spin"></i>
              <p>Loading activities...</p>
            </div>
          </div>
        </div>
      `;
      
      document.body.appendChild(modal);
      modal.style.display = 'flex';
      
      // You can implement AJAX call here to fetch more activities
      // For now, we'll show a placeholder
      setTimeout(() => {
        const content = modal.querySelector('.modal-content');
        content.innerHTML = `
          <div class="activity-list">
            <p>Extended activity history will be available in a future update.</p>
            <p>Currently showing the most recent activities on the dashboard.</p>
          </div>
        `;
      }, 1000);
    }
    
    function closeActivityModal() {
      const modal = document.querySelector('.modal-overlay');
      if (modal) {
        modal.remove();
      }
    }
    
    // Close modal when clicking outside
    document.addEventListener('click', function(e) {
      if (e.target.classList.contains('modal-overlay')) {
        closeActivityModal();
      }
    });
  </script>

  <!-- QR Code Libraries -->
  <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
  
  <!-- Dashboard JavaScript -->
  <script src="<?= base_url('js/dashboard.js') ?>"></script>
  <script src="<?= base_url('js/verification-functions.js') ?>"></script>
  
</body>
</html>
