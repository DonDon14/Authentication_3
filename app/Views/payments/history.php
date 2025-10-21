<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ClearPay Payment History - Transaction Records</title>
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
          <li class="nav-item active">
            <a href="<?= base_url('payments/history') ?>" class="nav-link">
              <i class="fas fa-history"></i>
              <span>Payment History</span>
            </a>
          </li>
        </ul>
        
        <div class="nav-divider"></div>
        
        <ul class="nav-list">
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
            <h4><?= session()->get('username') ?? 'Admin User' ?></h4>
            <p>Administrator</p>
          </div>
          <button class="profile-menu-btn" id="profileMenuBtn">
            <i class="fas fa-ellipsis-h"></i>
          </button>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Header -->
      <header class="header">
        <div class="header-left">
          <h1>Payment History</h1>
          <p class="page-subtitle">View and manage all payment transaction records</p>
        </div>
        <div class="header-right">
          <div class="search-container">
            <i class="fas fa-search"></i>
            <input type="text" class="search-input" placeholder="Search payments..." id="searchPayments">
          </div>
          
          <!-- Notification Center -->
          <div class="notification-center">
            <button class="notification-btn" id="notificationBtn">
              <i class="fas fa-bell"></i>
              <span class="notification-count">3</span>
            </button>
          </div>
          
          <!-- User Menu -->
          <div class="user-menu">
            <button class="user-menu-btn" id="userMenuBtn">
              <div class="user-avatar">
                <i class="fas fa-user"></i>
              </div>
              <span class="user-name"><?= session()->get('username') ?? 'Admin' ?></span>
              <i class="fas fa-chevron-down"></i>
            </button>
          </div>
        </div>
      </header>

      <!-- Dashboard Content -->
      <div class="dashboard-content">

        <!-- Stats Grid -->
        <div class="stats-grid">
          <div class="stat-card primary">
            <div class="stat-content">
              <div class="stat-header">
                <h3>Total Collected</h3>
                <div class="stat-icon">
                  <i class="fas fa-peso-sign"></i>
                </div>
              </div>
              <div class="stat-value">₱<?= number_format($totalAmount ?? 0, 2) ?></div>
              <div class="stat-footer">
                <span class="stat-change positive">
                  <i class="fas fa-arrow-up"></i>
                  All time revenue
                </span>
              </div>
            </div>
          </div>
          
          <div class="stat-card success">
            <div class="stat-content">
              <div class="stat-header">
                <h3>Verified</h3>
                <div class="stat-icon">
                  <i class="fas fa-check-circle"></i>
                </div>
              </div>
              <div class="stat-value"><?= $verifiedCount ?? 0 ?></div>
              <div class="stat-footer">
                <span class="stat-change positive">
                  <i class="fas fa-check"></i>
                  Completed payments
                </span>
              </div>
            </div>
          </div>
          
          <div class="stat-card warning">
            <div class="stat-content">
              <div class="stat-header">
                <h3>Pending</h3>
                <div class="stat-icon">
                  <i class="fas fa-clock"></i>
                </div>
              </div>
              <div class="stat-value"><?= $pendingCount ?? 0 ?></div>
              <div class="stat-footer">
                <span class="stat-change neutral">
                  <i class="fas fa-hourglass-half"></i>
                  Awaiting verification
                </span>
              </div>
            </div>
          </div>
          
          <div class="stat-card info">
            <div class="stat-content">
              <div class="stat-header">
                <h3>Today</h3>
                <div class="stat-icon">
                  <i class="fas fa-calendar-day"></i>
                </div>
              </div>
              <div class="stat-value"><?= $todayCount ?? 0 ?></div>
              <div class="stat-footer">
                <span class="stat-change positive">
                  <i class="fas fa-plus"></i>
                  Payments today
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="dashboard-card">
          <div class="card-header">
            <div>
              <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
              <p>Common tasks for payment management</p>
            </div>
          </div>
          <div class="card-content">
            <div class="quick-actions-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
              <button class="action-btn primary" onclick="window.location.href='<?= base_url('payments') ?>'">
                <div class="action-icon">
                  <i class="fas fa-plus"></i>
                </div>
                <div class="action-text">
                  <h4>New Payment</h4>
                  <p>Record payment</p>
                </div>
              </button>
              <button class="action-btn success" onclick="exportPayments()">
                <div class="action-icon">
                  <i class="fas fa-download"></i>
                </div>
                <div class="action-text">
                  <h4>Export Data</h4>
                  <p>Download reports</p>
                </div>
              </button>
              <button class="action-btn info" onclick="filterPayments('verified')">
                <div class="action-icon">
                  <i class="fas fa-filter"></i>
                </div>
                <div class="action-text">
                  <h4>Filter Records</h4>
                  <p>View specific status</p>
                </div>
              </button>
              <button class="action-btn warning" onclick="refreshHistory()">
                <div class="action-icon">
                  <i class="fas fa-sync-alt"></i>
                </div>
                <div class="action-text">
                  <h4>Refresh</h4>
                  <p>Update records</p>
                </div>
              </button>
            </div>
          </div>
        </div>

        <!-- Payment Records -->
        <div class="dashboard-card">
          <div class="card-header">
            <div>
              <h3><i class="fas fa-history"></i> Payment Records</h3>
              <p><?= count($payments ?? []) ?> payments found</p>
            </div>
            <div class="card-actions">
              <select id="statusFilter" style="background: var(--bg-tertiary); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 0.5rem 1rem; font-size: 0.9rem; margin-right: 0.5rem;">
                <option value="">All Status</option>
                <option value="verified">Verified</option>
                <option value="pending">Pending</option>
                <option value="rejected">Rejected</option>
              </select>
              <button class="btn-secondary" onclick="refreshHistory()">
                <i class="fas fa-sync-alt"></i>
                Refresh
              </button>
            </div>
          </div>
          <div class="card-content">
            <div id="paymentRecords">
              <?php if (!empty($payments)): ?>
                <div class="payments-list">
                  <?php foreach ($payments as $payment): ?>
                    <div class="payment-item searchable" data-status="<?= esc($payment['payment_status']) ?>">
                      <div class="payment-avatar">
                        <?= strtoupper(substr($payment['student_name'], 0, 2)) ?>
                      </div>
                      <div class="payment-details">
                        <h4><?= esc($payment['student_name']) ?></h4>
                        <p class="payment-type"><?= esc($payment['payment_type'] ?? 'General Payment') ?></p>
                        <p class="payment-time">
                          ID: <?= esc($payment['student_id']) ?> • <?= date('M j, Y g:i A', strtotime($payment['created_at'])) ?>
                        </p>
                      </div>
                      <div class="payment-amount">
                        <span class="amount">₱<?= number_format($payment['amount_paid'], 2) ?></span>
                        <span class="status status-<?= esc($payment['payment_status']) ?>"><?= ucfirst(esc($payment['payment_status'])) ?></span>
                      </div>
                      <div class="payment-actions">
                        <button class="btn-icon" onclick="viewPaymentDetails(<?= htmlspecialchars(json_encode($payment)) ?>)" title="View receipt">
                          <i class="fas fa-receipt"></i>
                        </button>
                        <button class="btn-icon" onclick="downloadReceipt(<?= $payment['id'] ?? 0 ?>)" title="Download QR" style="color: var(--success-color);">
                          <i class="fas fa-download"></i>
                        </button>
                        <?php if ($payment['payment_status'] === 'pending'): ?>
                        <button class="btn-icon" onclick="verifyPayment(<?= $payment['id'] ?? 0 ?>)" title="Verify payment" style="color: var(--warning-color);">
                          <i class="fas fa-check"></i>
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
                  <h4>No Payment Records</h4>
                  <p>No payment transactions have been recorded yet.</p>
                  <button class="btn-primary" onclick="window.location.href='<?= base_url('payments') ?>'">
                    <i class="fas fa-plus"></i>
                    Record First Payment
                  </button>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>

      </div>
    </main>
  </div>

  <!-- Payment Details Modal -->
  <div id="paymentDetailsModal" class="modal-overlay" style="display: none;">
    <div class="modal-container" style="max-width: 700px; width: 90%;">
      <div class="card-header">
        <div>
          <h3><i class="fas fa-receipt"></i> Payment Receipt</h3>
          <p>Detailed payment information and QR code</p>
        </div>
        <div class="card-actions">
          <button type="button" class="btn-icon" onclick="closePaymentModal()">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>
      <div style="padding: 1.5rem;">
        
        <!-- Student Information Card -->
        <div style="background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.5rem; margin-bottom: 1.5rem;">
          <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
            <div class="student-avatar-large" style="width: 60px; height: 60px; background: linear-gradient(135deg, var(--primary-color), var(--info-color)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--text-inverse); font-size: 1.25rem; flex-shrink: 0;">
              <i class="fas fa-user"></i>
            </div>
            <div>
              <h4 id="modalStudentName" style="font-size: 1.25rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.25rem;"></h4>
              <p id="modalStudentId" style="color: var(--text-secondary); margin-bottom: 0.25rem;"></p>
              <p id="modalPaymentDate" style="color: var(--text-tertiary); font-size: 0.9rem;"></p>
            </div>
          </div>
        </div>
        
        <!-- Payment Summary Grid -->
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
          <div style="text-align: center; padding: 1rem; background: var(--success-light); border-radius: var(--radius-md); border: 1px solid var(--success-color);">
            <div style="color: var(--success-color); font-size: 1.1rem; font-weight: 700; margin-bottom: 0.25rem;" id="modalTotalPaid">₱0.00</div>
            <div style="color: var(--success-color); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">Amount Paid</div>
          </div>
          <div style="text-align: center; padding: 1rem; background: var(--warning-light); border-radius: var(--radius-md); border: 1px solid var(--warning-color);">
            <div style="color: var(--warning-color); font-size: 1.1rem; font-weight: 700; margin-bottom: 0.25rem;" id="modalRemainingBalance">₱0.00</div>
            <div style="color: var(--warning-color); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">Remaining</div>
          </div>
          <div style="text-align: center; padding: 1rem; background: var(--info-light); border-radius: var(--radius-md); border: 1px solid var(--info-color);">
            <div style="color: var(--info-color); font-size: 1.1rem; font-weight: 700; margin-bottom: 0.25rem;" id="modalPaymentStatus">-</div>
            <div style="color: var(--info-color); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">Status</div>
          </div>
        </div>

        <!-- Payment Details -->
        <div style="background: var(--bg-primary); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.5rem; margin-bottom: 1.5rem;">
          <h5 style="margin-bottom: 1rem; color: var(--text-primary); font-size: 1rem; font-weight: 600;">Transaction Details</h5>
          <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
            <div>
              <span style="color: var(--text-secondary); font-size: 0.875rem; display: block; margin-bottom: 0.25rem;">Payment Type:</span>
              <span id="modalPaymentType" style="color: var(--text-primary); font-weight: 500;">-</span>
            </div>
            <div>
              <span style="color: var(--text-secondary); font-size: 0.875rem; display: block; margin-bottom: 0.25rem;">Payment Method:</span>
              <span id="modalPaymentMethod" style="color: var(--text-primary); font-weight: 500;">-</span>
            </div>
            <div>
              <span style="color: var(--text-secondary); font-size: 0.875rem; display: block; margin-bottom: 0.25rem;">Transaction ID:</span>
              <span id="modalTransactionId" style="color: var(--text-primary); font-weight: 500; font-family: monospace;">-</span>
            </div>
            <div>
              <span style="color: var(--text-secondary); font-size: 0.875rem; display: block; margin-bottom: 0.25rem;">Verified On:</span>
              <span id="modalVerifiedDate" style="color: var(--text-primary); font-weight: 500;">-</span>
            </div>
          </div>
        </div>
        
        <!-- QR Code Section -->
        <div style="text-align: center; background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.5rem;">
          <h5 style="margin-bottom: 1rem; color: var(--text-primary); font-size: 1rem; font-weight: 600;">QR Receipt Code</h5>
          <div id="modalQrCode" style="margin-bottom: 1rem; display: flex; justify-content: center; align-items: center; min-height: 200px; background: var(--bg-primary); border-radius: var(--radius-md); border: 2px dashed var(--border-color);">
            <div style="text-align: center; color: var(--text-tertiary);">
              <i class="fas fa-qrcode" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
              <p style="margin: 0;">QR Code will appear here</p>
            </div>
          </div>
          <div style="display: flex; gap: 1rem; justify-content: center;">
            <button class="btn-primary" onclick="downloadQR()" style="padding: 0.75rem 1.5rem;">
              <i class="fas fa-download"></i>
              Download QR
            </button>
            <button class="btn-secondary" onclick="printReceipt()" style="padding: 0.75rem 1.5rem;">
              <i class="fas fa-print"></i>
              Print Receipt
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

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
          <p>Payment has been successfully verified</p>
          <span class="notification-time">5 minutes ago</span>
        </div>
      </div>
      <div class="notification-item">
        <div class="notification-icon primary">
          <i class="fas fa-receipt"></i>
        </div>
        <div class="notification-content">
          <h4>Receipt Generated</h4>
          <p>QR receipt created for new payment</p>
          <span class="notification-time">1 hour ago</span>
        </div>
      </div>
      <div class="notification-item">
        <div class="notification-icon warning">
          <i class="fas fa-clock"></i>
        </div>
        <div class="notification-content">
          <h4>Payment Pending</h4>
          <p>New payment requires verification</p>
          <span class="notification-time">3 hours ago</span>
        </div>
      </div>
    </div>
    <div class="notification-footer">
      <a href="#" class="view-all-notifications">View all notifications</a>
    </div>
  </div>

  <!-- User Dropdown -->
  <div class="user-dropdown" id="userDropdown">
    <div class="dropdown-header">
      <div class="user-info">
        <h4><?= session()->get('username') ?? 'Admin User' ?></h4>
        <p>System Administrator</p>
      </div>
    </div>
    <div class="dropdown-menu">
      <a href="<?= base_url('profile') ?>" class="dropdown-item">
        <i class="fas fa-user"></i>
        <span>My Profile</span>
      </a>
      <a href="<?= base_url('dashboard') ?>" class="dropdown-item">
        <i class="fas fa-cog"></i>
        <span>Settings</span>
      </a>
      <div class="dropdown-divider"></div>
      <a href="<?= base_url('auth/logout') ?>" class="dropdown-item logout">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
      </a>
    </div>
  </div>

  <!-- Profile Menu Dropdown (for sidebar) -->
  <div class="profile-menu-dropdown" id="profileMenuDropdown">
    <div class="dropdown-content">
      <a href="<?= base_url('profile') ?>" class="dropdown-item">
        <i class="fas fa-user"></i>
        <span>Profile</span>
      </a>
      <a href="<?= base_url('dashboard') ?>" class="dropdown-item">
        <i class="fas fa-cog"></i>
        <span>Settings</span>
      </a>
      <div class="dropdown-divider"></div>
      <a href="<?= base_url('auth/logout') ?>" class="dropdown-item logout">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
      </a>
    </div>
  </div>

  <!-- Additional Styles -->
  <style>
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
    }

    .searchable {
      transition: opacity var(--transition-fast);
    }

    .searchable.hidden {
      opacity: 0.3;
      pointer-events: none;
    }

    /* Status filter styles */
    #statusFilter {
      transition: all var(--transition-fast);
    }

    #statusFilter:focus {
      outline: none;
      border-color: var(--primary-color);
      box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    /* Payment item hover effects */
    .payment-item:hover {
      background: var(--bg-secondary);
      transform: translateX(4px);
    }

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
      .quick-actions-grid {
        grid-template-columns: repeat(2, 1fr) !important;
      }
      
      .modal-container {
        width: 95%;
        margin: 1rem;
      }
      
      .payment-summary-grid {
        grid-template-columns: 1fr !important;
        gap: 0.75rem !important;
      }
      
      .card-actions {
        flex-direction: column;
        gap: 0.5rem;
      }
      
      #statusFilter {
        width: 100%;
        margin-right: 0 !important;
        margin-bottom: 0.5rem;
      }
    }
  </style>

  <!-- JavaScript -->
  <script>
    // Dashboard functionality
    document.addEventListener('DOMContentLoaded', function() {
      // Sidebar toggle
      const sidebarToggle = document.getElementById('sidebarToggle');
      const sidebar = document.querySelector('.sidebar');
      
      if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
          sidebar.classList.toggle('collapsed');
        });
      }
      
      // Notification dropdown
      const notificationBtn = document.getElementById('notificationBtn');
      const notificationDropdown = document.getElementById('notificationDropdown');
      
      if (notificationBtn) {
        notificationBtn.addEventListener('click', function(e) {
          e.stopPropagation();
          notificationDropdown.classList.toggle('active');
          document.getElementById('userDropdown')?.classList.remove('active');
        });
      }
      
      // User dropdown
      const userMenuBtn = document.getElementById('userMenuBtn');
      const userDropdown = document.getElementById('userDropdown');
      
      if (userMenuBtn) {
        userMenuBtn.addEventListener('click', function(e) {
          e.stopPropagation();
          userDropdown.classList.toggle('active');
          document.getElementById('notificationDropdown')?.classList.remove('active');
        });
      }
      
      // Profile menu (sidebar)
      const profileMenuBtn = document.getElementById('profileMenuBtn');
      const profileMenuDropdown = document.getElementById('profileMenuDropdown');
      
      if (profileMenuBtn) {
        profileMenuBtn.addEventListener('click', function(e) {
          e.stopPropagation();
          profileMenuDropdown.classList.toggle('active');
        });
      }
      
      // Search functionality
      const searchInput = document.getElementById('searchPayments');
      if (searchInput) {
        searchInput.addEventListener('input', function() {
          const searchTerm = this.value.toLowerCase();
          const paymentItems = document.querySelectorAll('.payment-item');
          
          paymentItems.forEach(item => {
            const studentName = item.querySelector('h4').textContent.toLowerCase();
            const studentId = item.querySelector('.payment-time').textContent.toLowerCase();
            const paymentType = item.querySelector('.payment-type').textContent.toLowerCase();
            
            const matches = studentName.includes(searchTerm) || 
                          studentId.includes(searchTerm) || 
                          paymentType.includes(searchTerm);
            
            if (matches) {
              item.style.display = 'flex';
              item.classList.remove('hidden');
            } else {
              item.style.display = 'none';
              item.classList.add('hidden');
            }
          });
        });
      }
      
      // Status filter functionality
      const statusFilter = document.getElementById('statusFilter');
      if (statusFilter) {
        statusFilter.addEventListener('change', function() {
          const selectedStatus = this.value;
          const paymentItems = document.querySelectorAll('.payment-item');
          
          paymentItems.forEach(item => {
            const itemStatus = item.dataset.status;
            
            if (selectedStatus === '' || itemStatus === selectedStatus) {
              item.style.display = 'flex';
              item.classList.remove('hidden');
            } else {
              item.style.display = 'none';
              item.classList.add('hidden');
            }
          });
        });
      }
      
      // Close dropdowns when clicking outside
      document.addEventListener('click', function() {
        document.querySelectorAll('.notification-dropdown, .user-dropdown, .profile-menu-dropdown').forEach(dropdown => {
          dropdown.classList.remove('active');
        });
      });
      
      // Prevent dropdown close when clicking inside
      document.querySelectorAll('.notification-dropdown, .user-dropdown, .profile-menu-dropdown').forEach(dropdown => {
        dropdown.addEventListener('click', function(e) {
          e.stopPropagation();
        });
      });
    });
    
    // Modal functions
    function viewPaymentDetails(paymentData) {
      const modal = document.getElementById('paymentDetailsModal');
      
      // Populate modal data
      document.getElementById('modalStudentName').textContent = paymentData.student_name || '-';
      document.getElementById('modalStudentId').textContent = 'ID: ' + (paymentData.student_id || '-');
      document.getElementById('modalPaymentDate').textContent = paymentData.created_at ? new Date(paymentData.created_at).toLocaleString() : '-';
      document.getElementById('modalTotalPaid').textContent = '₱' + parseFloat(paymentData.amount_paid || 0).toFixed(2);
      document.getElementById('modalRemainingBalance').textContent = '₱' + parseFloat(paymentData.remaining_balance || 0).toFixed(2);
      document.getElementById('modalPaymentStatus').textContent = (paymentData.payment_status || 'Unknown').toUpperCase();
      document.getElementById('modalPaymentType').textContent = paymentData.payment_type || 'General Payment';
      document.getElementById('modalPaymentMethod').textContent = paymentData.payment_method || 'Not specified';
      document.getElementById('modalTransactionId').textContent = paymentData.id || 'N/A';
      document.getElementById('modalVerifiedDate').textContent = paymentData.verified_at || 'Not verified';
      
      // Generate QR code
      generateQRCode(paymentData);
      
      // Show modal
      modal.style.display = 'flex';
      document.body.style.overflow = 'hidden';
    }
    
    function closePaymentModal() {
      const modal = document.getElementById('paymentDetailsModal');
      modal.style.display = 'none';
      document.body.style.overflow = 'auto';
    }
    
    function generateQRCode(paymentData) {
      const qrContainer = document.getElementById('modalQrCode');
      const qrData = {
        student_id: paymentData.student_id,
        student_name: paymentData.student_name,
        amount: paymentData.amount_paid,
        payment_id: paymentData.id,
        status: paymentData.payment_status,
        date: paymentData.created_at
      };
      
      // Clear previous QR code
      qrContainer.innerHTML = '';
      
      // Create QR code using external library if available
      if (typeof QRCode !== 'undefined') {
        QRCode.toCanvas(qrContainer, JSON.stringify(qrData), function (error) {
          if (error) {
            qrContainer.innerHTML = '<div style="color: var(--error-color); text-align: center;"><i class="fas fa-exclamation-triangle"></i><br>QR Code generation failed</div>';
          }
        });
      } else {
        qrContainer.innerHTML = '<div style="color: var(--warning-color); text-align: center;"><i class="fas fa-qrcode" style="font-size: 2rem;"></i><br>QR Code: ' + (paymentData.id || 'N/A') + '</div>';
      }
    }
    
    // Helper functions
    function refreshHistory() {
      window.location.reload();
    }
    
    function exportPayments() {
      alert('Export functionality coming soon!');
    }
    
    function filterPayments(status) {
      const statusFilter = document.getElementById('statusFilter');
      if (statusFilter) {
        statusFilter.value = status;
        statusFilter.dispatchEvent(new Event('change'));
      }
    }
    
    function downloadReceipt(paymentId) {
      alert('Download receipt for payment ID: ' + paymentId);
    }
    
    function verifyPayment(paymentId) {
      if (confirm('Are you sure you want to verify this payment?')) {
        alert('Payment verification for ID: ' + paymentId + ' - Feature coming soon!');
      }
    }
    
    function downloadQR() {
      alert('QR code download functionality coming soon!');
    }
    
    function printReceipt() {
      window.print();
    }
    
    // Close modal when clicking outside
    document.addEventListener('click', function(e) {
      const modal = document.getElementById('paymentDetailsModal');
      if (e.target === modal) {
        closePaymentModal();
      }
    });
  </script>

  <!-- External JS -->
  <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
  <script src="<?= base_url('js/history.js') ?>"></script>
</body>
</html>