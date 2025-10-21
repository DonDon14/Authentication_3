<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ClearPay Contributions - Manage Payment Types</title>
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
              <span class="nav-badge">New</span>
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
            <a href="#" class="nav-link">
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
            <h4><?= session()->get('username') ?? 'Admin User' ?></h4>
            <p>Administrator</p>
          </div>
          <button class="profile-menu-btn" id="profileMenuBtn">
            <i class="fas fa-ellipsis-h"></i>
          </button>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Header -->
      <header class="header">
        <div class="header-left">
          <h1>Contributions</h1>
          <p class="page-subtitle">Manage payment types and contribution settings</p>
        </div>
        <div class="header-right">
          <div class="search-container">
            <i class="fas fa-search"></i>
            <input type="text" class="search-input" placeholder="Search contributions...">
          </div>
          
          <!-- Notification Center -->
          <div class="notification-center">
            <button class="notification-btn" id="notificationBtn">
              <i class="fas fa-bell"></i>
              <span class="notification-count">3</span>
            </button>
          </div>
          
          <!-- User Menu -->
          <div class="user-menu">
            <button class="user-menu-btn" id="userMenuBtn">
              <div class="user-avatar">
                <i class="fas fa-user"></i>
              </div>
              <span class="user-name"><?= session()->get('username') ?? 'Admin' ?></span>
              <i class="fas fa-chevron-down"></i>
            </button>
          </div>
        </div>
      </header>

      <!-- Dashboard Content -->
      <div class="dashboard-content">

        <!-- Success/Error Messages -->
        <div id="successMessage" class="notification-message success" style="display: none;">
          <i class="fas fa-check-circle"></i>
          <span class="message-text"></span>
        </div>
        <div id="errorMessage" class="notification-message error" style="display: none;">
          <i class="fas fa-exclamation-triangle"></i>
          <span class="message-text"></span>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
          <div class="stat-card primary">
            <div class="stat-content">
              <div class="stat-header">
                <h3>Active</h3>
                <div class="stat-icon">
                  <i class="fas fa-check-circle"></i>
                </div>
              </div>
              <div class="stat-value"><?= isset($stats['active']) ? $stats['active'] : 0 ?></div>
              <div class="stat-footer">
                <span class="stat-change positive">
                  <i class="fas fa-arrow-up"></i>
                  Active contributions
                </span>
              </div>
            </div>
          </div>
          
          <div class="stat-card success">
            <div class="stat-content">
              <div class="stat-header">
                <h3>Total</h3>
                <div class="stat-icon">
                  <i class="fas fa-list"></i>
                </div>
              </div>
              <div class="stat-value"><?= isset($stats['total']) ? $stats['total'] : 0 ?></div>
              <div class="stat-footer">
                <span class="stat-change neutral">
                  <i class="fas fa-equals"></i>
                  All contributions
                </span>
              </div>
            </div>
          </div>
          
          <div class="stat-card warning">
            <div class="stat-content">
              <div class="stat-header">
                <h3>Inactive</h3>
                <div class="stat-icon">
                  <i class="fas fa-pause-circle"></i>
                </div>
              </div>
              <div class="stat-value"><?= isset($stats['inactive']) ? $stats['inactive'] : 0 ?></div>
              <div class="stat-footer">
                <span class="stat-change neutral">
                  <i class="fas fa-minus"></i>
                  Disabled contributions
                </span>
              </div>
            </div>
          </div>
          
          <div class="stat-card info">
            <div class="stat-content">
              <div class="stat-header">
                <h3>Today</h3>
                <div class="stat-icon">
                  <i class="fas fa-calendar-day"></i>
                </div>
              </div>
              <div class="stat-value"><?= count(array_filter($contributions ?? [], function($c) { return date('Y-m-d', strtotime($c['created_at'])) == date('Y-m-d'); })) ?></div>
              <div class="stat-footer">
                <span class="stat-change positive">
                  <i class="fas fa-plus"></i>
                  Created today
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="dashboard-card">
          <div class="card-header">
            <div>
              <h3><i class="fas fa-plus-circle"></i> Quick Actions</h3>
              <p>Manage your contribution types</p>
            </div>
          </div>
          <div class="card-content">
            <div class="quick-actions-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
              <button class="action-btn primary" id="addContributionBtn">
                <div class="action-icon">
                  <i class="fas fa-plus"></i>
                </div>
                <div class="action-text">
                  <h4>Add New</h4>
                  <p>Create contribution type</p>
                </div>
              </button>
              <button class="action-btn success" onclick="window.location.href='<?= base_url('payments') ?>'">
                <div class="action-icon">
                  <i class="fas fa-credit-card"></i>
                </div>
                <div class="action-text">
                  <h4>Record Payment</h4>
                  <p>Add student payment</p>
                </div>
              </button>
              <button class="action-btn info" onclick="window.location.href='<?= base_url('payments/history') ?>'">
                <div class="action-icon">
                  <i class="fas fa-history"></i>
                </div>
                <div class="action-text">
                  <h4>View History</h4>
                  <p>Payment records</p>
                </div>
              </button>
              <button class="action-btn warning" onclick="exportContributions()">
                <div class="action-icon">
                  <i class="fas fa-download"></i>
                </div>
                <div class="action-text">
                  <h4>Export Data</h4>
                  <p>Download reports</p>
                </div>
              </button>
            </div>
          </div>
        </div>

        <!-- Contributions List -->
        <div class="dashboard-card">
          <div class="card-header">
            <div>
              <h3><i class="fas fa-hand-holding-usd"></i> Active Contributions</h3>
              <p>Currently available payment types</p>
            </div>
            <div class="card-actions">
              <button class="btn-secondary" onclick="refreshContributions()">
                <i class="fas fa-sync-alt"></i>
                Refresh
              </button>
            </div>
          </div>
          <div class="card-content">
            <div id="contributionsList">
              <?php if (!empty($contributions)): ?>
                <div class="contributions-grid" style="display: grid; gap: 1.5rem;">
                  <?php foreach ($contributions as $contribution): ?>
                    <div class="contribution-card" 
                         data-id="<?= $contribution['id'] ?>" 
                         data-title="<?= esc($contribution['title']) ?>"
                         style="background: var(--bg-secondary); 
                                border: 1px solid var(--border-color); 
                                border-radius: var(--radius-lg); 
                                padding: 1.5rem; 
                                transition: all var(--transition-fast); 
                                cursor: pointer;
                                position: relative;
                                overflow: hidden;">
                      
                      <!-- Hover overlay -->
                      <div class="card-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, var(--primary-color), var(--info-color)); opacity: 0; transition: all var(--transition-fast); pointer-events: none;"></div>
                      
                      <div class="contribution-content" style="display: flex; align-items: flex-start; gap: 1.5rem; position: relative; z-index: 2;">
                        <div class="contribution-icon" style="width: 64px; height: 64px; background: linear-gradient(135deg, var(--primary-color), var(--info-color)); border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center; color: var(--text-inverse); font-size: 1.5rem; flex-shrink: 0; box-shadow: 0 4px 12px rgba(var(--primary-rgb), 0.3);">
                          <i class="fas fa-hand-holding-usd"></i>
                        </div>
                        
                        <div class="contribution-info" style="flex: 1; min-width: 0;">
                          <div class="contribution-header" style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 0.75rem; gap: 1rem;">
                            <div class="contribution-title-section" style="flex: 1; min-width: 0;">
                              <h4 style="font-size: 1.25rem; font-weight: 600; color: var(--text-primary); margin: 0 0 0.25rem 0; line-height: 1.3;"><?= esc($contribution['title']) ?></h4>
                              <div class="contribution-tags" style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                <span class="status status-<?= $contribution['status'] === 'active' ? 'verified' : 'pending' ?>" style="font-size: 0.75rem;"><?= ucfirst($contribution['status']) ?></span>
                                <span class="category-tag" style="background: var(--info-light); color: var(--info-color); padding: 0.25rem 0.75rem; border-radius: var(--radius-full); font-size: 0.75rem; font-weight: 500;"><?= esc($contribution['category']) ?></span>
                              </div>
                            </div>
                            <div class="contribution-amount" style="text-align: right; flex-shrink: 0;">
                              <div style="font-size: 1.5rem; font-weight: 700; color: var(--primary-color); line-height: 1;">$<?= number_format($contribution['amount'], 2) ?></div>
                              <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem;">Per payment</div>
                            </div>
                          </div>
                          
                          <p style="color: var(--text-secondary); margin-bottom: 1rem; font-size: 0.9rem; line-height: 1.5;"><?= esc($contribution['description']) ?></p>
                          
                          <div class="contribution-footer" style="display: flex; align-items: center; justify-content: space-between;">
                            <div class="contribution-stats" style="display: flex; gap: 1rem; align-items: center;">
                              <div class="stat-item" style="display: flex; align-items: center; gap: 0.5rem;">
                                <div style="width: 8px; height: 8px; background: var(--success-color); border-radius: 50%;"></div>
                                <span style="font-size: 0.8rem; color: var(--text-secondary);">Active</span>
                              </div>
                              <div class="stat-item" style="display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fas fa-users" style="font-size: 0.8rem; color: var(--text-secondary);"></i>
                                <span style="font-size: 0.8rem; color: var(--text-secondary);">View details</span>
                              </div>
                            </div>
                            
                            <div class="contribution-actions" style="display: flex; align-items: center; gap: 0.75rem; position: relative; z-index: 10;">
                              <div class="toggle-wrapper" style="position: relative;">
                                <label class="toggle-switch" style="position: relative; display: inline-block; width: 48px; height: 26px;">
                                  <input type="checkbox" <?= $contribution['status'] === 'active' ? 'checked' : '' ?> 
                                         data-contribution-id="<?= $contribution['id'] ?>" 
                                         class="contribution-toggle"
                                         style="opacity: 0; width: 0; height: 0;">
                                  <span class="slider" style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: <?= $contribution['status'] === 'active' ? 'var(--success-color)' : 'var(--border-color)' ?>; transition: var(--transition-fast); border-radius: 26px;">
                                    <span class="slider-button" style="position: absolute; content: ''; height: 20px; width: 20px; left: <?= $contribution['status'] === 'active' ? '25px' : '3px' ?>; bottom: 3px; background-color: white; transition: var(--transition-fast); border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.2);"></span>
                                  </span>
                                </label>
                              </div>
                              
                              <button class="btn-icon edit-btn" 
                                      data-contribution-id="<?= $contribution['id'] ?>" 
                                      title="Edit contribution"
                                      style="width: 32px; height: 32px; border-radius: var(--radius-md); background: var(--warning-light); color: var(--warning-color); border: none; display: flex; align-items: center; justify-content: center; transition: all var(--transition-fast);">
                                <i class="fas fa-edit" style="font-size: 0.8rem;"></i>
                              </button>
                              
                              <button class="btn-icon delete-btn" 
                                      data-contribution-id="<?= $contribution['id'] ?>" 
                                      title="Delete contribution"
                                      style="width: 32px; height: 32px; border-radius: var(--radius-md); background: var(--error-light); color: var(--error-color); border: none; display: flex; align-items: center; justify-content: center; transition: all var(--transition-fast);">
                                <i class="fas fa-trash" style="font-size: 0.8rem;"></i>
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php else: ?>
                <div class="empty-state">
                  <div class="empty-icon">
                    <i class="fas fa-hand-holding-usd"></i>
                  </div>
                  <h4>No Contributions Yet</h4>
                  <p>Start by adding your first contribution type to begin collecting payments</p>
                  <button class="btn-primary" onclick="document.getElementById('addContributionBtn').click()">
                    <i class="fas fa-plus"></i>
                    Add First Contribution
                  </button>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>

      </div>
    </main>
  </div>

  <!-- Add Contribution Modal -->
  <div id="contributionModal" class="modal-overlay" style="display: none;">
    <div class="modal-container" style="max-width: 600px; width: 90%;">
      <div class="card-header">
        <div>
          <h3 id="modalTitle"><i class="fas fa-plus-circle"></i> Add New Contribution</h3>
          <p>Create a new payment type for students</p>
        </div>
        <div class="card-actions">
          <button type="button" class="btn-icon" id="closeModal">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>
      <div style="padding: 1.5rem;">
        <form id="contributionForm">
          <input type="hidden" id="contributionId" name="contribution_id">
          
          <div class="form-group">
            <label for="contributionTitle">Contribution Title</label>
            <div style="position: relative;">
              <i class="fas fa-heading" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-tertiary);"></i>
              <input type="text" id="contributionTitle" name="title" class="search-input" placeholder="e.g., Uniform Payments" required style="padding-left: 2.5rem;">
            </div>
          </div>

          <div class="form-group">
            <label for="contributionDescription">Description</label>
            <div style="position: relative;">
              <i class="fas fa-align-left" style="position: absolute; left: 1rem; top: 1rem; color: var(--text-tertiary);"></i>
              <textarea id="contributionDescription" name="description" placeholder="Brief description of this contribution type" rows="3" style="padding-left: 2.5rem; background: var(--bg-tertiary); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 0.75rem 1rem 0.75rem 2.5rem; width: 100%; font-size: 0.9rem; font-family: inherit; resize: vertical;"></textarea>
            </div>
          </div>

          <div class="form-group">
            <label for="contributionAmount">Default Amount ($)</label>
            <div style="position: relative;">
              <i class="fas fa-dollar-sign" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-tertiary);"></i>
              <input type="number" id="contributionAmount" name="amount" class="search-input" placeholder="0.00" step="0.01" min="0" required style="padding-left: 2.5rem;">
            </div>
          </div>

          <div class="form-group">
            <label for="contributionCategory">Category</label>
            <div style="position: relative;">
              <i class="fas fa-list" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-tertiary);"></i>
              <select id="contributionCategory" name="category" required style="padding-left: 2.5rem; background: var(--bg-tertiary); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 0.75rem 1rem 0.75rem 2.5rem; width: 100%; font-size: 0.9rem;">
                <option value="">Select category</option>
                <option value="Uniform">Uniform</option>
                <option value="Activity">Activity</option>
                <option value="Meal">Meal</option>
                <option value="Education">Education</option>
                <option value="Transportation">Transportation</option>
                <option value="Other">Other</option>
              </select>
            </div>
          </div>

          <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
            <button type="button" class="btn-secondary" id="cancelBtn">
              <i class="fas fa-times"></i>
              Cancel
            </button>
            <button type="submit" class="btn-primary" id="submitBtn">
              <span class="btn-text">
                <i class="fas fa-plus"></i>
                Add Contribution
              </span>
              <i class="fas fa-spinner fa-spin btn-loader" style="display: none;"></i>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

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
          <h4>Contribution Added</h4>
          <p>New contribution type created successfully</p>
          <span class="notification-time">5 minutes ago</span>
        </div>
      </div>
      <div class="notification-item">
        <div class="notification-icon primary">
          <i class="fas fa-info-circle"></i>
        </div>
        <div class="notification-content">
          <h4>Status Updated</h4>
          <p>Contribution status changed to active</p>
          <span class="notification-time">2 hours ago</span>
        </div>
      </div>
      <div class="notification-item">
        <div class="notification-icon info">
          <i class="fas fa-edit"></i>
        </div>
        <div class="notification-content">
          <h4>Contribution Modified</h4>
          <p>Payment amount updated successfully</p>
          <span class="notification-time">1 day ago</span>
        </div>
      </div>
    </div>
    <div class="notification-footer">
      <a href="#" class="view-all-notifications">View all notifications</a>
    </div>
  </div>

  <!-- User Dropdown -->
  <div class="user-dropdown" id="userDropdown">
    <div class="dropdown-header">
      <div class="user-info">
        <h4><?= session()->get('username') ?? 'Admin User' ?></h4>
        <p>System Administrator</p>
      </div>
    </div>
    <div class="dropdown-menu">
      <a href="<?= base_url('profile') ?>" class="dropdown-item">
        <i class="fas fa-user"></i>
        <span>My Profile</span>
      </a>
      <a href="<?= base_url('dashboard') ?>" class="dropdown-item">
        <i class="fas fa-cog"></i>
        <span>Settings</span>
      </a>
      <div class="dropdown-divider"></div>
      <a href="<?= base_url('auth/logout') ?>" class="dropdown-item logout">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
      </a>
    </div>
  </div>

  <!-- Profile Menu Dropdown (for sidebar) -->
  <div class="profile-menu-dropdown" id="profileMenuDropdown">
    <div class="dropdown-content">
      <a href="<?= base_url('profile') ?>" class="dropdown-item">
        <i class="fas fa-user"></i>
        <span>Profile</span>
      </a>
      <a href="<?= base_url('dashboard') ?>" class="dropdown-item">
        <i class="fas fa-cog"></i>
        <span>Settings</span>
      </a>
      <div class="dropdown-divider"></div>
      <a href="<?= base_url('auth/logout') ?>" class="dropdown-item logout">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
      </a>
    </div>
  </div>

  <!-- Additional Styles -->
  <style>
    .modal-overlay {
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
    }

    .modal-container {
      background: var(--bg-primary);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow-xl);
      max-height: 90vh;
      overflow-y: auto;
    }

    .notification-message {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 1rem 1.5rem;
      border-radius: var(--radius-md);
      margin-bottom: 1.5rem;
      font-weight: 500;
    }

    .notification-message.success {
      background: var(--success-light);
      color: var(--success-color);
      border: 1px solid var(--success-color);
    }

    .notification-message.error {
      background: var(--error-light);
      color: var(--error-color);
      border: 1px solid var(--error-color);
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    .form-group label {
      display: block;
      font-weight: 600;
      color: var(--text-primary);
      margin-bottom: 0.5rem;
      font-size: 0.9rem;
    }

    .contribution-item:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-lg);
      border-color: var(--primary-color);
    }

    .btn-loader {
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    /* Toggle Switch Styles */
    .contribution-item input[type="checkbox"] + span {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: var(--border-color);
      transition: var(--transition-fast);
      border-radius: 24px;
    }

    .contribution-item input[type="checkbox"]:checked + span {
      background-color: var(--success-color);
    }

    .contribution-item input[type="checkbox"] + span:before {
      position: absolute;
      content: "";
      height: 18px;
      width: 18px;
      left: 3px;
      bottom: 3px;
      background-color: white;
      transition: var(--transition-fast);
      border-radius: 50%;
    }

    .contribution-item input[type="checkbox"]:checked + span:before {
      transform: translateX(20px);
    }

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
      .quick-actions-grid {
        grid-template-columns: repeat(2, 1fr) !important;
      }
      
      .modal-container {
        width: 95%;
        margin: 1rem;
      }
      
      .contribution-content {
        flex-direction: column !important;
        gap: 1rem !important;
      }
      
      .contribution-info {
        width: 100%;
      }
    }
  </style>

  <!-- JavaScript -->
  <script>
    // Pass base URL to JavaScript
    window.APP_BASE_URL = '<?= base_url() ?>';
    console.log('Base URL from PHP:', window.APP_BASE_URL);
    
    // Dashboard functionality
    document.addEventListener('DOMContentLoaded', function() {
      console.log('=== CONTRIBUTIONS PAGE LOADED ===');
      
      // Check if elements exist
      const toggles = document.querySelectorAll('.contribution-toggle');
      const altToggles = document.querySelectorAll('input[data-contribution-id]');
      const cards = document.querySelectorAll('.contribution-card');
      
      console.log('Found toggles (.contribution-toggle):', toggles.length);
      console.log('Found alt toggles (input[data-contribution-id]):', altToggles.length);
      console.log('Found cards (.contribution-card):', cards.length);
      
      // Add manual event listeners as backup
      altToggles.forEach((toggle, index) => {
        console.log(`Setting up manual toggle ${index} for contribution ID:`, toggle.getAttribute('data-contribution-id'));
        
        // Mark this toggle as having a manual handler to prevent conflicts
        toggle.setAttribute('data-manual-handler', 'true');
        
        // Remove any existing event listeners to prevent duplicates
        const newToggle = toggle.cloneNode(true);
        toggle.parentNode.replaceChild(newToggle, toggle);
        
        newToggle.addEventListener('change', function(e) {
          console.log('=== MANUAL TOGGLE CHANGE ===');
          console.log('Contribution ID:', this.getAttribute('data-contribution-id'));
          console.log('Checked:', this.checked);
          
          const contributionId = this.getAttribute('data-contribution-id');
          
          // Prevent any other handlers from executing
          e.stopImmediatePropagation();
          
          // Make the toggle request
          fetch(`<?= base_url() ?>contributions/toggle/${contributionId}`, {
            method: 'POST'
          })
          .then(response => response.json())
          .then(result => {
            console.log('Toggle result:', result);
            if (result.success) {
              // Update UI based on server response (not current toggle state)
              const slider = this.nextElementSibling;
              const sliderButton = slider ? slider.querySelector('.slider-button') : null;
              
              if (result.status === 'active') {
                if (slider) slider.style.backgroundColor = 'var(--success-color)';
                if (sliderButton) sliderButton.style.left = '25px';
                this.checked = true;
              } else {
                if (slider) slider.style.backgroundColor = 'var(--border-color)';
                if (sliderButton) sliderButton.style.left = '3px';
                this.checked = false;
              }
              
              // Update stats
              updateStatsManual();
              
              console.log(`Status successfully changed to: ${result.status}`);
            } else {
              console.error('Toggle failed:', result.message);
              // Reset to opposite of current state since the change failed
              this.checked = !this.checked;
            }
          })
          .catch(error => {
            console.error('Toggle error:', error);
            // Reset to opposite of current state since the change failed  
            this.checked = !this.checked;
          });
        });
        
        // Also handle clicks on the slider itself to prevent conflicts
        const slider = newToggle.nextElementSibling;
        if (slider && slider.classList.contains('slider')) {
          slider.addEventListener('click', function(e) {
            console.log('=== SLIDER CLICK (Manual) ===');
            e.stopPropagation();
            e.preventDefault();
            
            // Toggle the checkbox and trigger its change event
            newToggle.checked = !newToggle.checked;
            newToggle.dispatchEvent(new Event('change', { bubbles: false }));
          });
        }
      });
      
      // Function to manually update stats
      window.updateStatsManual = function() {
        const toggles = document.querySelectorAll('input[data-contribution-id]');
        let activeCount = 0;
        let inactiveCount = 0;
        
        toggles.forEach(toggle => {
          if (toggle.checked) {
            activeCount++;
          } else {
            inactiveCount++;
          }
        });
        
        console.log(`Manual stats update: Active: ${activeCount}, Inactive: ${inactiveCount}`);
        
        const activeStatCard = document.querySelector('.stat-card.primary .stat-value');
        const inactiveStatCard = document.querySelector('.stat-card.warning .stat-value');
        
        if (activeStatCard) activeStatCard.textContent = activeCount;
        if (inactiveStatCard) inactiveStatCard.textContent = inactiveCount;
      };
      
      // Add click handlers for contribution cards
      cards.forEach(card => {
        card.addEventListener('click', function(e) {
          // Don't navigate if clicking on toggle switch or action buttons
          if (e.target.closest('.contribution-actions')) {
            return;
          }
          
          const contributionId = this.getAttribute('data-id');
          console.log('Contribution card clicked, ID:', contributionId);
          window.location.href = `<?= base_url('payments/viewContribution/') ?>${contributionId}`;
        });
      });
      
      // Sidebar toggle
      const sidebarToggle = document.getElementById('sidebarToggle');
      const sidebar = document.querySelector('.sidebar');
      
      if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
          sidebar.classList.toggle('collapsed');
        });
      }
      
      // Notification dropdown
      const notificationBtn = document.getElementById('notificationBtn');
      const notificationDropdown = document.getElementById('notificationDropdown');
      
      if (notificationBtn) {
        notificationBtn.addEventListener('click', function(e) {
          e.stopPropagation();
          notificationDropdown.classList.toggle('active');
          document.getElementById('userDropdown')?.classList.remove('active');
        });
      }
      
      // User dropdown
      const userMenuBtn = document.getElementById('userMenuBtn');
      const userDropdown = document.getElementById('userDropdown');
      
      if (userMenuBtn) {
        userMenuBtn.addEventListener('click', function(e) {
          e.stopPropagation();
          userDropdown.classList.toggle('active');
          document.getElementById('notificationDropdown')?.classList.remove('active');
        });
      }
      
      // Profile menu (sidebar)
      const profileMenuBtn = document.getElementById('profileMenuBtn');
      const profileMenuDropdown = document.getElementById('profileMenuDropdown');
      
      if (profileMenuBtn) {
        profileMenuBtn.addEventListener('click', function(e) {
          e.stopPropagation();
          profileMenuDropdown.classList.toggle('active');
        });
      }
      
      // Close dropdowns when clicking outside
      document.addEventListener('click', function() {
        document.querySelectorAll('.notification-dropdown, .user-dropdown, .profile-menu-dropdown').forEach(dropdown => {
          dropdown.classList.remove('active');
        });
      });
      
      // Prevent dropdown close when clicking inside
      document.querySelectorAll('.notification-dropdown, .user-dropdown, .profile-menu-dropdown').forEach(dropdown => {
        dropdown.addEventListener('click', function(e) {
          e.stopPropagation();
        });
      });
      
      // Modal functionality
      const modal = document.getElementById('contributionModal');
      const addBtn = document.getElementById('addContributionBtn');
      const closeBtn = document.getElementById('closeModal');
      const cancelBtn = document.getElementById('cancelBtn');
      
      if (addBtn) {
        addBtn.addEventListener('click', function() {
          modal.style.display = 'flex';
          document.body.style.overflow = 'hidden';
        });
      }
      
      function closeModal() {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
        document.getElementById('contributionForm').reset();
      }
      
      if (closeBtn) closeBtn.addEventListener('click', closeModal);
      if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
      
      // Close modal when clicking outside
      modal.addEventListener('click', function(e) {
        if (e.target === modal) {
          closeModal();
        }
      });
    });
    
    // Helper functions
    function refreshContributions() {
      window.location.reload();
    }
    
    function exportContributions() {
      alert('Export functionality coming soon!');
    }
    
    function viewContributionPayments(contributionId) {
      window.location.href = `<?= base_url('payments/viewContribution/') ?>${contributionId}`;
    }
    
    function refreshContributions() {
      window.location.reload();
    }
    
    function exportContributions() {
      alert('Export functionality coming soon!');
    }
  </script>
  
  <!-- External JS -->
  <script src="<?= base_url('js/contributions.js') ?>"></script>
</body>
</html>