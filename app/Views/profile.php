<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile Settings - ClearPay Admin</title>
  <link rel="stylesheet" href="<?= base_url('css/dashboard.css') ?>">
  <link rel="stylesheet" href="<?= base_url('css/header-components.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Essential CSS for modal and profile elements -->
  <style>

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

    /* Profile Settings Section */
    .profile-settings-section {
      margin-top: 2rem;
    }

    .settings-cards-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
      gap: 2rem;
      margin-bottom: 2rem;
    }

    /* Card Styles - Base card class for consistency with dashboard */
    .card {
      background: var(--bg-primary);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow-sm);
      transition: all 0.2s ease;
      overflow: hidden;
    }

    .card:hover {
      box-shadow: var(--shadow-md);
      border-color: var(--border-hover);
    }

    .card-title {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      font-size: 1.125rem;
      font-weight: 600;
      color: var(--text-primary);
      margin: 0;
    }

    /* Button Styles */
    .btn {
      padding: 0.5rem 1rem;
      border: none;
      border-radius: var(--radius-md);
      font-weight: 500;
      font-size: 0.875rem;
      cursor: pointer;
      transition: all var(--transition-fast);
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      text-decoration: none;
    }

    .btn-sm {
      padding: 0.375rem 0.75rem;
      font-size: 0.8125rem;
    }

    .btn-secondary {
      background: var(--bg-secondary);
      color: var(--text-secondary);
      border: 1px solid var(--border-color);
    }

    .btn-secondary:hover {
      background: var(--bg-tertiary);
      color: var(--text-primary);
      border-color: var(--border-hover);
      transform: translateY(-1px);
    }

    .btn-primary {
      background: var(--primary-color);
      color: white;
    }

    .btn-primary:hover {
      background: var(--primary-hover);
      transform: translateY(-1px);
    }

    .btn-outline {
      background: transparent;
      color: var(--primary-color);
      border: 1px solid var(--primary-color);
    }

    .btn-outline:hover {
      background: var(--primary-color);
      color: white;
    }

    /* Activity List Styles */
    .activity-list {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    .activity-item {
      display: flex;
      align-items: flex-start;
      gap: 1rem;
      padding: 1rem;
      background: var(--surface-light);
      border-radius: var(--radius-md);
      border: 1px solid var(--border-light);
      transition: all var(--transition-fast);
    }

    .activity-item:hover {
      background: white;
      border-color: var(--border-hover);
      transform: translateY(-1px);
      box-shadow: var(--shadow-sm);
    }

    .activity-icon {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      font-size: 1rem;
    }

    .activity-icon.success {
      background: var(--success-light);
      color: var(--success-color);
    }

    .activity-icon.primary {
      background: var(--primary-light);
      color: var(--primary-color);
    }

    .activity-icon.warning {
      background: var(--warning-light);
      color: var(--warning-color);
    }

    .activity-icon.info {
      background: var(--info-light);
      color: var(--info-color);
    }

    .activity-details {
      flex: 1;
      min-width: 0;
    }

    .activity-details h4 {
      font-size: 0.875rem;
      font-weight: 600;
      color: var(--text-primary);
      margin: 0 0 0.25rem 0;
    }

    .activity-details p {
      font-size: 0.8125rem;
      color: var(--text-secondary);
      margin: 0 0 0.25rem 0;
    }

    .activity-time {
      font-size: 0.75rem;
      color: var(--text-tertiary);
      font-weight: 500;
    }

    /* Password Input Styles */
    .password-input {
      position: relative;
      display: flex;
      align-items: center;
    }

    .password-input input {
      flex: 1;
      padding-right: 3rem;
    }

    .password-toggle {
      position: absolute;
      right: 0.75rem;
      background: none;
      border: none;
      color: var(--text-tertiary);
      cursor: pointer;
      padding: 0.25rem;
      border-radius: var(--radius-sm);
      transition: all var(--transition-fast);
    }

    .password-toggle:hover {
      color: var(--text-secondary);
      background: var(--surface-light);
    }

    /* Security Status Indicators */
    .security-status {
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
      margin-top: 1rem;
      padding-top: 1rem;
      border-top: 1px solid var(--border-light);
    }

    .status-item {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 0.8125rem;
      color: var(--text-secondary);
    }

    .status-item i {
      width: 16px;
      height: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.75rem;
    }

    .status-item.success i {
      color: var(--success-color);
    }

    .status-item.info i {
      color: var(--info-color);
    }

    /* Toggle Switch Styles */
    .toggle-switch {
      position: relative;
      display: inline-block;
      width: 48px;
      height: 24px;
      cursor: pointer;
    }

    .toggle-switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    .toggle-slider {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: var(--border-color);
      border-radius: 24px;
      transition: all var(--transition-fast);
    }

    .toggle-slider:before {
      position: absolute;
      content: "";
      height: 20px;
      width: 20px;
      left: 2px;
      bottom: 2px;
      background: white;
      border-radius: 50%;
      transition: all var(--transition-fast);
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .toggle-switch input:checked + .toggle-slider {
      background: var(--primary-color);
    }

    .toggle-switch input:checked + .toggle-slider:before {
      transform: translateX(24px);
    }

    .toggle-switch:hover .toggle-slider {
      box-shadow: 0 0 0 4px rgba(var(--primary-rgb), 0.1);
    }

    /* Preference Items */
    .preference-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 1rem 0;
      border-bottom: 1px solid var(--border-light);
    }

    .preference-item:last-child {
      border-bottom: none;
    }

    .preference-info {
      flex: 1;
      margin-right: 1rem;
    }

    .preference-info h4 {
      font-size: 0.875rem;
      font-weight: 600;
      color: var(--text-primary);
      margin: 0 0 0.25rem 0;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .preference-info p {
      font-size: 0.8125rem;
      color: var(--text-secondary);
      margin: 0;
    }

    .preference-control {
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    /* Select Styling */
    select.form-input {
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
      background-position: right 0.5rem center;
      background-repeat: no-repeat;
      background-size: 1.5em 1.5em;
      padding-right: 2.5rem;
      appearance: none;
    }

    @media (max-width: 768px) {
      .settings-cards-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
      }

      .profile-picture-section {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
      }

      .profile-form-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
      }
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

    /* Profile Picture Section */
    .profile-picture-section {
      display: flex;
      align-items: center;
      gap: 2rem;
      padding: 2rem;
      background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
      border-radius: 12px;
      margin-bottom: 2rem;
      border: 1px solid #e2e8f0;
    }

    .profile-picture-container {
      position: relative;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 0.5rem;
    }

    .profile-picture-preview {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      overflow: hidden;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
    }

    .profile-picture-preview:hover {
      transform: scale(1.05);
    }

    .profile-picture-upload-btn {
      margin-top: 0.5rem;
    }
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
    }

    .profile-picture-preview {
      width: 100%;
      height: 100%;
      position: relative;
      background: linear-gradient(135deg, var(--primary-color), var(--success-color));
      border-radius: 50%;
    }

    .profile-picture-preview img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 50%;
    }

    .profile-picture-placeholder {
      width: 100%;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      color: white;
      font-size: 2.5rem;
      font-weight: 600;
      background: linear-gradient(135deg, var(--primary-color), var(--info-color));
    }

    .profile-picture-placeholder i {
      font-size: 3rem;
      margin-bottom: 0.5rem;
      opacity: 0.8;
    }

    .profile-picture-placeholder span {
      font-size: 3rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      line-height: 1;
    }
    }

    .profile-picture-overlay {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.6);
      display: none; /* Hidden by default, shown via JavaScript */
      align-items: center;
      justify-content: center;
      transition: all 0.3s ease;
      border-radius: 50%;
      cursor: pointer;
    }

    .profile-picture-overlay.edit-mode {
      display: flex !important;
    }

    .profile-picture-container:hover .profile-picture-overlay {
      /* Removed automatic hover opacity since we control via JavaScript */
    }

    .profile-picture-btn {
      background: rgba(255, 255, 255, 0.9);
      border: none;
      padding: 0.75rem 1rem;
      border-radius: 8px;
      font-size: 0.875rem;
      font-weight: 600;
      color: var(--text-primary);
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      transition: all 0.2s ease;
    }

    .profile-picture-btn:hover {
      background: white;
      transform: translateY(-1px);
    }

    .profile-picture-info h4 {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--text-primary);
      margin: 0 0 0.5rem 0;
    }

    .profile-picture-info p {
      font-size: 1rem;
      color: var(--text-secondary);
      margin: 0 0 0.75rem 0;
    }

    .profile-picture-info h4 {
      font-size: 1.25rem;
      font-weight: 600;
      color: var(--text-primary);
      margin: 0 0 0.25rem 0;
    }

    .profile-picture-info p {
      font-size: 0.875rem;
      color: var(--text-secondary);
      margin: 0 0 0.5rem 0;
    }

    .profile-picture-info small {
      font-size: 0.75rem;
      color: var(--text-tertiary);
      background: var(--surface-light);
      padding: 0.375rem 0.75rem;
      border-radius: var(--radius-sm);
      display: inline-block;
      border: 1px solid var(--border-light);
    }

    /* Profile Picture Notification Styles */
    .profile-notification {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 10000;
      min-width: 300px;
      max-width: 500px;
      border-radius: 8px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
      animation: slideInRight 0.3s ease-out;
    }

    .profile-notification-success {
      background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
      border: 1px solid #c3e6cb;
      color: #155724;
    }

    .profile-notification-error {
      background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
      border: 1px solid #f5c6cb;
      color: #721c24;
    }

    .profile-notification-info {
      background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
      border: 1px solid #bee5eb;
      color: #0c5460;
    }

    .notification-content {
      display: flex;
      align-items: center;
      padding: 1rem;
      gap: 0.75rem;
    }

    .notification-content i {
      font-size: 1.25rem;
    }

    .notification-close {
      margin-left: auto;
      background: none;
      border: none;
      cursor: pointer;
      opacity: 0.7;
      padding: 0.25rem;
      border-radius: 4px;
      transition: opacity 0.2s ease;
    }

    .notification-close:hover {
      opacity: 1;
    }

    @keyframes slideInRight {
      from {
        transform: translateX(100%);
        opacity: 0;
      }
      to {
        transform: translateX(0);
        opacity: 1;
      }
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

    .form-input:read-only {
      background: var(--surface-secondary);
      color: var(--text-tertiary);
      cursor: default;
      border-color: var(--border-light);
    }

    .form-input.editable {
      background: white !important;
      border-color: var(--primary-color) !important;
      color: var(--text-primary) !important;
      cursor: text !important;
    }

    .form-input.editable:focus {
      outline: none;
      border-color: var(--primary-color);
      box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
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
          <li class="nav-item active">
            <a href="<?= base_url('profile') ?>" class="nav-link">
              <i class="fas fa-user"></i>
              <span>Profile</span>
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
        <?= $this->include('partials/help_section') ?>
      </div>
    </aside>

    <!-- Main Content Area -->
    <main class="main-content">
      
      <!-- Top Header Bar -->
      <header class="header">
        <div class="header-left">
          <h1 class="page-title">Profile Settings</h1>
          <p class="page-subtitle">Manage your profile information and account settings</p>
        </div>
        
        <?= $this->include('partials/header_components') ?>
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
                    <?php if (!empty($profile_picture)): ?>
                        <img src="<?= esc($profile_picture) ?>" alt="Profile Picture" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                    <?php else: ?>
                        <i class="fas fa-user"></i>
                    <?php endif; ?>
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

        <!-- Profile Settings Section -->
        <div class="profile-settings-section">
          <div class="settings-cards-grid">
          
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
              <!-- Profile Picture Section -->
              <div class="profile-picture-section">
                <div class="profile-picture-container">
                  <div class="profile-picture-preview" id="profilePicturePreview">
                    <?php if (!empty($profile_picture)): ?>
                      <img src="<?= esc($profile_picture) ?>" alt="Profile Picture" id="profileImage">
                    <?php else: ?>
                      <div class="profile-picture-placeholder" id="profilePlaceholder">
                        <i class="fas fa-user"></i>
                        <span><?= esc(strtoupper(substr(session()->get('name') ?? 'A', 0, 1))) ?></span>
                      </div>
                    <?php endif; ?>
                  </div>
                  <input type="file" id="profilePictureInput" accept="image/*" onchange="handleProfilePictureChange(event)" style="display: none;">
                  
                  <!-- Upload Button - Only visible in edit mode -->
                  <div class="profile-picture-upload-btn" id="profilePictureUploadBtn" style="display: none;">
                    <button type="button" class="btn btn-primary btn-sm" onclick="triggerFileInput()">
                      <i class="fas fa-camera"></i>
                      Change Photo
                    </button>
                  </div>
                </div>
                <div class="profile-picture-info">
                  <h4><?= esc(session()->get('name') ?? 'Admin User') ?></h4>
                  <p><?= esc(session()->get('email') ?? 'admin@clearpay.com') ?></p>
                  <small>JPG, PNG or GIF. Max file size 2MB.</small>
                </div>
              </div>
              
              <form id="personalInfoForm" class="settings-form" action="<?= base_url('auth/updateProfile') ?>" method="POST">
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
                </div>
              </form>
            </div>
          </div>

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
                
                <div class="security-status">
                  <div class="status-item success">
                    <i class="fas fa-check-circle"></i>
                    <span>Last password change: 2 weeks ago</span>
                  </div>
                  <div class="status-item info">
                    <i class="fas fa-clock"></i>
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
                <div class="preference-item">
                  <div class="preference-info">
                    <h4><i class="fas fa-moon"></i> Dark Mode</h4>
                    <p>Enable dark theme for better night viewing</p>
                  </div>
                  <div class="preference-control">
                    <label class="toggle-switch">
                      <input type="checkbox" id="darkMode">
                      <span class="toggle-slider"></span>
                    </label>
                  </div>
                </div>
                
                <div class="preference-item">
                  <div class="preference-info">
                    <h4><i class="fas fa-bell"></i> Email Notifications</h4>
                    <p>Receive email alerts for important events</p>
                  </div>
                  <div class="preference-control">
                    <label class="toggle-switch">
                      <input type="checkbox" id="emailNotifications" checked>
                      <span class="toggle-slider"></span>
                    </label>
                  </div>
                </div>
                
                <div class="preference-item">
                  <div class="preference-info">
                    <h4><i class="fas fa-mobile-alt"></i> SMS Notifications</h4>
                    <p>Get SMS alerts for critical updates</p>
                  </div>
                  <div class="preference-control">
                    <label class="toggle-switch">
                      <input type="checkbox" id="smsNotifications">
                      <span class="toggle-slider"></span>
                    </label>
                  </div>
                </div>
                
                <div class="preference-item">
                  <div class="preference-info">
                    <h4><i class="fas fa-language"></i> Language</h4>
                    <p>Select your preferred language</p>
                  </div>
                  <div class="preference-control">
                    <select class="form-input">
                      <option value="en">English</option>
                      <option value="fil">Filipino</option>
                      <option value="es">Spanish</option>
                    </select>
                  </div>
                </div>
                
                <div class="preference-item">
                  <div class="preference-info">
                    <h4><i class="fas fa-money-bill"></i> Currency Format</h4>
                    <p>Default currency display format</p>
                  </div>
                  <div class="preference-control">
                    <select class="form-input">
                      <option value="php">PHP (₱)</option>
                      <option value="usd">USD ($)</option>
                      <option value="eur">EUR (€)</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Activity Log Card -->
          <div class="card">
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
          </div> <!-- Close settings-cards-grid -->
        </div> <!-- Close profile-settings-section -->
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
    // Global functions - defined immediately
    window.triggerFileInput = function() {
      const fileInput = document.getElementById('profilePictureInput');
      if (fileInput) {
        fileInput.click();
      }
    }

    window.toggleEditMode = function(section) {
      console.log('toggleEditMode called with:', section);
      const form = document.getElementById(section === 'personal' ? 'personalInfoForm' : 'securityForm');
      const inputs = form.querySelectorAll('input:not(#profilePictureInput)');
      const actions = document.getElementById(section + 'Actions');
      const editBtn = form.closest('.card').querySelector('.btn-secondary');
      
      if (!form || !actions || !editBtn) {
        console.error('Required elements not found for section:', section);
        return;
      }
      
      const isCurrentlyReadOnly = inputs[0].readOnly;
      
      inputs.forEach(input => {
        input.readOnly = !isCurrentlyReadOnly;
        if (!isCurrentlyReadOnly) {
          input.classList.add('editable');
        } else {
          input.classList.remove('editable');
        }
      });
      
      // Handle profile picture upload button for personal section
      if (section === 'personal') {
        const uploadBtn = document.getElementById('profilePictureUploadBtn');
        if (uploadBtn) {
          if (isCurrentlyReadOnly) {
            uploadBtn.style.display = 'block';
          } else {
            uploadBtn.style.display = 'none';
          }
        }
      }
      
      if (isCurrentlyReadOnly) {
        actions.style.display = 'flex';
        editBtn.innerHTML = '<i class="fas fa-times"></i> Cancel';
        editBtn.onclick = () => cancelEdit(section);
      } else {
        actions.style.display = 'none';
        editBtn.innerHTML = '<i class="fas fa-edit"></i> Edit';
        editBtn.onclick = () => toggleEditMode(section);
      }
    }

    window.cancelEdit = function(section) {
      const form = document.getElementById(section === 'personal' ? 'personalInfoForm' : 'securityForm');
      const inputs = form.querySelectorAll('input:not(#profilePictureInput)');
      const actions = document.getElementById(section + 'Actions');
      const editBtn = form.closest('.card').querySelector('.btn-secondary');
      
      inputs.forEach(input => {
        input.readOnly = true;
        input.classList.remove('editable');
      });
      
      if (section === 'personal') {
        const uploadBtn = document.getElementById('profilePictureUploadBtn');
        if (uploadBtn) {
          uploadBtn.style.display = 'none';
        }
      }
      
      actions.style.display = 'none';
      editBtn.innerHTML = '<i class="fas fa-edit"></i> Edit';
      editBtn.onclick = () => toggleEditMode(section);
      
      location.reload();
    }

    // Document ready functions
    document.addEventListener('DOMContentLoaded', function() {
      console.log('DOM loaded, initializing functions...');
      
      // Profile picture upload handler
      window.handleProfilePictureChange = function(event) {
        const file = event.target.files[0];
        if (!file) return;

        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
          showNotification('Please select a valid image file (JPG, PNG, or GIF)', 'error');
          return;
        }

        const maxSize = 2 * 1024 * 1024;
        if (file.size > maxSize) {
          showNotification('File size must be less than 2MB', 'error');
          return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
          const preview = document.getElementById('profilePicturePreview');
          const placeholder = document.getElementById('profilePlaceholder');
          let existingImg = document.getElementById('profileImage');
          
          if (existingImg) {
            existingImg.src = e.target.result;
          } else {
            const img = document.createElement('img');
            img.id = 'profileImage';
            img.src = e.target.result;
            img.alt = 'Profile Picture';
            
            if (placeholder) {
              placeholder.style.display = 'none';
            }
            
            preview.appendChild(img);
          }
        };
        reader.readAsDataURL(file);

        uploadProfilePicture(file);
      };

      function uploadProfilePicture(file) {
        console.log('uploadProfilePicture called with file:', file);
        const formData = new FormData();
        formData.append('profile_picture', file);

        showNotification('Uploading profile picture...', 'info');

        fetch('<?= base_url('profile/upload-picture') ?>', {
          method: 'POST',
          body: formData,
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        .then(response => {
          console.log('Upload response:', response);
          return response.json();
        })
        .then(data => {
          console.log('Upload data:', data);
          if (data.success) {
            showNotification('Profile picture updated successfully!', 'success');
            updateAllProfilePictures(data.profile_picture);
          } else {
            showNotification(data.message || 'Failed to upload profile picture', 'error');
          }
        })
        .catch(error => {
          console.error('Upload error:', error);
          showNotification('Error uploading profile picture', 'error');
        });
      }

      function updateAllProfilePictures(imageUrl) {
        console.log('Updating all profile pictures with URL:', imageUrl);
        
        const profileImage = document.getElementById('profileImage');
        const profilePlaceholder = document.getElementById('profilePlaceholder');
        const profilePreview = document.getElementById('profilePicturePreview');
        
        if (profilePlaceholder && profilePreview) {
          profilePreview.innerHTML = `<img src="${imageUrl}" alt="Profile Picture" id="profileImage">`;
        } else if (profileImage) {
          profileImage.src = imageUrl;
        }

        const sidebarAvatar = document.querySelector('.sidebar-footer .profile-avatar');
        if (sidebarAvatar) {
          sidebarAvatar.innerHTML = `<img src="${imageUrl}" alt="Profile Picture">`;
        }

        const headerAvatar = document.querySelector('.header .user-avatar');
        if (headerAvatar) {
          headerAvatar.innerHTML = `<img src="${imageUrl}" alt="Profile Picture">`;
        }

        const otherProfileImages = document.querySelectorAll('.profile-avatar img, .user-avatar img');
        otherProfileImages.forEach(img => {
          if (img.id !== 'profileImage') {
            img.src = imageUrl;
          }
        });
      }

    function uploadProfilePicture(file) {
      console.log('uploadProfilePicture called with file:', file);
      const formData = new FormData();
      formData.append('profile_picture', file);

      // Show loading state
      showNotification('Uploading profile picture...', 'info');

      fetch('<?= base_url('profile/upload-picture') ?>', {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => {
        console.log('Upload response:', response);
        return response.json();
      })
      .then(data => {
        console.log('Upload data:', data);
        if (data.success) {
          showNotification('Profile picture updated successfully!', 'success');
          // Update all profile pictures in the UI
          updateAllProfilePictures(data.profile_picture);
        } else {
          showNotification(data.message || 'Failed to upload profile picture', 'error');
        }
      })
      .catch(error => {
        console.error('Upload error:', error);
        showNotification('Error uploading profile picture', 'error');
      });
    }

    function updateAllProfilePictures(imageUrl) {
      // Update the main profile image on this page
      const profileImage = document.getElementById('profileImage');
      const profilePlaceholder = document.getElementById('profilePlaceholder');
      const profilePreview = document.getElementById('profilePicturePreview');
      
      if (profilePlaceholder && profilePreview) {
        // Replace placeholder with image
        profilePreview.innerHTML = `
          <img src="${imageUrl}" alt="Profile Picture" id="profileImage">
        `;
      } else if (profileImage) {
        // Update existing image
        profileImage.src = imageUrl;
      }

      // Update any other profile images on the page
      const otherProfileImages = document.querySelectorAll('.profile-avatar img, .user-avatar img');
      otherProfileImages.forEach(img => {
        if (img.id !== 'profileImage') { // Don't update the main one twice
          img.src = imageUrl;
        }
      });
    }
        profileImage.src = imageUrl;
      }

      // Update all profile picture instances throughout the UI
      const profileImages = document.querySelectorAll('.profile-avatar img, .profile-picture img');
      profileImages.forEach(img => {
        if (img.id !== 'profileImage') { // Don't update the main one again
          img.src = imageUrl;
        }
      });

      // Update placeholders to show images
      const placeholders = document.querySelectorAll('.profile-avatar:not(:has(img))');
      placeholders.forEach(placeholder => {
        const img = document.createElement('img');
        img.src = imageUrl;
        img.alt = 'Profile Picture';
        img.style.width = '100%';
        img.style.height = '100%';
        img.style.objectFit = 'cover';
        img.style.borderRadius = '50%';
        placeholder.innerHTML = '';
        placeholder.appendChild(img);
      });
    }

    function showNotification(message, type = 'info') {
      // Remove existing notifications
      const existingNotification = document.querySelector('.profile-notification');
      if (existingNotification) {
        existingNotification.remove();
      }

      // Create notification
      const notification = document.createElement('div');
      notification.className = `profile-notification profile-notification-${type}`;
      notification.innerHTML = `
        <div class="notification-content">
          <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
          <span>${message}</span>
          <button class="notification-close" onclick="this.parentElement.parentElement.remove()">
            <i class="fas fa-times"></i>
          </button>
        </div>
      `;

      document.body.appendChild(notification);

      // Auto remove after 5 seconds
      setTimeout(() => {
        if (notification.parentElement) {
          notification.remove();
        }
      }, 5000);
    }

    // Edit mode functionality
    function toggleEditMode(section) {
      const form = document.getElementById(section === 'personal' ? 'personalInfoForm' : 'securityForm');
      const inputs = form.querySelectorAll('input:not(#profilePictureInput)'); // Exclude file input
      const actions = document.getElementById(section + 'Actions');
      const editBtn = form.closest('.card').querySelector('.btn-secondary');
      
      if (!form || !actions || !editBtn) {
        console.error('Required elements not found for section:', section);
        return;
      }
      
      const isCurrentlyReadOnly = inputs[0].readOnly;
      
      inputs.forEach(input => {
        input.readOnly = !isCurrentlyReadOnly;
        if (!isCurrentlyReadOnly) {
          input.classList.add('editable');
        } else {
          input.classList.remove('editable');
        }
      });
      
      // Handle profile picture upload button for personal section
      if (section === 'personal') {
        const uploadBtn = document.getElementById('profilePictureUploadBtn');
        if (uploadBtn) {
          if (isCurrentlyReadOnly) {
            // Entering edit mode - show upload button
            uploadBtn.style.display = 'block';
          } else {
            // Exiting edit mode - hide upload button
            uploadBtn.style.display = 'none';
          }
        }
      }
      
      if (isCurrentlyReadOnly) {
        // Switch to edit mode
        actions.style.display = 'flex';
        editBtn.innerHTML = '<i class="fas fa-times"></i> Cancel';
        editBtn.onclick = () => cancelEdit(section);
      } else {
        // Switch to read-only mode
        actions.style.display = 'none';
        editBtn.innerHTML = '<i class="fas fa-edit"></i> Edit';
        editBtn.onclick = () => toggleEditMode(section);
      }
    }

    function cancelEdit(section) {
      const form = document.getElementById(section === 'personal' ? 'personalInfoForm' : 'securityForm');
      const inputs = form.querySelectorAll('input:not(#profilePictureInput)');
      const actions = document.getElementById(section + 'Actions');
      const editBtn = form.closest('.card').querySelector('.btn-secondary');
      
      inputs.forEach(input => {
        input.readOnly = true;
        input.classList.remove('editable');
      });
      
      // Handle profile picture upload button for personal section
      if (section === 'personal') {
        const uploadBtn = document.getElementById('profilePictureUploadBtn');
        if (uploadBtn) {
          uploadBtn.style.display = 'none';
        }
      }
      
      actions.style.display = 'none';
      editBtn.innerHTML = '<i class="fas fa-edit"></i> Edit';
      editBtn.onclick = () => toggleEditMode(section);
      
      // Reset form values to original
      location.reload(); // Simple way to reset - you could improve this
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
      
      const formData = new FormData(this);
      
      // Show loading state
      const submitBtn = this.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
      submitBtn.disabled = true;
      
      // Submit to the server
      fetch('<?= base_url('/profile/update') ?>', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showNotification('Personal information updated successfully!', 'success');
          cancelEdit('personal');
          
          // Update session data displayed on page
          setTimeout(() => {
            location.reload(); // Refresh to show updated data
          }, 1500);
        } else {
          showNotification(data.message || 'Error updating personal information', 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showNotification('Error updating personal information', 'error');
      })
      .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
      });
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
  
  <!-- Profile JavaScript -->
  <script src="<?= base_url('js/profile.js') ?>"></script>
  
  <!-- Dashboard JavaScript -->
  <script src="<?= base_url('js/dashboard.js') ?>"></script>
  <script src="<?= base_url('js/main.js') ?>"></script>
  <script src="<?= base_url('js/header-components.js') ?>"></script>
</body>
</html>