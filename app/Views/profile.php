<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile Settings - ClearPay Admin</title>
  <link rel="stylesheet" href="<?= base_url('css/dashboard.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Essential CSS for modal and profile elements -->
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

    /* Modal Styles - Essential for avatar upload */
    .modal-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 1000;
    }

    .modal-overlay.active {
      display: flex;
    }

    .modal-container {
      background: var(--bg-primary);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow-lg);
      max-width: 500px;
      width: 90%;
      max-height: 80vh;
      overflow-y: auto;
    }

    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1.5rem;
      border-bottom: 1px solid var(--border-color);
    }

    .modal-header h3 {
      margin: 0;
      font-size: 1.25rem;
      font-weight: 600;
      color: var(--text-primary);
    }

    .modal-close {
      background: none;
      border: none;
      color: var(--text-secondary);
      cursor: pointer;
      padding: 0.5rem;
      border-radius: var(--radius-sm);
      transition: var(--transition-fast);
    }

    .modal-close:hover {
      background: var(--bg-secondary);
      color: var(--text-primary);
    }

    .modal-content {
      padding: 1.5rem;
    }

    .modal-footer {
      display: flex;
      justify-content: flex-end;
      gap: 1rem;
      padding: 1.5rem;
      border-top: 1px solid var(--border-color);
    }

    .avatar-upload-area {
      text-align: center;
    }

    .upload-preview {
      margin-bottom: 2rem;
    }

    .preview-avatar {
      width: 120px;
      height: 120px;
      background: var(--bg-secondary);
      border: 2px dashed var(--border-color);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto;
      font-size: 2.5rem;
      color: var(--text-secondary);
      transition: var(--transition-fast);
    }

    .preview-avatar:hover {
      border-color: var(--primary-color);
      color: var(--primary-color);
    }

    .upload-options {
      display: flex;
      justify-content: center;
      gap: 1rem;
      margin-bottom: 1.5rem;
    }

    .upload-note {
      font-size: 0.875rem;
      color: var(--text-secondary);
      margin: 0;
    }

    /* Profile Summary Styles */
    .profile-summary {
      display: flex;
      align-items: center;
      gap: 1.5rem;
      margin-bottom: 2rem;
    }
    
    .profile-avatar {
      width: 80px;
      height: 80px;
      background: var(--primary-color);
      color: white;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      flex-shrink: 0;
      transition: var(--transition-fast);
    }

    .profile-avatar:hover {
      background: var(--primary-hover);
      transform: scale(1.05);
    }
    
    .profile-details h2 {
      margin: 0 0 0.5rem 0;
      font-size: 1.5rem;
      font-weight: 600;
      color: var(--text-primary);
    }
    
    .profile-role {
      margin: 0 0 0.25rem 0;
      font-weight: 500;
      color: var(--primary-color);
    }
    
    .profile-email {
      margin: 0;
      color: var(--text-secondary);
    }

    /* Form Styles for Personal Information */
    .profile-form-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1rem;
    }
    
    .form-group {
      display: flex;
      flex-direction: column;
    }
    
    .form-group label {
      margin-bottom: 0.5rem;
      font-weight: 500;
      color: var(--text-primary);
    }
    
    .form-input {
      padding: 0.75rem;
      border: 1px solid var(--border-color);
      border-radius: var(--radius-md);
      font-size: 0.875rem;
      transition: var(--transition-fast);
      background: var(--bg-primary);
      color: var(--text-primary);
    }
    
    .form-input:focus {
      outline: none;
      border-color: var(--primary-color);
      box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.1);
    }
    
    .form-input:read-only {
      background: var(--bg-secondary);
      color: var(--text-secondary);
    }

    .form-actions {
      display: flex;
      gap: 1rem;
      justify-content: flex-end;
      margin-top: 1.5rem;
    }

    .btn-sm {
      padding: 0.5rem 1rem;
      font-size: 0.875rem;
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
          <li class="nav-item active">
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
          <h1 class="page-title">Profile Settings</h1>
          <p class="page-subtitle">Manage your account information and security settings</p>
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
              <span class="notification-count">3</span>
            </button>
            
            <!-- Notification Dropdown -->
            <div class="notification-dropdown" id="notificationDropdown">
              <div class="notification-header">
                <h3>Notifications</h3>
                <button class="mark-read-btn">Mark all read</button>
              </div>
              <div class="notification-list">
                <div class="notification-item unread">
                  <div class="notification-icon success">
                    <i class="fas fa-check-circle"></i>
                  </div>
                  <div class="notification-content">
                    <h4>Settings Updated</h4>
                    <p>Your profile information has been saved successfully</p>
                    <span class="notification-time">Just now</span>
                  </div>
                </div>
                <div class="notification-item">
                  <div class="notification-icon primary">
                    <i class="fas fa-user-shield"></i>
                  </div>
                  <div class="notification-content">
                    <h4>Security Alert</h4>
                    <p>Password changed successfully</p>
                    <span class="notification-time">2 weeks ago</span>
                  </div>
                </div>
                <div class="notification-item">
                  <div class="notification-icon warning">
                    <i class="fas fa-exclamation-triangle"></i>
                  </div>
                  <div class="notification-content">
                    <h4>Login Alert</h4>
                    <p>New login from Windows device</p>
                    <span class="notification-time">2 hours ago</span>
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
                <i class="fas fa-user"></i>
              </div>
              <span class="user-name"><?= esc(session()->get('name') ? explode(' ', session()->get('name'))[0] : 'Admin') ?></span>
              <i class="fas fa-chevron-down"></i>
            </button>
            
            <!-- User Dropdown -->
            <div class="user-dropdown" id="userDropdown">
              <div class="dropdown-header">
                <div class="user-info">
                  <h4><?= esc(session()->get('name') ?? 'Admin User') ?></h4>
                  <p><?= esc(session()->get('email') ?? 'admin@clearpay.com') ?></p>
                </div>
              </div>
              <div class="dropdown-menu">
                <a href="<?= base_url('profile') ?>" class="dropdown-item">
                  <i class="fas fa-user"></i>
                  <span>Profile</span>
                </a>
                <a href="#" class="dropdown-item">
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

      <!-- Dashboard Content -->
      <div class="dashboard-content">
        
        <!-- Statistics Cards -->
        <div class="stats-grid">
          <div class="stat-card primary">
            <div class="stat-content">
              <div class="stat-header">
                <h3>Total Payments</h3>
                <div class="stat-icon">
                  <i class="fas fa-credit-card"></i>
                </div>
              </div>
              <div class="stat-value">45</div>
              <div class="stat-footer">
                <span class="stat-change positive">
                  <i class="fas fa-arrow-up"></i>
                  +12%
                </span>
                <span class="stat-period">this month</span>
              </div>
            </div>
          </div>
          
          <div class="stat-card success">
            <div class="stat-content">
              <div class="stat-header">
                <h3>Total Amount</h3>
                <div class="stat-icon">
                  <i class="fas fa-dollar-sign"></i>
                </div>
              </div>
              <div class="stat-value">₱125,430</div>
              <div class="stat-footer">
                <span class="stat-change positive">
                  <i class="fas fa-arrow-up"></i>
                  +8%
                </span>
                <span class="stat-period">this month</span>
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
              <div class="stat-value">12</div>
              <div class="stat-footer">
                <span class="stat-change positive">
                  <i class="fas fa-arrow-up"></i>
                  +3
                </span>
                <span class="stat-period">this week</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Profile Overview Section -->
        <div class="dashboard-grid">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-user-circle"></i>
                Profile Overview
              </h3>
            </div>
            <div class="card-content">
              <div class="profile-summary">
                <div class="profile-avatar" onclick="openAvatarModal()" style="cursor: pointer;" title="Click to change profile picture">
                  <i class="fas fa-user"></i>
                </div>
                <div class="profile-details">
                  <h2><?= esc(session()->get('name') ?? 'Admin User') ?></h2>
                  <p class="profile-role">System Administrator</p>
                  <p class="profile-email"><?= esc(session()->get('email') ?? 'admin@example.com') ?></p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Settings Cards Grid -->
        <div class="stats-grid">
          
          <!-- Personal Information Card -->
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-user"></i>
                Personal Information
              </h3>
              <div class="card-actions">
                <button class="btn btn-secondary btn-sm" onclick="toggleEditMode('personal')">
                  <i class="fas fa-edit"></i>
                  Edit
                </button>
              </div>
            </div>
            <div class="card-content">
              <form id="personalInfoForm" class="settings-form">
                <div class="profile-form-grid">
                  <div class="form-group">
                    <label for="fullName">Full Name</label>
                    <input type="text" id="fullName" name="full_name" value="<?= esc(session()->get('name') ?? 'John Doe') ?>" readonly class="form-input">
                  </div>
                  
                  <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?= esc(session()->get('username') ?? 'johndoe') ?>" readonly class="form-input">
                  </div>
                  
                  <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="<?= esc(session()->get('email') ?? 'john@example.com') ?>" readonly class="form-input">
                  </div>
                  
                  <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" value="<?= esc($phone ?? '+1 234 567 8900') ?>" readonly class="form-input">
                  </div>
                  
                  <div class="form-group">
                    <label for="department">Department</label>
                    <input type="text" id="department" name="department" value="Finance & Administration" readonly class="form-input">
                  </div>
                  
                  <div class="form-group">
                    <label for="position">Position</label>
                    <input type="text" id="position" name="position" value="System Administrator" readonly class="form-input">
                  </div>
                </div>
                
                <div class="form-actions" id="personalActions" style="display: none; margin-top: 1rem;">
                  <button type="button" class="btn btn-secondary btn-sm" onclick="cancelEdit('personal')">Cancel</button>
                  <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
          <!-- Security Settings Card -->
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-shield-alt"></i>
                Security Settings
              </h3>
              <div class="card-actions">
                <button class="btn btn-secondary btn-sm" onclick="toggleEditMode('security')">
                  <i class="fas fa-edit"></i>
                  Edit
                </button>
              </div>
            </div>
            <div class="card-content">
              <form id="securityForm" class="settings-form">
                <div class="profile-form-grid">
                  <div class="form-group" style="grid-column: 1 / -1;">
                    <label for="currentPassword">Current Password</label>
                    <div class="password-input">
                      <input type="password" id="currentPassword" name="current_password" placeholder="Enter current password" readonly class="form-input">
                      <button type="button" class="password-toggle" onclick="togglePassword('currentPassword')">
                        <i class="fas fa-eye"></i>
                      </button>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="newPassword">New Password</label>
                    <div class="password-input">
                      <input type="password" id="newPassword" name="new_password" placeholder="Enter new password" readonly class="form-input">
                      <button type="button" class="password-toggle" onclick="togglePassword('newPassword')">
                        <i class="fas fa-eye"></i>
                      </button>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="confirmPassword">Confirm Password</label>
                    <div class="password-input">
                      <input type="password" id="confirmPassword" name="confirm_password" placeholder="Confirm new password" readonly class="form-input">
                      <button type="button" class="password-toggle" onclick="togglePassword('confirmPassword')">
                        <i class="fas fa-eye"></i>
                      </button>
                    </div>
                  </div>
                </div>
                
                <div class="security-info" style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                  <div class="info-item" style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                    <i class="fas fa-check-circle" style="color: var(--success-color);"></i>
                    <span>Last password change: 2 weeks ago</span>
                  </div>
                  <div class="info-item" style="display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-clock" style="color: var(--info-color);"></i>
                    <span>Last login: Today at 9:30 AM</span>
                  </div>
                </div>
                
                <div class="form-actions" id="securityActions" style="display: none; margin-top: 1rem;">
                  <button type="button" class="btn btn-secondary btn-sm" onclick="cancelEdit('security')">Cancel</button>
                  <button type="submit" class="btn btn-primary btn-sm">Update Password</button>
                </div>
              </form>
            </div>
          </div>

          <!-- Preferences Card -->
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-cog"></i>
                Preferences
              </h3>
            </div>
            <div class="card-content">
              <div class="preference-list">
                <div class="preference-item" style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; border-bottom: 1px solid var(--border-color);">
                  <div class="preference-info" style="display: flex; align-items: center; gap: 1rem;">
                    <i class="fas fa-moon" style="color: var(--primary-color);"></i>
                    <div>
                      <h4 style="margin: 0; font-size: 1rem; font-weight: 600;">Dark Mode</h4>
                      <p style="margin: 0; color: var(--text-secondary); font-size: 0.875rem;">Enable dark theme for better night viewing</p>
                    </div>
                  </div>
                  <label class="toggle-switch">
                    <input type="checkbox" id="darkMode">
                    <span class="toggle-slider"></span>
                  </label>
                </div>
                
                <div class="preference-item" style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; border-bottom: 1px solid var(--border-color);">
                  <div class="preference-info" style="display: flex; align-items: center; gap: 1rem;">
                    <i class="fas fa-bell" style="color: var(--warning-color);"></i>
                    <div>
                      <h4 style="margin: 0; font-size: 1rem; font-weight: 600;">Email Notifications</h4>
                      <p style="margin: 0; color: var(--text-secondary); font-size: 0.875rem;">Receive email alerts for important events</p>
                    </div>
                  </div>
                  <label class="toggle-switch">
                    <input type="checkbox" id="emailNotifications" checked>
                    <span class="toggle-slider"></span>
                  </label>
                </div>
                
                <div class="preference-item">
                  <div class="preference-info">
                    <i class="fas fa-mobile-alt"></i>
                    <div>
                      <h4>SMS Notifications</h4>
                      <p>Get SMS alerts for critical updates</p>
                    </div>
                  </div>
                  <label class="toggle-switch">
                    <input type="checkbox" id="smsNotifications">
                    <span class="toggle-slider"></span>
                  </label>
                </div>
                
                <div class="preference-item">
                  <div class="preference-info">
                    <i class="fas fa-language"></i>
                    <div>
                      <h4>Language</h4>
                      <p>Select your preferred language</p>
                    </div>
                  </div>
                  <select class="preference-select">
                    <option value="en">English</option>
                    <option value="fil">Filipino</option>
                    <option value="es">Spanish</option>
                  </select>
                </div>
                
                <div class="preference-item">
                  <div class="preference-info">
                    <i class="fas fa-money-bill"></i>
                    <div>
                      <h4>Currency Format</h4>
                      <p>Default currency display format</p>
                    </div>
                  </div>
                  <select class="preference-select">
                    <option value="php">PHP (₱)</option>
                    <option value="usd">USD ($)</option>
                    <option value="eur">EUR (€)</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <!-- Activity Log Card -->
          <div class="settings-card">
            <div class="card-header">
              <div class="card-title">
                <i class="fas fa-history"></i>
                <h3>Recent Activity</h3>
              </div>
              <button class="btn btn-sm btn-outline">View All</button>
            </div>
            <div class="card-content">
              <div class="activity-list">
                <div class="activity-item">
                  <div class="activity-icon success">
                    <i class="fas fa-sign-in-alt"></i>
                  </div>
                  <div class="activity-details">
                    <h4>Successful Login</h4>
                    <p>Logged in from Windows PC</p>
                    <span class="activity-time">2 hours ago</span>
                  </div>
                </div>
                
                <div class="activity-item">
                  <div class="activity-icon primary">
                    <i class="fas fa-user-edit"></i>
                  </div>
                  <div class="activity-details">
                    <h4>Profile Updated</h4>
                    <p>Changed phone number</p>
                    <span class="activity-time">1 day ago</span>
                  </div>
                </div>
                
                <div class="activity-item">
                  <div class="activity-icon warning">
                    <i class="fas fa-key"></i>
                  </div>
                  <div class="activity-details">
                    <h4>Password Changed</h4>
                    <p>Security password updated</p>
                    <span class="activity-time">2 weeks ago</span>
                  </div>
                </div>
                
                <div class="activity-item">
                  <div class="activity-icon info">
                    <i class="fas fa-credit-card"></i>
                  </div>
                  <div class="activity-details">
                    <h4>Payment Processed</h4>
                    <p>Processed payment for Maria Santos</p>
                    <span class="activity-time">3 weeks ago</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </main>
  </div>

  <!-- Avatar Upload Modal -->
  <div class="modal-overlay" id="avatarModal">
    <div class="modal-container">
      <div class="modal-header">
        <h3>Change Profile Picture</h3>
        <button class="modal-close" onclick="closeAvatarModal()">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="modal-content">
        <div class="avatar-upload-area">
          <div class="upload-preview">
            <div class="preview-avatar">
              <i class="fas fa-user"></i>
            </div>
          </div>
          <div class="upload-options">
            <button class="btn btn-outline" onclick="triggerFileUpload()">
              <i class="fas fa-camera"></i>
              Choose Photo
            </button>
            <button class="btn btn-outline-danger" onclick="removeAvatar()">
              <i class="fas fa-trash"></i>
              Remove
            </button>
          </div>
          <input type="file" id="avatarFile" accept="image/*" style="display: none;" onchange="previewAvatar(this)">
          <p class="upload-note">Supported formats: JPG, PNG, GIF. Max size: 2MB</p>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" onclick="closeAvatarModal()">Cancel</button>
        <button class="btn btn-primary" onclick="saveAvatar()">Save Changes</button>
      </div>
    </div>
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
    const sidebar = document.querySelector('.sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mainContent = document.querySelector('.main-content');

    function toggleSidebar() {
      sidebar.classList.toggle('collapsed');
      mainContent.classList.toggle('expanded');
    }

    sidebarToggle?.addEventListener('click', toggleSidebar);

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

    // Edit mode functionality
    function toggleEditMode(section) {
      const form = document.getElementById(section === 'personal' ? 'personalInfoForm' : 'securityForm');
      const inputs = form.querySelectorAll('input');
      const actions = document.getElementById(section + 'Actions');
      const editBtn = form.closest('.settings-card').querySelector('.edit-btn');
      
      const isEditing = !inputs[0].readOnly;
      
      inputs.forEach(input => {
        input.readOnly = isEditing;
        if (!isEditing) {
          input.focus();
        }
      });
      
      if (isEditing) {
        actions.classList.add('hidden');
        editBtn.innerHTML = '<i class="fas fa-edit"></i> Edit';
      } else {
        actions.classList.remove('hidden');
        editBtn.innerHTML = '<i class="fas fa-times"></i> Cancel';
      }
    }

    function cancelEdit(section) {
      const form = document.getElementById(section === 'personal' ? 'personalInfoForm' : 'securityForm');
      const inputs = form.querySelectorAll('input');
      const actions = document.getElementById(section + 'Actions');
      const editBtn = form.closest('.settings-card').querySelector('.edit-btn');
      
      inputs.forEach(input => {
        input.readOnly = true;
      });
      
      actions.classList.add('hidden');
      editBtn.innerHTML = '<i class="fas fa-edit"></i> Edit';
      
      // Reset form values
      form.reset();
    }

    // Password toggle functionality
    function togglePassword(fieldId) {
      const field = document.getElementById(fieldId);
      const toggle = field.nextElementSibling.querySelector('i');
      
      if (field.type === 'password') {
        field.type = 'text';
        toggle.classList.remove('fa-eye');
        toggle.classList.add('fa-eye-slash');
      } else {
        field.type = 'password';
        toggle.classList.remove('fa-eye-slash');
        toggle.classList.add('fa-eye');
      }
    }

    // Avatar modal functionality
    function openAvatarModal() {
      document.getElementById('avatarModal').classList.add('active');
    }

    function closeAvatarModal() {
      document.getElementById('avatarModal').classList.remove('active');
    }

    function triggerFileUpload() {
      document.getElementById('avatarFile').click();
    }

    function previewAvatar(input) {
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
          const preview = document.querySelector('.preview-avatar');
          preview.innerHTML = `<img src="${e.target.result}" alt="Preview" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;
        };
        reader.readAsDataURL(input.files[0]);
      }
    }

    function removeAvatar() {
      const preview = document.querySelector('.preview-avatar');
      preview.innerHTML = '<i class="fas fa-user"></i>';
      document.getElementById('avatarFile').value = '';
    }

    function saveAvatar() {
      // Implement avatar save functionality
      showToast('successToast', 'Profile picture updated successfully!');
      closeAvatarModal();
    }

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

    // Form submissions
    document.getElementById('personalInfoForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Simulate API call
      setTimeout(() => {
        showToast('successToast', 'Personal information updated successfully!');
        cancelEdit('personal');
      }, 1000);
    });

    document.getElementById('securityForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const newPassword = document.getElementById('newPassword').value;
      const confirmPassword = document.getElementById('confirmPassword').value;
      
      if (newPassword !== confirmPassword) {
        showToast('errorToast', 'Passwords do not match!');
        return;
      }
      
      // Simulate API call
      setTimeout(() => {
        showToast('successToast', 'Password updated successfully!');
        cancelEdit('security');
      }, 1000);
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

    // Dark mode toggle
    document.getElementById('darkMode')?.addEventListener('change', function(e) {
      if (e.target.checked) {
        document.body.classList.add('dark-mode');
        localStorage.setItem('darkMode', 'enabled');
      } else {
        document.body.classList.remove('dark-mode');
        localStorage.setItem('darkMode', 'disabled');
      }
    });

    // Load saved preferences
    window.addEventListener('load', function() {
      const darkMode = localStorage.getItem('darkMode');
      if (darkMode === 'enabled') {
        document.getElementById('darkMode').checked = true;
        document.body.classList.add('dark-mode');
      }
    });

    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
      // Auto-hide notifications after some time
      setTimeout(() => {
        const notificationDropdown = document.getElementById('notificationDropdown');
        if (notificationDropdown && notificationDropdown.classList.contains('active')) {
          notificationDropdown.classList.remove('active');
        }
      }, 10000);
    });
  </script>
  
  <!-- Dashboard JavaScript -->
  <script src="<?= base_url('js/dashboard.js') ?>"></script>
</body>
</html>