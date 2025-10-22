<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contribution Analytics - ClearPay Admin</title>
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
        <button class="sidebar-toggle" onclick="toggleSidebar()">
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
      
      <?= $this->include('partials/help_section') ?>
    </aside>

    <!-- Main Content Area -->
    <main class="main-content">
      
      <!-- Top Header Bar -->
      <header class="header">
        <div class="header-left">
          <h1>Contribution Analytics</h1>
          <p class="page-subtitle">Track profitability and performance of contributions</p>
        </div>
        <div class="header-right">
          <a href="<?= base_url('contributions') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Back to Contributions
          </a>
        </div>
      </header>

      <!-- Dashboard Content -->
      <div class="dashboard-content">
        
        <!-- Profit Summary Cards -->
        <div class="stats-grid">
          <div class="stat-card success">
            <div class="stat-content">
              <div class="stat-header">
                <h3>Total Profit</h3>
                <div class="stat-icon">
                  <i class="fas fa-chart-line"></i>
                </div>
              </div>
              <div class="stat-value">$<?= number_format($profit_analytics['total_profit'] ?? 0, 2) ?></div>
              <div class="stat-footer">
                <span class="stat-change positive">
                  <i class="fas fa-arrow-up"></i>
                  From all active contributions
                </span>
              </div>
            </div>
          </div>
          
          <div class="stat-card primary">
            <div class="stat-content">
              <div class="stat-header">
                <h3>Average Profit Margin</h3>
                <div class="stat-icon">
                  <i class="fas fa-percentage"></i>
                </div>
              </div>
              <div class="stat-value"><?= number_format($profit_analytics['avg_profit_margin'] ?? 0, 1) ?>%</div>
              <div class="stat-footer">
                <span class="stat-change">
                  <i class="fas fa-calculator"></i>
                  Across all contributions
                </span>
              </div>
            </div>
          </div>
          
          <div class="stat-card info">
            <div class="stat-content">
              <div class="stat-header">
                <h3>Total Revenue</h3>
                <div class="stat-icon">
                  <i class="fas fa-dollar-sign"></i>
                </div>
              </div>
              <div class="stat-value">$<?= number_format($profit_analytics['total_revenue'] ?? 0, 2) ?></div>
              <div class="stat-footer">
                <span class="stat-change">
                  <i class="fas fa-coins"></i>
                  From <?= $profit_analytics['total_contributions'] ?? 0 ?> contributions
                </span>
              </div>
            </div>
          </div>
          
          <div class="stat-card warning">
            <div class="stat-content">
              <div class="stat-header">
                <h3>Total Costs</h3>
                <div class="stat-icon">
                  <i class="fas fa-receipt"></i>
                </div>
              </div>
              <div class="stat-value">$<?= number_format($profit_analytics['total_costs'] ?? 0, 2) ?></div>
              <div class="stat-footer">
                <span class="stat-change">
                  <i class="fas fa-minus-circle"></i>
                  Operating expenses
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Analytics Grid -->
        <div class="dashboard-grid">
          
          <!-- Top Profitable Contributions -->
          <div class="dashboard-card">
            <div class="card-header">
              <h3>Top Profitable Contributions</h3>
              <p>Highest profit margins and amounts</p>
            </div>
            <div class="card-content">
              <?php if (!empty($top_profitable)): ?>
                <div class="contribution-list">
                  <?php foreach ($top_profitable as $contribution): ?>
                    <div class="contribution-item" style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); margin-bottom: 0.5rem;">
                      <div>
                        <h4 style="margin: 0 0 0.25rem 0; font-size: 1rem; font-weight: 600;"><?= esc($contribution['title']) ?></h4>
                        <p style="margin: 0; font-size: 0.875rem; color: var(--text-secondary);">
                          Revenue: $<?= number_format($contribution['amount'], 2) ?> | 
                          Cost: $<?= number_format($contribution['cost_price'], 2) ?>
                        </p>
                      </div>
                      <div style="text-align: right;">
                        <div style="font-size: 1.125rem; font-weight: 700; color: var(--success-color);">
                          $<?= number_format($contribution['profit_amount'], 2) ?>
                        </div>
                        <div style="font-size: 0.8rem; color: var(--primary-color); font-weight: 500;">
                          <?= number_format($contribution['profit_margin'], 1) ?>% margin
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php else: ?>
                <p style="color: var(--text-secondary); text-align: center; padding: 2rem;">No contribution data available</p>
              <?php endif; ?>
            </div>
          </div>

          <!-- All Contributions Profit Analysis -->
          <div class="dashboard-card">
            <div class="card-header">
              <h3>All Contributions Analysis</h3>
              <p>Complete profitability breakdown</p>
            </div>
            <div class="card-content">
              <div class="table-container">
                <table class="data-table" style="width: 100%; border-collapse: collapse;">
                  <thead>
                    <tr style="background: var(--bg-secondary); border-bottom: 1px solid var(--border-color);">
                      <th style="padding: 0.75rem; text-align: left; font-weight: 600;">Title</th>
                      <th style="padding: 0.75rem; text-align: right; font-weight: 600;">Revenue</th>
                      <th style="padding: 0.75rem; text-align: right; font-weight: 600;">Cost</th>
                      <th style="padding: 0.75rem; text-align: right; font-weight: 600;">Profit</th>
                      <th style="padding: 0.75rem; text-align: right; font-weight: 600;">Margin</th>
                      <th style="padding: 0.75rem; text-align: center; font-weight: 600;">Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($contributions as $contribution): ?>
                      <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 0.75rem;">
                          <div>
                            <div style="font-weight: 500;"><?= esc($contribution['title']) ?></div>
                            <div style="font-size: 0.8rem; color: var(--text-secondary);"><?= esc($contribution['category']) ?></div>
                          </div>
                        </td>
                        <td style="padding: 0.75rem; text-align: right; font-weight: 500;">
                          $<?= number_format($contribution['amount'], 2) ?>
                        </td>
                        <td style="padding: 0.75rem; text-align: right;">
                          $<?= number_format($contribution['cost_price'] ?? 0, 2) ?>
                        </td>
                        <td style="padding: 0.75rem; text-align: right; font-weight: 600; color: <?= ($contribution['profit_amount'] ?? 0) >= 0 ? 'var(--success-color)' : 'var(--error-color)' ?>;">
                          $<?= number_format($contribution['profit_amount'] ?? 0, 2) ?>
                        </td>
                        <td style="padding: 0.75rem; text-align: right; font-weight: 500; color: <?= ($contribution['profit_margin'] ?? 0) >= 0 ? 'var(--success-color)' : 'var(--error-color)' ?>;">
                          <?= number_format($contribution['profit_margin'] ?? 0, 1) ?>%
                        </td>
                        <td style="padding: 0.75rem; text-align: center;">
                          <span class="status-badge <?= $contribution['status'] === 'active' ? 'success' : 'inactive' ?>" style="padding: 0.25rem 0.5rem; border-radius: var(--radius-sm); font-size: 0.75rem; font-weight: 500;">
                            <?= ucfirst($contribution['status']) ?>
                          </span>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>
      </div>
    </main>
  </div>

  <script>
    // Sidebar toggle functionality
    function toggleSidebar() {
      document.querySelector('.sidebar').classList.toggle('collapsed');
    }

    // Profile menu functionality
    function toggleProfileMenu() {
      console.log('Profile menu toggled');
    }
  </script>
</body>
</html>