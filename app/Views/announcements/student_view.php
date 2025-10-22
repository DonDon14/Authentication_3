<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Announcements - ClearPay</title>
  <?php if (isset($is_student) && $is_student): ?>
    <link rel="stylesheet" href="<?= base_url('css/user_dashboard.css') ?>">
  <?php else: ?>
    <link rel="stylesheet" href="<?= base_url('css/dashboard.css') ?>">
  <?php endif; ?>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Announcement specific styles -->
  <style>
    /* Announcement cards */
    .announcement-card {
      background: var(--bg-primary, white);
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      border: 1px solid #e1e5e9;
      margin-bottom: 1.5rem;
      overflow: hidden;
      transition: all 0.3s ease;
    }

    .announcement-card:hover {
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
      transform: translateY(-2px);
    }

    .announcement-header {
      padding: 1.5rem;
      border-bottom: 1px solid #e1e5e9;
      position: relative;
    }

    .announcement-title {
      margin: 0 0 0.5rem 0;
      font-size: 1.25rem;
      font-weight: 600;
      color: #2c3e50;
      line-height: 1.4;
    }

    .announcement-meta {
      display: flex;
      align-items: center;
      gap: 1rem;
      font-size: 0.875rem;
      color: #6c757d;
      flex-wrap: wrap;
    }

    .announcement-meta span {
      display: flex;
      align-items: center;
      gap: 0.25rem;
    }

    .announcement-body {
      padding: 1.5rem;
    }

    .announcement-content {
      color: #495057;
      line-height: 1.6;
      margin-bottom: 1rem;
    }

    .announcement-badges {
      display: flex;
      gap: 0.5rem;
      flex-wrap: wrap;
    }

    .badge {
      padding: 0.25rem 0.75rem;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .badge-priority-critical {
      background: #dc3545;
      color: white;
      animation: pulse 2s infinite;
    }

    .badge-priority-high {
      background: #fd7e14;
      color: white;
    }

    .badge-priority-medium {
      background: #0dcaf0;
      color: white;
    }

    .badge-priority-low {
      background: #6c757d;
      color: white;
    }

    .badge-type {
      background: #6f42c1;
      color: white;
    }

    .badge-audience {
      background: #20c997;
      color: white;
    }

    @keyframes pulse {
      0% { opacity: 1; }
      50% { opacity: 0.7; }
      100% { opacity: 1; }
    }

    /* Priority sections */
    .priority-section {
      margin-bottom: 2rem;
    }

    .priority-section h3 {
      margin-bottom: 1rem;
      padding: 0.75rem 1rem;
      border-radius: 8px;
      font-size: 1.1rem;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .priority-critical h3 {
      background: #dc3545;
      color: white;
    }

    .priority-high h3 {
      background: #fd7e14;
      color: white;
    }

    .priority-normal h3 {
      background: #f8f9fa;
      color: #495057;
      border: 1px solid #e1e5e9;
    }

    /* Search section */
    .search-section {
      background: var(--bg-primary, white);
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      border: 1px solid #e1e5e9;
      padding: 1.5rem;
      margin-bottom: 2rem;
    }

    .search-input-group {
      position: relative;
      max-width: 500px;
    }

    .search-input {
      width: 100%;
      padding: 0.75rem 1rem 0.75rem 3rem;
      border: 1px solid #e1e5e9;
      border-radius: 25px;
      font-size: 0.875rem;
      transition: all 0.3s ease;
      background: #f8f9fa;
    }

    .search-input:focus {
      outline: none;
      border-color: #007bff;
      box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
      background: white;
    }

    .search-icon {
      position: absolute;
      left: 1rem;
      top: 50%;
      transform: translateY(-50%);
      color: #6c757d;
    }

    /* Empty state */
    .empty-state {
      text-align: center;
      padding: 3rem 1rem;
      color: #6c757d;
    }

    .empty-state i {
      font-size: 3rem;
      margin-bottom: 1rem;
      opacity: 0.5;
    }

    .empty-state h3 {
      margin-bottom: 0.5rem;
      color: #495057;
    }

    .empty-state p {
      margin-bottom: 0;
    }

    /* Student specific styles */
    .student-header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 2rem 1rem;
      margin-bottom: 2rem;
      border-radius: 0 0 20px 20px;
    }

    .student-header h1 {
      margin: 0 0 0.5rem 0;
      font-size: 1.75rem;
      font-weight: 700;
    }

    .student-header p {
      margin: 0;
      opacity: 0.9;
    }

    /* Admin view styles */
    .admin-sidebar {
      /* Keep existing sidebar styles for admin */
    }

    /* Mobile responsive */
    @media (max-width: 768px) {
      .announcement-meta {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
      }
      
      .student-header {
        padding: 1.5rem 1rem;
      }
      
      .student-header h1 {
        font-size: 1.5rem;
      }
    }

    /* Stats for admin view */
    .announcements-stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
      margin-bottom: 2rem;
    }

    .stat-card-mini {
      background: var(--bg-primary, white);
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      border: 1px solid #e1e5e9;
      padding: 1rem;
      text-align: center;
    }

    .stat-number {
      font-size: 1.5rem;
      font-weight: 700;
      color: #007bff;
      margin-bottom: 0.25rem;
    }

    .stat-label {
      font-size: 0.875rem;
      color: #6c757d;
      text-transform: uppercase;
      font-weight: 600;
    }
  </style>
</head>
<body>
  <?php if (isset($is_student) && $is_student): ?>
    <!-- Student View -->
    <div class="dashboard-container">
      <!-- Student Header -->
      <div class="student-header">
        <h1><i class="fas fa-bullhorn"></i> Announcements</h1>
        <p>Stay updated with the latest news and important information</p>
      </div>

      <!-- Search Section -->
      <div class="search-section">
        <div class="search-input-group">
          <i class="fas fa-search search-icon"></i>
          <input type="text" class="search-input" placeholder="Search announcements..." id="searchInput">
        </div>
      </div>

      <!-- Main Content -->
      <div class="main-content" style="padding: 0 1rem;">
  <?php else: ?>
    <!-- Admin View -->
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
              </a>
            </li>
            <li class="nav-item">
              <a href="<?= base_url('contributions') ?>" class="nav-link">
                <i class="fas fa-hand-holding-usd"></i>
                <span>Contributions</span>
              </a>
            </li>
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
            <li class="nav-item active">
              <a href="<?= base_url('announcements/student-view') ?>" class="nav-link">
                <i class="fas fa-bullhorn"></i>
                <span>Announcements</span>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?= base_url('settings') ?>" class="nav-link">
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
              <h4><?= esc($name ? explode(' ', $name)[0] : 'Admin') ?></h4>
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
            <h1 class="page-title">Announcements</h1>
            <p class="page-subtitle">Stay updated with important news and information</p>
          </div>
          
          <div class="header-right">
            <!-- Search Bar -->
            <div class="search-container">
              <i class="fas fa-search"></i>
              <input type="text" placeholder="Search announcements..." class="search-input" id="searchInput">
            </div>
          </div>
        </header>

        <!-- Dashboard Content -->
        <div class="dashboard-content">
  <?php endif; ?>

        <!-- Stats (for admin) -->
        <?php if (!isset($is_student) || !$is_student): ?>
          <div class="announcements-stats">
            <div class="stat-card-mini">
              <div class="stat-number"><?= count($critical_announcements) ?></div>
              <div class="stat-label">Critical</div>
            </div>
            <div class="stat-card-mini">
              <div class="stat-number"><?= count($high_announcements) ?></div>
              <div class="stat-label">High Priority</div>
            </div>
            <div class="stat-card-mini">
              <div class="stat-number"><?= count($normal_announcements) ?></div>
              <div class="stat-label">Normal</div>
            </div>
            <div class="stat-card-mini">
              <div class="stat-number"><?= count($all_announcements) ?></div>
              <div class="stat-label">Total Active</div>
            </div>
          </div>
        <?php endif; ?>

        <!-- Critical Announcements -->
        <?php if (!empty($critical_announcements)): ?>
          <div class="priority-section priority-critical">
            <h3>
              <i class="fas fa-exclamation-triangle"></i>
              Critical Announcements
            </h3>
            <?php foreach ($critical_announcements as $announcement): ?>
              <div class="announcement-card">
                <div class="announcement-header">
                  <h4 class="announcement-title"><?= esc($announcement['title']) ?></h4>
                  <div class="announcement-meta">
                    <span><i class="fas fa-calendar"></i> <?= date('M j, Y g:i A', strtotime($announcement['published_at'])) ?></span>
                    <?php if ($announcement['expires_at']): ?>
                      <span><i class="fas fa-clock"></i> Expires <?= date('M j, Y', strtotime($announcement['expires_at'])) ?></span>
                    <?php endif; ?>
                  </div>
                </div>
                <div class="announcement-body">
                  <div class="announcement-content">
                    <?= nl2br(esc($announcement['content'])) ?>
                  </div>
                  <div class="announcement-badges">
                    <span class="badge badge-priority-critical">Critical</span>
                    <span class="badge badge-type"><?= ucfirst($announcement['type']) ?></span>
                    <span class="badge badge-audience"><?= ucfirst($announcement['target_audience']) ?></span>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <!-- High Priority Announcements -->
        <?php if (!empty($high_announcements)): ?>
          <div class="priority-section priority-high">
            <h3>
              <i class="fas fa-exclamation"></i>
              High Priority Announcements
            </h3>
            <?php foreach ($high_announcements as $announcement): ?>
              <div class="announcement-card">
                <div class="announcement-header">
                  <h4 class="announcement-title"><?= esc($announcement['title']) ?></h4>
                  <div class="announcement-meta">
                    <span><i class="fas fa-calendar"></i> <?= date('M j, Y g:i A', strtotime($announcement['published_at'])) ?></span>
                    <?php if ($announcement['expires_at']): ?>
                      <span><i class="fas fa-clock"></i> Expires <?= date('M j, Y', strtotime($announcement['expires_at'])) ?></span>
                    <?php endif; ?>
                  </div>
                </div>
                <div class="announcement-body">
                  <div class="announcement-content">
                    <?= nl2br(esc($announcement['content'])) ?>
                  </div>
                  <div class="announcement-badges">
                    <span class="badge badge-priority-high">High Priority</span>
                    <span class="badge badge-type"><?= ucfirst($announcement['type']) ?></span>
                    <span class="badge badge-audience"><?= ucfirst($announcement['target_audience']) ?></span>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <!-- Normal Announcements -->
        <?php if (!empty($normal_announcements)): ?>
          <div class="priority-section priority-normal">
            <h3>
              <i class="fas fa-info-circle"></i>
              General Announcements
            </h3>
            <?php foreach ($normal_announcements as $announcement): ?>
              <div class="announcement-card">
                <div class="announcement-header">
                  <h4 class="announcement-title"><?= esc($announcement['title']) ?></h4>
                  <div class="announcement-meta">
                    <span><i class="fas fa-calendar"></i> <?= date('M j, Y g:i A', strtotime($announcement['published_at'])) ?></span>
                    <?php if ($announcement['expires_at']): ?>
                      <span><i class="fas fa-clock"></i> Expires <?= date('M j, Y', strtotime($announcement['expires_at'])) ?></span>
                    <?php endif; ?>
                  </div>
                </div>
                <div class="announcement-body">
                  <div class="announcement-content">
                    <?= nl2br(esc($announcement['content'])) ?>
                  </div>
                  <div class="announcement-badges">
                    <span class="badge badge-priority-<?= $announcement['priority'] ?>"><?= ucfirst($announcement['priority']) ?></span>
                    <span class="badge badge-type"><?= ucfirst($announcement['type']) ?></span>
                    <span class="badge badge-audience"><?= ucfirst($announcement['target_audience']) ?></span>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <!-- Empty State -->
        <?php if (empty($all_announcements)): ?>
          <div class="empty-state">
            <i class="fas fa-bullhorn"></i>
            <h3>No Announcements</h3>
            <p>There are no active announcements at this time. Check back later for updates!</p>
          </div>
        <?php endif; ?>

  <?php if (isset($is_student) && $is_student): ?>
      </div>

      <!-- Bottom Navigation for Students -->
      <nav class="bottom-nav">
        <a href="<?= base_url('user/dashboard') ?>" class="nav-item">
          <i class="fas fa-home"></i>
          <span>Home</span>
        </a>
        <a href="<?= base_url('user/payment-history') ?>" class="nav-item">
          <i class="fas fa-history"></i>
          <span>History</span>
        </a>
        <a href="<?= base_url('announcements/student-view') ?>" class="nav-item active">
          <i class="fas fa-bullhorn"></i>
          <span>Announcements</span>
        </a>
        <a href="<?= base_url('user/profile') ?>" class="nav-item">
          <i class="fas fa-user"></i>
          <span>Profile</span>
        </a>
      </nav>
    </div>
  <?php else: ?>
        </div>
      </main>
    </div>
  <?php endif; ?>

  <!-- JavaScript -->
  <script>
    // Search functionality
    document.addEventListener('DOMContentLoaded', function() {
      const searchInput = document.getElementById('searchInput');
      
      searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const announcementCards = document.querySelectorAll('.announcement-card');
        
        announcementCards.forEach(card => {
          const title = card.querySelector('.announcement-title').textContent.toLowerCase();
          const content = card.querySelector('.announcement-content').textContent.toLowerCase();
          
          if (title.includes(searchTerm) || content.includes(searchTerm)) {
            card.style.display = 'block';
          } else {
            card.style.display = searchTerm ? 'none' : 'block';
          }
        });
        
        // Update section visibility
        document.querySelectorAll('.priority-section').forEach(section => {
          const visibleCards = section.querySelectorAll('.announcement-card[style="display: block"], .announcement-card:not([style*="display: none"])');
          section.style.display = visibleCards.length > 0 ? 'block' : 'none';
        });
      });
    });

    // Toggle functions for admin
    function toggleProfileMenu() {
      // Implementation for profile menu toggle
    }

    function toggleUserMenu() {
      // Implementation for user menu toggle
    }

    function toggleNotifications() {
      // Implementation for notifications toggle
    }
  </script>

  <!-- Load appropriate JavaScript based on user type -->
  <?php if (isset($is_student) && $is_student): ?>
    <script src="<?= base_url('js/user_dashboard.js') ?>"></script>
  <?php else: ?>
    <script src="<?= base_url('js/dashboard.js') ?>"></script>
  <?php endif; ?>
</body>
</html>