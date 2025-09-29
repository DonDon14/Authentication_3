<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile Settings</title>
  <link rel="stylesheet" href="<?= base_url('css/profile.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="profile-container">
    <!-- Header -->
    <div class="profile-header">
      <div class="header-left">
        <a href="<?= base_url('dashboard') ?>" class="back-btn">
          <i class="fas fa-arrow-left"></i>
        </a>
        <div class="header-content">
          <h2>Profile Settings</h2>
          <p class="description">Manage your account information</p>
        </div>
      </div>
      <div class="logout-section">
        <a href="<?= base_url('logout') ?>" class="logout-btn">
          <i class="fas fa-sign-out-alt"></i>
        </a>
      </div>
    </div>

    <!-- Profile Content -->
    <div class="profile-content">
      <!-- Profile Picture Section -->
      <div class="profile-picture-section">
        <div class="profile-avatar">
          <i class="fas fa-user"></i>
        </div>
        <button class="change-photo-btn">
          <i class="fas fa-camera"></i>
          Change Photo
        </button>
      </div>

      <!-- Profile Form -->
      <form id="profileForm" class="profile-form">
        <div class="form-section">
          <h3>Personal Information</h3>
          
          <div class="form-row">
            <div class="form-group">
              <label for="fullName">Full Name</label>
              <input type="text" id="fullName" name="full_name" value="<?= esc($name ?? 'John Doe') ?>" required>
            </div>
            
            <div class="form-group">
              <label for="username">Username</label>
              <input type="text" id="username" name="username" value="<?= esc($username ?? 'johndoe') ?>" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="email">Email Address</label>
              <input type="email" id="email" name="email" value="<?= esc($email ?? 'john@example.com') ?>" required>
            </div>
            
            <div class="form-group">
              <label for="phone">Phone Number</label>
              <input type="tel" id="phone" name="phone" value="<?= esc($phone ?? '+1 234 567 8900') ?>">
            </div>
          </div>
        </div>

        <div class="form-section">
          <h3>Security Settings</h3>
          
          <div class="form-group">
            <label for="currentPassword">Current Password</label>
            <input type="password" id="currentPassword" name="current_password" placeholder="Enter current password">
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="newPassword">New Password</label>
              <input type="password" id="newPassword" name="new_password" placeholder="Enter new password">
            </div>
            
            <div class="form-group">
              <label for="confirmPassword">Confirm Password</label>
              <input type="password" id="confirmPassword" name="confirm_password" placeholder="Confirm new password">
            </div>
          </div>
        </div>

        <div class="form-actions">
          <button type="button" class="btn-secondary" onclick="window.history.back()">Cancel</button>
          <button type="submit" class="btn-primary">Save Changes</button>
        </div>
      </form>
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
      <a href="<?= base_url('payments/history') ?>" class="nav-link">
        <i class="fas fa-clock"></i>
        <span>History</span>
      </a>
    </nav>
  </div>

  <script src="<?= base_url('js/profile.js') ?>"></script>
</body>
</html>