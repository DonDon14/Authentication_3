<link rel="stylesheet" href="/Authentication_3/public/css/header-components.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


<div class="dashboard-header">
  <div class="dashboard-right">
    <!-- Notification Bell-->
    <div class="notification">
      <button class="notification-btn" onclick="toggleNotifications()">
        <i class="fas fa-bell"></i>
        <span class="notification-count">3</span>
      </button>
    </div>

    <!-- Profile Section -->
    <div class="profile-section" id="profileSection">
      <button type="button" class="profile-btn" id="profileBtn">
        <div class="avatar">
          <?php if (!empty($profilePictureUrl)): ?>
            <img src="<?= esc($profilePictureUrl) ?>" alt="Profile Picture" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
          <?php else: ?>
            <i class="fas fa-user"></i>
          <?php endif; ?>
        </div>
        <span class="user-name"><?= esc($name ? explode(' ', $name)[0] : 'Admin') ?></span>
        <i class="fas fa-chevron-down"></i>
      </button>

      <!-- User Dropdown Menu -->
      <div class="user-dropdown" id="userDropdown" style="display: none;">
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
          <div class="dropdown-divider"></div>
          <a href="<?= base_url('logout') ?>" class="dropdown-item logout">
            <i class="fas fa-sign-out-alt"></i>
            <span>Sign Out</span>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="/Authentication_3/public/js/header-components.js"></script>
