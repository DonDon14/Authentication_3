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
        </ul>
        
        <div class="nav-divider"></div>
        
        <ul class="nav-list">
          <li class="nav-item">
            <a href="<?= base_url('profile') ?>" class="nav-link">
              <i class="fas fa-user-cog"></i>
              <span>Settings</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= base_url('auth/logout') ?>" class="nav-link">
              <i class="fas fa-sign-out-alt"></i>
              <span>Logout</span>
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
                       data-student-name="<?= strtolower(esc($payment['student_name'] ?? '')) ?>" 
                       data-student-id="<?= strtolower(esc($payment['student_id'] ?? '')) ?>"
                       onclick="showStudentPaymentHistory('<?= $payment['contribution_id'] ?? '' ?>', '<?= esc($payment['student_id'] ?? '') ?>')">
                    
                    <div class="card-content">
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
                        
                        <div class="student-actions">
                          <button class="btn btn-ghost btn-sm" title="View payment history">
                            <i class="fas fa-eye"></i>
                          </button>
                          <?php if (isset($payment['qr_code']) && !empty($payment['qr_code'])): ?>
                          <button class="btn btn-ghost btn-sm" title="View QR receipt" onclick="event.stopPropagation(); showQRCode('<?= $payment['qr_code'] ?? '' ?>')">
                            <i class="fas fa-qrcode"></i>
                          </button>
                          <?php endif; ?>
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

  <!-- QR Code Modal -->
  <div id="qrModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="modal-container" style="background: var(--bg-primary); border-radius: var(--radius-lg); padding: 0; max-width: 400px; width: 90%; box-shadow: var(--shadow-lg);">
      <div class="modal-header" style="padding: 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
        <h3 style="margin: 0; display: flex; align-items: center; gap: 0.5rem;"><i class="fas fa-qrcode"></i> Payment Receipt</h3>
        <button onclick="closeQRModal()" class="btn btn-ghost btn-sm" style="padding: 0.375rem; min-width: auto;">
          <i class="fas fa-times"></i>
        </button>
      </div>
      
      <div class="modal-content" style="padding: 1.5rem; text-align: center;">
        <div class="qr-display">
          <div id="qrCodeDisplay" style="margin-bottom: 1rem;"></div>
          <p style="margin: 0; color: var(--text-secondary);">Scan this QR code to verify the payment</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Include Dashboard JavaScript -->
  <script src="<?= base_url('js/dashboard.js') ?>"></script>
  
  <script>
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

    function showStudentPaymentHistory(contributionId, studentId) {
      // Show loading state
      document.getElementById('paymentDetailsContent').innerHTML = `
        <div class="loading-state" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
          <i class="fas fa-spinner fa-spin" style="font-size: 2rem; margin-bottom: 1rem;"></i>
          <p>Loading payment history...</p>
        </div>
      `;
      
      document.getElementById('paymentDetailsModal').style.display = 'flex';
      
      // Simulate API call - replace with actual implementation
      setTimeout(() => {
        document.getElementById('paymentDetailsContent').innerHTML = `
          <div class="payment-summary-card" style="border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1rem;">
            <h4 style="margin: 0 0 0.5rem 0;">Student Payment Summary</h4>
            <p style="margin: 0 0 1rem 0; color: var(--text-secondary);">Payment history for Student ID: ${studentId}</p>
            <div class="payment-list">
              <div class="payment-item" style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid var(--border-color);">
                <span>Latest Payment</span>
                <span style="font-weight: 600;">₱<?= number_format($contribution['amount'], 2) ?></span>
              </div>
            </div>
          </div>
        `;
      }, 1000);
    }

    function closePaymentModal() {
      document.getElementById('paymentDetailsModal').style.display = 'none';
    }

    function showQRCode(qrCode) {
      document.getElementById('qrCodeDisplay').innerHTML = `<img src="${qrCode}" alt="QR Code" style="max-width: 100%; border-radius: 8px;">`;
      document.getElementById('qrModal').style.display = 'flex';
    }

    function closeQRModal() {
      document.getElementById('qrModal').style.display = 'none';
    }

    function exportStudentData() {
      alert('Export functionality will be implemented soon!');
    }

    // Close modals when clicking outside
    document.addEventListener('click', function(e) {
      if (e.target.classList.contains('modal-overlay')) {
        e.target.style.display = 'none';
      }
    });
  </script>

</body>
</html>