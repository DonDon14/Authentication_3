<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Analytics Dashboard - ClearPay Admin</title>
  <link rel="stylesheet" href="<?= base_url('css/dashboard.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  
  <style>
    /* Additional styles for analytics specific elements */
    .summary-list {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    .summary-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0.75rem 0;
      border-bottom: 1px solid var(--border-color);
    }

    .summary-item:last-child {
      border-bottom: none;
    }

    .summary-label {
      color: var(--text-secondary);
      font-size: 0.875rem;
    }

    .summary-value {
      font-weight: 600;
      color: var(--text-primary);
    }

    .chart-container {
      position: relative;
      width: 100%;
    }

    .chart-container canvas {
      max-height: 100% !important;
    }

    .filter-select {
      background: var(--bg-primary);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-md);
      padding: 0.5rem 1rem;
      color: var(--text-primary);
      font-size: 0.875rem;
    }

    .filter-container {
      display: flex;
      align-items: center;
    }

    .table-container {
      overflow-x: auto;
    }

    .data-table {
      width: 100%;
      border-collapse: collapse;
    }

    .data-table th,
    .data-table td {
      padding: 0.75rem;
      text-align: left;
      border-bottom: 1px solid var(--border-color);
    }

    .data-table th {
      background: var(--bg-secondary);
      font-weight: 600;
      color: var(--text-primary);
      font-size: 0.875rem;
    }

    .data-table td {
      color: var(--text-secondary);
    }

    .data-table tr:hover {
      background: var(--bg-secondary);
    }

    .badge {
      display: inline-flex;
      align-items: center;
      padding: 0.25rem 0.75rem;
      border-radius: var(--radius-sm);
      font-size: 0.75rem;
      font-weight: 600;
      background: rgba(59, 130, 246, 0.1);
      color: var(--info-color);
    }

    @media (max-width: 768px) {
      .dashboard-grid {
        grid-template-columns: 1fr;
      }
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
          <li class="nav-item active">
            <a href="<?= base_url('analytics') ?>" class="nav-link">
              <i class="fas fa-chart-bar"></i>
              <span>Analytics</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
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
            <h4><?= esc(session()->get('name') ? explode(' ', session()->get('name'))[0] : 'Admin') ?></h4>
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
          <h1 class="page-title">Analytics Dashboard</h1>
          <p class="page-subtitle">Comprehensive insights into your payment system performance</p>
        </div>
        
        <div class="header-right">
          <!-- Search Bar -->
          <div class="search-container">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Search analytics..." class="search-input">
          </div>
          
          <!-- Date Filter -->
          <div class="filter-container">
            <select id="dateRange" onchange="updateAnalytics()" class="filter-select">
              <option value="30">Last 30 Days</option>
              <option value="7">Last 7 Days</option>
              <option value="90">Last 3 Months</option>
              <option value="365">Last Year</option>
              <option value="all">All Time</option>
            </select>
          </div>
          
          <!-- Export Menu -->
          <div class="user-menu">
            <button class="user-menu-btn" onclick="toggleExportMenu()">
              <i class="fas fa-download"></i>
              <span>Export</span>
              <i class="fas fa-chevron-down"></i>
            </button>
            
            <div class="user-dropdown" id="exportMenu">
              <div class="dropdown-menu">
                <a href="<?= base_url('analytics/export/pdf') ?>" class="dropdown-item">
                  <i class="fas fa-file-pdf"></i>
                  <span>Export PDF</span>
                </a>
                <a href="<?= base_url('analytics/export/csv') ?>" class="dropdown-item">
                  <i class="fas fa-file-csv"></i>
                  <span>Export CSV</span>
                </a>
                <a href="<?= base_url('analytics/export/excel') ?>" class="dropdown-item">
                  <i class="fas fa-file-excel"></i>
                  <span>Export Excel</span>
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
                <h3>Total Revenue</h3>
                <div class="stat-icon">
                  <i class="fas fa-dollar-sign"></i>
                </div>
              </div>
              <div class="stat-value">₱<?= number_format($overview['total_revenue'], 2) ?></div>
              <div class="stat-footer">
                <span class="stat-change <?= $overview['monthly_growth'] >= 0 ? 'positive' : 'negative' ?>">
                  <i class="fas fa-arrow-<?= $overview['monthly_growth'] >= 0 ? 'up' : 'down' ?>"></i>
                  <?= abs($overview['monthly_growth']) ?>%
                </span>
                <span class="stat-period">vs last month</span>
              </div>
            </div>
          </div>
          
          <div class="stat-card success">
            <div class="stat-content">
              <div class="stat-header">
                <h3>Total Profit</h3>
                <div class="stat-icon">
                  <i class="fas fa-chart-line"></i>
                </div>
              </div>
              <div class="stat-value">₱<?= number_format($overview['total_profit'], 2) ?></div>
              <div class="stat-footer">
                <span class="stat-change positive">
                  <i class="fas fa-percentage"></i>
                  <?= $overview['avg_profit_margin'] ?>%
                </span>
                <span class="stat-period">avg margin</span>
              </div>
            </div>
          </div>
          
          <div class="stat-card info">
            <div class="stat-content">
              <div class="stat-header">
                <h3>Active Contributors</h3>
                <div class="stat-icon">
                  <i class="fas fa-users"></i>
                </div>
              </div>
              <div class="stat-value"><?= number_format($overview['active_contributors']) ?></div>
              <div class="stat-footer">
                <span class="stat-change positive">
                  <i class="fas fa-arrow-up"></i>
                  Growing
                </span>
                <span class="stat-period">this month</span>
              </div>
            </div>
          </div>
          
          <div class="stat-card warning">
            <div class="stat-content">
              <div class="stat-header">
                <h3>Total Contributions</h3>
                <div class="stat-icon">
                  <i class="fas fa-hand-holding-usd"></i>
                </div>
              </div>
              <div class="stat-value"><?= number_format($overview['total_contributions']) ?></div>
              <div class="stat-footer">
                <span class="stat-change neutral">
                  <i class="fas fa-plus"></i>
                  Active
                </span>
                <span class="stat-period">campaigns</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Main Dashboard Grid -->
        <div class="dashboard-grid">
          
          <!-- Revenue Chart Panel -->
          <div class="dashboard-card">
            <div class="card-header">
              <h3>Revenue Trends</h3>
              <div class="card-actions">
                <span class="card-subtitle">Last 30 Days</span>
                <button class="btn-secondary" onclick="refreshCharts()">
                  <i class="fas fa-refresh"></i>
                </button>
              </div>
            </div>
            <div class="card-content">
              <div class="chart-container" style="height: 300px;">
                <canvas id="revenueChart"></canvas>
              </div>
            </div>
          </div>

          <!-- Quick Summary Panel -->
          <div class="dashboard-card">
            <div class="card-header">
              <h3>Quick Summary</h3>
              <p>Key performance indicators</p>
            </div>
            <div class="card-content">
              <div class="summary-list">
                <div class="summary-item">
                  <div class="summary-label">Monthly Revenue</div>
                  <div class="summary-value">₱<?= number_format($overview['monthly_revenue'], 2) ?></div>
                </div>
                
                <div class="summary-item">
                  <div class="summary-label">Avg Transaction</div>
                  <div class="summary-value">₱<?= number_format($payments['avg_transaction'], 2) ?></div>
                </div>
                
                <div class="summary-item">
                  <div class="summary-label">Profit Margin</div>
                  <div class="summary-value"><?= $overview['avg_profit_margin'] ?>%</div>
                </div>
                
                <div class="summary-item">
                  <div class="summary-label">Payment Methods</div>
                  <div class="summary-value"><?= count($payments['by_method']) ?> types</div>
                </div>
                
                <div class="summary-item">
                  <div class="summary-label">Categories</div>
                  <div class="summary-value"><?= count($contributions['by_category']) ?> active</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Transaction Chart -->
        <div class="dashboard-card" style="margin-bottom: 2rem;">
          <div class="card-header">
            <h3>Transaction Volume</h3>
            <div class="card-actions">
              <span class="card-subtitle">Daily transactions for the last 30 days</span>
            </div>
          </div>
          <div class="card-content">
            <div class="chart-container" style="height: 300px;">
              <canvas id="transactionChart"></canvas>
            </div>
          </div>
        </div>

        <!-- Data Tables Grid -->
        <div class="dashboard-grid">
          
          <!-- Top Profitable Contributions Panel -->
          <div class="dashboard-card">
            <div class="card-header">
              <h3>Top Profitable Contributions</h3>
              <div class="card-actions">
                <a href="<?= base_url('contributions/analytics') ?>" class="btn-primary">
                  View All
                </a>
              </div>
            </div>
            <div class="card-content">
              <?php if (count($contributions['top_profitable']) > 0): ?>
                <div class="table-container">
                  <table class="data-table">
                    <thead>
                      <tr>
                        <th>Contribution</th>
                        <th>Revenue</th>
                        <th>Profit</th>
                        <th>Margin</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach (array_slice($contributions['top_profitable'], 0, 5) as $contribution): ?>
                      <tr>
                        <td><?= esc($contribution['title']) ?></td>
                        <td>₱<?= number_format($contribution['amount'], 2) ?></td>
                        <td>₱<?= number_format($contribution['profit_amount'], 2) ?></td>
                        <td>
                          <span class="status status-<?= $contribution['profit_margin'] >= 30 ? 'success' : ($contribution['profit_margin'] >= 15 ? 'warning' : 'danger') ?>">
                            <?= number_format($contribution['profit_margin'], 1) ?>%
                          </span>
                        </td>
                      </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              <?php else: ?>
                <div class="empty-state">
                  <div class="empty-icon">
                    <i class="fas fa-chart-line"></i>
                  </div>
                  <h4>No Profit Data</h4>
                  <p>No profitable contributions recorded yet.</p>
                </div>
              <?php endif; ?>
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
              <?php if (count($payments['recent_payments']) > 0): ?>
                <div class="payments-list">
                  <?php foreach (array_slice($payments['recent_payments'], 0, 5) as $payment): ?>
                    <div class="payment-item">
                      <div class="payment-avatar">
                        <i class="fas fa-user"></i>
                      </div>
                      <div class="payment-details">
                        <h4><?= esc($payment['student_name']) ?></h4>
                        <p class="payment-type"><?= esc($payment['contribution_title']) ?></p>
                        <span class="payment-time"><?= date('M j, Y g:i A', strtotime($payment['created_at'])) ?></span>
                      </div>
                      <div class="payment-amount">
                        <span class="amount">₱<?= number_format($payment['amount'], 2) ?></span>
                        <span class="status status-<?= strtolower($payment['status']) ?>">
                          <?= ucfirst($payment['status']) ?>
                        </span>
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
                    Record Payment
                  </button>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- Category Performance Panel -->
        <div class="dashboard-card" style="margin-bottom: 2rem;">
          <div class="card-header">
            <h3>Performance by Category</h3>
            <p>Revenue and profit breakdown by contribution categories</p>
          </div>
          <div class="card-content">
            <?php if (count($contributions['by_category']) > 0): ?>
              <div class="table-container">
                <table class="data-table">
                  <thead>
                    <tr>
                      <th>Category</th>
                      <th>Contributions</th>
                      <th>Total Revenue</th>
                      <th>Total Profit</th>
                      <th>Avg Margin</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($contributions['by_category'] as $category): ?>
                    <tr>
                      <td><?= esc(ucfirst($category['category'])) ?></td>
                      <td>
                        <span class="badge"><?= number_format($category['count']) ?></span>
                      </td>
                      <td>₱<?= number_format($category['total_amount'], 2) ?></td>
                      <td>₱<?= number_format($category['total_profit'], 2) ?></td>
                      <td>
                        <span class="status status-<?= ($category['total_profit'] / $category['total_amount'] * 100) >= 30 ? 'success' : (($category['total_profit'] / $category['total_amount'] * 100) >= 15 ? 'warning' : 'danger') ?>">
                          <?= number_format(($category['total_profit'] / $category['total_amount'] * 100), 1) ?>%
                        </span>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php else: ?>
              <div class="empty-state">
                <div class="empty-icon">
                  <i class="fas fa-chart-pie"></i>
                </div>
                <h4>No Category Data</h4>
                <p>No category performance data available yet.</p>
              </div>
            <?php endif; ?>
          </div>
        </div>

    </main>
  </div>

  <script>
    // Chart initialization
    const ctx1 = document.getElementById('revenueChart').getContext('2d');
    const ctx2 = document.getElementById('transactionChart').getContext('2d');

    // Revenue Chart
    const revenueChart = new Chart(ctx1, {
      type: 'line',
      data: {
        labels: <?= json_encode($charts['daily_revenue']['labels']) ?>,
        datasets: [{
          label: 'Daily Revenue',
          data: <?= json_encode($charts['daily_revenue']['data']) ?>,
          borderColor: 'rgb(59, 130, 246)',
          backgroundColor: 'rgba(59, 130, 246, 0.1)',
          borderWidth: 3,
          fill: true,
          tension: 0.4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function(value) {
                return '₱' + value.toLocaleString();
              }
            }
          }
        }
      }
    });

    // Transaction Chart
    const transactionChart = new Chart(ctx2, {
      type: 'bar',
      data: {
        labels: <?= json_encode($charts['daily_transactions']['labels']) ?>,
        datasets: [{
          label: 'Daily Transactions',
          data: <?= json_encode($charts['daily_transactions']['data']) ?>,
          backgroundColor: 'rgba(16, 185, 129, 0.8)',
          borderColor: 'rgb(16, 185, 129)',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });

    // Functions
    function toggleExportMenu() {
      const menu = document.getElementById('exportMenu');
      menu.classList.toggle('active');
    }

    function updateAnalytics() {
      const range = document.getElementById('dateRange').value;
      console.log('Updating analytics for range:', range);
      // You can reload the page with the new range or use AJAX
    }

    function refreshAnalytics() {
      location.reload();
    }

    function refreshCharts() {
      revenueChart.update();
      transactionChart.update();
    }

    function refreshPayments() {
      location.reload();
    }

    // Close export menu when clicking outside
    document.addEventListener('click', function(event) {
      const exportDropdown = document.querySelector('.user-menu');
      const exportMenu = document.getElementById('exportMenu');
      
      if (exportDropdown && !exportDropdown.contains(event.target)) {
        exportMenu.classList.remove('active');
      }
    });

    // Sidebar functionality
    const sidebar = document.querySelector('.sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mainContent = document.querySelector('.main-content');

    function toggleSidebar() {
      sidebar.classList.toggle('collapsed');
      mainContent.classList.toggle('expanded');
    }

    sidebarToggle?.addEventListener('click', toggleSidebar);

    function toggleProfileMenu() {
      console.log('Profile menu toggled');
    }
  </script>
  
  <!-- Dashboard JavaScript -->
  <script src="<?= base_url('js/dashboard.js') ?>"></script>
</body>
</html>