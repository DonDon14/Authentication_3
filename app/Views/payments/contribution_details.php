<?php
use App\Models\UsersModel;
$usersModel = new UsersModel();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($contribution['title']) ?> - Payment Details | ClearPay</title>
  <link rel="stylesheet" href="<?= base_url('css/dashboard.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Additional styles for contribution details -->
  <style>
    /* Profile avatar styles */
    .profile-avatar {
      position: relative;
      overflow: hidden;
      width: 40px;
      height: 40px;
      border-radius: 50%;
    }
    
    .profile-avatar img {
      width: 100% !important;
      height: 100% !important;
      object-fit: cover !important;
      border-radius: 50%;
    }
    
    /* Header user avatar styles */
    .user-avatar {
      position: relative;
      overflow: hidden;
      width: 40px;
      height: 40px;
      border-radius: 50%;
    }
    
    .user-avatar img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 50%;
    }

    .contribution-info {
      margin-bottom: 1.5rem;
    }
    
    .contribution-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 1rem;
    }
    
    .contribution-header h4 {
      margin: 0;
      font-size: 1.25rem;
      font-weight: 600;
      color: var(--text-primary);
    }
    
    .contribution-description {
      margin: 0.5rem 0 1rem 0;
      color: var(--text-secondary);
      line-height: 1.5;
    }
    
    .contribution-meta {
      display: flex;
      flex-wrap: wrap;
      gap: 1.5rem;
    }
    
    .meta-item {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 0.875rem;
      color: var(--text-secondary);
    }
    
    .meta-item i {
      color: var(--primary-color);
    }
    
    .students-list {
      display: flex;
      flex-direction: column;
      gap: 1rem;
      margin-top: 1rem;
    }
    
    .student-item {
      cursor: pointer;
      transition: var(--transition-fast);
    }
    
    .student-item:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-lg);
    }
    
    .student-info {
      display: flex;
      align-items: center;
      gap: 1rem;
    }
    
    .student-avatar {
      width: 48px;
      height: 48px;
      background: var(--primary-color);
      color: white;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.25rem;
      flex-shrink: 0;
    }
    
    .student-details {
      flex: 1;
    }
    
    .student-details h4 {
      margin: 0 0 0.25rem 0;
      font-size: 1rem;
      font-weight: 600;
      color: var(--text-primary);
    }
    
    .student-details p {
      margin: 0 0 0.25rem 0;
      font-size: 0.875rem;
      color: var(--text-secondary);
    }
    
    .payment-meta {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 0.75rem;
      color: var(--text-tertiary);
    }
    
    .payment-meta i {
      color: var(--info-color);
    }
    
    .student-amount {
      text-align: right;
      margin-right: 1rem;
    }
    
    .amount-value {
      font-size: 1.25rem;
      font-weight: 700;
      color: var(--success-color);
      margin-bottom: 0.25rem;
    }
    
    .amount-label {
      font-size: 0.75rem;
      color: var(--text-secondary);
    }
    
    .student-actions {
      display: flex;
      gap: 0.5rem;
      flex-shrink: 0;
    }
    
    .empty-state {
      text-align: center;
      padding: 3rem;
      color: var(--text-secondary);
    }
    
    .empty-state .empty-icon {
      font-size: 3rem;
      margin-bottom: 1rem;
      opacity: 0.5;
    }
    
    .empty-state h4 {
      margin: 0 0 0.5rem 0;
      color: var(--text-primary);
    }
    
    .empty-state p {
      margin: 0 0 1.5rem 0;
    }
  </style>
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
          <a href="<?= base_url('dashboard') ?>" class="app-name-link">
            <h2 class="app-name">ClearPay</h2>
          </a>
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
          <li class="nav-item active">
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
            <a href="#" class="nav-link">
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
            <a href="<?= base_url('profile') ?>" class="nav-link">
              <i class="fas fa-user"></i>
              <span>Profile</span>
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
        <?= $this->include('partials/help_section') ?>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Header -->
      <header class="header">
        <div class="header-left">
          <button class="back-btn" onclick="window.location.href='<?= base_url('contributions') ?>'" style="background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 0.5rem; margin-right: 1rem; color: var(--text-primary); cursor: pointer; transition: var(--transition-fast);">
            <i class="fas fa-arrow-left"></i>
          </button>
          <div>
            <h1><?= esc($contribution['title']) ?></h1>
            <p class="page-subtitle">Payment details and student tracking</p>
          </div>
        </div>
        <div class="header-right">
          <div class="search-container">
            <i class="fas fa-search"></i>
            <input type="text" id="studentSearchInput" class="search-input" placeholder="Search students...">
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
              <?php 
                $user = $usersModel->find(session()->get('user_id'));
                $profilePicture = !empty($user['profile_picture']) ? 
                    base_url('payments/serveUpload/' . $user['profile_picture']) : 
                    session()->get('profile_picture');
              ?>
              <?php if (!empty($user['profile_picture'])): ?>
                <img src="<?= base_url('payments/serveUpload/' . basename($user['profile_picture'])) ?>" alt="Profile Picture" class="user-avatar">
              <?php else: ?>
                <div class="user-avatar">
                  <i class="fas fa-user"></i>
                </div>
              <?php endif; ?>
              <span class="user-name"><?= session()->get('name') ?? session()->get('username') ?? 'Admin' ?></span>
              <i class="fas fa-chevron-down"></i>
            </button>
          </div>
        </div>
      </header>

      <!-- Dashboard Content -->
      <div class="dashboard-content">

        <!-- Success/Error Messages -->
        <div id="successMessage" class="notification-message success" style="display: none;">
          <i class="fas fa-check-circle"></i>
          <span class="message-text"></span>
        </div>
        <div id="errorMessage" class="notification-message error" style="display: none;">
          <i class="fas fa-exclamation-triangle"></i>
          <span class="message-text"></span>
        </div>
        <!-- Contribution Details Card -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">
              <i class="fas fa-info-circle"></i>
              Contribution Details
            </h3>
            <div class="card-actions">
              <button class="btn btn-primary" onclick="window.location.href='<?= base_url('payments?contribution=' . $contribution['id']) ?>'">
                <i class="fas fa-plus"></i>
                Add Payment
              </button>
            </div>
          </div>
          <div class="card-content">
            <div class="contribution-info">
              <div class="contribution-header">
                <h4><?= esc($contribution['title']) ?></h4>
                <div class="status-badge status-<?= $contribution['status'] ?>">
                  <i class="fas fa-<?= $contribution['status'] === 'active' ? 'check-circle' : 'pause-circle' ?>"></i>
                  <?= ucfirst($contribution['status']) ?>
                </div>
              </div>
              <p class="contribution-description"><?= esc($contribution['description']) ?></p>
              
              <div class="contribution-meta">
                <div class="meta-item">
                  <i class="fas fa-clock"></i>
                  <span>Created: <?= date('M d, Y', strtotime($contribution['created_at'])) ?></span>
                </div>
                <div class="meta-item">
                  <i class="fas fa-tag"></i>
                  <span>Category: <?= esc($contribution['category']) ?></span>
                </div>
                <?php if (isset($contribution['updated_at']) && !empty($contribution['updated_at'])): ?>
                <div class="meta-item">
                  <i class="fas fa-edit"></i>
                  <span>Updated: <?= date('M d, Y', strtotime($contribution['updated_at'])) ?></span>
                </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Statistics Cards - Using exact dashboard structure -->
        <div class="stats-grid">
          <div class="stat-card primary">
            <div class="stat-content">
              <div class="stat-header">
                <h3>Target Amount</h3>
                <div class="stat-icon">
                  <i class="fas fa-bullseye"></i>
                </div>
              </div>
              <div class="stat-value">₱<?= number_format($contribution['amount'], 2) ?></div>
              <div class="stat-footer">
                <span class="stat-change neutral">
                  <i class="fas fa-circle"></i>
                  Per student
                </span>
                <span class="stat-period">contribution</span>
              </div>
            </div>
          </div>
          
          <div class="stat-card success">
            <div class="stat-content">
              <div class="stat-header">
                <h3>Total Collected</h3>
                <div class="stat-icon">
                  <i class="fas fa-dollar-sign"></i>
                </div>
              </div>
              <div class="stat-value">₱<?= number_format($stats['total_amount'], 2) ?></div>
              <div class="stat-footer">
                <span class="stat-change positive">
                  <i class="fas fa-arrow-up"></i>
                  Revenue generated
                </span>
                <span class="stat-period">from payments</span>
              </div>
            </div>
          </div>
          
          <div class="stat-card warning">
            <div class="stat-content">
              <div class="stat-header">
                <h3>Remaining</h3>
                <div class="stat-icon">
                  <i class="fas fa-hourglass-half"></i>
                </div>
              </div>
              <div class="stat-value">₱<?= number_format($contribution['amount'] - $stats['total_amount'], 2) ?></div>
              <div class="stat-footer">
                <span class="stat-change neutral">
                  <i class="fas fa-minus"></i>
                  Still needed
                </span>
                <span class="stat-period">to collect</span>
              </div>
            </div>
          </div>
          
          <div class="stat-card info">
            <div class="stat-content">
              <div class="stat-header">
                <h3>Students Paid</h3>
                <div class="stat-icon">
                  <i class="fas fa-users"></i>
                </div>
              </div>
              <div class="stat-value"><?= $stats['total_payments'] ?></div>
              <div class="stat-footer">
                <span class="stat-change positive">
                  <i class="fas fa-check"></i>
                  Active payments
                </span>
                <span class="stat-period">completed</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Students Payment History -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">
              <i class="fas fa-users"></i>
              Students Who Paid
            </h3>
            <div class="card-actions">
              <?php if (count($payments) > 0): ?>
                <div class="search-container">
                  <i class="fas fa-search"></i>
                  <input type="text" 
                         id="studentSearchInput" 
                         placeholder="Search students..." 
                         class="search-input">
                </div>
                <button class="btn btn-secondary" onclick="exportStudentData()">
                  <i class="fas fa-download"></i>
                  Export
                </button>
              <?php endif; ?>
            </div>
          </div>
          
          <div class="card-content">
            <?php if (count($payments) > 0): ?>
              <div class="card-subtitle">
                <span id="searchResults">Showing <?= count($payments) ?> of <?= count($payments) ?> students</span>
              </div>
              
              <div class="students-list" id="studentsList">
                <?php foreach ($payments as $payment): ?>
                  <div class="student-item card" 
                       data-payment-id="<?= $payment['id'] ?? '' ?>" 
                       data-student-name="<?= esc($payment['student_name'] ?? '') ?>" 
                       data-student-id="<?= esc($payment['student_id'] ?? '') ?>"
                       data-contribution-id="<?= $payment['contribution_id'] ?? '' ?>"
                       onclick="showStudentPaymentHistory('<?= $payment['contribution_id'] ?? '' ?>', '<?= esc($payment['student_id'] ?? '') ?>')" 
                       style="cursor: pointer;">
                    
                    <div class="card-content" style="pointer-events: none;">
                      <div class="student-info">
                        <div class="student-avatar">
                          <i class="fas fa-user-graduate"></i>
                        </div>
                        
                        <div class="student-details">
                          <h4 class="student-name"><?= esc($payment['student_name'] ?? 'Unknown Student') ?></h4>
                          <p class="student-id">ID: <?= esc($payment['student_id'] ?? 'N/A') ?></p>
                          <div class="payment-meta">
                            <?php if (isset($payment['payment_count']) && $payment['payment_count'] > 1): ?>
                              <span class="payment-count">
                                <i class="fas fa-repeat"></i>
                                <?= $payment['payment_count'] ?> payments
                              </span>
                            <?php else: ?>
                              <span class="payment-date">
                                <i class="fas fa-calendar-alt"></i>
                                <?= isset($payment['payment_date']) ? date('M j, Y', strtotime($payment['payment_date'])) : 'Date unknown' ?>
                              </span>
                            <?php endif; ?>
                          </div>
                        </div>
                        
                        <div class="student-amount">
                          <div class="amount-value">₱<?= number_format($payment['amount_paid'] ?? 0, 2) ?></div>
                          <div class="amount-label">
                            <?php if (isset($payment['payment_count']) && $payment['payment_count'] > 1): ?>
                              Total paid
                            <?php else: ?>
                              Amount
                            <?php endif; ?>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php else: ?>
              <div class="empty-state" style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                <div class="empty-icon" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;">
                  <i class="fas fa-users-slash"></i>
                </div>
                <h4 style="margin: 0 0 0.5rem 0; color: var(--text-primary);">No Payments Yet</h4>
                <p style="margin: 0 0 1.5rem 0;">No students have made payments for this contribution yet. Payments will appear here once students start paying.</p>
                <button class="btn btn-primary" onclick="window.location.href='<?= base_url('payments?contribution=' . $contribution['id']) ?>'">
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

  <!-- Payment Details Modal -->
  <div id="paymentDetailsModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="modal-container" style="background: var(--bg-primary); border-radius: var(--radius-lg); padding: 0; max-width: 600px; width: 90%; max-height: 80vh; overflow: hidden; box-shadow: var(--shadow-lg);">
      <div class="modal-header" style="padding: 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
        <h3 style="margin: 0; display: flex; align-items: center; gap: 0.5rem;"><i class="fas fa-receipt"></i> Payment History</h3>
        <button onclick="closePaymentModal()" class="btn btn-ghost btn-sm" style="padding: 0.375rem; min-width: auto;">
          <i class="fas fa-times"></i>
        </button>
      </div>
      
      <div class="modal-content" id="paymentDetailsContent" style="padding: 1.5rem; max-height: 60vh; overflow-y: auto;">
        <!-- Payment details will be loaded here -->
      </div>
    </div>
  </div>



  <!-- Include Dashboard JavaScript -->
  <script src="<?= base_url('js/dashboard.js') ?>"></script>
  
  <script>
    // Global functions that need to be accessible from onclick attributes
    function showStudentPaymentHistory(contributionId, studentId) {
      console.log('Function called! ContributionId: ' + contributionId + ', StudentId: ' + studentId);
      
      // Show loading state
      document.getElementById('paymentDetailsContent').innerHTML = `
        <div class="loading-state" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
          <i class="fas fa-spinner fa-spin" style="font-size: 2rem; margin-bottom: 1rem;"></i>
          <p>Loading payment history...</p>
        </div>
      `;
      
      document.getElementById('paymentDetailsModal').style.display = 'flex';
      
      // Fetch payment history via AJAX
      fetch(`<?= base_url('payments/getStudentHistory') ?>/${contributionId}/${studentId}`)
        .then(response => {
          console.log('Response status:', response.status);
          return response.json();
        })
        .then(data => {
          console.log('Response data:', data);
          if (data.success) {
            renderPaymentTimeline(data.payments, data.student);
          } else {
            showErrorInModal(data.message || 'Failed to load payment history');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          showErrorInModal('Failed to load payment history');
        });
    }

    function renderPaymentTimeline(payments, student) {
      console.log('Rendering payment timeline for:', student, 'Payments:', payments);
      
      // Calculate summary data
      const totalPaid = payments.reduce((sum, payment) => sum + parseFloat(payment.amount_paid || 0), 0);
      const contributionAmount = parseFloat('<?= $contribution['amount'] ?? 0 ?>');
      const remainingBalance = contributionAmount - totalPaid;
      
      const timelineHTML = `
        <div class="payment-history-container">
          <div class="student-header" style="margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border-color);">
            <div style="display: flex; align-items: center; gap: 1rem;">
              <div class="student-avatar" style="width: 60px; height: 60px; background: var(--primary-color); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                <i class="fas fa-user-graduate"></i>
              </div>
              <div>
                <h4 style="margin: 0; font-size: 1.2rem; color: var(--text-primary);">${student.name}</h4>
                <p style="margin: 0.25rem 0 0 0; color: var(--text-secondary); font-size: 0.9rem;">ID: ${student.id}</p>
              </div>
            </div>
          </div>
          
          <div class="payment-summary" style="background: #f8f9fa; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
            <div class="summary-details" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
              <div class="total-section">
                <h5 style="margin: 0 0 5px 0; color: #666;">Total Paid:</h5>
                <p class="amount" style="margin: 0; font-size: 1.2em; font-weight: bold; color: #28a745;">₱${totalPaid.toFixed(2)}</p>
              </div>
              <div class="total-section">
                <h5 style="margin: 0 0 5px 0; color: #666;">Remaining Balance:</h5>
                <p class="amount" style="margin: 0; font-size: 1.2em; font-weight: bold; color: ${remainingBalance <= 0 ? '#28a745' : '#dc3545'};">₱${remainingBalance.toFixed(2)}</p>
              </div>
              <div class="total-section">
                <h5 style="margin: 0 0 5px 0; color: #666;">Payment Status:</h5>
                <p class="status-badge ${remainingBalance <= 0 ? 'fully-paid' : 'partial'}" style="margin: 0; padding: 5px 10px; border-radius: 4px; font-size: 0.9em; font-weight: 600; color: white; background: ${remainingBalance <= 0 ? '#28a745' : '#ffc107'};">
                  ${remainingBalance <= 0 ? 'FULLY PAID' : 'PARTIAL PAYMENT'}
                </p>
              </div>
            </div>
          </div>

          <div class="payment-history-list">
            <h5 style="margin: 0 0 15px 0; color: #444;">Payment Transactions (${payments.length})</h5>
            ${payments.map(payment => `
              <div class="payment-record" style="background: white; border: 1px solid #eee; border-radius: 6px; padding: 15px; margin-bottom: 10px;">
                <div class="payment-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                  <div class="payment-date" style="color: #666; font-size: 0.9em;">
                    <i class="fas fa-calendar" style="margin-right: 5px;"></i>
                    ${new Date(payment.payment_date || payment.created_at).toLocaleString()}
                  </div>
                  <div class="payment-amount" style="font-size: 1.2em; font-weight: bold; color: #28a745;">₱${parseFloat(payment.amount_paid || 0).toFixed(2)}</div>
                </div>
                <div class="payment-details" style="display: grid; gap: 8px; font-size: 0.9em;">
                  <div class="detail-item" style="display: flex; gap: 10px;">
                    <label style="color: #666; min-width: 120px;"><strong>Method:</strong></label>
                    <span>${(payment.payment_method || 'Cash').toUpperCase()}</span>
                  </div>
                  <div class="detail-item" style="display: flex; gap: 10px;">
                    <label style="color: #666; min-width: 120px;"><strong>Verification:</strong></label>
                    <span class="verification-code" style="font-family: monospace; background: #f8f9fa; padding: 2px 6px; border-radius: 3px;">${payment.verification_code || 'N/A'}</span>
                  </div>
                  ${payment.reference_number ? `
                    <div class="detail-item" style="display: flex; gap: 10px;">
                      <label style="color: #666; min-width: 120px;"><strong>Reference:</strong></label>
                      <span>${payment.reference_number}</span>
                    </div>
                  ` : ''}
                  ${payment.notes ? `
                    <div class="detail-item" style="display: flex; gap: 10px;">
                      <label style="color: #666; min-width: 120px;"><strong>Notes:</strong></label>
                      <span>${payment.notes}</span>
                    </div>
                  ` : ''}
                  ${payment.qr_receipt_path ? `
                    <div class="qr-section" style="text-align: center; margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 4px;">
                      <img src="<?= base_url() ?>writable/uploads/${payment.qr_receipt_path}" 
                           alt="Payment QR Code" 
                           style="max-width: 150px; height: auto; margin-bottom: 10px; border: 2px solid #ddd; border-radius: 4px;">
                      <br>
                      <button onclick="downloadQR('${payment.qr_receipt_path}')" 
                              style="background: #007bff; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; font-size: 0.875rem;">
                        <i class="fas fa-download"></i> Download QR Receipt
                      </button>
                    </div>
                  ` : `
                    <div class="no-qr-section" style="text-align: center; margin-top: 10px; padding: 10px; background: #fff3cd; border-radius: 4px; color: #856404;">
                      <i class="fas fa-info-circle"></i> No QR receipt available for this payment
                    </div>
                  `}
                </div>
              </div>
            `).join('')}
          </div>
        </div>
      `;
      
      document.getElementById('paymentDetailsContent').innerHTML = timelineHTML;
    }

    function showErrorInModal(message) {
      document.getElementById('paymentDetailsContent').innerHTML = `
        <div class="error-state" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
          <i class="fas fa-exclamation-triangle" style="font-size: 2rem; margin-bottom: 1rem; color: var(--error-color);"></i>
          <h4 style="margin: 0 0 0.5rem 0; color: var(--text-primary);">Error Loading Data</h4>
          <p style="margin: 0;">${message}</p>
        </div>
      `;
    }

    function testFunction() {
      alert('Test function works! JavaScript is working.');
      console.log('Test function called');
    }

    function exportStudentData() {
      alert('Export functionality will be implemented soon!');
    }

    function closePaymentModal() {
      document.getElementById('paymentDetailsModal').style.display = 'none';
    }

    // Helper function to download QR code
    function downloadQR(qrPath) {
      if (!qrPath) {
        console.error('No QR path provided');
        return;
      }

      const filename = `payment_qr_${Date.now()}.png`;
      const downloadUrl = `<?= base_url() ?>writable/uploads/${qrPath}`;
      
      // Create download link
      const link = document.createElement('a');
      link.href = downloadUrl;
      link.download = filename;
      link.target = '_blank';
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    }

    // Search functionality
    document.addEventListener('DOMContentLoaded', function() {
      const searchInput = document.getElementById('studentSearchInput');
      const studentsList = document.getElementById('studentsList');
      const searchResults = document.getElementById('searchResults');
      
      if (searchInput && studentsList) {
        const allStudents = studentsList.querySelectorAll('.student-item');
        const totalStudents = allStudents.length;
        
        searchInput.addEventListener('input', function() {
          const searchTerm = this.value.toLowerCase().trim();
          let visibleCount = 0;
          
          allStudents.forEach(student => {
            const studentName = student.getAttribute('data-student-name');
            const studentId = student.getAttribute('data-student-id');
            
            if (studentName.includes(searchTerm) || studentId.includes(searchTerm)) {
              student.style.display = 'block';
              visibleCount++;
            } else {
              student.style.display = 'none';
            }
          });
          
          if (searchResults) {
            searchResults.textContent = `Showing ${visibleCount} of ${totalStudents} students`;
          }
        });
      }
    });

    // Close modals when clicking outside
    document.addEventListener('click', function(e) {
      if (e.target.classList.contains('modal-overlay')) {
        e.target.style.display = 'none';
      }
    });
  </script>

</body>
</html>
