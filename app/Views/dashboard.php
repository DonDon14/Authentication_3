<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ClearPay Dashboard - Admin Portal</title>
  <link rel="stylesheet" href="<?= base_url('css/dashboard.css') ?>">
  <link rel="stylesheet" href="<?= base_url('css/header-components.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <style>
    /* Profile avatar styles for dashboard */
    .profile-avatar {
      position: relative;
      overflow: hidden;
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
    }
    
    .user-avatar img {
      width: 100% !important;
      height: 100% !important;
      object-fit: cover !important;
      border-radius: 50%;
    }
  </style>
</head>
<body>
  <!-- Main App Container -->
  <div class="app-container">
    
    <!-- Sidebar Navigation -->
    <?= $this->include('partials/sidebar') ?>

    <!-- Main Content Area -->
    <main class="main-content">
      
      <!-- Top Header Bar -->
      <header class="header">
        <div class="header-left">
          <h1 class="page-title">Dashboard</h1>
          <p class="page-subtitle">Welcome back, <?= esc($name) ?>! Here's your overview.</p>
        </div>

        <div class="header-right">
          <?= $this->include('partials/header_components') ?>
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
                    <div class="payment-item clickable" onclick="viewPaymentDetails(<?= htmlspecialchars(json_encode($payment)) ?>)" style="cursor: pointer; transition: all 0.2s ease;">
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
                      <div class="payment-actions" onclick="event.stopPropagation();">
                        <?php if ($payment['qr_receipt_path']): ?>
                          <button class="btn-icon" onclick="downloadQRReceipt(<?= $payment['id'] ?>)" title="Download QR Receipt">
                            <i class="fas fa-qrcode"></i>
                          </button>
                        <?php endif; ?>
                        <button class="btn-icon" onclick="window.location.href='<?= base_url('students/details/') ?><?= urlencode($payment['student_id']) ?>'" title="View Student Details">
                          <i class="fas fa-user-circle"></i>
                        </button>
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
  <script>
    // Initialize dashboard functionality
    document.addEventListener('DOMContentLoaded', function() {
      initializeDashboard();
    });

    function initializeDashboard() {
      // Auto-refresh recent payments every 30 seconds
      setInterval(refreshPayments, 30000);

      // Initialize verification functionality
      if (typeof initializeVerifyButton === 'function') {
        initializeVerifyButton();
      }
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

    // Payment interaction functions
    function viewPaymentDetails(paymentData) {
      // Create modal to show payment details similar to the contribution details modal
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
              <h3 style="margin: 0; color: var(--text-primary);"><i class="fas fa-receipt"></i> Payment History</h3>
              <p style="margin: 0.5rem 0 0 0; color: var(--text-secondary);">Payment details and QR receipt</p>
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
                <h4 style="margin: 0; font-size: 1.25rem; color: var(--text-primary);">${paymentData.student_name || 'Unknown Student'}</h4>
                <p style="margin: 0.25rem 0; color: var(--text-secondary);">ID: ${paymentData.student_id || 'N/A'}</p>
              </div>
            </div>

            <!-- Payment Summary -->
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
              <div style="text-align: center; padding: 1rem; background: var(--success-light, #f0fff4); border-radius: var(--radius-md); border: 1px solid var(--success-color, #38a169);">
                <div style="color: var(--success-color, #38a169); font-size: 1.1rem; font-weight: 700; margin-bottom: 0.25rem;">₱${parseFloat(paymentData.amount_paid || 0).toFixed(2)}</div>
                <div style="color: var(--success-color, #38a169); font-size: 0.8rem; text-transform: uppercase;">Total Paid</div>
              </div>
              <div style="text-align: center; padding: 1rem; background: var(--info-light, #ebf8ff); border-radius: var(--radius-md); border: 1px solid var(--info-color, #3182ce);">
                <div style="color: var(--info-color, #3182ce); font-size: 1.1rem; font-weight: 700; margin-bottom: 0.25rem;">₱0.00</div>
                <div style="color: var(--info-color, #3182ce); font-size: 0.8rem; text-transform: uppercase;">Remaining</div>
              </div>
              <div style="text-align: center; padding: 1rem; background: var(--warning-light, #fffbeb); border-radius: var(--radius-md); border: 1px solid var(--warning-color, #d69e2e);">
                <div style="color: var(--warning-color, #d69e2e); font-size: 1.1rem; font-weight: 700; margin-bottom: 0.25rem; text-transform: uppercase;">${paymentData.payment_status || 'Unknown'}</div>
                <div style="color: var(--warning-color, #d69e2e); font-size: 0.8rem; text-transform: uppercase;">Status</div>
              </div>
            </div>

            <!-- Payment Details -->
            <div style="background: var(--bg-primary); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.5rem; margin-bottom: 1.5rem;">
              <h5 style="margin-bottom: 1rem; color: var(--text-primary); font-size: 1rem; font-weight: 600;">Payment Transactions (1)</h5>
              
              <div style="border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1rem; background: var(--bg-secondary);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                  <span style="font-weight: 600; color: var(--text-primary);">${new Date(paymentData.payment_date || paymentData.created_at).toLocaleDateString('en-US', { year: 'numeric', month: '2-digit', day: '2-digit' })}, ${new Date(paymentData.payment_date || paymentData.created_at).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true })}</span>
                  <span style="font-weight: 700; color: var(--success-color); font-size: 1.1rem;">₱${parseFloat(paymentData.amount_paid || 0).toFixed(2)}</span>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; font-size: 0.9rem;">
                  <div><strong>Method:</strong> ${(paymentData.payment_method || 'cash').toUpperCase()}</div>
                  <div><strong>Verification:</strong> ${paymentData.reference_number || 'VERIFIED'}</div>
                </div>
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

      // Generate QR code if QR library is available
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
          border: 1px solid #dee2e6;
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
        // Payment doesn't have QR code - generate new one or show fallback
        const qrData = {
          payment_id: paymentData.id,
          student_id: paymentData.student_id,
          student_name: paymentData.student_name,
          amount: paymentData.amount_paid,
          date: paymentData.payment_date || paymentData.created_at,
          status: paymentData.payment_status,
          reference: paymentData.reference_number || 'VERIFIED'
        };

        // If QRCode library is available, generate QR code
        if (typeof QRCode !== 'undefined') {
          const canvas = document.createElement('canvas');
          qrContainer.appendChild(canvas);
          
          QRCode.toCanvas(canvas, JSON.stringify(qrData), {
            width: 150,
            height: 150,
            margin: 2,
            color: {
              dark: '#000000',
              light: '#FFFFFF'
            }
          }, function (error) {
            if (error) {
              console.error('QR Code generation error:', error);
              qrContainer.innerHTML = `
                <div style="width: 150px; height: 150px; background: #f8f9fa; display: flex; flex-direction: column; align-items: center; justify-content: center; border: 2px dashed #dee2e6; border-radius: 8px;">
                  <i class="fas fa-exclamation-triangle" style="font-size: 2rem; color: #dc3545; margin-bottom: 0.5rem;"></i>
                  <span style="font-size: 0.8rem; color: #6c757d; text-align: center;">QR Generation Failed</span>
                </div>
              `;
            }
          });
        } else {
          // Fallback if QR library not available and no existing QR
          qrContainer.innerHTML = `
            <div style="width: 150px; height: 150px; background: #f8f9fa; display: flex; flex-direction: column; align-items: center; justify-content: center; border: 2px dashed #dee2e6; border-radius: 8px;">
              <i class="fas fa-qrcode" style="font-size: 3rem; color: #6c757d; margin-bottom: 0.5rem;"></i>
              <span style="font-size: 0.8rem; color: #6c757d; text-align: center;">Payment #${paymentData.id}</span>
            </div>
          `;
        }
      }
    }

    function refreshPayments() {
      // Show loading state
      const paymentsContainer = document.querySelector('.payments-list');
      if (paymentsContainer) {
        paymentsContainer.innerHTML = `
          <div class="loading-state" style="text-align: center; padding: 2rem;">
            <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: var(--primary-color);"></i>
            <p style="margin-top: 1rem; color: var(--text-secondary);">Refreshing payments...</p>
          </div>
        `;
      }
      
      // Reload the page to refresh data
      setTimeout(() => {
        window.location.reload();
      }, 1000);
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
  <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
  
  <!-- Dashboard JavaScript -->
  <!-- JavaScript Dependencies -->
  <script src="<?= base_url('js/header-components.js') ?>"></script>
  <script src="<?= base_url('js/main.js') ?>"></script>
  <script src="<?= base_url('js/dashboard.js') ?>"></script>
  <script src="<?= base_url('js/verification-functions.js') ?>"></script>
  
  <script>
    // Listen for profile picture updates from other pages
    window.addEventListener('storage', function(e) {
      if (e.key === 'profilePictureUpdated') {
        // Update profile picture in sidebar
        const sidebarAvatar = document.querySelector('.sidebar-footer .profile-avatar');
        if (sidebarAvatar && e.newValue) {
          sidebarAvatar.innerHTML = `<img src="${e.newValue}" alt="Profile Picture">`;
        }
        
        // Update profile picture in header
        const headerAvatar = document.querySelector('.user-menu .user-avatar');
        if (headerAvatar && e.newValue) {
          headerAvatar.innerHTML = `<img src="${e.newValue}" alt="Profile Picture">`;
        }
      }
    });
  </script>
  
</body>
</html>
