<!-- Header Right Components -->
<div class="header-right">
  <!-- Search Bar -->
  <div class="search-container">
    <i class="fas fa-search"></i>
    <input type="text" class="search-input" placeholder="Search students, payments...">
  </div>
  
  <!-- Notification Center -->
  <div class="notification-center">
    <button class="notification-btn" id="notificationBtn" title="Notifications">
      <i class="fas fa-bell"></i>
      <span class="notification-count">3</span>
    </button>
  </div>
  
  <!-- User Menu -->
  <div class="user-menu">
    <button class="user-menu-btn" id="userMenuBtn" title="User Menu">
      <div class="user-avatar">
        <?php if (!empty($profilePictureUrl)): ?>
          <img src="<?= esc($profilePictureUrl) ?>" alt="Profile Picture">
        <?php else: ?>
          <i class="fas fa-user"></i>
        <?php endif; ?>
      </div>
      <span class="user-name"><?= isset($name) ? esc(explode(' ', $name)[0]) : (session()->get('username') ?? 'Admin') ?></span>
      <i class="fas fa-chevron-down"></i>
    </button>
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
        <h4>Payment Verified</h4>
        <p>Successfully verified payment</p>
        <span class="notification-time">2 minutes ago</span>
      </div>
    </div>
    <div class="notification-item">
      <div class="notification-icon primary">
        <i class="fas fa-info-circle"></i>
      </div>
      <div class="notification-content">
        <h4>System Update</h4>
        <p>QR scanner functionality improved</p>
        <span class="notification-time">1 hour ago</span>
      </div>
    </div>
    <div class="notification-item">
      <div class="notification-icon info">
        <i class="fas fa-user-plus"></i>
      </div>
      <div class="notification-content">
        <h4>New Student</h4>
        <p>Student profile created successfully</p>
        <span class="notification-time">3 hours ago</span>
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
      <div class="user-avatar">
        <?php if (!empty($profilePictureUrl)): ?>
          <img src="<?= esc($profilePictureUrl) ?>" alt="Profile Picture">
        <?php else: ?>
          <i class="fas fa-user"></i>
        <?php endif; ?>
      </div>
      <div>
        <h4><?= isset($name) ? esc($name) : (session()->get('username') ?? 'Admin User') ?></h4>
        <p>System Administrator</p>
      </div>
    </div>
  </div>
  <div class="dropdown-menu">
    <a href="<?= base_url('profile') ?>" class="dropdown-item">
      <i class="fas fa-user"></i>
      <span>My Profile</span>
    </a>
    <a href="<?= base_url('settings') ?>" class="dropdown-item">
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