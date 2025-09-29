document.addEventListener('DOMContentLoaded', function() {
  // Initialize student search functionality
  initializeStudentSearch();
  
  // Initialize manual QR input functionality
  initializeManualQRInput();
  
  // Initialize amount field for pre-selected contributions
  initializeAmountField();
  
  const paymentForm = document.getElementById('paymentForm');
  const successMessage = document.getElementById('successMessage');
  const errorMessage = document.getElementById('errorMessage');

  if (paymentForm) {
    paymentForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      
      // Clear previous messages
      hideMessages();
      
      // Get form data
      const formData = new FormData(paymentForm);
      const studentId = formData.get('student_id')?.trim();
      const studentName = formData.get('student_name')?.trim();
      let amount = formData.get('amount');
      const contributionId = formData.get('contribution_id');
      const contributionType = formData.get('contribution_type');
      const paymentMethod = formData.get('payment_method');
      
      // Debug: Log all form data
      console.log('FormData values:', {
        student_id: studentId,
        student_name: studentName,
        amount: amount,
        contribution_id: contributionId,
        contribution_type: contributionType,
        payment_method: paymentMethod
      });
      
      // Basic validation
      if (!studentId || !studentName) {
        showError('Student ID and Student Name are required');
        return;
      }
      
      // Check amount - always get from field directly to handle readonly fields
      const amountField = document.getElementById('amount');
      let finalAmount = amountField ? amountField.value.trim() : amount;
      
      // If field is empty but we have a forced value, use that
      if ((!finalAmount || finalAmount === '0.00') && amountField) {
        const forcedValue = amountField.getAttribute('data-forced-value');
        const attributeValue = amountField.getAttribute('value');
        finalAmount = forcedValue || attributeValue || finalAmount;
      }
      
      console.log('Amount validation:', {
        formDataAmount: amount,
        fieldValue: amountField ? amountField.value : 'field not found',
        forcedValue: amountField ? amountField.getAttribute('data-forced-value') : 'no forced value',
        attributeValue: amountField ? amountField.getAttribute('value') : 'no attribute value',
        finalAmount: finalAmount,
        isReadonly: amountField ? amountField.readOnly : 'field not found'
      });
      
      if (!finalAmount || parseFloat(finalAmount) <= 0 || isNaN(parseFloat(finalAmount))) {
        showError('Amount is required and must be greater than 0');
        return;
      }
      
      // Check for contribution selection
      if (!contributionId && !contributionType) {
        showError('Please select a contribution type');
        return;
      }
      
      // Show loading state
      const submitBtn = paymentForm.querySelector('.btn-primary');
      const originalText = submitBtn.innerHTML;
      submitBtn.classList.add('loading');
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
      submitBtn.disabled = true;
      
      try {
        // Always ensure the correct amount is in form data
        formData.set('amount', finalAmount);
        
        console.log('Sending payment data:', {
          student_id: studentId,
          student_name: studentName,
          amount: finalAmount,
          contribution_id: contributionId,
          contribution_type: contributionType,
          payment_method: paymentMethod
        });
        
        const baseUrl = window.location.pathname.includes('Authentication_3') ? '/Authentication_3' : '';
        const response = await fetch(`${baseUrl}/payments/save`, {
          method: 'POST',
          body: formData
        });
        
        const result = await response.json();
        console.log('Server response:', result);
        
        if (result.success) {
          showSuccess(result.message || 'Payment recorded successfully!');
          
          // Show QR receipt if available
          if (result.show_receipt && result.receipt) {
            showQRReceipt(result.receipt, result.qr_download_url);
          }
          
          // Clear form but keep contribution info if present
          const contributionId = document.getElementById('contributionId');
          const amount = document.getElementById('amount');
          const paymentMethod = document.getElementById('paymentMethod');
          
          // Reset form
          paymentForm.reset();
          
          // Restore contribution-specific data
          if (contributionId && contributionId.value) {
            contributionId.value = contributionId.value; // Keep contribution ID
            if (amount.hasAttribute('readonly')) {
              amount.value = amount.getAttribute('value'); // Restore amount if readonly
            }
            paymentMethod.selectedIndex = 0; // Reset to first option
          }
          
          // Clear student fields specifically
          document.getElementById('studentName').value = '';
          document.getElementById('studentId').value = '';
          
          // Don't auto-refresh if showing receipt modal
          if (!result.show_receipt) {
            setTimeout(() => {
              window.location.reload();
            }, 2000);
          }
        } else {
          showError(result.message || 'Failed to record payment');
        }
      } catch (error) {
        console.error('Payment error:', error);
        showError('An error occurred while recording payment');
      } finally {
        // Reset button state
        submitBtn.classList.remove('loading');
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
      }
    });
  }

  // Handle contribution dropdown change
  const contributionSelect = document.getElementById('contributionType');
  const amountField = document.getElementById('amount');
  
  if (contributionSelect && amountField) {
    contributionSelect.addEventListener('change', function() {
      const selectedOption = this.options[this.selectedIndex];
      const amount = selectedOption.getAttribute('data-amount');
      
      if (amount) {
        amountField.value = parseFloat(amount).toFixed(2);
      } else {
        amountField.value = '';
      }
    });
  }
  
  // Form validation on input
  const inputs = document.querySelectorAll('input, select');
  inputs.forEach(input => {
    input.addEventListener('blur', validateField);
    input.addEventListener('input', clearFieldError);
  });
  
  function validateField(e) {
    const field = e.target;
    const value = field.value.trim();
    
    // Remove existing error classes
    field.classList.remove('error');
    
    // Validation rules
    if (field.required && !value) {
      addFieldError(field, 'This field is required');
      return;
    }
    
    if (field.type === 'number' && value && parseFloat(value) <= 0) {
      addFieldError(field, 'Amount must be greater than 0');
      return;
    }
    
    if (field.name === 'student_id' && value && !/^[A-Z]{3}\d{3}$/i.test(value)) {
      addFieldError(field, 'Student ID format: STU001');
      return;
    }
  }
  
  function addFieldError(field, message) {
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
  
  function clearFieldError(e) {
    const field = e.target;
    field.classList.remove('error');
    const errorElement = field.parentNode.querySelector('.error-text');
    if (errorElement) {
      errorElement.remove();
    }
  }
  
  function showSuccess(message) {
    hideMessages();
    successMessage.textContent = message;
    successMessage.classList.add('show');
    successMessage.style.display = 'block';
  }
  
  function showError(message) {
    hideMessages();
    errorMessage.textContent = message;
    errorMessage.classList.add('show');
    errorMessage.style.display = 'block';
  }
  
  function hideMessages() {
    successMessage.style.display = 'none';
    errorMessage.style.display = 'none';
    successMessage.classList.remove('show');
    errorMessage.classList.remove('show');
  }
  
  // Clear form fields on page load to prevent auto-fill
  window.addEventListener('load', function() {
    if (paymentForm) {
      paymentForm.reset();
    }
    
    const fields = ['studentName', 'studentId', 'contributionType', 'amount'];
    fields.forEach(fieldId => {
      const field = document.getElementById(fieldId);
      if (field) field.value = '';
    });
  });
  
  // Format amount input
  const amountInput = document.getElementById('amount');
  if (amountInput) {
    amountInput.addEventListener('input', function(e) {
      let value = e.target.value;
      
      // Remove any non-numeric characters except decimal point
      value = value.replace(/[^0-9.]/g, '');
      
      // Ensure only one decimal point
      const parts = value.split('.');
      if (parts.length > 2) {
        value = parts[0] + '.' + parts.slice(1).join('');
      }
      
      // Limit to 2 decimal places
      if (parts[1] && parts[1].length > 2) {
        value = parseFloat(value).toFixed(2);
      }
      
      e.target.value = value;
    });
  }

  // QR Scanner functionality
  initializeQRScanner();
  initializeQRUpload();
});

// QR Upload Implementation
function initializeQRUpload() {
  const uploadQRButton = document.getElementById('uploadQRButton');
  const qrFileInput = document.getElementById('qrFileInput');
  
  // Handle upload button click
  uploadQRButton?.addEventListener('click', function() {
    qrFileInput.click();
  });
  
  // Handle file selection
  qrFileInput?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file && file.type.startsWith('image/')) {
      processUploadedQRImage(file);
    } else {
      alert('Please select a valid image file.');
    }
  });
}

function processUploadedQRImage(file) {
  // Show upload processing indicator
  showUploadProcessing('Processing uploaded QR code...');
  
  const reader = new FileReader();
  
  reader.onload = function(e) {
    const img = new Image();
    img.onload = function() {
      // Create canvas to process the image
      const canvas = document.createElement('canvas');
      const context = canvas.getContext('2d');
      
      // Set canvas size to image size
      canvas.width = img.width;
      canvas.height = img.height;
      
      // Draw image to canvas
      context.drawImage(img, 0, 0);
      
      // Get image data
      const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
      
      // Scan for QR code
      const code = jsQR(imageData.data, imageData.width, imageData.height);
      
      if (code) {
        console.log('QR Code detected from upload:', code.data);
        
        // Search student in database using QR data
        searchStudentByQR(code.data)
          .then(result => {
            hideUploadProcessing();
            
            if (result.success) {
              // Prepare data for form population
              const formData = {
                studentId: result.data.student_id,
                studentName: result.data.student_name,
                course: result.data.course || 'N/A'
              };
              
              populateFormFromUpload(formData);
              
              if (result.new_student) {
                showUploadSuccess('New student detected and added to form!', formData);
              } else {
                showUploadSuccess('Existing student found and added to form!', formData);
              }
            } else {
              showUploadError(result.message || 'Student not found in QR code.');
            }
          })
          .catch(error => {
            console.error('QR upload search error:', error);
            hideUploadProcessing();
            showUploadError('Error searching for student. Please try again.');
          });
      } else {
        hideUploadProcessing();
        showUploadError('No QR code found in the uploaded image. Please try a clearer image.');
      }
    };
    
    img.onerror = function() {
      hideUploadProcessing();
      showUploadError('Error loading the uploaded image. Please try another file.');
    };
    
    img.src = e.target.result;
  };
  
  reader.onerror = function() {
    hideUploadProcessing();
    showUploadError('Error reading the file. Please try again.');
  };
  
  reader.readAsDataURL(file);
}

  function populateFormFromUpload(data) {
    const studentNameField = document.getElementById('studentName');
    const studentIdField = document.getElementById('studentId');
    
    if (studentNameField) {
      studentNameField.value = data.studentName;
      studentNameField.dispatchEvent(new Event('input'));
    }
    
    if (studentIdField) {
      studentIdField.value = data.studentId;
      studentIdField.dispatchEvent(new Event('input'));
    }
    
    // Clear the file input
    const qrFileInput = document.getElementById('qrFileInput');
    if (qrFileInput) {
      qrFileInput.value = '';
    }
  }

  function showUploadSuccess(message, data) {
    const successMsg = document.getElementById('successMessage');
    if (successMsg) {
      const idLength = data.studentId.length;
      successMsg.innerHTML = `
        <div style="text-align: left;">
          <strong>${message}</strong><br>
          <small>Student ID: ${data.studentId} (${idLength} digits) | Name: ${data.studentName} | Course: ${data.course}</small>
        </div>
      `;
      successMsg.style.display = 'block';
      successMsg.classList.add('show');
      
      setTimeout(() => {
        successMsg.style.display = 'none';
        successMsg.classList.remove('show');
      }, 5000);
    }
  }

function showUploadError(message) {
  const errorMsg = document.getElementById('errorMessage');
  if (errorMsg) {
    errorMsg.textContent = message;
    errorMsg.style.display = 'block';
    errorMsg.classList.add('show');
    
    setTimeout(() => {
      errorMsg.style.display = 'none';
      errorMsg.classList.remove('show');
    }, 5000);
  }
  
  // Clear the file input
  const qrFileInput = document.getElementById('qrFileInput');
  if (qrFileInput) {
    qrFileInput.value = '';
  }
}

// Upload processing indicator functions
function showUploadProcessing(text) {
  const uploadProcessing = document.getElementById('uploadProcessing');
  const uploadProcessingText = document.getElementById('uploadProcessingText');
  
  if (uploadProcessing && uploadProcessingText) {
    uploadProcessingText.textContent = text;
    uploadProcessing.style.display = 'block';
  }
}

function hideUploadProcessing() {
  const uploadProcessing = document.getElementById('uploadProcessing');
  if (uploadProcessing) {
    uploadProcessing.style.display = 'none';
  }
}

// QR Scanner Implementation
function initializeQRScanner() {
  const scanQRButton = document.getElementById('scanQRButton');
  const qrModal = document.getElementById('qrScannerModal');
  const closeQRButton = document.getElementById('closeQRScanner');
  const qrVideo = document.getElementById('qrVideo');
  const qrCanvas = document.getElementById('qrCanvas');
  const scannerStatus = document.getElementById('scannerStatus');
  const scannerResult = document.getElementById('scannerResult');
  const useDataButton = document.getElementById('useScannedData');
  const scanAgainButton = document.getElementById('scanAgain');
  
  let stream = null;
  let scanning = false;
  let scannedData = null;

  // Open QR Scanner Modal
  scanQRButton?.addEventListener('click', async function() {
    try {
      qrModal.style.display = 'flex';
      scannerResult.style.display = 'none';
      scannerStatus.textContent = 'Starting camera...';
      
      await startScanning();
    } catch (error) {
      console.error('Error opening QR scanner:', error);
      scannerStatus.textContent = 'Error: Unable to access camera';
    }
  });

  // Close QR Scanner Modal
  closeQRButton?.addEventListener('click', stopScanning);
  
  // Close modal when clicking outside
  qrModal?.addEventListener('click', function(e) {
    if (e.target === qrModal) {
      stopScanning();
    }
  });

  // Use scanned data
  useDataButton?.addEventListener('click', function() {
    if (scannedData) {
      populateFormFields(scannedData);
      stopScanning();
    }
  });

  // Scan again
  scanAgainButton?.addEventListener('click', function() {
    scannerResult.style.display = 'none';
    scannerStatus.textContent = 'Position the QR code within the scanner box';
    startVideoScan();
  });

  async function startScanning() {
    try {
      // Request camera access
      stream = await navigator.mediaDevices.getUserMedia({
        video: { 
          facingMode: 'environment', // Use back camera if available
          width: { ideal: 1280 },
          height: { ideal: 720 }
        }
      });
      
      qrVideo.srcObject = stream;
      qrVideo.setAttribute('playsinline', true);
      
      qrVideo.addEventListener('loadedmetadata', function() {
        // Set canvas dimensions to match video
        qrCanvas.width = qrVideo.videoWidth;
        qrCanvas.height = qrVideo.videoHeight;
        
        scannerStatus.textContent = 'Camera ready - Position QR code in the box';
        startVideoScan();
      });
      
    } catch (error) {
      console.error('Camera access error:', error);
      scannerStatus.textContent = 'Camera access denied. Please allow camera permission.';
    }
  }

  function startVideoScan() {
    scanning = true;
    scanFrame();
  }

  function scanFrame() {
    if (!scanning || !qrVideo || qrVideo.readyState !== qrVideo.HAVE_ENOUGH_DATA) {
      if (scanning) {
        requestAnimationFrame(scanFrame);
      }
      return;
    }

    const canvas = qrCanvas;
    const context = canvas.getContext('2d');
    
    // Draw video frame to canvas
    context.drawImage(qrVideo, 0, 0, canvas.width, canvas.height);
    
    // Get image data
    const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
    
    // Scan for QR code
    const code = jsQR(imageData.data, imageData.width, imageData.height);
    
    if (code) {
      console.log('QR Code detected:', code.data);
      processQRCode(code.data);
    } else if (scanning) {
      requestAnimationFrame(scanFrame);
    }
  }

  function processQRCode(qrData) {
    scanning = false;
    
    // Show processing indicator
    showProcessingIndicator('Searching for student...');
    
    // Search student in database using QR data
    searchStudentByQR(qrData)
      .then(result => {
        hideProcessingIndicator();
        
        if (result.success) {
          // Prepare data for display
          const displayData = {
            studentId: result.data.student_id,
            studentName: result.data.student_name,
            course: result.data.course || 'N/A',
            parseMethod: result.new_student ? 'new student' : 'existing student'
          };
          
          scannedData = displayData;
          
          if (result.new_student) {
            scannerStatus.textContent = 'New student detected from QR code!';
          } else {
            scannerStatus.textContent = 'Existing student found!';
          }
          
          displayScanResult(displayData);
        } else {
          scannerStatus.textContent = result.message || 'Student not found in QR code.';
          setTimeout(() => {
            scannerStatus.textContent = 'Position the QR code within the scanner box';
            startVideoScan();
          }, 3000);
        }
      })
      .catch(error => {
        console.error('QR search error:', error);
        hideProcessingIndicator();
        scannerStatus.textContent = 'Error searching for student. Please try again.';
          setTimeout(() => {
            scannerStatus.textContent = 'Position the QR code within the scanner box';
            startVideoScan();
          }, 3000);
        });
  }

  // Search student by QR code using backend API
  async function searchStudentByQR(qrData) {
    try {
      const baseUrl = window.location.pathname.includes('Authentication_3') ? '/Authentication_3' : '';
      const response = await fetch(`${baseUrl}/payments/searchByQR`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `qr_data=${encodeURIComponent(qrData)}`
      });
      
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      
      const result = await response.json();
      return result;
    } catch (error) {
      console.error('API call error:', error);
      return {
        success: false,
        message: 'Failed to connect to server. Please try again.'
      };
    }
  }

  function parseStudentQRCode(qrData) {
    // Expected format: IDNUMBERFULLNAMECOURSE
    // Examples: 
    // - 154989Floro C. OCEROBSIT1 (6 digits)
    // - 12345678Juan DelaCruzBSCS4 (8 digits)
    // - 1234Maria SantosBSIT2 (4 digits)
    
    try {
      // Clean the data
      const cleanData = qrData.trim();
      
      // Try multiple parsing strategies to handle variable ID lengths
      let parsedResult = null;
      
      console.log('Parsing QR data:', cleanData);
      
      // Strategy 1: Look for course pattern first, then work backwards
      parsedResult = parseWithCourseFirst(cleanData);
      if (parsedResult) {
        console.log('Parsed using course-first method:', parsedResult);
        return parsedResult;
      }
      
      // Strategy 2: Try different ID lengths if first strategy fails
      parsedResult = parseWithVariableIdLength(cleanData);
      if (parsedResult) {
        console.log('Parsed using variable ID length method:', parsedResult);
        return parsedResult;
      }
      
      // Strategy 3: Try alternative formats
      parsedResult = parseAlternativeFormats(cleanData);
      if (parsedResult) {
        console.log('Parsed using alternative format method:', parsedResult);
        return parsedResult;
      }
      
      console.log('Failed to parse QR data with any method');
      return null;
      
    } catch (error) {
      console.error('QR parsing error:', error);
      return null;
    }
  }

  function parseWithCourseFirst(cleanData) {
    try {
      // Extract course (last part that matches pattern like BSIT1, BSCS4, etc.)
      const coursePatterns = [
        /(BS[A-Z]{2,6}\d*)$/i,           // BSIT1, BSCS4, BSCOMPSCI2, etc.
        /([A-Z]{2,6}\d*)$/i,             // IT1, CS4, COMPSCI2, etc.
        /(BS\s[A-Z\s]{2,15}\d*)$/i       // BS Computer Science 4, etc.
      ];
      
      let courseMatch = null;
      let course = '';
      
      for (const pattern of coursePatterns) {
        courseMatch = cleanData.match(pattern);
        if (courseMatch) {
          course = courseMatch[1].replace(/\s+/g, ' ').trim();
          break;
        }
      }
      
      if (!courseMatch) {
        return null;
      }
      
      // Remove course from the end to get ID + Name
      const idAndName = cleanData.substring(0, cleanData.lastIndexOf(course)).trim();
      
      // Extract ID (variable length digits at the start)
      const idMatch = idAndName.match(/^(\d{3,12})/); // Support 3-12 digit IDs
      if (!idMatch) {
        return null;
      }
      
      const studentId = idMatch[1];
      const studentName = idAndName.substring(studentId.length).trim();
      
      if (!studentName || studentName.length < 2) {
        return null;
      }
      
      return {
        studentId: studentId,
        studentName: studentName,
        course: course,
        rawData: cleanData,
        parseMethod: 'course-first'
      };
      
    } catch (error) {
      return null;
    }
  }

  function parseWithVariableIdLength(cleanData) {
    try {
      // Try different ID lengths from 3 to 12 digits
      for (let idLength = 3; idLength <= 12; idLength++) {
        const idRegex = new RegExp(`^(\\d{${idLength}})`);
        const idMatch = cleanData.match(idRegex);
        
        if (idMatch) {
          const studentId = idMatch[1];
          const remaining = cleanData.substring(idLength);
          
          // Look for course pattern in the remaining string
          const coursePatterns = [
            /(BS[A-Z]{2,6}\d*)$/i,
            /([A-Z]{2,6}\d*)$/i,
            /(BS\s[A-Z\s]{2,15}\d*)$/i
          ];
          
          for (const pattern of coursePatterns) {
            const courseMatch = remaining.match(pattern);
            if (courseMatch) {
              const course = courseMatch[1].replace(/\s+/g, ' ').trim();
              const nameEndIndex = remaining.lastIndexOf(courseMatch[1]);
              const studentName = remaining.substring(0, nameEndIndex).trim();
              
              if (studentName && studentName.length >= 2) {
                return {
                  studentId: studentId,
                  studentName: studentName,
                  course: course,
                  rawData: cleanData,
                  parseMethod: `variable-id-${idLength}`
                };
              }
            }
          }
        }
      }
      
      return null;
    } catch (error) {
      return null;
    }
  }

  function parseAlternativeFormats(cleanData) {
    try {
      // Handle formats with separators or different patterns
      const alternativePatterns = [
        // Format: ID-Name-Course or ID_Name_Course
        /^(\d{3,12})[-_\s]+([A-Za-z\s.]+?)[-_\s]+(BS[A-Z\s]{2,15}\d*|[A-Z]{2,6}\d*)$/i,
        
        // Format: Name ID Course (reversed)
        /^([A-Za-z\s.]+?)\s+(\d{3,12})\s+(BS[A-Z\s]{2,15}\d*|[A-Z]{2,6}\d*)$/i,
        
        // Format: Course Name ID (different order)
        /^(BS[A-Z\s]{2,15}\d*|[A-Z]{2,6}\d*)\s+([A-Za-z\s.]+?)\s+(\d{3,12})$/i
      ];
      
      for (let i = 0; i < alternativePatterns.length; i++) {
        const match = cleanData.match(alternativePatterns[i]);
        if (match) {
          let studentId, studentName, course;
          
          switch (i) {
            case 0: // ID-Name-Course
              studentId = match[1];
              studentName = match[2].trim();
              course = match[3].replace(/\s+/g, ' ').trim();
              break;
            case 1: // Name ID Course
              studentName = match[1].trim();
              studentId = match[2];
              course = match[3].replace(/\s+/g, ' ').trim();
              break;
            case 2: // Course Name ID
              course = match[1].replace(/\s+/g, ' ').trim();
              studentName = match[2].trim();
              studentId = match[3];
              break;
          }
          
          if (studentId && studentName && course && 
              studentId.length >= 3 && studentName.length >= 2) {
            return {
              studentId: studentId,
              studentName: studentName,
              course: course,
              rawData: cleanData,
              parseMethod: `alternative-${i + 1}`
            };
          }
        }
      }
      
      return null;
    } catch (error) {
      return null;
    }
  }

  function displayScanResult(data) {
    document.getElementById('scannedId').textContent = `${data.studentId} (${data.studentId.length} digits)`;
    document.getElementById('scannedName').textContent = data.studentName;
    document.getElementById('scannedCourse').textContent = data.course;
    
    scannerResult.style.display = 'block';
    scannerStatus.textContent = `QR code successfully scanned using ${data.parseMethod} method!`;
  }

  function populateFormFields(data) {
    const studentNameField = document.getElementById('studentName');
    const studentIdField = document.getElementById('studentId');
    
    if (studentNameField) {
      studentNameField.value = data.studentName;
      studentNameField.dispatchEvent(new Event('input'));
    }
    
    if (studentIdField) {
      studentIdField.value = data.studentId;
      studentIdField.dispatchEvent(new Event('input'));
    }
    
    // Show success message
    const successMsg = document.getElementById('successMessage');
    if (successMsg) {
      const idLength = data.studentId.length;
      successMsg.innerHTML = `
        <div style="text-align: left;">
          <strong>Student information filled from QR code!</strong><br>
          <small>ID: ${data.studentId} (${idLength} digits) | Name: ${data.studentName}</small>
        </div>
      `;
      successMsg.style.display = 'block';
      successMsg.classList.add('show');
      
      setTimeout(() => {
        successMsg.style.display = 'none';
        successMsg.classList.remove('show');
      }, 3000);
    }
  }

  function stopScanning() {
    scanning = false;
    
    if (stream) {
      stream.getTracks().forEach(track => track.stop());
      stream = null;
    }
    
    if (qrVideo) {
      qrVideo.srcObject = null;
    }
    
    qrModal.style.display = 'none';
    scannerResult.style.display = 'none';
    scannerStatus.textContent = 'Position the QR code within the scanner box';
    scannedData = null;
    hideProcessingIndicator();
  }

  // Processing indicator functions
  function showProcessingIndicator(text) {
    const processingIndicator = document.getElementById('processingIndicator');
    const processingText = document.getElementById('processingText');
    
    if (processingIndicator && processingText) {
      processingText.textContent = text;
      processingIndicator.style.display = 'block';
    }
  }

  function hideProcessingIndicator() {
    const processingIndicator = document.getElementById('processingIndicator');
    if (processingIndicator) {
      processingIndicator.style.display = 'none';
    }
  }
}

/**
 * Initialize student search functionality
 */
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
  const contributionId = document.getElementById('contributionId');
  
  console.log('Initializing amount field...', {
    amountField: amountField,
    contributionId: contributionId ? contributionId.value : 'not found',
    currentValue: amountField ? amountField.value : 'field not found',
    attributeValue: amountField ? amountField.getAttribute('value') : 'field not found'
  });
  
  if (!amountField) {
    console.error('Amount field not found!');
    return;
  }
  
  // Force set the value from the attribute
  const expectedValue = amountField.getAttribute('value');
  if (expectedValue && expectedValue !== '' && expectedValue !== '0' && expectedValue !== '0.00') {
    amountField.value = expectedValue;
    console.log('Forced amount field value to:', expectedValue);
    
    // Also set a data attribute to preserve the value
    amountField.setAttribute('data-forced-value', expectedValue);
    
    // Trigger input event to ensure any listeners are notified
    amountField.dispatchEvent(new Event('input', { bubbles: true }));
    
    // Verify the value was set
    setTimeout(() => {
      console.log('Amount field value after timeout:', amountField.value);
    }, 100);
  }
}

// Show QR Receipt Modal
function showQRReceipt(receiptData, downloadUrl) {
  // Create modal HTML
  const modalHTML = `
    <div id="qrReceiptModal" class="qr-receipt-modal" style="
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.8);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 9999;
    ">
      <div class="receipt-modal-content" style="
        background: white;
        border-radius: 15px;
        padding: 30px;
        max-width: 500px;
        width: 90%;
        text-align: center;
        box-shadow: 0 15px 35px rgba(0,0,0,0.2);
      ">
        <div class="receipt-header">
          <h2 style="color: #667eea; margin-bottom: 10px;">
            <i class="fas fa-receipt"></i> Payment Receipt
          </h2>
          <p style="color: #666; margin-bottom: 20px;">QR Code Generated Successfully</p>
        </div>
        
        <div class="receipt-details" style="
          background: #f8f9fa;
          border-radius: 10px;
          padding: 20px;
          margin-bottom: 20px;
          text-align: left;
        ">
          <div class="detail-row" style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <strong>Student:</strong> <span>${receiptData.student_name}</span>
          </div>
          <div class="detail-row" style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <strong>Student ID:</strong> <span>${receiptData.student_id}</span>
          </div>
          <div class="detail-row" style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <strong>Contribution:</strong> <span>${receiptData.contribution_title}</span>
          </div>
          <div class="detail-row" style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <strong>Amount:</strong> <span>$${parseFloat(receiptData.amount).toFixed(2)}</span>
          </div>
          <div class="detail-row" style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <strong>Payment Method:</strong> <span>${receiptData.payment_method}</span>
          </div>
          <div class="detail-row" style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <strong>Verification Code:</strong> <span style="font-family: monospace; background: #e9ecef; padding: 2px 5px; border-radius: 3px;">${receiptData.verification_code}</span>
          </div>
        </div>
        
        <div class="receipt-actions" style="display: flex; gap: 15px; justify-content: center;">
          <a href="${downloadUrl}" class="btn-download" style="
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
          " download>
            <i class="fas fa-download"></i> Download QR Receipt
          </a>
          <button onclick="closeQRReceiptModal()" style="
            background: #6c757d;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
          ">
            <i class="fas fa-times"></i> Close
          </button>
        </div>
        
        <div class="receipt-note" style="
          margin-top: 20px;
          padding: 15px;
          background: #e3f2fd;
          border-radius: 8px;
          font-size: 0.9em;
          color: #1565c0;
        ">
          <i class="fas fa-info-circle"></i>
          <strong>Note:</strong> Save this QR receipt! Students can use it for verification when claiming their items.
        </div>
      </div>
    </div>
  `;
  
  // Add modal to page
  document.body.insertAdjacentHTML('beforeend', modalHTML);
  
  // Prevent body scroll
  document.body.style.overflow = 'hidden';
}

// Close QR Receipt Modal
function closeQRReceiptModal() {
  const modal = document.getElementById('qrReceiptModal');
  if (modal) {
    modal.remove();
    document.body.style.overflow = '';
    
    // Refresh page after closing modal
    setTimeout(() => {
      window.location.reload();
    }, 500);
  }
}