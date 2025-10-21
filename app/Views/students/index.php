<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Students Management - ClearPay Admin</title>
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
          <li class="nav-item">
            <a href="<?= base_url('analytics') ?>" class="nav-link">
              <i class="fas fa-chart-bar"></i>
              <span>Analytics</span>
            </a>
          </li>
          <li class="nav-item active">
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
      
      <!-- Header -->
      <header class="header">
        <div class="header-left">
          <h1 class="page-title">Students Management</h1>
          <p class="page-subtitle">Manage student payment records and information</p>
        </div>
        
        <div class="header-right">
          <div class="search-container">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Search students..." class="search-input" id="studentSearch">
          </div>
          
          <button class="btn btn-primary" onclick="refreshStudents()">
            <i class="fas fa-sync-alt"></i>
            Refresh
          </button>
        </div>
      </header>

      <!-- Dashboard Content -->
      <div class="dashboard-content">
        
        <!-- Statistics Cards -->
        <div class="stats-grid" style="margin-bottom: 2rem;">
          <div class="stat-card primary">
            <div class="stat-content">
              <div class="stat-header">
                <h3>Total Students</h3>
                <div class="stat-icon">
                  <i class="fas fa-users"></i>
                </div>
              </div>
              <div class="stat-value"><?= count($students) ?></div>
              <div class="stat-footer">
                <span class="stat-change">
                  <i class="fas fa-user-plus"></i>
                  Active contributors
                </span>
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
              <div class="stat-value">₱<?= number_format(array_sum(array_column($students, 'total_paid')), 2) ?></div>
              <div class="stat-footer">
                <span class="stat-change">
                  <i class="fas fa-arrow-up"></i>
                  All time total
                </span>
              </div>
            </div>
          </div>
          
          <div class="stat-card info">
            <div class="stat-content">
              <div class="stat-header">
                <h3>Avg per Student</h3>
                <div class="stat-icon">
                  <i class="fas fa-calculator"></i>
                </div>
              </div>
              <div class="stat-value">₱<?= count($students) > 0 ? number_format(array_sum(array_column($students, 'total_paid')) / count($students), 2) : '0.00' ?></div>
              <div class="stat-footer">
                <span class="stat-change">
                  <i class="fas fa-chart-line"></i>
                  Average contribution
                </span>
              </div>
            </div>
          </div>
          
          <div class="stat-card warning">
            <div class="stat-content">
              <div class="stat-header">
                <h3>Total Transactions</h3>
                <div class="stat-icon">
                  <i class="fas fa-receipt"></i>
                </div>
              </div>
              <div class="stat-value"><?= array_sum(array_column($students, 'total_payments')) ?></div>
              <div class="stat-footer">
                <span class="stat-change">
                  <i class="fas fa-credit-card"></i>
                  Payment records
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Students Table -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">
              <i class="fas fa-users"></i>
              Students Directory
            </h3>
            <div class="card-actions">
              <button class="btn btn-outline" onclick="exportStudents()">
                <i class="fas fa-download"></i>
                Export
              </button>
            </div>
          </div>
          <div class="card-content">
            <?php if (count($students) > 0): ?>
            <div class="table-container">
              <table class="data-table">
                <thead>
                  <tr>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Total Payments</th>
                    <th>Total Amount Paid</th>
                    <th>Contributions</th>
                    <th>Last Payment</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($students as $student): ?>
                  <tr>
                    <td>
                      <span class="student-id"><?= esc($student['student_id']) ?></span>
                    </td>
                    <td>
                      <div class="student-info">
                        <div class="student-avatar">
                          <i class="fas fa-user"></i>
                        </div>
                        <div>
                          <h4><?= esc($student['student_name']) ?></h4>
                          <p class="student-meta">ID: <?= esc($student['student_id']) ?></p>
                        </div>
                      </div>
                    </td>
                    <td>
                      <span class="badge badge-info"><?= $student['total_payments'] ?></span>
                    </td>
                    <td>
                      <span class="amount">₱<?= number_format($student['total_paid'], 2) ?></span>
                    </td>
                    <td>
                      <span class="badge badge-success"><?= $student['contributions_count'] ?></span>
                    </td>
                    <td>
                      <span class="date"><?= date('M j, Y', strtotime($student['last_payment'])) ?></span>
                    </td>
                    <td>
                      <div class="action-buttons">
                        <button class="btn-icon btn-primary" onclick="viewStudent('<?= $student['student_id'] ?>')" title="View Details">
                          <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn-icon btn-info" onclick="viewPaymentHistory('<?= $student['student_id'] ?>')" title="Payment History">
                          <i class="fas fa-history"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
            <?php else: ?>
            <div class="empty-state">
              <div class="empty-icon">
                <i class="fas fa-users"></i>
              </div>
              <h4>No Students Found</h4>
              <p>No student payment records available yet.</p>
              <button class="btn btn-primary" onclick="window.location.href='<?= base_url('payments') ?>'">
                <i class="fas fa-plus"></i>
                Record First Payment
              </button>
            </div>
            <?php endif; ?>
          </div>
        </div>

      </div>

    </main>
  </div>

  <style>
    .student-info {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .student-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 1rem;
    }

    .student-info h4 {
      margin: 0;
      font-size: 0.9rem;
      font-weight: 600;
      color: var(--text-primary);
    }

    .student-meta {
      margin: 0.25rem 0 0 0;
      font-size: 0.75rem;
      color: var(--text-secondary);
    }

    .student-id {
      font-family: 'Courier New', monospace;
      background: var(--bg-secondary);
      padding: 0.25rem 0.5rem;
      border-radius: var(--radius-sm);
      font-size: 0.875rem;
    }

    .amount {
      font-weight: 600;
      color: var(--success-color);
    }

    .date {
      font-size: 0.875rem;
      color: var(--text-secondary);
    }

    .badge {
      display: inline-flex;
      align-items: center;
      padding: 0.25rem 0.75rem;
      border-radius: var(--radius-sm);
      font-size: 0.75rem;
      font-weight: 600;
    }

    .badge-info {
      background: rgba(59, 130, 246, 0.1);
      color: var(--info-color);
    }

    .badge-success {
      background: rgba(16, 185, 129, 0.1);
      color: var(--success-color);
    }

    .action-buttons {
      display: flex;
      gap: 0.5rem;
    }

    .btn-icon {
      width: 32px;
      height: 32px;
      border-radius: var(--radius-sm);
      border: none;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: var(--transition-fast);
    }

    .btn-icon.btn-primary {
      background: var(--primary-color);
      color: white;
    }

    .btn-icon.btn-info {
      background: var(--info-color);
      color: white;
    }

    .btn-icon:hover {
      transform: scale(1.1);
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
      padding: 1rem;
      text-align: left;
      border-bottom: 1px solid var(--border-color);
    }

    .data-table th {
      background: var(--bg-secondary);
      font-weight: 600;
      color: var(--text-primary);
      font-size: 0.875rem;
    }

    .data-table tr:hover {
      background: var(--bg-secondary);
    }
  </style>

  <script>
    function viewStudent(studentId) {
      window.location.href = '<?= base_url('students/details') ?>/' + studentId;
    }

    function viewPaymentHistory(studentId) {
      // Implement payment history view
      console.log('View payment history for student:', studentId);
    }

    function exportStudents() {
      // Implement export functionality
      console.log('Export students data');
    }

    function refreshStudents() {
      location.reload();
    }

    // Search functionality
    document.getElementById('studentSearch').addEventListener('input', function(e) {
      const searchTerm = e.target.value.toLowerCase();
      const rows = document.querySelectorAll('.data-table tbody tr');
      
      rows.forEach(row => {
        const studentName = row.querySelector('.student-info h4').textContent.toLowerCase();
        const studentId = row.querySelector('.student-id').textContent.toLowerCase();
        
        if (studentName.includes(searchTerm) || studentId.includes(searchTerm)) {
          row.style.display = 'table-row';
        } else {
          row.style.display = 'none';
        }
      });
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