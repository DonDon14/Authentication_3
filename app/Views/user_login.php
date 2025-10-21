
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Login - ClearPay</title>
  <link rel="stylesheet" href="<?= base_url('css/user_login.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <img src="<?= base_url('images/clearpaylogo.png') ?>" alt="ClearPay Logo" class="logo">
        <h1>ClearPay</h1>
        <h2>Student Portal</h2>
        <p class="description">Access your payment records</p>
      </div>

      <!-- Error/Success Messages -->
      <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-error">
          <i class="fas fa-exclamation-circle"></i>
          <?= session()->getFlashdata('error') ?>
        </div>
      <?php endif; ?>

      <?php if (session()->getFlashdata('message')): ?>
        <div class="alert alert-success">
          <i class="fas fa-check-circle"></i>
          <?= session()->getFlashdata('message') ?>
        </div>
      <?php endif; ?>

      <div class="error-message" id="errorMessage" style="display: none;"></div>

      <form id="userLoginForm" class="login-form" autocomplete="off">
        <div class="form-group">
          <label for="studentName">Full Name</label>
          <div class="input-wrapper">
            <input type="text" id="studentName" name="student_name" 
                   placeholder="Enter your full name as registered" 
                   autocomplete="off" required>
            <i class="fas fa-user input-icon"></i>
          </div>
        </div>

        <div class="form-group">
          <label for="studentId">Student ID</label>
          <div class="input-wrapper">
            <input type="text" id="studentId" name="student_id" 
                   placeholder="Enter your student ID" 
                   autocomplete="off" required>
            <i class="fas fa-id-card input-icon"></i>
          </div>
        </div>

        <button type="submit" class="login-btn" id="loginBtn">
          <i class="fas fa-sign-in-alt"></i>
          <span>Access My Records</span>
        </button>
      </form>

      <div class="login-footer">
        <p class="help-text">
          <i class="fas fa-info-circle"></i>
          Enter your name and student ID exactly as they appear in your payment records
        </p>
        
        <div class="admin-link">
          <a href="<?= base_url('/') ?>" class="link-secondary">
            <i class="fas fa-user-shield"></i>
            Admin Login
          </a>
        </div>
      </div>
    </div>
  </div>

  <script src="<?= base_url('js/user_login.js') ?>"></script>
</body>
</html>