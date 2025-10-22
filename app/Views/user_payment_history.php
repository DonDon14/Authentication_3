<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment History - ClearPay</title>
  <link rel="stylesheet" href="<?= base_url('css/user_dashboard.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .page-header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 2rem 1rem;
      margin-bottom: 2rem;
      border-radius: 0 0 20px 20px;
    }

    .page-header h1 {
      margin: 0 0 0.5rem 0;
      font-size: 1.75rem;
      font-weight: 700;
    }

    .page-header p {
      margin: 0;
      opacity: 0.9;
    }

    .back-button {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      background: rgba(255, 255, 255, 0.2);
      color: white;
      padding: 0.5rem 1rem;
      border-radius: 8px;
      text-decoration: none;
      font-size: 0.875rem;
      margin-bottom: 1rem;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .back-button:hover {
      background: rgba(255, 255, 255, 0.3);
      color: white;
      text-decoration: none;
    }

    .history-container {
      padding: 0 1rem 2rem 1rem;
    }

    .filter-section {
      background: white;
      border-radius: 12px;
      padding: 1.5rem;
      margin-bottom: 2rem;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .filter-row {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
      margin-bottom: 1rem;
    }

    .filter-group {
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
    }

    .filter-group label {
      font-weight: 600;
      color: #495057;
      font-size: 0.875rem;
    }

    .filter-input {
      padding: 0.75rem;
      border: 2px solid #e9ecef;
      border-radius: 8px;
      font-size: 0.875rem;
    }

    .filter-input:focus {
      outline: none;
      border-color: #667eea;
    }

    .payment-item {
      background: white;
      border-radius: 12px;
      padding: 1.5rem;
      margin-bottom: 1rem;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      border: 1px solid #e9ecef;
    }

    .payment-item:hover {
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
      transform: translateY(-2px);
      transition: all 0.3s ease;
    }

    .payment-header {
      display: flex;
      justify-content: between;
      align-items: flex-start;
      margin-bottom: 1rem;
    }

    .payment-title {
      font-size: 1.125rem;
      font-weight: 600;
      color: #212529;
      margin: 0 0 0.25rem 0;
    }

    .payment-subtitle {
      color: #6c757d;
      font-size: 0.875rem;
      margin: 0;
    }

    .payment-amount {
      font-size: 1.25rem;
      font-weight: 700;
      color: #28a745;
    }

    .payment-details {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
      gap: 1rem;
      margin-bottom: 1rem;
    }

    .detail-item {
      display: flex;
      flex-direction: column;
      gap: 0.25rem;
    }

    .detail-label {
      font-size: 0.75rem;
      color: #6c757d;
      text-transform: uppercase;
      font-weight: 600;
      letter-spacing: 0.5px;
    }

    .detail-value {
      font-size: 0.875rem;
      font-weight: 500;
      color: #495057;
    }

    .status-badge {
      display: inline-flex;
      align-items: center;
      padding: 0.375rem 0.75rem;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .status-completed {
      background: #d4edda;
      color: #155724;
    }

    .status-pending {
      background: #fff3cd;
      color: #856404;
    }

    .status-failed {
      background: #f8d7da;
      color: #721c24;
    }

    .no-payments {
      text-align: center;
      padding: 3rem 1rem;
      color: #6c757d;
    }

    .no-payments i {
      font-size: 3rem;
      margin-bottom: 1rem;
      opacity: 0.5;
    }

    .summary-stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
      margin-bottom: 2rem;
    }

    .stat-card {
      background: white;
      border-radius: 12px;
      padding: 1.5rem;
      text-align: center;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      border: 1px solid #e9ecef;
    }

    .stat-value {
      font-size: 1.5rem;
      font-weight: 700;
      color: #667eea;
      margin-bottom: 0.5rem;
    }

    .stat-label {
      font-size: 0.875rem;
      color: #6c757d;
      text-transform: uppercase;
      font-weight: 600;
    }

    @media (max-width: 768px) {
      .payment-header {
        flex-direction: column;
        gap: 0.5rem;
      }

      .payment-details {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <div class="dashboard-container">
    <!-- Page Header -->
    <div class="page-header">
      <a href="<?= base_url('user/dashboard') ?>" class="back-button">
        <i class="fas fa-arrow-left"></i>
        Back to Dashboard
      </a>
      <h1><i class="fas fa-history"></i> Payment History</h1>
      <p>View all your payment transactions and receipts</p>
    </div>

    <!-- Main Content -->
    <div class="history-container">
      <!-- Summary Statistics -->
      <div class="summary-stats">
        <div class="stat-card">
          <div class="stat-value">₱<?= number_format($stats['total_paid'], 2) ?></div>
          <div class="stat-label">Total Paid</div>
        </div>
        <div class="stat-card">
          <div class="stat-value"><?= $stats['total_payments'] ?></div>
          <div class="stat-label">Total Payments</div>
        </div>
        <div class="stat-card">
          <div class="stat-value"><?= $stats['completed_payments'] ?></div>
          <div class="stat-label">Completed</div>
        </div>
        <div class="stat-card">
          <div class="stat-value"><?= $stats['pending_payments'] ?></div>
          <div class="stat-label">Pending</div>
        </div>
      </div>

      <!-- Filter Section -->
      <div class="filter-section">
        <div class="filter-row">
          <div class="filter-group">
            <label for="searchFilter">Search Payments</label>
            <input type="text" id="searchFilter" class="filter-input" placeholder="Search by contribution title...">
          </div>
          <div class="filter-group">
            <label for="statusFilter">Payment Status</label>
            <select id="statusFilter" class="filter-input">
              <option value="">All Statuses</option>
              <option value="completed">Completed</option>
              <option value="pending">Pending</option>
              <option value="failed">Failed</option>
              <option value="verified">Verified</option>
            </select>
          </div>
          <div class="filter-group">
            <label for="dateFilter">Date Range</label>
            <select id="dateFilter" class="filter-input">
              <option value="">All Time</option>
              <option value="today">Today</option>
              <option value="week">This Week</option>
              <option value="month">This Month</option>
              <option value="year">This Year</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Payments List -->
      <div class="payments-list" id="paymentsList">
        <?php if (!empty($payments)): ?>
          <?php foreach ($payments as $payment): ?>
            <div class="payment-item" data-payment='<?= json_encode($payment) ?>'>
              <div class="payment-header">
                <div>
                  <h3 class="payment-title"><?= esc($payment['contribution_title'] ?? 'General Payment') ?></h3>
                  <p class="payment-subtitle">Payment ID: #<?= str_pad($payment['id'], 6, '0', STR_PAD_LEFT) ?></p>
                </div>
                <div class="payment-amount">₱<?= number_format($payment['amount_paid'], 2) ?></div>
              </div>

              <div class="payment-details">
                <div class="detail-item">
                  <span class="detail-label">Date & Time</span>
                  <span class="detail-value">
                    <i class="fas fa-calendar"></i>
                    <?= date('M j, Y - g:i A', strtotime($payment['payment_date'])) ?>
                  </span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Method</span>
                  <span class="detail-value">
                    <i class="fas fa-credit-card"></i>
                    <?= ucfirst(str_replace('_', ' ', $payment['payment_method'])) ?>
                  </span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Status</span>
                  <span class="status-badge status-<?= $payment['payment_status'] ?>">
                    <i class="fas fa-<?= $payment['payment_status'] === 'completed' ? 'check-circle' : ($payment['payment_status'] === 'pending' ? 'clock' : 'times-circle') ?>"></i>
                    <?= ucfirst($payment['payment_status']) ?>
                  </span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Reference</span>
                  <span class="detail-value"><?= esc($payment['reference_number'] ?? 'N/A') ?></span>
                </div>
              </div>

              <?php if (!empty($payment['notes'])): ?>
                <div class="payment-notes">
                  <span class="detail-label">Notes</span>
                  <p class="detail-value"><?= esc($payment['notes']) ?></p>
                </div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="no-payments">
            <i class="fas fa-receipt"></i>
            <h3>No Payment History</h3>
            <p>You haven't made any payments yet. Your payment history will appear here.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Bottom Navigation -->
    <nav class="bottom-nav">
      <a href="<?= base_url('user/dashboard') ?>" class="nav-item">
        <i class="fas fa-home"></i>
        <span>Home</span>
      </a>
      <a href="<?= base_url('user/payment-history') ?>" class="nav-item active">
        <i class="fas fa-history"></i>
        <span>History</span>
      </a>
      <a href="<?= base_url('announcements/student-view') ?>" class="nav-item">
        <i class="fas fa-bullhorn"></i>
        <span>News</span>
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

  <script>
    // Filter functionality
    document.addEventListener('DOMContentLoaded', function() {
      const searchFilter = document.getElementById('searchFilter');
      const statusFilter = document.getElementById('statusFilter');
      const dateFilter = document.getElementById('dateFilter');
      const paymentItems = document.querySelectorAll('.payment-item');

      function filterPayments() {
        const searchTerm = searchFilter.value.toLowerCase();
        const statusValue = statusFilter.value;
        const dateValue = dateFilter.value;

        paymentItems.forEach(item => {
          const paymentData = JSON.parse(item.dataset.payment);
          const title = (paymentData.contribution_title || 'General Payment').toLowerCase();
          const status = paymentData.payment_status;
          const paymentDate = new Date(paymentData.payment_date);
          
          let showItem = true;

          // Search filter
          if (searchTerm && !title.includes(searchTerm)) {
            showItem = false;
          }

          // Status filter
          if (statusValue && status !== statusValue) {
            showItem = false;
          }

          // Date filter
          if (dateValue) {
            const now = new Date();
            const startOfDay = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            const startOfWeek = new Date(startOfDay.getTime() - (startOfDay.getDay() * 24 * 60 * 60 * 1000));
            const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);
            const startOfYear = new Date(now.getFullYear(), 0, 1);

            switch (dateValue) {
              case 'today':
                if (paymentDate < startOfDay) showItem = false;
                break;
              case 'week':
                if (paymentDate < startOfWeek) showItem = false;
                break;
              case 'month':
                if (paymentDate < startOfMonth) showItem = false;
                break;
              case 'year':
                if (paymentDate < startOfYear) showItem = false;
                break;
            }
          }

          item.style.display = showItem ? 'block' : 'none';
        });
      }

      // Attach event listeners
      searchFilter.addEventListener('input', filterPayments);
      statusFilter.addEventListener('change', filterPayments);
      dateFilter.addEventListener('change', filterPayments);
    });
  </script>
  
  <script src="<?= base_url('js/user_dashboard.js') ?>"></script>
</body>
</html>