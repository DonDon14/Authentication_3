<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment History</title>
  <link rel="stylesheet" href="<?= base_url('css/history.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="history-container">
    <!-- Header -->
    <div class="history-header">
      <div class="welcome-section">
        <h2>Payment History</h2>
        <p class="description">View all payment records</p>
      </div>
    </div>

    <!-- Stats Section -->
    <div class="stats-grid">
      <div class="stat-card stat-card-primary">
        <div class="stat-icon">
          <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-content">
          <p class="stat-label">Total</p>
          <p class="stat-value">$190</p>
        </div>
      </div>
      
      <div class="stat-card stat-card-success">
        <div class="stat-icon">
          <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
          <p class="stat-label">Verified</p>
          <p class="stat-value">1</p>
        </div>
      </div>
      
      <div class="stat-card stat-card-warning">
        <div class="stat-icon">
          <i class="fas fa-clock"></i>
        </div>
        <div class="stat-content">
          <p class="stat-label">Pending</p>
          <p class="stat-value">1</p>
        </div>
      </div>
      
      <div class="stat-card stat-card-info">
        <div class="stat-icon">
          <i class="fas fa-calendar-day"></i>
        </div>
        <div class="stat-content">
          <p class="stat-label">Today</p>
          <p class="stat-value">0</p>
        </div>
      </div>
    </div>

    <!-- Filters & Search Section -->
    <div class="filters-section">
      <div class="section-header">
        <h3><i class="fas fa-filter"></i> Filters & Search</h3>
      </div>
      
      <div class="search-container">
        <div class="search-box">
          <input type="text" id="searchInput" placeholder="Search by name, type, or ID..." class="search-input">
          <i class="fas fa-search search-icon"></i>
        </div>
      </div>
      
      <div class="filter-controls">
        <div class="filter-group">
          <label for="statusFilter">Status</label>
          <select id="statusFilter" class="filter-select">
            <option value="">All Status</option>
            <option value="verified">Verified</option>
            <option value="pending">Pending</option>
            <option value="failed">Failed</option>
          </select>
        </div>
        
        <div class="filter-group">
          <label for="sortBy">Sort by</label>
          <select id="sortBy" class="filter-select">
            <option value="latest">Latest First</option>
            <option value="oldest">Oldest First</option>
            <option value="amount-high">Amount (High to Low)</option>
            <option value="amount-low">Amount (Low to High)</option>
          </select>
        </div>
      </div>
      
      <div class="export-section">
        <button class="btn-export" id="exportBtn">
          <i class="fas fa-download"></i>
          Export History
        </button>
      </div>
    </div>

    <!-- Payment Records Section -->
    <div class="records-section">
      <div class="section-header">
        <h3>Payment Records</h3>
        <p class="records-count">3 payments found</p>
      </div>
      
      <div class="payment-records" id="paymentRecords">
        <!-- Payment Record 1 -->
        <div class="payment-record" data-status="verified">
          <div class="record-header">
            <div class="student-info">
              <h4>John Doe</h4>
              <p class="student-id">ID: STU001</p>
            </div>
            <div class="payment-amount">
              <span class="amount">$150</span>
              <span class="status-badge status-verified">Verified</span>
            </div>
          </div>
          <div class="record-details">
            <div class="payment-info">
              <p class="payment-type">Uniform Payments</p>
              <p class="payment-date">Jan 15, 2024</p>
              <p class="qr-reference">QR: Q5001</p>
            </div>
            <div class="record-actions">
              <button class="action-btn view-receipt">
                View Receipt
              </button>
            </div>
          </div>
        </div>

        <!-- Payment Record 2 -->
        <div class="payment-record" data-status="pending">
          <div class="record-header">
            <div class="student-info">
              <h4>Jane Smith</h4>
              <p class="student-id">ID: STU002</p>
            </div>
            <div class="payment-amount">
              <span class="amount">$25</span>
              <span class="status-badge status-pending">Pending</span>
            </div>
          </div>
          <div class="record-details">
            <div class="payment-info">
              <p class="payment-type">Daily Dues</p>
              <p class="payment-date">Jan 14, 2024</p>
              <p class="qr-reference">QR: Q5002</p>
            </div>
            <div class="record-actions">
              <button class="action-btn verify-payment" data-id="2">
                Verify Payment
              </button>
            </div>
          </div>
        </div>

        <!-- Payment Record 3 -->
        <div class="payment-record" data-status="pending">
          <div class="record-header">
            <div class="student-info">
              <h4>Mike Johnson</h4>
              <p class="student-id">ID: STU003</p>
            </div>
            <div class="payment-amount">
              <span class="amount">$15</span>
              <span class="status-badge status-pending">Pending</span>
            </div>
          </div>
          <div class="record-details">
            <div class="payment-info">
              <p class="payment-type">Lunch Money</p>
              <p class="payment-date">Jan 13, 2024</p>
              <p class="qr-reference">QR: Q5003</p>
            </div>
            <div class="record-actions">
              <button class="action-btn verify-payment" data-id="3">
                Verify Payment
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom Navigation -->
    <nav class="bottom-nav">
      <a href="<?= base_url('dashboard') ?>" class="nav-link">
        <i class="fas fa-home"></i>
        <span>Home</span>
      </a>
      <a href="<?= base_url('payments') ?>" class="nav-link">
        <i class="fas fa-credit-card"></i>
        <span>Payments</span>
      </a>
      <a href="<?= base_url('contributions') ?>" class="nav-link">
        <i class="fas fa-hand-holding-usd"></i>
        <span>Contributions</span>
      </a>
      <a href="<?= base_url('payments/history') ?>" class="nav-link active">
        <i class="fas fa-clock"></i>
        <span>History</span>
      </a>
    </nav>
  </div>

  <!-- External JS -->
  <script src="<?= base_url('js/history.js') ?>"></script>
</body>
</html>