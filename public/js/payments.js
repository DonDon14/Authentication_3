// Initialize student search functionality
function initializeStudentSearch() {
  const searchInput = document.getElementById('studentSearch');
  const searchResults = document.getElementById('searchResults');
  const studentNameInput = document.getElementById('studentName');
  const studentIdInput = document.getElementById('studentId');
  
  if (!searchInput || !searchResults || !window.STUDENTS_DATA) {
    return;
  }
  
  let searchTimeout;
  
  // Search as user types
  searchInput.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    const query = this.value.trim();
    
    if (query.length < 2) {
      searchResults.style.display = 'none';
      return;
    }
    
    searchTimeout = setTimeout(() => {
      performSearch(query);
    }, 300);
  });
  
  // Hide results when clicking outside
  document.addEventListener('click', function(e) {
    if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
      searchResults.style.display = 'none';
    }
  });
  
  function performSearch(query) {
    const students = window.STUDENTS_DATA || [];
    const results = students.filter(student => {
      const name = (student.name || '').toLowerCase();
      const username = (student.username || '').toLowerCase();
      const queryLower = query.toLowerCase();
      
      return name.includes(queryLower) || username.includes(queryLower);
    }).slice(0, 5); // Limit to 5 results
    
    if (results.length === 0) {
      searchResults.innerHTML = '<div class="search-result-item">No students found</div>';
    } else {
      searchResults.innerHTML = results.map(student => `
        <div class="search-result-item" data-student='${JSON.stringify(student)}'>
          <div class="search-result-name">${student.name || student.username}</div>
          <div class="search-result-id">ID: ${student.username}</div>
        </div>
      `).join('');
      
      // Add click handlers to result items
      searchResults.querySelectorAll('.search-result-item[data-student]').forEach(item => {
        item.addEventListener('click', function() {
          const student = JSON.parse(this.getAttribute('data-student'));
          
          // Fill in the form fields
          studentNameInput.value = student.name || student.username;
          studentIdInput.value = student.username;
          searchInput.value = student.name || student.username;
          
          // Hide search results
          searchResults.style.display = 'none';
          
          console.log('Selected student:', student);
        });
      });
    }
    
    searchResults.style.display = 'block';
  }
}

// Initialize Manual QR Input functionality
function initializeManualQRInput() {
  const manualQRInput = document.getElementById('manualQR');
  const searchQRBtn = document.getElementById('searchQRBtn');
  
  if (!manualQRInput || !searchQRBtn) return;
  
  // Handle search button click
  searchQRBtn.addEventListener('click', function() {
    const qrData = manualQRInput.value.trim();
    if (!qrData) {
      showError('Please enter QR code data first.');
      return;
    }
    
    searchQRBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    searchQRBtn.disabled = true;
    
    searchStudentByQR(qrData)
      .then(result => {
        searchQRBtn.innerHTML = '<i class="fas fa-search"></i>';
        searchQRBtn.disabled = false;
        
        if (result.success) {
          // Fill form fields
          const studentNameField = document.getElementById('studentName');
          const studentIdField = document.getElementById('studentId');
          
          if (studentNameField) {
            studentNameField.value = result.data.student_name;
            studentNameField.dispatchEvent(new Event('input'));
          }
          
          if (studentIdField) {
            studentIdField.value = result.data.student_id;
            studentIdField.dispatchEvent(new Event('input'));
          }
          
          // Clear the QR input
          manualQRInput.value = '';
          
          // Show success message
          if (result.new_student) {
            showSuccess('New student detected from QR code and added to form!');
          } else {
            showSuccess('Existing student found and added to form!');
          }
        } else {
          showError(result.message || 'Student not found in QR code.');
        }
      })
      .catch(error => {
        console.error('Manual QR search error:', error);
        searchQRBtn.innerHTML = '<i class="fas fa-search"></i>';
        searchQRBtn.disabled = false;
        showError('Error searching for student. Please try again.');
      });
  });
  
  // Allow Enter key to trigger search
  manualQRInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      searchQRBtn.click();
    }
  });
}

// Initialize amount field for pre-selected contributions
function initializeAmountField() {
  const amountField = document.getElementById('amount');
  
  if (!amountField) {
    return;
  }
  
  // Force set the value from the attribute
  const expectedValue = amountField.getAttribute('value');
  if (expectedValue && expectedValue !== '' && expectedValue !== '0' && expectedValue !== '0.00') {
    amountField.value = expectedValue;
    amountField.setAttribute('data-forced-value', expectedValue);
    amountField.dispatchEvent(new Event('input', { bubbles: true }));
  }
}

// Initialize payment type functionality
function initializePaymentType() {
  const fullPaymentRadio = document.getElementById('fullPayment');
  const partialPaymentRadio = document.getElementById('partialPayment');
  const amountField = document.getElementById('amount');
  const studentIdField = document.getElementById('studentId');
  const contributionField = document.getElementById('contributionType') || document.getElementById('contributionId');
  
  if (!fullPaymentRadio || !partialPaymentRadio) return;
  
  // Handle payment type change
  [fullPaymentRadio, partialPaymentRadio].forEach(radio => {
    radio.addEventListener('change', function() {
      updatePaymentTypeUI();
      checkExistingPayments();
    });
  });
  
  // Check payments when student or contribution changes
  if (studentIdField) {
    studentIdField.addEventListener('blur', checkExistingPayments);
  }
  
  if (contributionField) {
    contributionField.addEventListener('change', checkExistingPayments);
  }
}

// Main initialization
document.addEventListener('DOMContentLoaded', function() {
  // Initialize search
  initializeStudentSearch();
  
  // Initialize QR functionality
  initializeManualQRInput();
  initializeQRScanner();
  initializeQRUpload();
  
  // Initialize form functionality
  initializeAmountField();
  initializePaymentType();

  // Initialize form submission handler
  initializePaymentForm();

  // Initialize partial payments if on that page
  if (window.location.pathname.includes('/partial')) {
    initializePartialPayments();
  }
});

// Export necessary functions
window.openPaymentModal = openPaymentModal;
window.closePaymentModal = closePaymentModal;
window.showQRReceipt = showQRReceipt;
window.closeQRReceiptModal = closeQRReceiptModal;