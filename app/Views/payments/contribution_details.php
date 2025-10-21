<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($contribution['title']) ?> - Payment Details</title>
  <link rel="stylesheet" href="<?= base_url('css/contribution_details_modern.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
  <!-- Main Dashboard Layout -->
  <div class="dashboard-layout">
    <!-- Sidebar Navigation -->
    <aside class="sidebar">
      <div class="sidebar-header">
        <div class="logo">
          <i class="fas fa-graduation-cap"></i>
          <span>ClearPay</span>
        </div>
      </div>
      
      <nav class="sidebar-nav">
        <a href="<?= base_url('dashboard') ?>" class="nav-item">
          <i class="fas fa-home"></i>
          <span>Dashboard</span>
        </a>
        <a href="<?= base_url('contributions') ?>" class="nav-item active">
          <i class="fas fa-hand-holding-usd"></i>
          <span>Contributions</span>
        </a>
        <a href="<?= base_url('payments') ?>" class="nav-item">
          <i class="fas fa-credit-card"></i>
          <span>Payments</span>
        </a>
        <a href="<?= base_url('profile') ?>" class="nav-item">
          <i class="fas fa-user"></i>
          <span>Profile</span>
        </a>
      </nav>
      
      <div class="sidebar-footer">
        <div class="user-info">
          <div class="user-avatar">
            <i class="fas fa-user-shield"></i>
          </div>
          <div class="user-details">
            <div class="user-name">Administrator</div>
            <div class="user-role">System Admin</div>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Header -->
      <header class="page-header">
        <div class="header-left">
          <button class="back-btn" onclick="window.location.href='<?= base_url('contributions') ?>'">
            <i class="fas fa-arrow-left"></i>
          </button>
          <div class="page-title-section">
            <h1><?= esc($contribution['title']) ?></h1>
            <p>Detailed payment tracking and history</p>
          </div>
        </div>
        <div class="header-right">
          <button class="btn-primary" onclick="window.location.href='<?= base_url('payments?contribution=' . $contribution['id']) ?>'">
            <i class="fas fa-plus"></i>
            Add Payment
          </button>
        </div>
      </header>

      <!-- Content Area -->
      <div class="content-wrapper">
        
        <!-- Contribution Overview Card -->
        <div class="dashboard-card">
          <div class="card-header">
            <div>
              <h3><i class="fas fa-info-circle"></i> Contribution Overview</h3>
              <p>Summary and details of this contribution type</p>
            </div>
            <div class="contribution-status">
              <span class="status status-<?= $contribution['status'] === 'active' ? 'verified' : 'pending' ?>">
                <i class="fas fa-<?= $contribution['status'] === 'active' ? 'check-circle' : 'pause-circle' ?>"></i>
                <?= ucfirst($contribution['status']) ?>
              </span>
            </div>
          </div>
          <div class="card-content">
            <div class="contribution-overview">
              <div class="overview-main">
                <div class="contribution-details">
                  <h4><?= esc($contribution['title']) ?></h4>
                  <p class="description"><?= esc($contribution['description']) ?></p>
                  <div class="contribution-meta">
                    <div class="meta-item">
                      <span class="meta-label">Amount per payment:</span>
                      <span class="meta-value amount">$<?= number_format($contribution['amount'], 2) ?></span>
                    </div>
                    <div class="meta-item">
                      <span class="meta-label">Category:</span>
                      <span class="meta-value category"><?= esc($contribution['category']) ?></span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Statistics Grid -->
        <div class="stats-section">
          <h3 class="section-title"><i class="fas fa-chart-bar"></i> Payment Statistics</h3>
          <div class="stats-grid">
            <div class="stat-card primary">
              <div class="stat-icon">
                <i class="fas fa-users"></i>
              </div>
              <div class="stat-content">
                <div class="stat-number"><?= $stats['total_payments'] ?></div>
                <div class="stat-label">Students Paid</div>
                <div class="stat-change positive">
                  <i class="fas fa-arrow-up"></i>
                  Active payments
                </div>
              </div>
            </div>
            
            <div class="stat-card success">
              <div class="stat-icon">
                <i class="fas fa-dollar-sign"></i>
              </div>
              <div class="stat-content">
                <div class="stat-number">₱<?= number_format($stats['total_amount'], 2) ?></div>
                <div class="stat-label">Total Collected</div>
                <div class="stat-change positive">
                  <i class="fas fa-arrow-up"></i>
                  Revenue generated
                </div>
              </div>
            </div>
            
            <div class="stat-card info">
              <div class="stat-icon">
                <i class="fas fa-chart-line"></i>
              </div>
              <div class="stat-content">
                <div class="stat-number">₱<?= number_format($stats['average_amount'], 2) ?></div>
                <div class="stat-label">Average Payment</div>
                <div class="stat-change neutral">
                  <i class="fas fa-equals"></i>
                  Per transaction
                </div>
              </div>
            </div>
            
            <div class="stat-card warning">
              <div class="stat-icon">
                <i class="fas fa-percentage"></i>
              </div>
              <div class="stat-content">
                <div class="stat-number"><?= $stats['total_payments'] > 0 ? number_format(($stats['total_amount'] / ($contribution['amount'] * $stats['total_payments'])) * 100, 1) : '0' ?>%</div>
                <div class="stat-label">Payment Rate</div>
                <div class="stat-change positive">
                  <i class="fas fa-chart-pie"></i>
                  Completion rate
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Students Payment History -->
        <div class="dashboard-card">
          <div class="card-header">
            <div>
              <h3><i class="fas fa-users"></i> Students Who Paid</h3>
              <p>
                <?php if (count($payments) > 0): ?>
                  <?= count($payments) ?> student<?= count($payments) > 1 ? 's have' : ' has' ?> paid for this contribution
                <?php else: ?>
                  No students have paid for this contribution yet
                <?php endif; ?>
              </p>
            </div>
            
            <?php if (count($payments) > 0): ?>
            <div class="card-actions">
              <div class="search-container">
                <input type="text" 
                       id="studentSearchInput" 
                       placeholder="Search students..." 
                       class="search-input">
                <i class="fas fa-search search-icon"></i>
              </div>
              <button class="btn-secondary" onclick="exportStudentData()">
                <i class="fas fa-download"></i>
                Export
              </button>
            </div>
            <?php endif; ?>
          </div>
          
          <div class="card-content">
            <?php if (count($payments) > 0): ?>
              <div class="search-stats">
                <span id="searchResults">Showing <?= count($payments) ?> of <?= count($payments) ?> students</span>
              </div>
              
              <div class="students-grid" id="studentsList">
                <?php foreach ($payments as $payment): ?>
                  <div class="student-card" 
                       data-payment-id="<?= $payment['id'] ?? '' ?>" 
                       data-student-name="<?= strtolower(esc($payment['student_name'] ?? '')) ?>" 
                       data-student-id="<?= strtolower(esc($payment['student_id'] ?? '')) ?>"
                       onclick="showStudentPaymentHistory('<?= $payment['contribution_id'] ?? '' ?>', '<?= esc($payment['student_id'] ?? '') ?>')">>
                    
                    <div class="student-card-content">
                      <div class="student-avatar">
                        <i class="fas fa-user-graduate"></i>
                      </div>
                      
                      <div class="student-info">
                        <h4 class="student-name"><?= esc($payment['student_name'] ?? 'Unknown Student') ?></h4>
                        <p class="student-id">ID: <?= esc($payment['student_id'] ?? 'N/A') ?></p>
                        <div class="payment-info">
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
                        <div class="amount-display">₱<?= number_format($payment['amount_paid'] ?? 0, 2) ?></div>
                        <div class="amount-label">
                          <?php if (isset($payment['payment_count']) && $payment['payment_count'] > 1): ?>
                            Total paid
                          <?php else: ?>
                            Amount
                          <?php endif; ?>
                        </div>
                      </div>
                      
                      <div class="student-actions">
                        <button class="action-btn view-btn" title="View payment history">
                          <i class="fas fa-eye"></i>
                        </button>
                        <?php if (isset($payment['qr_code']) && !empty($payment['qr_code'])): ?>
                        <button class="action-btn qr-btn" title="View QR receipt" onclick="event.stopPropagation(); showQRCode('<?= $payment['qr_code'] ?? '' ?>')">>
                          <i class="fas fa-qrcode"></i>
                        </button>
                        <?php endif; ?>
                      </div>
                    </div>
                    
                    <div class="card-hover-effect"></div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php else: ?>
              <div class="empty-state">
                <div class="empty-icon">
                  <i class="fas fa-users-slash"></i>
                </div>
                <h4>No Payments Yet</h4>
                <p>No students have made payments for this contribution yet. Payments will appear here once students start paying.</p>
                <button class="btn-primary" onclick="window.location.href='<?= base_url('payments?contribution=' . $contribution['id']) ?>'">
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
  <!-- Payment Details Modal -->
  <div id="paymentDetailsModal" class="modal-overlay" style="display: none;">
    <div class="modal-container">
      <div class="modal-header">
        <h3><i class="fas fa-receipt"></i> Payment History</h3>
        <button onclick="closePaymentModal()" class="close-btn">
          <i class="fas fa-times"></i>
        </button>
      </div>
      
      <div class="modal-content" id="paymentDetailsContent">
        <!-- Payment details will be loaded here -->
      </div>
    </div>
  </div>

  <!-- QR Code Modal -->
  <div id="qrModal" class="modal-overlay" style="display: none;">
    <div class="modal-container qr-modal">
      <div class="modal-header">
        <h3><i class="fas fa-qrcode"></i> Payment Receipt</h3>
        <button onclick="closeQRModal()" class="close-btn">
          <i class="fas fa-times"></i>
        </button>
      </div>
      
      <div class="modal-content">
        <div class="qr-display">
          <div id="qrCodeDisplay"></div>
          <p>Scan this QR code to verify the payment</p>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Search functionality
    document.addEventListener('DOMContentLoaded', function() {
      const searchInput = document.getElementById('studentSearchInput');
      const studentsList = document.getElementById('studentsList');
      const searchResults = document.getElementById('searchResults');
      
      if (searchInput && studentsList) {
        const allStudents = studentsList.querySelectorAll('.student-card');
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
        <div class="loading-state">
          <i class="fas fa-spinner fa-spin"></i>
          <p>Loading payment history...</p>
        </div>
      `;
      
      document.getElementById('paymentDetailsModal').style.display = 'flex';
      
      // Simulate API call - replace with actual implementation
      setTimeout(() => {
        document.getElementById('paymentDetailsContent').innerHTML = `
          <div class="payment-summary-card">
            <h4>Student Payment Summary</h4>
            <p>Payment history for Student ID: ${studentId}</p>
            <div class="payment-list">
              <div class="payment-item">
                <span>Latest Payment</span>
                <span>₱<?= number_format($contribution['amount'], 2) ?></span>
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
</html>