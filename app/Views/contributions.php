<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contributions</title>
  <link rel="stylesheet" href="<?= base_url('css/contributions.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="contributions-container">
    <!-- Header -->
    <div class="contributions-header">
      <div class="welcome-section">
        <h2>Contributions</h2>
        <p class="description">Manage payment types and settings</p>
      </div>
    </div>

    <!-- Stats Section -->
    <div class="stats-grid">
      <div class="stat-card stat-card-primary">
        <div class="stat-icon">
          <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
          <p class="stat-label">Active</p>
          <p class="stat-value"><?= isset($stats['active']) ? $stats['active'] : 0 ?></p>
        </div>
      </div>
      
      <div class="stat-card stat-card-success">
        <div class="stat-icon">
          <i class="fas fa-list"></i>
        </div>
        <div class="stat-content">
          <p class="stat-label">Total</p>
          <p class="stat-value"><?= isset($stats['total']) ? $stats['total'] : 0 ?></p>
        </div>
      </div>
      
      <div class="stat-card stat-card-warning">
        <div class="stat-icon">
          <i class="fas fa-pause-circle"></i>
        </div>
        <div class="stat-content">
          <p class="stat-label">Inactive</p>
          <p class="stat-value"><?= isset($stats['inactive']) ? $stats['inactive'] : 0 ?></p>
        </div>
      </div>
      
      <div class="stat-card stat-card-info">
        <div class="stat-icon">
          <i class="fas fa-calendar-day"></i>
        </div>
        <div class="stat-content">
          <p class="stat-label">Today</p>
          <p class="stat-value"><?= count(array_filter($contributions ?? [], function($c) { return date('Y-m-d', strtotime($c['created_at'])) == date('Y-m-d'); })) ?></p>
        </div>
      </div>
    </div>

    <!-- Add New Contribution Button -->
    <div class="add-contribution-section">
      <button class="btn-primary btn-add-contribution" id="addContributionBtn">
        <i class="fas fa-plus"></i>
        Add New Contribution
      </button>
    </div>

    <!-- Success/Error Messages -->
    <div class="success-message" id="successMessage" style="display: none;"></div>
    <div class="error-message" id="errorMessage" style="display: none;"></div>

    <!-- Active Contributions -->
    <div class="contributions-content">
      <div class="section-header">
        <h3>Active Contributions</h3>
        <p class="description">Currently available payment types</p>
      </div>
      
      <div class="contributions-list" id="contributionsList">
        <?php if (!empty($contributions)): ?>
          <?php foreach ($contributions as $contribution): ?>
            <div class="contribution-item" data-id="<?= $contribution['id'] ?>" data-title="<?= esc($contribution['title']) ?>">
              <div class="contribution-info clickable-area" style="cursor: pointer;" title="Click to view payments for this contribution">
                <div class="contribution-details">
                  <h4><?= esc($contribution['title']) ?></h4>
                  <p class="contribution-desc"><?= esc($contribution['description']) ?></p>
                  <div class="contribution-tags">
                    <span class="tag tag-<?= strtolower(str_replace(' ', '-', $contribution['category'])) ?>"><?= esc($contribution['category']) ?></span>
                    <span class="tag tag-clickable">
                      <i class="fas fa-eye"></i> View Payments
                    </span>
                  </div>
                </div>
                <div class="contribution-amount">
                  <span class="amount">$<?= number_format($contribution['amount'], 2) ?></span>
                </div>
              </div>
              <div class="contribution-actions">
                <label class="toggle-switch">
                  <input type="checkbox" <?= $contribution['status'] === 'active' ? 'checked' : '' ?> data-contribution-id="<?= $contribution['id'] ?>">
                  <span class="slider"></span>
                </label>
                <button class="action-btn edit-btn" data-contribution-id="<?= $contribution['id'] ?>">
                  <i class="fas fa-edit"></i>
                </button>
                <button class="action-btn delete-btn" data-contribution-id="<?= $contribution['id'] ?>">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="no-contributions">
            <div class="no-data-icon">
              <i class="fas fa-hand-holding-usd"></i>
            </div>
            <h3>No Contributions Yet</h3>
            <p>Start by adding your first contribution type</p>
            <button class="btn-primary" onclick="document.getElementById('addContributionBtn').click()">
              <i class="fas fa-plus"></i>
              Add First Contribution
            </button>
          </div>
        <?php endif; ?>
      </div>
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
      <a href="<?= base_url('contributions') ?>" class="nav-link active">
        <i class="fas fa-hand-holding-usd"></i>
        <span>Contributions</span>
      </a>
      <a href="<?= base_url('payments/history') ?>" class="nav-link">
        <i class="fas fa-clock"></i>
        <span>History</span>
      </a>
    </nav>
  </div>

  <!-- Add Contribution Modal -->
  <div id="contributionModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3 id="modalTitle">Add New Contribution</h3>
        <button type="button" class="close-btn" id="closeModal">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="modal-body">
        <form id="contributionForm">
          <input type="hidden" id="contributionId" name="contribution_id">
          
          <div class="form-group">
            <label for="contributionTitle">Contribution Title</label>
            <input type="text" id="contributionTitle" name="title" placeholder="e.g., Uniform Payments" required>
            <i class="fas fa-heading input-icon"></i>
          </div>

          <div class="form-group">
            <label for="contributionDescription">Description</label>
            <textarea id="contributionDescription" name="description" placeholder="Brief description of this contribution type" rows="3"></textarea>
            <i class="fas fa-align-left input-icon"></i>
          </div>

          <div class="form-group">
            <label for="contributionAmount">Default Amount ($)</label>
            <input type="number" id="contributionAmount" name="amount" placeholder="0.00" step="0.01" min="0" required>
            <i class="fas fa-dollar-sign input-icon"></i>
          </div>

          <div class="form-group">
            <label for="contributionCategory">Category</label>
            <select id="contributionCategory" name="category" required>
              <option value="">Select category</option>
              <option value="Uniform">Uniform</option>
              <option value="Activity">Activity</option>
              <option value="Meal">Meal</option>
              <option value="Education">Education</option>
              <option value="Transportation">Transportation</option>
              <option value="Other">Other</option>
            </select>
            <i class="fas fa-list input-icon"></i>
          </div>

          <div class="form-group form-row-full">
            <div class="button-group">
              <button type="button" class="btn-secondary" id="cancelBtn">Cancel</button>
              <button type="submit" class="btn-primary" id="submitBtn">
                <span class="btn-text">Add Contribution</span>
                <i class="fas fa-spinner fa-spin btn-loader" style="display: none;"></i>
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- External JS -->
  <script>
    // Pass base URL to JavaScript
    window.APP_BASE_URL = '<?= base_url() ?>';
    console.log('Base URL from PHP:', window.APP_BASE_URL);
  </script>
  <script src="<?= base_url('js/contributions.js') ?>"></script>
</body>
</html>