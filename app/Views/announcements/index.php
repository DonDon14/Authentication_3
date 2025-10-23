<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Announcements Management - ClearPay Admin</title>
  <link rel="stylesheet" href="<?= base_url('css/dashboard.css') ?>">
  <link rel="stylesheet" href="<?= base_url('css/header-components.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Additional CSS for announcements -->
  <style>
    /* Announcement specific styles */
    .announcement-card {
      background: var(--bg-primary);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow-sm);
      border: 1px solid var(--border-color);
      margin-bottom: 1rem;
      overflow: hidden;
      transition: var(--transition-fast);
    }

    .announcement-card:hover {
      box-shadow: var(--shadow-md);
      border-color: var(--primary-color);
    }

    .announcement-header {
      padding: 1rem 1.5rem;
      border-bottom: 1px solid var(--border-color);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .announcement-title {
      margin: 0;
      font-size: 1.125rem;
      font-weight: 600;
      color: var(--text-primary);
    }

    .announcement-meta {
      display: flex;
      align-items: center;
      gap: 1rem;
      font-size: 0.875rem;
      color: var(--text-secondary);
    }

    .announcement-body {
      padding: 1.5rem;
    }

    .announcement-content {
      color: var(--text-primary);
      line-height: 1.6;
      margin-bottom: 1rem;
    }

    .announcement-badges {
      display: flex;
      gap: 0.5rem;
      flex-wrap: wrap;
    }

    .badge {
      padding: 0.25rem 0.75rem;
      border-radius: var(--radius-sm);
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
    }

    .badge-priority-critical {
      background: var(--error-bg);
      color: var(--error-color);
    }

    .badge-priority-high {
      background: var(--warning-bg);
      color: var(--warning-color);
    }

    .badge-priority-medium {
      background: var(--info-bg);
      color: var(--info-color);
    }

    .badge-priority-low {
      background: var(--bg-secondary);
      color: var(--text-secondary);
    }

    .badge-status-published {
      background: var(--success-bg);
      color: var(--success-color);
    }

    .badge-status-draft {
      background: var(--warning-bg);
      color: var(--warning-color);
    }

    .badge-status-archived {
      background: var(--bg-secondary);
      color: var(--text-secondary);
    }

    .badge-audience {
      background: var(--primary-bg);
      color: var(--primary-color);
    }

    .badge-type {
      background: var(--info-bg);
      color: var(--info-color);
    }

    .announcement-actions {
      display: flex;
      gap: 0.5rem;
    }

    .btn-icon {
      padding: 0.5rem;
      border: none;
      border-radius: var(--radius-md);
      background: var(--bg-secondary);
      color: var(--text-secondary);
      cursor: pointer;
      transition: var(--transition-fast);
    }

    .btn-icon:hover {
      background: var(--primary-color);
      color: white;
    }

    .btn-icon.danger:hover {
      background: var(--error-color);
    }

    .btn-icon.warning:hover {
      background: var(--warning-color);
    }

    /* Modal styles */
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
      max-width: 600px;
      width: 90%;
      max-height: 90vh;
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

    .form-group {
      margin-bottom: 1rem;
    }

    .form-label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 500;
      color: var(--text-primary);
    }

    .form-input, .form-textarea, .form-select {
      width: 100%;
      padding: 0.75rem;
      border: 1px solid var(--border-color);
      border-radius: var(--radius-md);
      font-size: 0.875rem;
      transition: var(--transition-fast);
      background: var(--bg-primary);
      color: var(--text-primary);
    }

    .form-input:focus, .form-textarea:focus, .form-select:focus {
      outline: none;
      border-color: var(--primary-color);
      box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.1);
    }

    .form-textarea {
      resize: vertical;
      min-height: 100px;
    }

    /* Stats cards */
    .announcements-stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
      margin-bottom: 2rem;
    }

    .stat-card-small {
      background: var(--bg-primary);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow-sm);
      border: 1px solid var(--border-color);
      padding: 1rem;
      text-align: center;
    }

    .stat-number {
      font-size: 2rem;
      font-weight: 700;
      color: var(--primary-color);
      margin-bottom: 0.5rem;
    }

    .stat-label {
      font-size: 0.875rem;
      color: var(--text-secondary);
      text-transform: uppercase;
      font-weight: 600;
    }

    /* Search and filter section */
    .filters-section {
      background: var(--bg-primary);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow-sm);
      border: 1px solid var(--border-color);
      padding: 1.5rem;
      margin-bottom: 2rem;
    }

    .filters-grid {
      display: grid;
      grid-template-columns: 2fr 1fr 1fr auto;
      gap: 1rem;
      align-items: end;
    }

    .search-group {
      position: relative;
    }

    .search-input {
      padding-left: 2.5rem;
    }

    .search-icon {
      position: absolute;
      left: 0.75rem;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-secondary);
    }

    /* Empty state */
    .empty-state {
      text-align: center;
      padding: 3rem;
      color: var(--text-secondary);
    }

    .empty-state i {
      font-size: 3rem;
      margin-bottom: 1rem;
      opacity: 0.5;
    }

    .empty-state h3 {
      margin-bottom: 0.5rem;
      color: var(--text-primary);
    }

    .empty-state p {
      margin-bottom: 1.5rem;
    }
  </style>
</head>
<body>
  <!-- Main App Container -->
  <div class="app-container">
    
    <!-- Sidebar Navigation -->
    <?= $this->include('partials/sidebar') ?>

    <!-- Main Content Area -->
    <main class="main-content">
      
      <!-- Top Header Bar -->
      <header class="header">
        <div class="header-left">
          <h1 class="page-title">Announcements Management</h1>
          <p class="page-subtitle">Create and manage system announcements for students and staff</p>
        </div>
        
        <?= $this->include('partials/header_components') ?>
      </header>

      <!-- Announcements Content -->
      <div class="dashboard-content">
        
        <!-- Statistics Cards -->
        <div class="announcements-stats">
          <div class="stat-card-small">
            <div class="stat-number"><?= $status_counts['total'] ?></div>
            <div class="stat-label">Total</div>
          </div>
          <div class="stat-card-small">
            <div class="stat-number"><?= $status_counts['published'] ?></div>
            <div class="stat-label">Published</div>
          </div>
          <div class="stat-card-small">
            <div class="stat-number"><?= $status_counts['draft'] ?></div>
            <div class="stat-label">Drafts</div>
          </div>
          <div class="stat-card-small">
            <div class="stat-number"><?= $status_counts['archived'] ?></div>
            <div class="stat-label">Archived</div>
          </div>
        </div>

        <!-- Filters and Search -->
        <div class="filters-section">
          <div class="filters-grid">
            <div class="search-group">
              <i class="fas fa-search search-icon"></i>
              <input type="text" class="form-input search-input" placeholder="Search announcements..." id="searchInput">
            </div>
            <div class="form-group">
              <select class="form-select" id="statusFilter">
                <option value="">All Status</option>
                <option value="published">Published</option>
                <option value="draft">Draft</option>
                <option value="archived">Archived</option>
              </select>
            </div>
            <div class="form-group">
              <select class="form-select" id="priorityFilter">
                <option value="">All Priority</option>
                <option value="critical">Critical</option>
                <option value="high">High</option>
                <option value="medium">Medium</option>
                <option value="low">Low</option>
              </select>
            </div>
            <div>
              <button class="btn btn-primary" onclick="openAddModal()">
                <i class="fas fa-plus"></i>
                Add Announcement
              </button>
            </div>
          </div>
        </div>

        <!-- Announcements List -->
        <div class="announcements-list" id="announcementsList">
          <?php if (!empty($announcements)): ?>
            <?php foreach ($announcements as $announcement): ?>
              <div class="announcement-card" data-status="<?= $announcement['status'] ?>" data-priority="<?= $announcement['priority'] ?>">
                <div class="announcement-header">
                  <div>
                    <h3 class="announcement-title"><?= esc($announcement['title']) ?></h3>
                    <div class="announcement-meta">
                      <span><i class="fas fa-calendar"></i> <?= date('M j, Y g:i A', strtotime($announcement['created_at'])) ?></span>
                      <?php if ($announcement['published_at']): ?>
                        <span><i class="fas fa-eye"></i> Published <?= date('M j, Y', strtotime($announcement['published_at'])) ?></span>
                      <?php endif; ?>
                      <?php if ($announcement['expires_at']): ?>
                        <span><i class="fas fa-clock"></i> Expires <?= date('M j, Y', strtotime($announcement['expires_at'])) ?></span>
                      <?php endif; ?>
                    </div>
                  </div>
                  <div class="announcement-actions">
                    <button class="btn-icon" onclick="editAnnouncement(<?= $announcement['id'] ?>)" title="Edit">
                      <i class="fas fa-edit"></i>
                    </button>
                    <?php if ($announcement['status'] !== 'archived'): ?>
                      <button class="btn-icon warning" onclick="archiveAnnouncement(<?= $announcement['id'] ?>)" title="Archive">
                        <i class="fas fa-archive"></i>
                      </button>
                    <?php endif; ?>
                    <button class="btn-icon danger" onclick="deleteAnnouncement(<?= $announcement['id'] ?>)" title="Delete">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </div>
                <div class="announcement-body">
                  <div class="announcement-content">
                    <?= nl2br(esc($announcement['content'])) ?>
                  </div>
                  <div class="announcement-badges">
                    <span class="badge badge-priority-<?= $announcement['priority'] ?>"><?= ucfirst($announcement['priority']) ?></span>
                    <span class="badge badge-status-<?= $announcement['status'] ?>"><?= ucfirst($announcement['status']) ?></span>
                    <span class="badge badge-audience"><?= ucfirst($announcement['target_audience']) ?></span>
                    <span class="badge badge-type"><?= ucfirst($announcement['type']) ?></span>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="empty-state">
              <i class="fas fa-bullhorn"></i>
              <h3>No Announcements Yet</h3>
              <p>Start by creating your first announcement to communicate with students and staff.</p>
              <button class="btn btn-primary" onclick="openAddModal()">
                <i class="fas fa-plus"></i>
                Create First Announcement
              </button>
            </div>
          <?php endif; ?>
        </div>
      </div>

    </main>
  </div>

  <!-- Add/Edit Announcement Modal -->
  <div class="modal-overlay" id="announcementModal">
    <div class="modal-container">
      <div class="modal-header">
        <h3 id="modalTitle">Add New Announcement</h3>
        <button class="modal-close" onclick="closeModal()">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="modal-content">
        <form id="announcementForm">
          <input type="hidden" id="announcementId">
          
          <div class="form-group">
            <label class="form-label" for="title">Title</label>
            <input type="text" id="title" name="title" class="form-input" required maxlength="255">
          </div>
          
          <div class="form-group">
            <label class="form-label" for="content">Content</label>
            <textarea id="content" name="content" class="form-textarea" required rows="6"></textarea>
          </div>
          
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
              <label class="form-label" for="type">Type</label>
              <select id="type" name="type" class="form-select" required>
                <option value="">Select Type</option>
                <option value="general">General</option>
                <option value="urgent">Urgent</option>
                <option value="maintenance">Maintenance</option>
                <option value="event">Event</option>
                <option value="deadline">Deadline</option>
              </select>
            </div>
            
            <div class="form-group">
              <label class="form-label" for="priority">Priority</label>
              <select id="priority" name="priority" class="form-select" required>
                <option value="">Select Priority</option>
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
                <option value="critical">Critical</option>
              </select>
            </div>
          </div>
          
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
              <label class="form-label" for="target_audience">Target Audience</label>
              <select id="target_audience" name="target_audience" class="form-select" required>
                <option value="">Select Audience</option>
                <option value="all">All Users</option>
                <option value="students">Students Only</option>
                <option value="admins">Admins Only</option>
                <option value="staff">Staff Only</option>
              </select>
            </div>
            
            <div class="form-group">
              <label class="form-label" for="status">Status</label>
              <select id="status" name="status" class="form-select" required>
                <option value="draft">Draft</option>
                <option value="published">Published</option>
                <option value="archived">Archived</option>
              </select>
            </div>
          </div>
          
          <div class="form-group">
            <label class="form-label" for="expires_at">Expiration Date (Optional)</label>
            <input type="datetime-local" id="expires_at" name="expires_at" class="form-input">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="saveAnnouncementForm(event)">Save Announcement</button>
      </div>
    </div>
  </div>

  <!-- JavaScript -->
  <script>
    let currentEditId = null;

    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
      setupEventListeners();
    });

    function setupEventListeners() {
      // Search functionality
      document.getElementById('searchInput').addEventListener('input', filterAnnouncements);
      document.getElementById('headerSearch').addEventListener('input', filterAnnouncements);
      document.getElementById('statusFilter').addEventListener('change', filterAnnouncements);
      document.getElementById('priorityFilter').addEventListener('change', filterAnnouncements);
    }

    // Modal functions
    function openAddModal() {
      currentEditId = null;
      document.getElementById('modalTitle').textContent = 'Add New Announcement';
      document.getElementById('announcementForm').reset();
      document.getElementById('announcementId').value = '';
      document.getElementById('announcementModal').classList.add('active');
    }

    function closeModal() {
      document.getElementById('announcementModal').classList.remove('active');
      currentEditId = null;
    }

    // Edit announcement
    async function editAnnouncement(id) {
      try {
        const response = await fetch(`<?= base_url('announcements/get') ?>/${id}`);
        const data = await response.json();
        
        if (data.success) {
          currentEditId = id;
          const announcement = data.announcement;
          
          document.getElementById('modalTitle').textContent = 'Edit Announcement';
          document.getElementById('announcementId').value = id;
          document.getElementById('title').value = announcement.title;
          document.getElementById('content').value = announcement.content;
          document.getElementById('type').value = announcement.type;
          document.getElementById('priority').value = announcement.priority;
          document.getElementById('target_audience').value = announcement.target_audience;
          document.getElementById('status').value = announcement.status;
          document.getElementById('expires_at').value = announcement.expires_at ? announcement.expires_at.replace(' ', 'T') : '';
          
          document.getElementById('announcementModal').classList.add('active');
        } else {
          showNotification('Error loading announcement', 'error');
        }
      } catch (error) {
        console.error('Error loading announcement:', error);
        showNotification('Error loading announcement', 'error');
      }
    }

    // Save announcement
    async function saveAnnouncementForm(event) {
      // Prevent any default behavior if event exists
      if (event) {
        event.preventDefault();
        event.stopPropagation();
      }
      
      console.log('Save announcement form function called');
      
      // Wait a bit for modal to be fully rendered
      await new Promise(resolve => setTimeout(resolve, 100));
      
      const form = document.getElementById('announcementForm');
      console.log('Form element found:', form);
      
      if (!form) {
        console.error('Form not found - checking all forms on page');
        const allForms = document.querySelectorAll('form');
        console.log('All forms on page:', allForms);
        alert('Form not found');
        return;
      }
      
      // Check if form is actually a form element
      if (form.tagName !== 'FORM') {
        console.error('Element found but not a form element:', form.tagName);
        alert('Form element is not valid');
        return;
      }
      
      // Manually collect form data instead of using FormData constructor
      const formData = new FormData();
      
      // Get form values manually
      const title = document.getElementById('title').value;
      const content = document.getElementById('content').value;
      const type = document.getElementById('type').value;
      const priority = document.getElementById('priority').value;
      const target_audience = document.getElementById('target_audience').value;
      const status = document.getElementById('status').value;
      const expires_at = document.getElementById('expires_at').value;
      
      // Add to FormData
      formData.append('title', title);
      formData.append('content', content);
      formData.append('type', type);
      formData.append('priority', priority);
      formData.append('target_audience', target_audience);
      formData.append('status', status);
      if (expires_at) {
        formData.append('expires_at', expires_at);
      }
      
      // Log form data for debugging
      console.log('Form data:');
      console.log('title:', title);
      console.log('content:', content);
      console.log('type:', type);
      console.log('priority:', priority);
      console.log('target_audience:', target_audience);
      console.log('status:', status);
      console.log('expires_at:', expires_at);
      
      // Validate required fields
      if (!title || !content || !type || !priority || !target_audience || !status) {
        alert('Please fill in all required fields');
        return;
      }
      
      // Additional validation
      if (title.length < 3) {
        alert('Title must be at least 3 characters long');
        return;
      }
      
      if (content.length < 10) {
        alert('Content must be at least 10 characters long');
        return;
      }
      
      // Disable the save button to prevent double-clicks
      const saveBtn = event ? event.target : document.querySelector('.btn-primary');
      if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.textContent = 'Saving...';
      }
      
      try {
        // Use PHP's base_url() function directly
        const url = currentEditId 
          ? `<?= base_url() ?>announcements/update/${currentEditId}`
          : `<?= base_url() ?>announcements/create`;
          
        console.log('Full base URL from PHP:', '<?= base_url() ?>');
        console.log('Posting to URL:', url);
        console.log('Current edit ID:', currentEditId);
        
        const response = await fetch(url, {
          method: 'POST',
          body: formData,
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        });
        
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const responseText = await response.text();
        console.log('Raw response:', responseText);
        
        let data;
        try {
          data = JSON.parse(responseText);
        } catch (parseError) {
          console.error('Failed to parse JSON response:', parseError);
          console.error('Response text:', responseText);
          throw new Error('Invalid JSON response from server');
        }
        
        console.log('Parsed response data:', data);
        
        if (data.success) {
          alert('Announcement saved successfully!');
          closeModal();
          setTimeout(() => {
            window.location.reload();
          }, 500);
        } else {
          let errorMessage = data.message || 'Error saving announcement';
          if (data.errors && Object.keys(data.errors).length > 0) {
            errorMessage += '\n\nValidation errors:';
            Object.keys(data.errors).forEach(field => {
              errorMessage += '\n- ' + data.errors[field];
            });
          }
          alert(errorMessage);
          if (data.errors) {
            console.error('Validation errors:', data.errors);
          }
        }
      } catch (error) {
        console.error('Error saving announcement:', error);
        alert('Error saving announcement: ' + error.message);
      } finally {
        // Re-enable the save button
        if (saveBtn) {
          saveBtn.disabled = false;
          saveBtn.textContent = 'Save Announcement';
        }
      }
    }

    // Archive announcement
    async function archiveAnnouncement(id) {
      if (!confirm('Are you sure you want to archive this announcement?')) {
        return;
      }
      
      try {
        const response = await fetch(`<?= base_url('announcements/archive') ?>/${id}`, {
          method: 'POST'
        });
        
        const data = await response.json();
        
        if (data.success) {
          showNotification(data.message, 'success');
          setTimeout(() => {
            window.location.reload();
          }, 1000);
        } else {
          showNotification(data.message || 'Error archiving announcement', 'error');
        }
      } catch (error) {
        console.error('Error archiving announcement:', error);
        showNotification('Error archiving announcement', 'error');
      }
    }

    // Delete announcement
    async function deleteAnnouncement(id) {
      if (!confirm('Are you sure you want to delete this announcement? This action cannot be undone.')) {
        return;
      }
      
      try {
        const response = await fetch(`<?= base_url('announcements/delete') ?>/${id}`, {
          method: 'DELETE'
        });
        
        const data = await response.json();
        
        if (data.success) {
          showNotification(data.message, 'success');
          setTimeout(() => {
            window.location.reload();
          }, 1000);
        } else {
          showNotification(data.message || 'Error deleting announcement', 'error');
        }
      } catch (error) {
        console.error('Error deleting announcement:', error);
        showNotification('Error deleting announcement', 'error');
      }
    }

    // Filter announcements
    function filterAnnouncements() {
      const searchTerm = (document.getElementById('searchInput').value || document.getElementById('headerSearch').value).toLowerCase();
      const statusFilter = document.getElementById('statusFilter').value;
      const priorityFilter = document.getElementById('priorityFilter').value;
      
      const cards = document.querySelectorAll('.announcement-card');
      
      cards.forEach(card => {
        const title = card.querySelector('.announcement-title').textContent.toLowerCase();
        const content = card.querySelector('.announcement-content').textContent.toLowerCase();
        const status = card.dataset.status;
        const priority = card.dataset.priority;
        
        const matchesSearch = !searchTerm || title.includes(searchTerm) || content.includes(searchTerm);
        const matchesStatus = !statusFilter || status === statusFilter;
        const matchesPriority = !priorityFilter || priority === priorityFilter;
        
        if (matchesSearch && matchesStatus && matchesPriority) {
          card.style.display = 'block';
        } else {
          card.style.display = 'none';
        }
      });
    }

    // Notification system
    function showNotification(message, type = 'info') {
      // Create notification element
      const notification = document.createElement('div');
      notification.className = `notification notification-${type}`;
      notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
        <span>${message}</span>
      `;
      
      // Add to page
      document.body.appendChild(notification);
      
      // Show notification
      setTimeout(() => notification.classList.add('show'), 100);
      
      // Remove notification
      setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
      }, 3000);
    }

    // Toggle functions for header
    function toggleNotifications() {
      // Implementation for notifications toggle
    }

    function toggleUserMenu() {
      const dropdown = document.getElementById('userDropdown');
      dropdown.classList.toggle('active');
    }

    function toggleProfileMenu() {
      // Implementation for profile menu toggle
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
      if (!e.target.closest('.user-menu')) {
        document.getElementById('userDropdown')?.classList.remove('active');
      }
    });
  </script>


  <!-- Dashboard JavaScript -->
  <!-- JavaScript Dependencies -->
  <script src="<?= base_url('js/main.js') ?>"></script>
  <script src="<?= base_url('js/dashboard.js') ?>"></script>
  <script src="<?= base_url('js/header-components.js') ?>"></script>

  <!-- Profile Picture Update Listener -->
  <script>
    // Listen for profile picture updates from other pages
    window.addEventListener('storage', function(e) {
      if (e.key === 'profilePictureUpdated') {
        // Reload the page to show updated profile picture
        window.location.reload();
      }
    });
  </script>
</body>
</html>