<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ClearPay Payments - Record Payment</title>
  <link rel="stylesheet" href="<?= base_url('css/dashboard.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <style>
    /* Profile avatar styles for payments */
    .profile-avatar {
      position: relative;
      overflow: hidden;
    }
    
    .profile-avatar img {
      width: 100% !important;
      height: 100% !important;
      object-fit: cover !important;
      border-radius: 50%;
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
  </style>
  
  <!-- QR Code Scanner Library -->
  <script src="https://unpkg.com/jsqr@1.4.0/dist/jsQR.js"></script>
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
          <li class="nav-item active">
            <a href="<?= base_url('payments') ?>" class="nav-link">
              <i class="fas fa-credit-card"></i>
              <span>Payments</span>
              <span class="nav-badge">New</span>
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
        </ul>
        
        <div class="nav-divider"></div>
        
        <ul class="nav-list">
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
            <?php if (!empty($profilePictureUrl)): ?>
              <img src="<?= esc($profilePictureUrl) ?>" alt="Profile Picture">
            <?php else: ?>
              <i class="fas fa-user"></i>
            <?php endif; ?>
          </div>
          <div class="profile-info">
            <h4><?= isset($name) ? esc(explode(' ', $name)[0]) : (session()->get('username') ?? 'Admin User') ?></h4>
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
          <h1>Record Payment</h1>
          <p class="page-subtitle">Add a new student payment to the system</p>
        </div>
        <div class="header-right">
          <div class="search-container">
            <i class="fas fa-search"></i>
            <input type="text" class="search-input" placeholder="Search students, payments...">
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

        <!-- Contribution Information Card -->
        <?php if (isset($contribution)): ?>
        <div class="dashboard-card">
          <div class="card-header">
            <div>
              <h3><i class="fas fa-hand-holding-usd"></i> Recording Payment For</h3>
              <p>Selected contribution details</p>
            </div>
            <div class="card-actions">
              <button class="btn-icon">
                <i class="fas fa-edit"></i>
              </button>
            </div>
          </div>
          <div class="card-content">
            <div class="contribution-details">
              <h4><?= esc($contribution['title']) ?></h4>
              <p class="contribution-desc"><?= esc($contribution['description']) ?></p>
              <div class="stats-grid" style="grid-template-columns: repeat(3, 1fr); margin-top: 1rem;">
                <div class="stat-card info">
                  <div class="stat-content">
                    <div class="stat-header">
                      <h3>Amount</h3>
                      <div class="stat-icon">
                        <i class="fas fa-peso-sign"></i>
                      </div>
                    </div>
                    <div class="stat-value">₱<?= number_format($contribution['amount'], 2) ?></div>
                  </div>
                </div>
                <div class="stat-card success">
                  <div class="stat-content">
                    <div class="stat-header">
                      <h3>Category</h3>
                      <div class="stat-icon">
                        <i class="fas fa-tag"></i>
                      </div>
                    </div>
                    <div class="stat-value" style="font-size: 1.2rem;"><?= esc($contribution['category']) ?></div>
                  </div>
                </div>
                <?php if (isset($payments) && count($payments) > 0): ?>
                <div class="stat-card primary">
                  <div class="stat-content">
                    <div class="stat-header">
                      <h3>Collected</h3>
                      <div class="stat-icon">
                        <i class="fas fa-users"></i>
                      </div>
                    </div>
                    <div class="stat-value">₱<?= number_format(array_sum(array_column($payments, 'amount_paid')), 2) ?></div>
                    <div class="stat-footer">
                      <span class="stat-period">From <?= count($payments) ?> students</span>
                    </div>
                  </div>
                </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
        <?php endif; ?>

        <!-- Partial Payment Information Card -->
        <?php if (isset($mode) && $mode === 'partial_payment'): ?>
        <div class="dashboard-card">
          <div class="card-header">
            <div>
              <h3><i class="fas fa-clock"></i> Adding Partial Payment</h3>
              <p>Continue existing payment plan</p>
            </div>
          </div>
          <div class="card-content">
            <div class="contribution-details">
              <h4><?= esc($contribution['title']) ?></h4>
              <p class="contribution-desc"><?= esc($contribution['description']) ?></p>
              
              <div class="stats-grid" style="grid-template-columns: repeat(3, 1fr); margin-top: 1rem;">
                <div class="stat-card info">
                  <div class="stat-content">
                    <div class="stat-header">
                      <h3>Total Due</h3>
                      <div class="stat-icon">
                        <i class="fas fa-file-invoice-dollar"></i>
                      </div>
                    </div>
                    <div class="stat-value">$<?= number_format($payment_status['total_amount_due'], 2) ?></div>
                  </div>
                </div>
                <div class="stat-card success">
                  <div class="stat-content">
                    <div class="stat-header">
                      <h3>Already Paid</h3>
                      <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                      </div>
                    </div>
                    <div class="stat-value">$<?= number_format($payment_status['total_paid'], 2) ?></div>
                  </div>
                </div>
                <div class="stat-card warning">
                  <div class="stat-content">
                    <div class="stat-header">
                      <h3>Remaining</h3>
                      <div class="stat-icon">
                        <i class="fas fa-hourglass-half"></i>
                      </div>
                    </div>
                    <div class="stat-value">$<?= number_format($payment_status['remaining_balance'], 2) ?></div>
                  </div>
                </div>
              </div>
              
              <?php if (count($payment_status['payments']) > 0): ?>
              <div class="payment-history" style="margin-top: 1.5rem;">
                <h5 style="margin-bottom: 1rem; color: var(--text-secondary);">Payment History</h5>
                <div class="activity-timeline">
                  <?php foreach ($payment_status['payments'] as $payment): ?>
                    <div class="timeline-item">
                      <div class="timeline-marker success">
                        <i class="fas fa-dollar-sign"></i>
                      </div>
                      <div class="timeline-content">
                        <h4>Payment <?= $payment['payment_sequence'] ?></h4>
                        <p>Amount: $<?= number_format($payment['amount_paid'], 2) ?></p>
                        <span class="timeline-time"><?= date('M j, Y', strtotime($payment['payment_date'])) ?></span>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <?php endif; ?>

        <!-- Main Content Grid -->
        <div class="dashboard-grid">
          
          <!-- QR Code Scanner Card -->
          <div class="dashboard-card">
            <div class="card-header">
              <div>
                <h3><i class="fas fa-qrcode"></i> QR Code Scanner</h3>
                <p>Scan or upload student QR codes</p>
              </div>
            </div>
            <div class="card-content">
              <div class="quick-actions-grid" style="grid-template-columns: 1fr 1fr;">
                <button type="button" class="action-btn primary" id="scanQRButton">
                  <div class="action-icon">
                    <i class="fas fa-qrcode"></i>
                  </div>
                  <div class="action-text">
                    <h4>Scan Now</h4>
                    <p>Use camera to scan</p>
                  </div>
                </button>
                <button type="button" class="action-btn info" id="uploadQRButton">
                  <div class="action-icon">
                    <i class="fas fa-upload"></i>
                  </div>
                  <div class="action-text">
                    <h4>Upload QR</h4>
                    <p>Select image file</p>
                  </div>
                </button>
              </div>
              
              <div class="upload-processing" id="uploadProcessing" style="display: none; text-align: center; margin-top: 1rem; padding: 1rem; background: var(--bg-secondary); border-radius: var(--radius-md);">
                <div class="upload-spinner" style="margin: 0 auto 0.5rem; width: 24px; height: 24px; border: 2px solid var(--border-color); border-top-color: var(--primary-color); border-radius: 50%; animation: spin 1s linear infinite;"></div>
                <p id="uploadProcessingText" style="color: var(--text-secondary); font-size: 0.9rem;">Processing uploaded QR code...</p>
              </div>
              <input type="file" id="qrFileInput" accept="image/*" style="display: none;">
            </div>
          </div>

          <!-- Payment Form Card -->
          <div class="dashboard-card">
            <div class="card-header">
              <div>
                <h3><i class="fas fa-credit-card"></i> Payment Information</h3>
                <p>Enter student and payment details</p>
              </div>
            </div>
            <div class="card-content">
              <form id="paymentForm" autocomplete="off">
                <!-- Hidden fields for contribution data -->
                <?php if (isset($contribution_id)): ?>
                <input type="hidden" id="contributionId" name="contribution_id" value="<?= $contribution_id ?>">
                <?php endif; ?>
                
                <!-- Student Search Section -->
                <div class="form-group">
                  <label for="studentSearch">Search Existing Student</label>
                  <div class="search-container" style="position: relative;">
                    <i class="fas fa-search" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-tertiary);"></i>
                    <input type="text" id="studentSearch" class="search-input" placeholder="Search by name or ID..." autocomplete="off" style="padding-left: 2.5rem;">
                    <div class="search-results" id="searchResults" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: var(--bg-primary); border: 1px solid var(--border-color); border-radius: var(--radius-md); box-shadow: var(--shadow-lg); z-index: 100;"></div>
                  </div>
                  <small style="color: var(--text-secondary); font-size: 0.875rem; margin-top: 0.5rem; display: block;">Search for existing students or add new student information below</small>
                </div>

                <!-- Manual QR Code Entry -->
                <div class="form-group">
                  <label for="manualQR">Or Enter QR Code Data</label>
                  <div style="position: relative; display: flex; gap: 0.5rem;">
                    <div style="flex: 1; position: relative;">
                      <i class="fas fa-qrcode" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-tertiary);"></i>
                      <input type="text" id="manualQR" class="search-input" placeholder="Paste or type QR code data here..." autocomplete="off" style="padding-left: 2.5rem;">
                    </div>
                    <button type="button" id="searchQRBtn" class="btn-secondary">
                      <i class="fas fa-search"></i>
                    </button>
                  </div>
                  <small style="color: var(--text-secondary); font-size: 0.875rem; margin-top: 0.5rem; display: block;">Enter QR code data manually to auto-fill student information</small>
                </div>

                <div class="form-group">
                  <label for="studentName">Student Name</label>
                  <div style="position: relative;">
                    <i class="fas fa-user" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-tertiary);"></i>
                    <input type="text" id="studentName" name="student_name" class="search-input" placeholder="Enter student full name" required style="padding-left: 2.5rem;">
                  </div>
                </div>

                <div class="form-group">
                  <label for="studentId">Student ID</label>
                  <div style="position: relative;">
                    <i class="fas fa-id-card" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-tertiary);"></i>
                    <input type="text" id="studentId" name="student_id" class="search-input" placeholder="e.g., STU001" required style="padding-left: 2.5rem;">
                  </div>
                </div>

                <div class="form-group">
                  <label for="contactNumber">Contact Number</label>
                  <div style="position: relative;">
                    <i class="fas fa-phone" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-tertiary);"></i>
                    <input type="tel" id="contactNumber" name="contact_number" class="search-input" placeholder="e.g., 09123456789" style="padding-left: 2.5rem;">
                  </div>
                  <small style="color: var(--text-secondary); font-size: 0.875rem; margin-top: 0.25rem; display: block;">Optional - Contact number of the person making the payment</small>
                </div>

                <div class="form-group">
                  <label for="emailAddress">Email Address</label>
                  <div style="position: relative;">
                    <i class="fas fa-envelope" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-tertiary);"></i>
                    <input type="email" id="emailAddress" name="email_address" class="search-input" placeholder="e.g., student@example.com" style="padding-left: 2.5rem;">
                  </div>
                  <small style="color: var(--text-secondary); font-size: 0.875rem; margin-top: 0.25rem; display: block;">Optional - Email address for payment receipts and notifications</small>
                </div>

                <?php if (!isset($contribution)): ?>
                <div class="form-group">
                  <label for="contributionType">
                    Contribution Type 
                    <a href="<?= base_url('contributions') ?>" class="btn-secondary" style="margin-left: 0.5rem; padding: 0.25rem 0.5rem; font-size: 0.75rem; text-decoration: none;">Manage</a>
                  </label>
                  <div style="position: relative;">
                    <i class="fas fa-list" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-tertiary);"></i>
                    <select id="contributionType" name="contribution_type" required style="padding-left: 2.5rem; background: var(--bg-tertiary); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 0.75rem 1rem 0.75rem 2.5rem; width: 100%; font-size: 0.9rem;">
                      <option value="">Select contribution type</option>
                      <?php if (isset($all_contributions) && !empty($all_contributions)): ?>
                        <?php foreach ($all_contributions as $contrib): ?>
                          <option value="<?= $contrib['id'] ?>" data-amount="<?= $contrib['amount'] ?>">
                            <?= esc($contrib['title']) ?> - $<?= number_format($contrib['amount'], 2) ?>
                          </option>
                        <?php endforeach; ?>
                      <?php else: ?>
                        <option value="" disabled>No active contributions available</option>
                      <?php endif; ?>
                    </select>
                  </div>
                </div>
                <?php endif; ?>

                <div class="form-group">
                  <label for="amount">Amount (₱)</label>
                  <div style="position: relative;">
                    <i class="fas fa-peso-sign" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-tertiary);"></i>
                    <input type="number" id="amount" name="amount" 
                           value="<?= isset($contribution) ? number_format($contribution['amount'], 2, '.', '') : '' ?>" 
                           class="search-input" placeholder="0.00" step="0.01" min="0" required
                           style="padding-left: 2.5rem;"
                           <?= isset($contribution) ? '' : '' ?>>
                  </div>
                  
                  <!-- Payment Type Selection -->
                  <div style="margin-top: 1rem;">
                    <div style="display: flex; gap: 1rem;">
                      <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="radio" name="payment_type" value="full" id="fullPayment" checked style="margin: 0;">
                        <span style="color: var(--text-primary); font-weight: 500;">Full Payment</span>
                      </label>
                      <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="radio" name="payment_type" value="partial" id="partialPayment" style="margin: 0;">
                        <span style="color: var(--text-primary); font-weight: 500;">Partial Payment</span>
                      </label>
                    </div>
                  </div>
                  
                  <!-- Payment Status Display -->
                  <div id="paymentStatusDisplay" style="display: none; margin-top: 1rem; padding: 1rem; background: var(--info-light); border-radius: var(--radius-md); border-left: 4px solid var(--info-color);">
                    <div class="status-info">
                      <p style="margin-bottom: 0.5rem;"><strong>Payment Status:</strong> <span id="statusText"></span></p>
                      <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem; margin: 0.5rem 0;">
                        <p style="margin: 0;"><strong>Total Due:</strong> $<span id="totalDue">0.00</span></p>
                        <p style="margin: 0;"><strong>Already Paid:</strong> $<span id="totalPaid">0.00</span></p>
                        <p style="margin: 0;"><strong>Remaining:</strong> $<span id="remainingBalance">0.00</span></p>
                      </div>
                    </div>
                    <div id="paymentHistory" style="margin-top: 1rem;">
                      <strong>Payment History:</strong>
                      <ul id="paymentHistoryList" style="margin: 0.5rem 0; padding-left: 1.25rem;"></ul>
                    </div>
                  </div>
                  
                  <?php if (isset($contribution)): ?>
                  <small style="color: var(--text-secondary); font-size: 0.875rem; margin-top: 0.5rem; display: block;">Full amount: $<?= number_format($contribution['amount'], 2) ?> | You can make partial payments</small>
                  <?php endif; ?>
                </div>

                <div class="form-group">
                  <label for="paymentMethod">Payment Method</label>
                  <div style="position: relative;">
                    <i class="fas fa-credit-card" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-tertiary);"></i>
                    <select id="paymentMethod" name="payment_method" required style="padding-left: 2.5rem; background: var(--bg-tertiary); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 0.75rem 1rem 0.75rem 2.5rem; width: 100%; font-size: 0.9rem;">
                      <option value="cash">Cash</option>
                      <option value="card">GCash</option>
                    </select>
                  </div>
                </div>

                <!-- Submit Button -->
                <div class="form-group" style="margin-top: 2rem;">
                  <button type="submit" class="btn-primary" style="width: 100%; padding: 1rem; font-size: 1rem;">
                    <i class="fas fa-plus-circle"></i>
                    Record Payment
                  </button>
                </div>
              </form>
            </div>
          </div>
          
        </div>

        <!-- Info Card -->
        <div class="dashboard-card">
          <div class="card-header">
            <div>
              <h3><i class="fas fa-info-circle"></i> Payment Information</h3>
              <p>Important notes about payment recording</p>
            </div>
          </div>
          <div class="card-content">
            <div style="display: flex; align-items: flex-start; gap: 1rem;">
              <div style="width: 48px; height: 48px; background: var(--info-light); border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-receipt" style="color: var(--info-color); font-size: 1.25rem;"></i>
              </div>
              <div>
                <p style="margin-bottom: 1rem; color: var(--text-primary);">
                  <strong>Automatic QR Receipt:</strong> A QR receipt will be automatically generated after recording the payment. Students can use this receipt for verification purposes.
                </p>
                <div style="padding: 0.75rem; background: var(--bg-secondary); border-radius: var(--radius-md); border-left: 4px solid var(--success-color);">
                  <p style="margin: 0; color: var(--text-secondary); font-size: 0.9rem;">
                    <strong>Available Contributions:</strong> 
                    <?php 
                    $contributionCount = isset($all_contributions) ? count($all_contributions) : 0;
                    echo $contributionCount;
                    ?> contribution<?= $contributionCount != 1 ? ' types' : ' type' ?> available for payment recording
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </main>
  </div>

  <!-- QR Scanner Modal -->
  <div id="qrScannerModal" class="modal-overlay" style="display: none;">
    <div class="modal-container" style="max-width: 600px; width: 90%;">
      <div class="card-header">
        <div>
          <h3><i class="fas fa-qrcode"></i> Scan Student QR Code</h3>
          <p>Position the QR code within the scanner area</p>
        </div>
        <div class="card-actions">
          <button type="button" class="btn-icon" id="closeQRScanner">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>
      <div style="padding: 1.5rem;">
        <div class="scanner-container" style="position: relative; background: var(--bg-dark); border-radius: var(--radius-lg); overflow: hidden; margin-bottom: 1rem;">
          <video id="qrVideo" autoplay playsinline style="width: 100%; height: 300px; object-fit: cover;"></video>
          <canvas id="qrCanvas" style="display: none;"></canvas>
          <div class="scanner-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; display: flex; align-items: center; justify-content: center; pointer-events: none;">
            <div class="scanner-box" style="width: 200px; height: 200px; border: 2px solid var(--primary-color); border-radius: var(--radius-md); position: relative; background: rgba(79, 70, 229, 0.1);">
              <div class="scanner-line" style="position: absolute; top: 0; left: 0; right: 0; height: 2px; background: var(--primary-color); animation: scanner-line 2s linear infinite;"></div>
            </div>
          </div>
        </div>
        
        <div class="scanner-status" style="text-align: center; margin-bottom: 1rem;">
          <p id="scannerStatus" style="color: var(--text-secondary); margin: 0;">Position the QR code within the scanner box</p>
        </div>
        
        <div class="processing-indicator" id="processingIndicator" style="display: none; text-align: center; margin-bottom: 1rem;">
          <div style="margin: 0 auto 0.5rem; width: 32px; height: 32px; border: 3px solid var(--border-color); border-top-color: var(--primary-color); border-radius: 50%; animation: spin 1s linear infinite;"></div>
          <p id="processingText" style="color: var(--text-secondary); margin: 0;">Processing QR Code...</p>
        </div>
        
        <div class="scanner-result" id="scannerResult" style="display: none;">
          <div style="background: var(--success-light); border: 1px solid var(--success-color); border-radius: var(--radius-md); padding: 1rem; margin-bottom: 1rem;">
            <h4 style="color: var(--success-color); margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
              <i class="fas fa-check-circle"></i>
              Scanned Information:
            </h4>
            <div style="display: grid; gap: 0.5rem;">
              <p style="margin: 0;"><strong>Student ID:</strong> <span id="scannedId"></span></p>
              <p style="margin: 0;"><strong>Student Name:</strong> <span id="scannedName"></span></p>
              <p style="margin: 0;"><strong>Course:</strong> <span id="scannedCourse"></span></p>
            </div>
          </div>
          <div style="display: flex; gap: 1rem; justify-content: center;">
            <button type="button" class="btn-primary" id="useScannedData">
              <i class="fas fa-check"></i>
              Use This Data
            </button>
            <button type="button" class="btn-secondary" id="scanAgain">
              <i class="fas fa-redo"></i>
              Scan Again
            </button>
          </div>
        </div>
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
          <h4>Payment Recorded</h4>
          <p>Successfully recorded payment for John Doe</p>
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

    @keyframes scanner-line {
      0% { top: 0; }
      50% { top: calc(100% - 2px); }
      100% { top: 0; }
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    /* Form Styles */
    .search-results {
      background: var(--bg-primary);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-md);
      box-shadow: var(--shadow-lg);
      max-height: 200px;
      overflow-y: auto;
    }

    .search-result-item {
      padding: 0.75rem 1rem;
      border-bottom: 1px solid var(--border-color);
      cursor: pointer;
      transition: background-color var(--transition-fast);
    }

    .search-result-item:hover {
      background: var(--bg-secondary);
    }

    .search-result-item:last-child {
      border-bottom: none;
    }

    .search-result-name {
      font-weight: 600;
      color: var(--text-primary);
      margin-bottom: 0.25rem;
    }

    .search-result-details {
      font-size: 0.875rem;
      color: var(--text-secondary);
    }

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
      .dashboard-grid {
        grid-template-columns: 1fr;
      }
      
      .modal-container {
        width: 95%;
        margin: 1rem;
      }
      
      .scanner-container video {
        height: 250px;
      }
    }
  </style>

  <!-- JavaScript -->
  <script>
    // Pass student data to JavaScript
    window.STUDENTS_DATA = <?= json_encode($all_users ?? []) ?>;
    
    // Dashboard functionality
    document.addEventListener('DOMContentLoaded', function() {
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
    });
  </script>
  
  <!-- External JS -->
  <script src="<?= base_url('js/payments.js') ?>"></script>
</body>
</html>
