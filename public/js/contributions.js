/**
 * Contributions Page JavaScript
 * Handles contribution management functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded - initializing contributions');
    initializeContributions();
});

// Debug function for testing in browser console
window.debugContributions = function() {
    console.log('=== CONTRIBUTION DEBUG INFO ===');
    
    const toggles = document.querySelectorAll('.contribution-toggle');
    console.log('Found .contribution-toggle elements:', toggles.length);
    
    const altToggles = document.querySelectorAll('input[data-contribution-id]');
    console.log('Found input[data-contribution-id] elements:', altToggles.length);
    
    const contributionCards = document.querySelectorAll('.contribution-card');
    console.log('Found .contribution-card elements:', contributionCards.length);
    
    altToggles.forEach((toggle, index) => {
        console.log(`Toggle ${index}:`, {
            element: toggle,
            contributionId: toggle.getAttribute('data-contribution-id'),
            checked: toggle.checked,
            hasChangeListener: toggle._hasChangeListener || false
        });
    });
    
    // Try to manually initialize if needed
    if (toggles.length === 0 && altToggles.length > 0) {
        console.log('Trying to manually initialize toggles...');
        initializeToggleSwitches();
    }
};

// Force initialization for testing
window.forceInit = function() {
    console.log('=== FORCE INITIALIZATION ===');
    initializeContributions();
};

// Fallback initialization in case DOMContentLoaded already fired
if (document.readyState === 'loading') {
    // Still loading, wait for DOMContentLoaded
    console.log('Document still loading, waiting for DOMContentLoaded');
} else {
    // DOM already loaded
    console.log('DOM already loaded, initializing immediately');
    initializeContributions();
}

function initializeContributions() {
    console.log('=== INITIALIZING CONTRIBUTIONS ===');
    
    // Initialize contribution click handlers
    initializeContributionClicks();
    
    // Initialize toggle switches
    initializeToggleSwitches();
    
    // Initialize action buttons
    initializeActionButtons();
    
    // Initialize add contribution button
    initializeAddContribution();
    
    // Initialize animations
    initializeAnimations();
    
    console.log('Contributions page initialized successfully');
}

/**
 * Initialize contribution click handlers
 */
function initializeContributionClicks() {
    const contributionItems = document.querySelectorAll('.contribution-info.clickable-area');
    
    contributionItems.forEach(clickableArea => {
        clickableArea.addEventListener('click', function(event) {
            event.stopPropagation();
            
            const contributionItem = this.closest('.contribution-item');
            const contributionId = contributionItem.getAttribute('data-id');
            const contributionTitle = contributionItem.getAttribute('data-title');
            
            console.log('Contribution clicked via event listener:', contributionId, contributionTitle);
            handleContributionClick(contributionId, contributionTitle);
        });
    });
}



/**
 * Initialize toggle switches functionality
 */
function initializeToggleSwitches() {
    console.log('=== INITIALIZING TOGGLE SWITCHES ===');
    
    const toggleSwitches = document.querySelectorAll('.contribution-toggle');
    
    console.log(`Found ${toggleSwitches.length} toggle switches`);
    console.log('Toggle switches:', toggleSwitches);
    
    if (toggleSwitches.length === 0) {
        console.warn('No toggle switches found! Checking for alternative selectors...');
        
        // Try alternative selectors
        const altToggles1 = document.querySelectorAll('input[data-contribution-id]');
        const altToggles2 = document.querySelectorAll('.toggle-switch input');
        
        console.log('Alternative selector 1 (input[data-contribution-id]):', altToggles1.length);
        console.log('Alternative selector 2 (.toggle-switch input):', altToggles2.length);
    }
    
    toggleSwitches.forEach((toggle, index) => {
        console.log(`Checking toggle ${index}:`, toggle);
        
        // Check if manual handlers are already set up (to avoid conflicts)
        if (toggle.hasAttribute('data-manual-handler')) {
            console.log(`Toggle ${index} already has manual handler, skipping`);
            return;
        }
        
        console.log(`Setting up toggle ${index}:`, toggle);
        
        toggle.addEventListener('change', function(event) {
            console.log('=== TOGGLE CHANGE EVENT (External JS) ===');
            event.stopPropagation();
            
            const contributionId = this.getAttribute('data-contribution-id');
            const contributionItem = this.closest('.contribution-card');
            const contributionTitle = contributionItem ? contributionItem.getAttribute('data-title') : 'Unknown';
            
            console.log('Toggle contribution:', contributionId, 'checked:', this.checked, 'title:', contributionTitle);
            
            // Call the toggle function
            toggleContribution(contributionId);
            
            // Visual feedback
            if (contributionItem) {
                contributionItem.style.transform = 'scale(0.98)';
                setTimeout(() => {
                    contributionItem.style.transform = 'scale(1)';
                }, 150);
            }
        });
        
        // Also handle click on the slider span to ensure it works
        const slider = toggle.nextElementSibling;
        if (slider && slider.classList.contains('slider')) {
            console.log(`Setting up slider click for toggle ${index}`);
            slider.addEventListener('click', function(event) {
                console.log('=== SLIDER CLICK EVENT (External JS) ===');
                event.stopPropagation();
                toggle.checked = !toggle.checked;
                toggle.dispatchEvent(new Event('change'));
            });
        }
    });
}

/**
 * Initialize action buttons (edit/delete)
 */
function initializeActionButtons() {
    // Edit buttons
    const editButtons = document.querySelectorAll('.edit-btn');
    console.log(`Found ${editButtons.length} edit buttons`);
    
    editButtons.forEach(btn => {
        btn.addEventListener('click', function(event) {
            event.stopPropagation();
            
            const contributionId = this.getAttribute('data-contribution-id');
            console.log('Edit contribution:', contributionId);
            editContribution(contributionId);
        });
    });
    
    // Delete buttons
    const deleteButtons = document.querySelectorAll('.delete-btn');
    console.log(`Found ${deleteButtons.length} delete buttons`);
    
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function(event) {
            event.stopPropagation();
            
            const contributionId = this.getAttribute('data-contribution-id');
            console.log('Delete contribution:', contributionId);
            deleteContribution(contributionId);
        });
    });
}

/**
 * Initialize add contribution button and modal functionality
 */
function initializeAddContribution() {
    const addButton = document.getElementById('addContributionBtn');
    const modal = document.getElementById('contributionModal');
    const closeBtn = document.getElementById('closeModal');
    const cancelBtn = document.getElementById('cancelBtn');
    const form = document.getElementById('contributionForm');
    
    // Open modal
    if (addButton) {
        addButton.addEventListener('click', function() {
            openContributionModal();
        });
    }
    
    // Close modal
    [closeBtn, cancelBtn].forEach(btn => {
        if (btn) {
            btn.addEventListener('click', function() {
                closeContributionModal();
            });
        }
    });
    
    // Close modal when clicking outside
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeContributionModal();
            }
        });
    }
    
    // Handle form submission
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            submitContributionForm();
        });
    }
}

/**
 * Initialize animations
 */
function initializeAnimations() {
    // Add stagger animation to contribution items
    const contributionItems = document.querySelectorAll('.contribution-item');
    
    contributionItems.forEach((item, index) => {
        item.style.animationDelay = `${index * 0.1}s`;
        item.classList.add('fade-in-up');
    });
    
    // Add hover effects
    contributionItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px) scale(1.01)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
}

/**
 * Show edit modal for contribution
 */
function showEditModal(title, contributionItem) {
    const currentAmount = contributionItem.querySelector('.amount').textContent;
    const currentDesc = contributionItem.querySelector('.contribution-desc').textContent;
    
    const modal = createModal({
        title: `Edit ${title}`,
        content: `
            <div class="modal-form">
                <div class="form-group">
                    <label for="edit-title">Title:</label>
                    <input type="text" id="edit-title" value="${title}" class="form-input">
                </div>
                <div class="form-group">
                    <label for="edit-description">Description:</label>
                    <input type="text" id="edit-description" value="${currentDesc}" class="form-input">
                </div>
                <div class="form-group">
                    <label for="edit-amount">Amount:</label>
                    <input type="text" id="edit-amount" value="${currentAmount}" class="form-input">
                </div>
            </div>
        `,
        buttons: [
            {
                text: 'Cancel',
                class: 'btn-secondary',
                action: () => closeModal(modal)
            },
            {
                text: 'Save Changes',
                class: 'btn-primary',
                action: () => {
                    const newTitle = document.getElementById('edit-title').value;
                    const newDesc = document.getElementById('edit-description').value;
                    const newAmount = document.getElementById('edit-amount').value;
                    
                    if (validateContributionForm(newTitle, newDesc, newAmount)) {
                        updateContribution(contributionItem, newTitle, newDesc, newAmount);
                        showNotification('Contribution updated successfully', 'success');
                        closeModal(modal);
                    }
                }
            }
        ]
    });
}

/**
 * Show delete confirmation
 */
function showDeleteConfirmation(title, contributionItem) {
    const modal = createModal({
        title: 'Confirm Deletion',
        content: `
            <div class="delete-confirmation">
                <p>Are you sure you want to delete <strong>${title}</strong>?</p>
                <p class="warning-text">This action cannot be undone.</p>
            </div>
        `,
        buttons: [
            {
                text: 'Cancel',
                class: 'btn-secondary',
                action: () => closeModal(modal)
            },
            {
                text: 'Delete',
                class: 'btn-danger',
                action: () => {
                    deleteContribution(contributionItem);
                    showNotification(`${title} deleted successfully`, 'success');
                    closeModal(modal);
                }
            }
        ]
    });
}

/**
 * Show add contribution modal
 */
function showAddContributionModal() {
    const modal = createModal({
        title: 'Add New Contribution',
        content: `
            <div class="modal-form">
                <div class="form-group">
                    <label for="new-title">Title:</label>
                    <input type="text" id="new-title" placeholder="Enter contribution title" class="form-input">
                </div>
                <div class="form-group">
                    <label for="new-description">Description:</label>
                    <input type="text" id="new-description" placeholder="Enter description" class="form-input">
                </div>
                <div class="form-group">
                    <label for="new-amount">Amount:</label>
                    <input type="text" id="new-amount" placeholder="$0" class="form-input">
                </div>
                <div class="form-group">
                    <label for="new-tag">Category:</label>
                    <select id="new-tag" class="form-input">
                        <option value="uniform">Uniform</option>
                        <option value="activity">Activity</option>
                        <option value="meal">Meal</option>
                        <option value="education">Education</option>
                    </select>
                </div>
            </div>
        `,
        buttons: [
            {
                text: 'Cancel',
                class: 'btn-secondary',
                action: () => closeModal(modal)
            },
            {
                text: 'Add Contribution',
                class: 'btn-primary',
                action: () => {
                    const title = document.getElementById('new-title').value;
                    const description = document.getElementById('new-description').value;
                    const amount = document.getElementById('new-amount').value;
                    const tag = document.getElementById('new-tag').value;
                    
                    if (validateContributionForm(title, description, amount)) {
                        addNewContribution(title, description, amount, tag);
                        showNotification('New contribution added successfully', 'success');
                        closeModal(modal);
                    }
                }
            }
        ]
    });
}

/**
 * Create modal element
 */
function createModal({ title, content, buttons }) {
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    
    modal.innerHTML = `
        <div class="modal-container">
            <div class="modal-header">
                <h3>${title}</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                ${content}
            </div>
            <div class="modal-footer">
                ${buttons.map(btn => `
                    <button class="btn ${btn.class}" data-action="${btn.text}">
                        ${btn.text}
                    </button>
                `).join('')}
            </div>
        </div>
    `;
    
    // Add event listeners
    const closeBtn = modal.querySelector('.modal-close');
    closeBtn.addEventListener('click', () => closeModal(modal));
    
    buttons.forEach((btn, index) => {
        const btnElement = modal.querySelectorAll('.modal-footer .btn')[index];
        btnElement.addEventListener('click', btn.action);
    });
    
    // Close on overlay click
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal(modal);
        }
    });
    
    document.body.appendChild(modal);
    return modal;
}

/**
 * Close modal
 */
function closeModal(modal) {
    modal.remove();
}

/**
 * Validate contribution form
 */
function validateContributionForm(title, description, amount) {
    if (!title.trim()) {
        showNotification('Title is required', 'error');
        return false;
    }
    
    if (!description.trim()) {
        showNotification('Description is required', 'error');
        return false;
    }
    
    if (!amount.trim()) {
        showNotification('Amount is required', 'error');
        return false;
    }
    
    return true;
}

/**
 * Update existing contribution
 */
function updateContribution(contributionItem, title, description, amount) {
    contributionItem.querySelector('h4').textContent = title;
    contributionItem.querySelector('.contribution-desc').textContent = description;
    contributionItem.querySelector('.amount').textContent = amount;
    
    // Add update animation
    contributionItem.style.transform = 'scale(1.05)';
    contributionItem.style.background = 'rgba(102, 126, 234, 0.1)';
    
    setTimeout(() => {
        contributionItem.style.transform = 'scale(1)';
        contributionItem.style.background = 'rgba(255, 255, 255, 0.9)';
    }, 300);
}

/**
 * Delete contribution
 */
function deleteContribution(contributionItem) {
    contributionItem.style.transform = 'scale(0)';
    contributionItem.style.opacity = '0';
    
    setTimeout(() => {
        contributionItem.remove();
        updateStats();
    }, 300);
}

/**
 * Add new contribution
 */
function addNewContribution(title, description, amount, tag) {
    const contributionsList = document.querySelector('.contributions-list');
    
    const newItem = document.createElement('div');
    newItem.className = 'contribution-item';
    newItem.style.transform = 'scale(0)';
    newItem.style.opacity = '0';
    
    newItem.innerHTML = `
        <div class="contribution-info">
            <div class="contribution-details">
                <h4>${title}</h4>
                <p class="contribution-desc">${description}</p>
                <div class="contribution-tags">
                    <span class="tag tag-${tag}">${tag.charAt(0).toUpperCase() + tag.slice(1)}</span>
                </div>
            </div>
            <div class="contribution-amount">
                <span class="amount">${amount}</span>
            </div>
        </div>
        <div class="contribution-actions">
            <label class="toggle-switch">
                <input type="checkbox" checked>
                <span class="slider"></span>
            </label>
            <button class="action-btn edit-btn">
                <i class="fas fa-edit"></i>
            </button>
            <button class="action-btn delete-btn">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    
    contributionsList.appendChild(newItem);
    
    // Animate in
    setTimeout(() => {
        newItem.style.transform = 'scale(1)';
        newItem.style.opacity = '1';
    }, 100);
    
    // Re-initialize event listeners for new item
    initializeActionButtons();
    initializeToggleSwitches();
    updateStats();
}

/**
 * Update statistics
 */
function updateStats() {
    const totalContributions = document.querySelectorAll('.contribution-item').length;
    const activeContributions = document.querySelectorAll('.contribution-item input:checked').length;
    
    document.querySelector('.stat-card-primary .stat-value').textContent = activeContributions;
    document.querySelector('.stat-card-success .stat-value').textContent = totalContributions;
}

/**
 * Show notification
 */
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Add CSS for modals and notifications
const additionalStyles = `
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        animation: fadeIn 0.3s ease;
    }
    
    .modal-container {
        background: white;
        border-radius: 15px;
        width: 90%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
        animation: slideInUp 0.3s ease;
    }
    
    .modal-header {
        padding: 20px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .modal-header h3 {
        margin: 0;
        color: #111827;
    }
    
    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #6b7280;
    }
    
    .modal-body {
        padding: 20px;
    }
    
    .modal-footer {
        padding: 20px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        gap: 10px;
        justify-content: flex-end;
    }
    
    .modal-form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    
    .form-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    
    .form-group label {
        font-weight: 500;
        color: #374151;
    }
    
    .form-input {
        padding: 10px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.9rem;
    }
    
    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .btn-secondary {
        background: #f3f4f6;
        color: #374151;
    }
    
    .btn-danger {
        background: #ef4444;
        color: white;
    }
    
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        color: white;
        z-index: 1001;
        transform: translateX(400px);
        transition: transform 0.3s ease;
    }
    
    .notification.show {
        transform: translateX(0);
    }
    
    .notification-success {
        background: #10b981;
    }
    
    .notification-error {
        background: #ef4444;
    }
    
    .notification-info {
        background: #3b82f6;
    }
    
    .fade-in-up {
        animation: fadeInUp 0.6s ease-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .delete-confirmation {
        text-align: center;
    }
    
    .warning-text {
        color: #ef4444;
        font-size: 0.9rem;
        margin-top: 10px;
    }
`;

// Inject additional styles
const styleSheet = document.createElement('style');
styleSheet.textContent = additionalStyles;
document.head.appendChild(styleSheet);

// Modal and Form Handling Functions
function openContributionModal(contributionData = null) {
    const modal = document.getElementById('contributionModal');
    const modalTitle = document.getElementById('modalTitle');
    const form = document.getElementById('contributionForm');
    const submitBtn = document.getElementById('submitBtn');
    
    if (contributionData) {
        // Edit mode
        modalTitle.textContent = 'Edit Contribution';
        document.getElementById('contributionId').value = contributionData.id;
        document.getElementById('contributionTitle').value = contributionData.title;
        document.getElementById('contributionDescription').value = contributionData.description || '';
        document.getElementById('contributionAmount').value = contributionData.amount;
        document.getElementById('contributionCategory').value = contributionData.category;
        submitBtn.querySelector('.btn-text').textContent = 'Update Contribution';
    } else {
        // Add mode
        modalTitle.textContent = 'Add New Contribution';
        form.reset();
        document.getElementById('contributionId').value = '';
        submitBtn.querySelector('.btn-text').textContent = 'Add Contribution';
    }
    
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeContributionModal() {
    const modal = document.getElementById('contributionModal');
    const form = document.getElementById('contributionForm');
    
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
    form.reset();
    clearFormErrors();
}

async function submitContributionForm() {
    console.log('submitContributionForm() called');
    
    const form = document.getElementById('contributionForm');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnLoader = submitBtn.querySelector('.btn-loader');
    const contributionId = document.getElementById('contributionId').value;
    
    console.log('Form elements found:', { form, submitBtn, btnText, btnLoader });
    
    // Clear previous errors
    clearFormErrors();
    
    // Get form data
    const formData = new FormData(form);
    
    // Log form data for debugging
    console.log('Form data:');
    for (let [key, value] of formData.entries()) {
        console.log(key, value);
    }
    
    // Validate form
    if (!validateContributionForm(formData)) {
        console.log('Form validation failed');
        return;
    }
    
    // Show loading state
    submitBtn.disabled = true;
    if (btnText) btnText.style.display = 'none';
    if (btnLoader) btnLoader.style.display = 'inline-block';
    
    try {
        const baseUrl = window.location.pathname.includes('Authentication_3') ? '/Authentication_3' : '';
        const url = contributionId ? `${baseUrl}/contributions/update/${contributionId}` : `${baseUrl}/contributions/add`;
        console.log('Making request to:', url);
        
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });
        
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const responseText = await response.text();
        console.log('Raw response:', responseText);
        
        let result;
        try {
            result = JSON.parse(responseText);
        } catch (parseError) {
            console.error('Failed to parse JSON:', parseError);
            console.error('Response text:', responseText);
            throw new Error('Invalid JSON response from server');
        }
        
        console.log('Parsed result:', result);
        
        if (result.success) {
            showMessage(result.message, 'success');
            closeContributionModal();
            // Reload page to show updated data
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showMessage(result.message || 'An error occurred', 'error');
        }
    } catch (error) {
        console.error('Form submission error:', error);
        showMessage('Network error: ' + error.message, 'error');
    } finally {
        // Reset button state
        submitBtn.disabled = false;
        if (btnText) btnText.style.display = 'inline-block';
        if (btnLoader) btnLoader.style.display = 'none';
    }
}

function validateContributionForm(formData) {
    const title = formData.get('title')?.trim();
    const amount = formData.get('amount');
    const category = formData.get('category');
    
    let isValid = true;
    
    if (!title) {
        showFieldError('contributionTitle', 'Title is required');
        isValid = false;
    }
    
    if (!amount || parseFloat(amount) < 0) {
        showFieldError('contributionAmount', 'Valid amount is required');
        isValid = false;
    }
    
    if (!category) {
        showFieldError('contributionCategory', 'Category is required');
        isValid = false;
    }
    
    return isValid;
}

function showFieldError(fieldId, message) {
    const field = document.getElementById(fieldId);
    if (!field) return;
    
    field.classList.add('error');
    
    // Remove existing error message
    const existingError = field.parentNode.querySelector('.error-text');
    if (existingError) {
        existingError.remove();
    }
    
    // Add new error message
    const errorElement = document.createElement('div');
    errorElement.className = 'error-text';
    errorElement.textContent = message;
    field.parentNode.appendChild(errorElement);
}

function clearFormErrors() {
    const errorFields = document.querySelectorAll('.form-group input.error, .form-group select.error, .form-group textarea.error');
    errorFields.forEach(field => field.classList.remove('error'));
    
    const errorTexts = document.querySelectorAll('.error-text');
    errorTexts.forEach(error => error.remove());
}

function showMessage(message, type = 'info') {
    const messageEl = type === 'success' ? 
        document.getElementById('successMessage') : 
        document.getElementById('errorMessage');
    
    if (messageEl) {
        messageEl.textContent = message;
        messageEl.style.display = 'block';
        
        setTimeout(() => {
            messageEl.style.display = 'none';
        }, 5000);
    }
}

// Database interaction functions
async function toggleContribution(id) {
    console.log('=== TOGGLE CONTRIBUTION FUNCTION ===');
    console.log('toggleContribution called with ID:', id);
    
    try {
        // Determine the base URL
        const baseUrl = window.location.pathname.includes('Authentication_3') ? '/Authentication_3' : '';
        const url = `${baseUrl}/contributions/toggle/${id}`;
        
        console.log('Making request to:', url);
        
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        });
        
        console.log('Response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        console.log('Toggle result:', result);
        
        if (result.success) {
            showMessage(result.message, 'success');
            
            // Update the toggle switch UI
            const toggle = document.querySelector(`[data-contribution-id="${id}"]`);
            if (toggle) {
                const slider = toggle.nextElementSibling;
                const sliderButton = slider ? slider.querySelector('.slider-button') : null;
                
                if (result.status === 'active') {
                    slider.style.backgroundColor = 'var(--success-color)';
                    if (sliderButton) sliderButton.style.left = '25px';
                    toggle.checked = true;
                } else {
                    slider.style.backgroundColor = 'var(--border-color)';
                    if (sliderButton) sliderButton.style.left = '3px';
                    toggle.checked = false;
                }
            }
            
            // Update the stats cards at the top
            updateStatsCards();
            
        } else {
            showMessage(result.message || 'Failed to toggle contribution', 'error');
            
            // Reset toggle state on error
            const toggle = document.querySelector(`[data-contribution-id="${id}"]`);
            if (toggle) {
                toggle.checked = !toggle.checked;
            }
        }
    } catch (error) {
        console.error('Toggle error:', error);
        showMessage('Network error. Please try again.', 'error');
        
        // Reset toggle state on error
        const toggle = document.querySelector(`[data-contribution-id="${id}"]`);
        if (toggle) {
            toggle.checked = !toggle.checked;
        }
    }
}

/**
 * Update the stats cards at the top of the page
 */
function updateStatsCards() {
    console.log('Updating stats cards...');
    
    const contributionCards = document.querySelectorAll('.contribution-card');
    let activeCount = 0;
    let inactiveCount = 0;
    
    contributionCards.forEach(card => {
        const toggle = card.querySelector('.contribution-toggle');
        if (toggle) {
            if (toggle.checked) {
                activeCount++;
            } else {
                inactiveCount++;
            }
        }
    });
    
    console.log(`Stats: Active: ${activeCount}, Inactive: ${inactiveCount}, Total: ${contributionCards.length}`);
    
    // Update the stat cards
    const activeStatCard = document.querySelector('.stat-card.primary .stat-value');
    const inactiveStatCard = document.querySelector('.stat-card.warning .stat-value');
    const totalStatCard = document.querySelector('.stat-card.success .stat-value');
    
    if (activeStatCard) {
        activeStatCard.textContent = activeCount;
        console.log('Updated active count to:', activeCount);
    }
    
    if (inactiveStatCard) {
        inactiveStatCard.textContent = inactiveCount;
        console.log('Updated inactive count to:', inactiveCount);
    }
    
    if (totalStatCard) {
        totalStatCard.textContent = contributionCards.length;
        console.log('Updated total count to:', contributionCards.length);
    }
}

async function editContribution(id) {
    try {
        const baseUrl = window.location.pathname.includes('Authentication_3') ? '/Authentication_3' : '';
        const response = await fetch(`${baseUrl}/contributions/get/${id}`);
        const result = await response.json();
        
        if (result.success) {
            openContributionModal(result.data);
        } else {
            showMessage('Failed to load contribution data', 'error');
        }
    } catch (error) {
        console.error('Edit error:', error);
        showMessage('Network error. Please try again.', 'error');
    }
}

async function deleteContribution(id) {
    if (!confirm('Are you sure you want to delete this contribution? This action cannot be undone.')) {
        return;
    }
    
    try {
        const baseUrl = window.location.pathname.includes('Authentication_3') ? '/Authentication_3' : '';
        const response = await fetch(`${baseUrl}/contributions/delete/${id}`, {
            method: 'POST'
        });
        
        const result = await response.json();
        
        if (result.success) {
            showMessage(result.message, 'success');
            // Remove the item from DOM
            const contributionItem = document.querySelector(`[data-id="${id}"]`);
            if (contributionItem) {
                contributionItem.remove();
            }
            // Reload to update stats
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showMessage(result.message || 'Failed to delete contribution', 'error');
        }
    } catch (error) {
        console.error('Delete error:', error);
        showMessage('Network error. Please try again.', 'error');
    }
}

/**
 * Handle contribution click with comprehensive debugging
 */
function handleContributionClick(contributionId, contributionTitle) {
    console.log('=== Contribution Click Handler ===');
    console.log('ID:', contributionId, 'Title:', contributionTitle);
    console.log('Current URL:', window.location.href);
    
    // Check if this function is being called multiple times
    if (window.clickInProgress) {
        console.warn('Click already in progress, ignoring duplicate');
        return;
    }
    
    window.clickInProgress = true;
    
    // Call the actual navigation function
    console.log('Calling viewPayments...');
    viewPayments(contributionId);
    
    // Reset click lock after 2 seconds (in case navigation fails)
    setTimeout(() => {
        window.clickInProgress = false;
    }, 2000);
}

/**
 * Navigate to view contribution details showing students who paid
 */
function viewPayments(contributionId) {
    console.log('=== viewPayments Debug ===');
    console.log('Called with ID:', contributionId);
    console.log('Current URL:', window.location.href);
    
    // Try multiple URL approaches
    const protocol = window.location.protocol;
    const host = window.location.host;
    
    // Method 1: Try without Authentication_3 path
    let targetUrl = protocol + '//' + host + '/payments/viewContribution/' + contributionId;
    
    console.log('Method 1 Target URL:', targetUrl);
    console.log('Navigating now...');
    
    window.location.href = targetUrl;
}


