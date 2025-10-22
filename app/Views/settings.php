<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>System Settings - ClearPay Admin</title>
  <link rel="stylesheet" href="<?= base_url('css/dashboard.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Essential CSS for settings elements -->
  <style>
    /* Force correct sidebar footer styling */
    .sidebar-footer {
      padding: 1rem !important;
      border-top: 1px solid rgba(255, 255, 255, 0.1) !important;
    }

    .sidebar-footer .user-profile {
      display: flex !important;
      align-items: center !important;
      gap: 0.75rem !important;
      position: relative !important;
    }

    .sidebar-footer .profile-avatar {
      width: 40px !important;
      height: 40px !important;
      background: linear-gradient(135deg, var(--primary-color), var(--success-color)) !important;
      border-radius: 50% !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      font-size: 1rem !important;
      color: white !important;
    }

    .sidebar-footer .profile-info {
      flex: 1 !important;
      transition: opacity var(--transition-normal) !important;
    }

    .sidebar-footer .profile-info h4 {
      font-size: 0.9rem !important;
      font-weight: 600 !important;
      color: var(--text-inverse) !important;
      margin: 0 !important;
    }

    .sidebar-footer .profile-info p {
      font-size: 0.75rem !important;
      color: rgba(255, 255, 255, 0.6) !important;
      margin: 0 !important;
    }

    .sidebar-footer .profile-menu-btn {
      background: none !important;
      border: none !important;
      color: rgba(255, 255, 255, 0.7) !important;
      cursor: pointer !important;
      padding: 0.5rem !important;
      border-radius: var(--radius-md) !important;
      transition: all var(--transition-fast) !important;
    }

    .sidebar-footer .profile-menu-btn:hover {
      background-color: rgba(255, 255, 255, 0.1) !important;
      color: var(--text-inverse) !important;
    }

    /* Toast notifications - proper positioning */
    .toast {
      position: fixed !important;
      top: 20px !important;
      right: 20px !important;
      background: white !important;
      border-radius: var(--radius-lg) !important;
      box-shadow: var(--shadow-lg) !important;
      padding: 1rem !important;
      min-width: 300px !important;
      z-index: 9999 !important;
      transform: translateX(100%) !important;
      transition: transform 0.3s ease !important;
      opacity: 0 !important;
      visibility: hidden !important;
    }

    .toast.show {
      transform: translateX(0) !important;
      opacity: 1 !important;
      visibility: visible !important;
    }

    .toast-content {
      display: flex !important;
      align-items: center !important;
      gap: 0.75rem !important;
    }

    .toast-text {
      flex: 1 !important;
    }

    .toast-title {
      display: block !important;
      font-weight: 600 !important;
      margin-bottom: 0.25rem !important;
    }

    .toast-message {
      display: block !important;
      font-size: 0.875rem !important;
      color: var(--text-secondary) !important;
    }

    .toast-close {
      background: none !important;
      border: none !important;
      color: var(--text-secondary) !important;
      cursor: pointer !important;
      padding: 0.25rem !important;
    }

    .toast.success {
      border-left: 4px solid var(--success-color) !important;
    }

    .toast.error {
      border-left: 4px solid var(--error-color) !important;
    }

    .stats-grid {
      display: grid !important;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)) !important;
      gap: 1.5rem !important;
      margin-bottom: 2rem !important;
    }

    /* Settings specific styles */
    .settings-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
      gap: 1.5rem;
    }

    .settings-card {
      background: var(--bg-primary);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow-sm);
      border: 1px solid var(--border-color);
      overflow: hidden;
      transition: var(--transition-fast);
    }

    .settings-card:hover {
      box-shadow: var(--shadow-md);
      border-color: var(--primary-color);
    }

    .card-header {
      padding: 1.5rem;
      border-bottom: 1px solid var(--border-color);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .card-title {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      margin: 0;
      font-size: 1.125rem;
      font-weight: 600;
      color: var(--text-primary);
    }

    .card-title i {
      color: var(--primary-color);
    }

    .card-content {
      padding: 1.5rem;
    }

    .setting-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem 0;
      border-bottom: 1px solid var(--border-color);
    }

    .setting-item:last-child {
      border-bottom: none;
    }

    .setting-info {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .setting-icon {
      width: 40px;
      height: 40px;
      background: var(--bg-secondary);
      border-radius: var(--radius-md);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--primary-color);
    }

    .setting-details h4 {
      margin: 0 0 0.25rem 0;
      font-size: 1rem;
      font-weight: 600;
      color: var(--text-primary);
    }

    .setting-details p {
      margin: 0;
      font-size: 0.875rem;
      color: var(--text-secondary);
    }

    .setting-control {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    /* Toggle Switch */
    .toggle-switch {
      position: relative;
      display: inline-block;
      width: 60px;
      height: 34px;
    }

    .toggle-switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    .toggle-slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: var(--border-color);
      transition: 0.4s;
      border-radius: 34px;
    }

    .toggle-slider:before {
      position: absolute;
      content: "";
      height: 26px;
      width: 26px;
      left: 4px;
      bottom: 4px;
      background-color: white;
      transition: 0.4s;
      border-radius: 50%;
    }

    input:checked + .toggle-slider {
      background-color: var(--primary-color);
    }

    input:checked + .toggle-slider:before {
      transform: translateX(26px);
    }

    /* Select styling */
    .setting-select {
      padding: 0.5rem 1rem;
      border: 1px solid var(--border-color);
      border-radius: var(--radius-md);
      background: var(--bg-primary);
      color: var(--text-primary);
      font-size: 0.875rem;
      min-width: 120px;
    }

    .setting-select:focus {
      outline: none;
      border-color: var(--primary-color);
      box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.1);
    }

    /* Button styling */
    .btn-settings {
      padding: 0.5rem 1rem;
      border: 1px solid var(--border-color);
      border-radius: var(--radius-md);
      background: var(--bg-primary);
      color: var(--text-primary);
      font-size: 0.875rem;
      cursor: pointer;
      transition: var(--transition-fast);
    }

    .btn-settings:hover {
      background: var(--bg-secondary);
      border-color: var(--primary-color);
    }

    .btn-settings.primary {
      background: var(--primary-color);
      color: white;
      border-color: var(--primary-color);
    }

    .btn-settings.primary:hover {
      background: var(--primary-hover);
    }

    .btn-settings.danger {
      background: var(--error-color);
      color: white;
      border-color: var(--error-color);
    }

    .btn-settings.danger:hover {
      background: var(--error-hover);
    }

    /* Status indicators */
    .status-indicator {
      padding: 0.25rem 0.75rem;
      border-radius: var(--radius-sm);
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
    }

    .status-indicator.active {
      background: var(--success-bg);
      color: var(--success-color);
    }

    .status-indicator.inactive {
      background: var(--error-bg);
      color: var(--error-color);
    }

    .status-indicator.pending {
      background: var(--warning-bg);
      color: var(--warning-color);
    }

    /* System info section */
    .system-info {
      background: var(--bg-secondary);
      border-radius: var(--radius-md);
      padding: 1rem;
      margin-top: 1rem;
    }

    .system-info-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
    }

    .info-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0.5rem 0;
    }

    .info-label {
      font-weight: 500;
      color: var(--text-secondary);
    }

    .info-value {
      color: var(--text-primary);
      font-weight: 600;
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
    
    /* Sidebar profile avatar styles */
    .sidebar-footer .profile-avatar img {
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
          <li class="nav-item active">
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
            <?php if (!empty($profilePictureUrl)): ?>
              <img src="<?= esc($profilePictureUrl) ?>" alt="Profile Picture">
            <?php else: ?>
              <i class="fas fa-user"></i>
            <?php endif; ?>
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
          <h1 class="page-title">System Settings</h1>
          <p class="page-subtitle">Configure system preferences and administrative settings</p>
        </div>
        
        <div class="header-right">
          <!-- Search Bar -->
          <div class="search-container">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Search settings..." class="search-input">
          </div>
          
          <!-- Notifications -->
          <div class="notification-center">
            <button class="notification-btn" onclick="toggleNotifications()">
              <i class="fas fa-bell"></i>
              <span class="notification-count">2</span>
            </button>
            
            <!-- Notification Dropdown -->
            <div class="notification-dropdown" id="notificationDropdown">
              <div class="notification-header">
                <h3>Notifications</h3>
                <button class="mark-read-btn">Mark all read</button>
              </div>
              <div class="notification-list">
                <div class="notification-item unread">
                  <div class="notification-icon warning">
                    <i class="fas fa-exclamation-triangle"></i>
                  </div>
                  <div class="notification-content">
                    <h4>System Update Available</h4>
                    <p>A new version is available for download</p>
                    <span class="notification-time">1 hour ago</span>
                  </div>
                </div>
                <div class="notification-item">
                  <div class="notification-icon info">
                    <i class="fas fa-server"></i>
                  </div>
                  <div class="notification-content">
                    <h4>Database Backup Complete</h4>
                    <p>Daily backup completed successfully</p>
                    <span class="notification-time">3 hours ago</span>
                  </div>
                </div>
              </div>
              <div class="notification-footer">
                <a href="#" class="view-all-btn">View all notifications</a>
              </div>
            </div>
          </div>
          
          <!-- User Menu -->
          <div class="user-menu">
            <button class="user-menu-btn" onclick="toggleUserMenu()">
              <div class="user-avatar">
                <?php if (!empty($profilePictureUrl)): ?>
                  <img src="<?= esc($profilePictureUrl) ?>" alt="Profile Picture">
                <?php else: ?>
                  <i class="fas fa-user"></i>
                <?php endif; ?>
              </div>
              <span class="user-name"><?= esc($name ? explode(' ', $name)[0] : 'Admin') ?></span>
              <i class="fas fa-chevron-down"></i>
            </button>
            
            <!-- User Dropdown -->
            <div class="user-dropdown" id="userDropdown">
              <div class="dropdown-header">
                <div class="user-info">
                  <h4><?= esc($name ?? 'Admin User') ?></h4>
                  <p><?= esc($email ?? 'admin@clearpay.com') ?></p>
                </div>
              </div>
              <div class="dropdown-menu">
                <a href="<?= base_url('profile') ?>" class="dropdown-item">
                  <i class="fas fa-user"></i>
                  <span>Profile</span>
                </a>
                <a href="<?= base_url('settings') ?>" class="dropdown-item">
                  <i class="fas fa-cog"></i>
                  <span>Settings</span>
                </a>
                <a href="#" class="dropdown-item">
                  <i class="fas fa-question-circle"></i>
                  <span>Help</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="<?= base_url('logout') ?>" class="dropdown-item logout">
                  <i class="fas fa-sign-out-alt"></i>
                  <span>Sign Out</span>
                </a>
              </div>
            </div>
          </div>
        </div>
      </header>

      <!-- Settings Content -->
      <div class="dashboard-content">
        
        <!-- Settings Grid -->
        <div class="settings-grid">
          
          <!-- System Configuration Card -->
          <div class="settings-card">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-server"></i>
                System Configuration
              </h3>
            </div>
            <div class="card-content">
              <div class="setting-item">
                <div class="setting-info">
                  <div class="setting-icon">
                    <i class="fas fa-shield-alt"></i>
                  </div>
                  <div class="setting-details">
                    <h4>Maintenance Mode</h4>
                    <p>Enable to restrict system access during updates</p>
                  </div>
                </div>
                <div class="setting-control">
                  <label class="toggle-switch">
                    <input type="checkbox" id="maintenanceMode">
                    <span class="toggle-slider"></span>
                  </label>
                </div>
              </div>
              
              <div class="setting-item">
                <div class="setting-info">
                  <div class="setting-icon">
                    <i class="fas fa-database"></i>
                  </div>
                  <div class="setting-details">
                    <h4>Automatic Backups</h4>
                    <p>Schedule regular database backups</p>
                  </div>
                </div>
                <div class="setting-control">
                  <label class="toggle-switch">
                    <input type="checkbox" id="autoBackups" checked>
                    <span class="toggle-slider"></span>
                  </label>
                </div>
              </div>
              
              <div class="setting-item">
                <div class="setting-info">
                  <div class="setting-icon">
                    <i class="fas fa-clock"></i>
                  </div>
                  <div class="setting-details">
                    <h4>Backup Frequency</h4>
                    <p>How often to create backups</p>
                  </div>
                </div>
                <div class="setting-control">
                  <select class="setting-select" id="backupFrequency">
                    <option value="daily" selected>Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                  </select>
                </div>
              </div>
              
              <div class="setting-item">
                <div class="setting-info">
                  <div class="setting-icon">
                    <i class="fas fa-history"></i>
                  </div>
                  <div class="setting-details">
                    <h4>Backup Retention</h4>
                    <p>Number of backups to keep</p>
                  </div>
                </div>
                <div class="setting-control">
                  <select class="setting-select" id="backupRetention">
                    <option value="7">7 backups</option>
                    <option value="14" selected>14 backups</option>
                    <option value="30">30 backups</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <!-- Payment Settings Card -->
          <div class="settings-card">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-credit-card"></i>
                Payment Settings
              </h3>
            </div>
            <div class="card-content">
              <div class="setting-item">
                <div class="setting-info">
                  <div class="setting-icon">
                    <i class="fas fa-qrcode"></i>
                  </div>
                  <div class="setting-details">
                    <h4>QR Code Generation</h4>
                    <p>Enable QR receipt generation for payments</p>
                  </div>
                </div>
                <div class="setting-control">
                  <label class="toggle-switch">
                    <input type="checkbox" id="qrGeneration" checked>
                    <span class="toggle-slider"></span>
                  </label>
                </div>
              </div>
              
              <div class="setting-item">
                <div class="setting-info">
                  <div class="setting-icon">
                    <i class="fas fa-bell"></i>
                  </div>
                  <div class="setting-details">
                    <h4>Payment Notifications</h4>
                    <p>Send notifications for payment events</p>
                  </div>
                </div>
                <div class="setting-control">
                  <label class="toggle-switch">
                    <input type="checkbox" id="paymentNotifications" checked>
                    <span class="toggle-slider"></span>
                  </label>
                </div>
              </div>
              
              <div class="setting-item">
                <div class="setting-info">
                  <div class="setting-icon">
                    <i class="fas fa-percent"></i>
                  </div>
                  <div class="setting-details">
                    <h4>Partial Payment Threshold</h4>
                    <p>Minimum percentage for partial payments</p>
                  </div>
                </div>
                <div class="setting-control">
                  <select class="setting-select" id="partialThreshold">
                    <option value="10">10%</option>
                    <option value="25" selected>25%</option>
                    <option value="50">50%</option>
                  </select>
                </div>
              </div>
              
              <div class="setting-item">
                <div class="setting-info">
                  <div class="setting-icon">
                    <i class="fas fa-calendar"></i>
                  </div>
                  <div class="setting-details">
                    <h4>Payment Due Period</h4>
                    <p>Days before payment is considered overdue</p>
                  </div>
                </div>
                <div class="setting-control">
                  <select class="setting-select" id="duePeriod">
                    <option value="7">7 days</option>
                    <option value="14">14 days</option>
                    <option value="30" selected>30 days</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <!-- Security Settings Card -->
          <div class="settings-card">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-lock"></i>
                Security Settings
              </h3>
            </div>
            <div class="card-content">
              <div class="setting-item">
                <div class="setting-info">
                  <div class="setting-icon">
                    <i class="fas fa-user-shield"></i>
                  </div>
                  <div class="setting-details">
                    <h4>Two-Factor Authentication</h4>
                    <p>Require 2FA for admin access</p>
                  </div>
                </div>
                <div class="setting-control">
                  <label class="toggle-switch">
                    <input type="checkbox" id="twoFactorAuth">
                    <span class="toggle-slider"></span>
                  </label>
                </div>
              </div>
              
              <div class="setting-item">
                <div class="setting-info">
                  <div class="setting-icon">
                    <i class="fas fa-clock"></i>
                  </div>
                  <div class="setting-details">
                    <h4>Session Timeout</h4>
                    <p>Automatic logout after inactivity</p>
                  </div>
                </div>
                <div class="setting-control">
                  <select class="setting-select" id="sessionTimeout">
                    <option value="15">15 minutes</option>
                    <option value="30" selected>30 minutes</option>
                    <option value="60">1 hour</option>
                    <option value="120">2 hours</option>
                  </select>
                </div>
              </div>
              
              <div class="setting-item">
                <div class="setting-info">
                  <div class="setting-icon">
                    <i class="fas fa-key"></i>
                  </div>
                  <div class="setting-details">
                    <h4>Password Policy</h4>
                    <p>Enforce strong password requirements</p>
                  </div>
                </div>
                <div class="setting-control">
                  <label class="toggle-switch">
                    <input type="checkbox" id="passwordPolicy" checked>
                    <span class="toggle-slider"></span>
                  </label>
                </div>
              </div>
              
              <div class="setting-item">
                <div class="setting-info">
                  <div class="setting-icon">
                    <i class="fas fa-history"></i>
                  </div>
                  <div class="setting-details">
                    <h4>Activity Logging</h4>
                    <p>Log all user activities and changes</p>
                  </div>
                </div>
                <div class="setting-control">
                  <label class="toggle-switch">
                    <input type="checkbox" id="activityLogging" checked>
                    <span class="toggle-slider"></span>
                  </label>
                </div>
              </div>
            </div>
          </div>

          <!-- Email Settings Card -->
          <div class="settings-card">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-envelope"></i>
                Email Settings
              </h3>
            </div>
            <div class="card-content">
              <div class="setting-item">
                <div class="setting-info">
                  <div class="setting-icon">
                    <i class="fas fa-server"></i>
                  </div>
                  <div class="setting-details">
                    <h4>SMTP Configuration</h4>
                    <p>Email server settings</p>
                  </div>
                </div>
                <div class="setting-control">
                  <button class="btn-settings" onclick="configureEmail()">Configure</button>
                </div>
              </div>
              
              <div class="setting-item">
                <div class="setting-info">
                  <div class="setting-icon">
                    <i class="fas fa-paper-plane"></i>
                  </div>
                  <div class="setting-details">
                    <h4>Email Notifications</h4>
                    <p>Send email alerts for system events</p>
                  </div>
                </div>
                <div class="setting-control">
                  <label class="toggle-switch">
                    <input type="checkbox" id="emailNotifications" checked>
                    <span class="toggle-slider"></span>
                  </label>
                </div>
              </div>
              
              <div class="setting-item">
                <div class="setting-info">
                  <div class="setting-icon">
                    <i class="fas fa-envelope-open"></i>
                  </div>
                  <div class="setting-details">
                    <h4>Email Templates</h4>
                    <p>Customize notification email templates</p>
                  </div>
                </div>
                <div class="setting-control">
                  <button class="btn-settings" onclick="manageTemplates()">Manage</button>
                </div>
              </div>
              
              <div class="setting-item">
                <div class="setting-info">
                  <div class="setting-icon">
                    <i class="fas fa-vial"></i>
                  </div>
                  <div class="setting-details">
                    <h4>Test Email</h4>
                    <p>Send a test email to verify configuration</p>
                  </div>
                </div>
                <div class="setting-control">
                  <button class="btn-settings primary" onclick="testEmail()">Send Test</button>
                </div>
              </div>
            </div>
          </div>

          <!-- Application Settings Card -->
          <div class="settings-card">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-cogs"></i>
                Application Settings
              </h3>
            </div>
            <div class="card-content">
              <div class="setting-item">
                <div class="setting-info">
                  <div class="setting-icon">
                    <i class="fas fa-palette"></i>
                  </div>
                  <div class="setting-details">
                    <h4>Default Theme</h4>
                    <p>System-wide default theme</p>
                  </div>
                </div>
                <div class="setting-control">
                  <select class="setting-select" id="defaultTheme">
                    <option value="light" selected>Light</option>
                    <option value="dark">Dark</option>
                    <option value="auto">Auto</option>
                  </select>
                </div>
              </div>
              
              <div class="setting-item">
                <div class="setting-info">
                  <div class="setting-icon">
                    <i class="fas fa-language"></i>
                  </div>
                  <div class="setting-details">
                    <h4>Default Language</h4>
                    <p>System default language</p>
                  </div>
                </div>
                <div class="setting-control">
                  <select class="setting-select" id="defaultLanguage">
                    <option value="en" selected>English</option>
                    <option value="fil">Filipino</option>
                    <option value="es">Spanish</option>
                  </select>
                </div>
              </div>
              
              <div class="setting-item">
                <div class="setting-info">
                  <div class="setting-icon">
                    <i class="fas fa-clock"></i>
                  </div>
                  <div class="setting-details">
                    <h4>Timezone</h4>
                    <p>System timezone setting</p>
                  </div>
                </div>
                <div class="setting-control">
                  <select class="setting-select" id="timezone">
                    <option value="Asia/Manila" selected>Asia/Manila (GMT+8)</option>
                    <option value="UTC">UTC (GMT+0)</option>
                    <option value="America/New_York">Eastern Time (GMT-5)</option>
                  </select>
                </div>
              </div>
              
              <div class="setting-item">
                <div class="setting-info">
                  <div class="setting-icon">
                    <i class="fas fa-money-bill"></i>
                  </div>
                  <div class="setting-details">
                    <h4>Default Currency</h4>
                    <p>System currency format</p>
                  </div>
                </div>
                <div class="setting-control">
                  <select class="setting-select" id="defaultCurrency">
                    <option value="PHP" selected>Philippine Peso (₱)</option>
                    <option value="USD">US Dollar ($)</option>
                    <option value="EUR">Euro (€)</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <!-- System Information Card -->
          <div class="settings-card">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-info-circle"></i>
                System Information
              </h3>
              <div class="setting-control">
                <span class="status-indicator active">Online</span>
              </div>
            </div>
            <div class="card-content">
              <div class="system-info">
                <div class="system-info-grid">
                  <div class="info-item">
                    <span class="info-label">System Version:</span>
                    <span class="info-value">ClearPay v1.0.0</span>
                  </div>
                  <div class="info-item">
                    <span class="info-label">PHP Version:</span>
                    <span class="info-value"><?= phpversion() ?></span>
                  </div>
                  <div class="info-item">
                    <span class="info-label">CodeIgniter:</span>
                    <span class="info-value">4.x</span>
                  </div>
                  <div class="info-item">
                    <span class="info-label">Database:</span>
                    <span class="info-value">MySQL</span>
                  </div>
                  <div class="info-item">
                    <span class="info-label">Last Backup:</span>
                    <span class="info-value"><?= date('M j, Y g:i A', strtotime('-2 hours')) ?></span>
                  </div>
                  <div class="info-item">
                    <span class="info-label">Uptime:</span>
                    <span class="info-value">24 days, 6 hours</span>
                  </div>
                </div>
              </div>
              
              <div class="setting-item" style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                <div class="setting-info">
                  <div class="setting-icon">
                    <i class="fas fa-download"></i>
                  </div>
                  <div class="setting-details">
                    <h4>System Logs</h4>
                    <p>Download system log files for analysis</p>
                  </div>
                </div>
                <div class="setting-control">
                  <button class="btn-settings" onclick="downloadLogs()">Download</button>
                </div>
              </div>
              
              <div class="setting-item">
                <div class="setting-info">
                  <div class="setting-icon">
                    <i class="fas fa-database"></i>
                  </div>
                  <div class="setting-details">
                    <h4>Create Backup</h4>
                    <p>Manually create a database backup</p>
                  </div>
                </div>
                <div class="setting-control">
                  <button class="btn-settings primary" onclick="createBackup()">Create Backup</button>
                </div>
              </div>
              
              <div class="setting-item">
                <div class="setting-info">
                  <div class="setting-icon">
                    <i class="fas fa-trash"></i>
                  </div>
                  <div class="setting-details">
                    <h4>Clear Cache</h4>
                    <p>Clear system cache and temporary files</p>
                  </div>
                </div>
                <div class="setting-control">
                  <button class="btn-settings danger" onclick="clearCache()">Clear Cache</button>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

    </main>
  </div>

  <!-- Success Toast -->
  <div class="toast success" id="successToast">
    <div class="toast-content">
      <i class="fas fa-check-circle"></i>
      <div class="toast-text">
        <span class="toast-title">Success!</span>
        <span class="toast-message">Settings updated successfully</span>
      </div>
    </div>
    <button class="toast-close" onclick="hideToast('successToast')">
      <i class="fas fa-times"></i>
    </button>
  </div>

  <!-- Error Toast -->
  <div class="toast error" id="errorToast">
    <div class="toast-content">
      <i class="fas fa-exclamation-circle"></i>
      <div class="toast-text">
        <span class="toast-title">Error!</span>
        <span class="toast-message">Failed to update settings</span>
      </div>
    </div>
    <button class="toast-close" onclick="hideToast('errorToast')">
      <i class="fas fa-times"></i>
    </button>
  </div>

  <script>
    // Sidebar functionality
    // Notifications functionality
    function toggleNotifications() {
      const dropdown = document.getElementById('notificationDropdown');
      const userDropdown = document.getElementById('userDropdown');
      
      // Close user dropdown if open
      if (userDropdown) {
        userDropdown.classList.remove('active');
      }
      
      dropdown.classList.toggle('active');
    }

    // User menu functionality
    function toggleUserMenu() {
      const dropdown = document.getElementById('userDropdown');
      const notificationDropdown = document.getElementById('notificationDropdown');
      
      // Close notification dropdown if open
      if (notificationDropdown) {
        notificationDropdown.classList.remove('active');
      }
      
      dropdown.classList.toggle('active');
    }

    // Profile menu functionality
    function toggleProfileMenu() {
      // Add functionality for profile menu in sidebar footer
      console.log('Profile menu toggled');
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
      const notificationDropdown = document.getElementById('notificationDropdown');
      const userDropdown = document.getElementById('userDropdown');
      const notificationBtn = document.querySelector('.notification-btn');
      const userMenuBtn = document.querySelector('.user-menu-btn');
      
      if (notificationDropdown && !notificationBtn.contains(event.target) && !notificationDropdown.contains(event.target)) {
        notificationDropdown.classList.remove('active');
      }
      
      if (userDropdown && !userMenuBtn.contains(event.target) && !userDropdown.contains(event.target)) {
        userDropdown.classList.remove('active');
      }
    });

    // Toast notifications
    function showToast(toastId, message) {
      const toast = document.getElementById(toastId);
      const messageEl = toast.querySelector('.toast-message');
      messageEl.textContent = message;
      
      toast.classList.add('show');
      
      setTimeout(() => {
        hideToast(toastId);
      }, 5000);
    }

    function hideToast(toastId) {
      document.getElementById(toastId).classList.remove('show');
    }

    // Settings functions
    function configureEmail() {
      showToast('successToast', 'Email configuration dialog opened');
      // Open email configuration modal
    }

    function manageTemplates() {
      showToast('successToast', 'Email template management opened');
      // Open template management interface
    }

    function testEmail() {
      showToast('successToast', 'Test email sent successfully');
      // Send test email
    }

    function downloadLogs() {
      showToast('successToast', 'System logs download started');
      // Download logs functionality
    }

    function createBackup() {
      showToast('successToast', 'Database backup created successfully');
      // Create backup functionality
    }

    function clearCache() {
      showToast('successToast', 'System cache cleared successfully');
      // Clear cache functionality
    }

    // Settings change handlers
    document.addEventListener('change', function(e) {
      if (e.target.type === 'checkbox' || e.target.tagName === 'SELECT') {
        // Save setting change
        showToast('successToast', 'Setting updated successfully');
      }
    });

    // Search functionality
    const searchInput = document.querySelector('.search-input');
    searchInput?.addEventListener('input', function(e) {
      const searchTerm = e.target.value.toLowerCase();
      const settingsCards = document.querySelectorAll('.settings-card');
      
      settingsCards.forEach(card => {
        const title = card.querySelector('h3').textContent.toLowerCase();
        const content = card.textContent.toLowerCase();
        
        if (title.includes(searchTerm) || content.includes(searchTerm)) {
          card.style.display = 'block';
        } else {
          card.style.display = searchTerm ? 'none' : 'block';
        }
      });
    });

    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
      console.log('System Settings page loaded');
    });
    
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
  
  <!-- Dashboard JavaScript -->
  <script src="<?= base_url('js/dashboard.js') ?>"></script>
</body>
</html>