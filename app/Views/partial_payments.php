<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ClearPay Partial Payments - Installment Manager</title>
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
          <li class="nav-item active">
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
          <h1>Partial Payments</h1>
          <p class="page-subtitle">Manage installment payments and track student progress</p>
        </div>
        <div class="header-right">
          <div class="search-container">
            <i class="fas fa-search"></i>
            <input type="text" class="search-input" placeholder="Search partial payments..." id="searchPartialPayments">
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

        <!-- Quick Actions -->
        <div class="dashboard-card">
          <div class="card-header">
            <div>
              <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
              <p>Common tasks for partial payment management</p>
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
                  <p>Record fresh payment</p>
                </div>
              </button>
              <button class="action-btn success" onclick="showPaymentStats()">
                <div class="action-icon">
                  <i class="fas fa-chart-line"></i>
                </div>
                <div class="action-text">
                  <h4>View Stats</h4>
                  <p>Payment analytics</p>
                </div>
              </button>
              <button class="action-btn info" onclick="exportPartialPayments()">
                <div class="action-icon">
                  <i class="fas fa-download"></i>
                </div>
                <div class="action-text">
                  <h4>Export Data</h4>
                  <p>Download reports</p>
                </div>
              </button>
              <button class="action-btn warning" onclick="sendReminders()">
                <div class="action-icon">
                  <i class="fas fa-bell"></i>
                </div>
                <div class="action-text">
                  <h4>Send Reminders</h4>
                  <p>Notify students</p>
                </div>
              </button>
            </div>
          </div>
        </div>

        <!-- Partial Payments List -->
        <div class="dashboard-card">
          <div class="card-header">
            <div>
              <h3><i class="fas fa-clock"></i> Active Partial Payments</h3>
              <p>Students with ongoing installment payments</p>
            </div>
            <div class="card-actions">
              <button class="btn-secondary" onclick="refreshPayments()">
                <i class="fas fa-sync-alt"></i>
                Refresh
              </button>
            </div>
          </div>
          <div class="card-content">
            <div id="partialPaymentsList">
              <?php if (!empty($partialPayments)): ?>
                <div class="payments-grid" style="display: grid; gap: 1rem;">
                  <?php foreach ($partialPayments as $payment): ?>
                    <?php 
                      $paidAmount = $payment['total_amount_due'] - $payment['remaining_balance'];
                      $progressPercentage = ($paidAmount / $payment['total_amount_due']) * 100;
                    ?>
                    <div class="partial-payment-card" 
                         onclick="openPaymentModal(<?= $payment['contribution_id'] ?>, '<?= esc($payment['student_id']) ?>', '<?= esc($payment['student_name']) ?>', '<?= esc($payment['contribution_title']) ?>', <?= $payment['total_amount_due'] ?>, <?= $payment['remaining_balance'] ?>)"
                         data-contribution="<?= $payment['contribution_id'] ?>" 
                         data-student="<?= esc($payment['student_id']) ?>"
                         style="background: var(--bg-primary); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.5rem; cursor: pointer; transition: all var(--transition-fast);">
                      
                      <div class="payment-card-header" style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1rem;">
                        <div class="student-info">
                          <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.75rem;">
                            <div class="student-avatar" style="width: 48px; height: 48px; background: linear-gradient(135deg, var(--primary-color), var(--info-color)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--text-inverse); font-size: 1rem; flex-shrink: 0;">
                              <?= strtoupper(substr($payment['student_name'], 0, 2)) ?>
                            </div>
                            <div>
                              <h4 style="font-size: 1.125rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.25rem;"><?= esc($payment['student_name']) ?></h4>
                              <p style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 0.25rem;">ID: <?= esc($payment['student_id']) ?></p>
                              <p style="color: var(--text-tertiary); font-size: 0.8rem;">Last payment: <?= date('M j, Y', strtotime($payment['created_at'])) ?></p>
                            </div>
                          </div>
                          <div style="background: var(--info-light); padding: 0.5rem 0.75rem; border-radius: var(--radius-md); border-left: 4px solid var(--info-color);">
                            <p style="font-weight: 500; color: var(--info-color); margin: 0; font-size: 0.9rem;"><?= esc($payment['contribution_title']) ?></p>
                          </div>
                        </div>
                        
                        <div class="payment-status" style="text-align: right;">
                          <div style="background: var(--warning-light); color: var(--warning-color); padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem;">
                            PARTIAL
                          </div>
                          <div style="font-size: 0.875rem; color: var(--text-secondary);">
                            <?= number_format($progressPercentage, 1) ?>% Complete
                          </div>
                        </div>
                      </div>
                      
                      <div class="payment-progress" style="margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                          <span style="font-size: 1.25rem; font-weight: 700; color: var(--success-color);">$<?= number_format($paidAmount, 2) ?></span>
                          <span style="color: var(--text-tertiary); font-size: 0.9rem;">of</span>
                          <span style="font-size: 1.1rem; font-weight: 600; color: var(--text-primary);">$<?= number_format($payment['total_amount_due'], 2) ?></span>
                        </div>
                        
                        <div style="background: var(--bg-tertiary); height: 8px; border-radius: 4px; overflow: hidden; margin-bottom: 0.75rem;">
                          <div style="height: 100%; background: linear-gradient(90deg, var(--success-color), var(--info-color)); width: <?= $progressPercentage ?>%; transition: width var(--transition-normal);"></div>
                        </div>
                        
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                          <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-exclamation-circle" style="color: var(--warning-color); font-size: 0.9rem;"></i>
                            <span style="color: var(--text-secondary); font-size: 0.875rem;">Remaining:</span>
                            <span style="font-weight: 600; color: var(--warning-color);">$<?= number_format($payment['remaining_balance'], 2) ?></span>
                          </div>
                          <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--primary-color); font-size: 0.875rem; font-weight: 500;">
                            <i class="fas fa-plus-circle"></i>
                            <span>Add Payment</span>
                          </div>
                        </div>
                      </div>
                      
                      <div style="padding-top: 1rem; border-top: 1px solid var(--border-color); display: flex; gap: 0.5rem;">
                        <button class="btn-secondary" style="flex: 1; padding: 0.5rem 1rem; font-size: 0.875rem;" onclick="event.stopPropagation(); viewPaymentHistory(<?= $payment['contribution_id'] ?>, '<?= esc($payment['student_id']) ?>')">
                          <i class="fas fa-history"></i>
                          History
                        </button>
                        <button class="btn-primary" style="flex: 2; padding: 0.5rem 1rem; font-size: 0.875rem;" onclick="event.stopPropagation(); openPaymentModal(<?= $payment['contribution_id'] ?>, '<?= esc($payment['student_id']) ?>', '<?= esc($payment['student_name']) ?>', '<?= esc($payment['contribution_title']) ?>', <?= $payment['total_amount_due'] ?>, <?= $payment['remaining_balance'] ?>)">
                          <i class="fas fa-plus"></i>
                          Add Installment
                        </button>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php else: ?>
                <div class="empty-state">
                  <div class="empty-icon">
                    <i class="fas fa-check-circle"></i>
                  </div>
                  <h4>No Partial Payments</h4>
                  <p>All students have either fully paid or haven't started payments yet.</p>
                  <button class="btn-primary" onclick="window.location.href='<?= base_url('payments') ?>'">
                    <i class="fas fa-plus"></i>
                    Record New Payment
                  </button>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>

      </div>
    </main>
  </div>

  <!-- Payment Modal -->
  <div id="partialPaymentModal" class="modal-overlay" style="display: none;">
    <div class="modal-container" style="max-width: 600px; width: 90%;">
      <div class="card-header">
        <div>
          <h3 id="modalTitle"><i class="fas fa-plus-circle"></i> Add Payment Installment</h3>
          <p>Record additional payment for this student</p>
        </div>
        <div class="card-actions">
          <button type="button" class="btn-icon" onclick="closePaymentModal()">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>
      <div style="padding: 1.5rem;">
        
        <!-- Payment Summary Card -->
        <div style="background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.5rem; margin-bottom: 1.5rem;">
          <div class="student-summary" style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
            <div class="student-avatar-large" style="width: 60px; height: 60px; background: linear-gradient(135deg, var(--primary-color), var(--info-color)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--text-inverse); font-size: 1.25rem; flex-shrink: 0;">
              <i class="fas fa-user"></i>
            </div>
            <div>
              <h4 id="modalStudentName" style="font-size: 1.25rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.25rem;"></h4>
              <p style="color: var(--text-secondary); margin-bottom: 0.25rem;">ID: <span id="modalStudentId"></span></p>
              <p style="color: var(--text-tertiary); font-size: 0.9rem;">Contribution: <span id="modalContributionTitle"></span></p>
            </div>
          </div>
          
          <div class="payment-status-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
            <div style="text-align: center; padding: 1rem; background: var(--bg-primary); border-radius: var(--radius-md); border: 1px solid var(--border-color);">
              <div style="color: var(--info-color); font-size: 1.1rem; font-weight: 700; margin-bottom: 0.25rem;" id="modalTotalDue">$0.00</div>
              <div style="color: var(--text-secondary); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">Total Due</div>
            </div>
            <div style="text-align: center; padding: 1rem; background: var(--bg-primary); border-radius: var(--radius-md); border: 1px solid var(--border-color);">
              <div style="color: var(--success-color); font-size: 1.1rem; font-weight: 700; margin-bottom: 0.25rem;" id="modalAlreadyPaid">$0.00</div>
              <div style="color: var(--text-secondary); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">Already Paid</div>
            </div>
            <div style="text-align: center; padding: 1rem; background: var(--warning-light); border-radius: var(--radius-md); border: 1px solid var(--warning-color);">
              <div style="color: var(--warning-color); font-size: 1.1rem; font-weight: 700; margin-bottom: 0.25rem;" id="modalRemainingBalance">$0.00</div>
              <div style="color: var(--warning-color); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">Remaining</div>
            </div>
          </div>
        </div>
        
        <!-- Payment Form -->
        <form id="partialPaymentForm">
          <input type="hidden" id="hiddenContributionId" name="contribution_id">
          <input type="hidden" id="hiddenStudentId" name="student_id">
          <input type="hidden" id="hiddenStudentName" name="student_name">
          
          <div class="form-group">
            <label for="paymentAmount">Payment Amount ($)</label>
            <div style="position: relative;">
              <i class="fas fa-dollar-sign" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-tertiary);"></i>
              <input type="number" id="paymentAmount" name="amount" class="search-input" step="0.01" min="0.01" required style="padding-left: 2.5rem;" placeholder="Enter installment amount">
            </div>
            <small style="color: var(--text-secondary); font-size: 0.875rem; margin-top: 0.5rem; display: block;">Enter the amount for this installment payment</small>
          </div>
          
          <div class="form-group">
            <label for="paymentMethodModal">Payment Method</label>
            <div style="position: relative;">
              <i class="fas fa-credit-card" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-tertiary);"></i>
              <select id="paymentMethodModal" name="payment_method" required style="padding-left: 2.5rem; background: var(--bg-tertiary); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 0.75rem 1rem 0.75rem 2.5rem; width: 100%; font-size: 0.9rem;">
                <option value="cash">Cash</option>
                <option value="card">Card</option>
                <option value="bank_transfer">Bank Transfer</option>
                <option value="gcash">GCash</option>
                <option value="mobile_payment">Mobile Payment</option>
              </select>
            </div>
          </div>
          
          <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
            <button type="button" class="btn-secondary" onclick="closePaymentModal()">
              <i class="fas fa-times"></i>
              Cancel
            </button>
            <button type="submit" class="btn-primary">
              <i class="fas fa-plus"></i>
              Record Payment
            </button>
          </div>
        </form>
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
        <div class="notification-icon warning">
          <i class="fas fa-clock"></i>
        </div>
        <div class="notification-content">
          <h4>Payment Reminder</h4>
          <p>3 students have pending partial payments</p>
          <span class="notification-time">10 minutes ago</span>
        </div>
      </div>
      <div class="notification-item">
        <div class="notification-icon success">
          <i class="fas fa-check-circle"></i>
        </div>
        <div class="notification-content">
          <h4>Installment Recorded</h4>
          <p>Payment installment added successfully</p>
          <span class="notification-time">1 hour ago</span>
        </div>
      </div>
      <div class="notification-item">
        <div class="notification-icon info">
          <i class="fas fa-chart-line"></i>
        </div>
        <div class="notification-content">
          <h4>Progress Update</h4>
          <p>Student completed 75% of payment plan</p>
          <span class="notification-time">2 hours ago</span>
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

    .form-group {
      margin-bottom: 1.5rem;
    }

    .form-group label {
      display: block;
      font-weight: 600;
      color: var(--text-primary);
      margin-bottom: 0.5rem;
      font-size: 0.9rem;
    }

    .partial-payment-card:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-lg);
      border-color: var(--primary-color);
    }

    .searchable {
      transition: opacity var(--transition-fast);
    }

    .searchable.hidden {
      opacity: 0.3;
      pointer-events: none;
    }

    /* Progress Bar Animation */
    .partial-payment-card:hover .progress-fill {
      animation: pulse-progress 2s ease-in-out infinite;
    }

    @keyframes pulse-progress {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.7; }
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
      
      .payment-status-grid {
        grid-template-columns: 1fr !important;
        gap: 0.75rem !important;
      }
      
      .student-summary {
        flex-direction: column !important;
        text-align: center;
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
      const searchInput = document.getElementById('searchPartialPayments');
      if (searchInput) {
        searchInput.addEventListener('input', function() {
          const searchTerm = this.value.toLowerCase();
          const paymentCards = document.querySelectorAll('.partial-payment-card');
          
          paymentCards.forEach(card => {
            const studentName = card.querySelector('h4').textContent.toLowerCase();
            const studentId = card.querySelector('[id*="student_id"]')?.textContent.toLowerCase() || '';
            const contributionTitle = card.dataset.contribution || '';
            
            const matches = studentName.includes(searchTerm) || 
                          studentId.includes(searchTerm) || 
                          contributionTitle.toLowerCase().includes(searchTerm);
            
            card.style.display = matches ? 'block' : 'none';
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
    function openPaymentModal(contributionId, studentId, studentName, contributionTitle, totalDue, remainingBalance) {
      const modal = document.getElementById('partialPaymentModal');
      const alreadyPaid = totalDue - remainingBalance;
      
      // Populate modal data
      document.getElementById('modalStudentName').textContent = studentName;
      document.getElementById('modalStudentId').textContent = studentId;
      document.getElementById('modalContributionTitle').textContent = contributionTitle;
      document.getElementById('modalTotalDue').textContent = '$' + totalDue.toFixed(2);
      document.getElementById('modalAlreadyPaid').textContent = '$' + alreadyPaid.toFixed(2);
      document.getElementById('modalRemainingBalance').textContent = '$' + remainingBalance.toFixed(2);
      
      // Populate hidden form fields
      document.getElementById('hiddenContributionId').value = contributionId;
      document.getElementById('hiddenStudentId').value = studentId;
      document.getElementById('hiddenStudentName').value = studentName;
      
      // Set max payment amount
      document.getElementById('paymentAmount').max = remainingBalance.toFixed(2);
      document.getElementById('paymentAmount').placeholder = 'Max: $' + remainingBalance.toFixed(2);
      
      // Show modal
      modal.style.display = 'flex';
      document.body.style.overflow = 'hidden';
    }
    
    function closePaymentModal() {
      const modal = document.getElementById('partialPaymentModal');
      modal.style.display = 'none';
      document.body.style.overflow = 'auto';
      document.getElementById('partialPaymentForm').reset();
    }
    
    // Helper functions
    function refreshPayments() {
      window.location.reload();
    }
    
    function showPaymentStats() {
      alert('Payment analytics feature coming soon!');
    }
    
    function exportPartialPayments() {
      alert('Export functionality coming soon!');
    }
    
    function sendReminders() {
      alert('Reminder system coming soon!');
    }
    
    function viewPaymentHistory(contributionId, studentId) {
      window.location.href = `<?= base_url('payments/history') ?>?contribution_id=${contributionId}&student_id=${studentId}`;
    }
    
    // Close modal when clicking outside
    document.addEventListener('click', function(e) {
      const modal = document.getElementById('partialPaymentModal');
      if (e.target === modal) {
        closePaymentModal();
      }
    });
  </script>

  <script src="<?= base_url('js/payments.js') ?>"></script>
</body>
</html>