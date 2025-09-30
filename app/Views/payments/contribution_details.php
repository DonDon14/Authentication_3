<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($contribution['title']) ?> - Payment Details</title>
  <link rel="stylesheet" href="<?= base_url('css/contribution_details.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <script>
    // Define functions in head so they're available when HTML loads
    function showPaymentDetails(paymentId) {
      console.log('showPaymentDetails called with ID:', paymentId);
      
      // Create modal dynamically since HTML modal isn't working
      let modal = document.getElementById('dynamicPaymentModal');
      if (!modal) {
        modal = document.createElement('div');
        modal.id = 'dynamicPaymentModal';
        modal.innerHTML = `
          <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center;">
            <div style="background: white; padding: 20px; border-radius: 8px; max-width: 500px; width: 90%; max-height: 80vh; overflow-y: auto;">
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                <h3>Payment Details</h3>
                <span onclick="closeDynamicModal()" style="cursor: pointer; font-size: 24px; font-weight: bold;">&times;</span>
              </div>
              <div id="dynamicPaymentContent">
                <div style="text-align: center; padding: 20px;">
                  <div style="font-size: 20px;">⏳</div>
                  <p>Loading payment details...</p>
                </div>
              </div>
            </div>
          </div>
        `;
        document.body.appendChild(modal);
      }
      
      modal.style.display = 'block';
      
      // Fetch payment details
      fetch('<?= base_url('payments/getPaymentDetails/') ?>' + paymentId)
        .then(response => response.json())
        .then(data => {
          console.log('Payment API Response:', data);
          const content = document.getElementById('dynamicPaymentContent');
          if (data.success) {
            displayDynamicPaymentDetails(data.data, content);
          } else {
            content.innerHTML = '<div style="color: red; text-align: center;">Error: ' + (data.message || 'Failed to load') + '</div>';
          }
        })
        .catch(error => {
          console.error('Error:', error);
          const content = document.getElementById('dynamicPaymentContent');
          content.innerHTML = '<div style="color: red; text-align: center;">Error loading payment details</div>';
        });
      
      // Show modal with loading state
      modal.style.display = 'block';
      content.innerHTML = `
        <div class="loading">
          <i class="fas fa-spinner fa-spin"></i>
          <p>Loading payment details...</p>
        </div>
      `;
      
      // Fetch payment details
      fetch(`<?= base_url('payments/getPaymentDetails/') ?>${paymentId}`)
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            displayPaymentDetails(data.data);
          } else {
            showError(data.message || 'Failed to load payment details');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          showError('Error loading payment details');
        });
    }

    // Display payment details in dynamic modal
    function displayDynamicPaymentDetails(data, content) {
      const payment = data.payment;
      const contribution = data.contribution;
      const qrImageUrl = data.qr_image_url;
      
      let html = '<div style="line-height: 1.6;">';
      html += '<div style="margin-bottom: 15px;">';
      html += '<h4 style="color: #333; margin-bottom: 10px;">📄 Payment Information</h4>';
      html += '<p><strong>Payment ID:</strong> #' + payment.id + '</p>';
      html += '<p><strong>Student:</strong> ' + payment.student_name + ' (' + payment.student_id + ')</p>';
      html += '<p><strong>Contribution:</strong> ' + contribution.title + '</p>';
      html += '<p><strong>Amount:</strong> <span style="color: #28a745; font-weight: bold;">$' + parseFloat(payment.amount_paid).toFixed(2) + '</span></p>';
      html += '<p><strong>Method:</strong> ' + payment.payment_method.charAt(0).toUpperCase() + payment.payment_method.slice(1).replace('_', ' ') + '</p>';
      html += '<p><strong>Date:</strong> ' + new Date(payment.payment_date).toLocaleString() + '</p>';
      html += '<p><strong>Status:</strong> <span style="color: #28a745;">' + payment.payment_status.charAt(0).toUpperCase() + payment.payment_status.slice(1) + '</span></p>';
      html += '<p><strong>Verification Code:</strong> <code style="background: #f1f1f1; padding: 2px 6px; border-radius: 3px;">' + payment.verification_code + '</code></p>';
      html += '</div>';
      
      console.log('QR Image URL:', qrImageUrl);
      
      if (qrImageUrl) {
        html += '<div style="border-top: 1px solid #eee; padding-top: 15px; text-align: center;">';
        html += '<h4 style="color: #333; margin-bottom: 10px;">📱 QR Receipt</h4>';
        html += '<img src="' + qrImageUrl + '" alt="QR Code" style="max-width: 200px; border: 2px solid #007bff; border-radius: 8px; margin: 10px 0;" onerror="console.log(\'QR image failed to load:\', this.src)">';
        html += '<br><small style="color: #666;">Scan this QR code to verify payment</small>';
        html += '<br><button onclick="downloadQR(\'' + qrImageUrl + '\', \'receipt_' + payment.id + '\')" style="margin-top: 10px; padding: 8px 16px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">📥 Download QR</button>';
        html += '</div>';
      } else {
        html += '<div style="border-top: 1px solid #eee; padding-top: 15px; text-align: center; color: #999;">';
        html += '<p>⚠️ QR code not available for this payment</p>';
        html += '<p><small>QR URL was: ' + (qrImageUrl || 'null') + '</small></p>';
        html += '</div>';
      }
      
      html += '</div>';
      content.innerHTML = html;
    }

    // Close dynamic modal
    function closeDynamicModal() {
      const modal = document.getElementById('dynamicPaymentModal');
      if (modal) {
        modal.style.display = 'none';
      }
    }

    // Download QR function
    function downloadQR(qrUrl, filename) {
      const link = document.createElement('a');
      link.href = qrUrl;
      link.download = filename + '.png';
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    }

    // Display payment details in modal
    function displayPaymentDetails(data) {
      const content = document.getElementById('paymentDetailsContent');
      const payment = data.payment;
      const contribution = data.contribution;
      const qrImageUrl = data.qr_image_url;
      
      content.innerHTML = `
        <div class="payment-details-grid">
          <div class="payment-info">
            <h4><i class="fas fa-receipt"></i> Payment Information</h4>
            <div class="detail-row">
              <span class="label">Payment ID:</span>
              <span class="value">#${payment.id}</span>
            </div>
            <div class="detail-row">
              <span class="label">Student Name:</span>
              <span class="value">${payment.student_name}</span>
            </div>
            <div class="detail-row">
              <span class="label">Student ID:</span>
              <span class="value">${payment.student_id}</span>
            </div>
            <div class="detail-row">
              <span class="label">Contribution:</span>
              <span class="value">${contribution.title}</span>
            </div>
            <div class="detail-row">
              <span class="label">Amount Paid:</span>
              <span class="value amount">$${parseFloat(payment.amount_paid).toFixed(2)}</span>
            </div>
            <div class="detail-row">
              <span class="label">Payment Method:</span>
              <span class="value">${payment.payment_method.charAt(0).toUpperCase() + payment.payment_method.slice(1).replace('_', ' ')}</span>
            </div>
            <div class="detail-row">
              <span class="label">Payment Date:</span>
              <span class="value">${new Date(payment.payment_date).toLocaleString()}</span>
            </div>
            <div class="detail-row">
              <span class="label">Status:</span>
              <span class="value status-badge status-${payment.payment_status.toLowerCase()}">${payment.payment_status.charAt(0).toUpperCase() + payment.payment_status.slice(1)}</span>
            </div>
            <div class="detail-row">
              <span class="label">Verification Code:</span>
              <span class="value verification-code">${payment.verification_code}</span>
            </div>
          </div>
          
          <div class="qr-section">
            <h4><i class="fas fa-qrcode"></i> QR Receipt</h4>
            ${qrImageUrl ? `
              <div class="qr-container">
                <img src="${qrImageUrl}" alt="Payment QR Code" class="qr-image">
                <p class="qr-instruction">Scan this QR code to verify payment</p>
                <button class="btn-secondary" onclick="downloadQR('${qrImageUrl}', 'receipt_${payment.id}')">
                  <i class="fas fa-download"></i> Download QR Code
                </button>
              </div>
            ` : `
              <div class="no-qr">
                <i class="fas fa-exclamation-circle"></i>
                <p>QR code not available for this payment</p>
              </div>
            `}
          </div>
        </div>
      `;
    }

    // Show error in modal
    function showError(message) {
      const content = document.getElementById('paymentDetailsContent');
      content.innerHTML = `
        <div class="error-message">
          <i class="fas fa-exclamation-triangle"></i>
          <p>${message}</p>
        </div>
      `;
    }

    // Close modal
    function closePaymentModal() {
      document.getElementById('paymentDetailsModal').style.display = 'none';
    }

    // Download QR code
    function downloadQR(qrUrl, filename) {
      const link = document.createElement('a');
      link.href = qrUrl;
      link.download = filename + '.png';
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
      const modal = document.getElementById('paymentDetailsModal');
      if (event.target === modal) {
        modal.style.display = 'none';
      }
    }
    
    // Test function to check if modal exists when page loads
    window.addEventListener('DOMContentLoaded', function() {
      console.log('DOM loaded, checking for modal...');
      
      // Check debug marker
      const debugMarker = document.getElementById('debug-marker');
      console.log('Debug marker found:', !!debugMarker);
      
      const modal = document.getElementById('paymentDetailsModal');
      const content = document.getElementById('paymentDetailsContent');
      console.log('Modal found on load:', !!modal);
      console.log('Content found on load:', !!content);
      
      if (modal) {
        console.log('Modal display style:', window.getComputedStyle(modal).display);
      }
      
      // Show all elements with 'modal' in their ID
      const allElements = document.querySelectorAll('*[id*="modal"], *[id*="Modal"]');
      console.log('All modal-related elements:', allElements);
    });
  </script>
</head>
<body>
  <div class="contribution-details-container">
    <!-- Header -->
    <div class="contribution-details-header">
      <div class="back-button">
        <a href="<?= base_url('contributions') ?>" class="back-btn">
          <i class="fas fa-arrow-left"></i>
        </a>
      </div>
      <div class="header-content">
        <h2><?= esc($contribution['title']) ?></h2>
        <p class="description">Payment Details & History</p>
      </div>
      <div class="header-actions">
        <a href="<?= base_url('payments?contribution=' . $contribution['id']) ?>" class="btn-primary">
          <i class="fas fa-plus"></i> Add Payment
        </a>
      </div>
    </div>

    <!-- Contribution Summary -->
    <div class="contribution-summary">
      <div class="summary-card">
        <div class="contribution-info">
          <h3><?= esc($contribution['title']) ?></h3>
          <p class="contribution-description"><?= esc($contribution['description']) ?></p>
          <div class="contribution-meta">
            <span class="amount-badge">$<?= number_format($contribution['amount'], 2) ?></span>
            <span class="category-badge"><?= esc($contribution['category']) ?></span>
            <span class="status-badge status-<?= strtolower($contribution['status']) ?>">
              <i class="fas fa-<?= $contribution['status'] === 'active' ? 'check-circle' : 'pause-circle' ?>"></i>
              <?= ucfirst($contribution['status']) ?>
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Statistics -->
    <div class="stats-grid">
      <div class="stat-card stat-primary">
        <div class="stat-icon">
          <i class="fas fa-users"></i>
        </div>
        <div class="stat-content">
          <h4><?= $stats['total_payments'] ?></h4>
          <p>Students Paid</p>
        </div>
      </div>
      
      <div class="stat-card stat-success">
        <div class="stat-icon">
          <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-content">
          <h4>₱<?= number_format($stats['total_amount'], 2) ?></h4>
          <p>Total Collected</p>
        </div>
      </div>
      
      <div class="stat-card stat-info">
        <div class="stat-icon">
          <i class="fas fa-chart-line"></i>
        </div>
        <div class="stat-content">
          <h4>₱<?= number_format($stats['average_amount'], 2) ?></h4>
          <p>Average Payment</p>
        </div>
      </div>
      
      <div class="stat-card stat-warning">
        <div class="stat-icon">
          <i class="fas fa-percentage"></i>
        </div>
        <div class="stat-content">
          <h4><?= $stats['total_payments'] > 0 ? number_format(($stats['total_amount'] / ($contribution['amount'] * $stats['total_payments'])) * 100, 1) : '0' ?>%</h4>
          <p>Payment Rate</p>
        </div>
      </div>
    </div>

    <!-- Payment History -->
    <div class="payment-history-section">
      <div class="section-header">
        <h3><i class="fas fa-history"></i> Payment History</h3>
        <p class="section-description">
          <?php if (count($payments) > 0): ?>
            Showing <?= count($payments) ?> payment<?= count($payments) > 1 ? 's' : '' ?> for this contribution
          <?php else: ?>
            No payments recorded yet for this contribution
          <?php endif; ?>
        </p>
      </div>

      <?php if (count($payments) > 0): ?>
        <div class="payments-list">
          <?php foreach ($payments as $payment): ?>
            <div class="payment-item" data-payment-id="<?= $payment['id'] ?>" style="cursor: pointer; position: relative;">
              <!-- Invisible click overlay -->
              <div class="click-overlay" onclick="showPaymentDetails(<?= $payment['id'] ?>)" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 10; cursor: pointer;"></div>
              
              <div class="student-info">
                <div class="student-details">
                  <h4 class="student-name"><?= esc($payment['student_name']) ?></h4>
                  <p class="student-id">ID: <?= esc($payment['student_id']) ?></p>
                  <p class="payment-date">
                    <i class="fas fa-calendar"></i>
                    <?= date('M j, Y \a\t g:i A', strtotime($payment['payment_date'])) ?>
                  </p>
                </div>
                <div class="payment-meta">
                  <span class="payment-method">
                    <i class="fas fa-<?= $payment['payment_method'] === 'cash' ? 'money-bill' : ($payment['payment_method'] === 'card' ? 'credit-card' : 'mobile-alt') ?>"></i>
                    <?= ucfirst(str_replace('_', ' ', $payment['payment_method'])) ?>
                  </span>
                </div>
              </div>
              <div class="payment-amount">
                <span class="amount">₱<?= number_format((float)($payment['amount_paid'] ?? $payment['amount'] ?? 0), 2) ?></span>
                <span class="status status-<?= strtolower($payment['payment_status']) ?>">
                  <i class="fas fa-<?= $payment['payment_status'] === 'completed' ? 'check-circle' : 'clock' ?>"></i>
                  <?= ucfirst($payment['payment_status']) ?>
                </span>
                <div class="click-indicator">
                  <i class="fas fa-eye"></i>
                  <span>View Details</span>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="no-payments">
          <div class="no-payments-icon">
            <i class="fas fa-receipt"></i>
          </div>
          <h3>No Payments Yet</h3>
          <p>No students have made payments for this contribution yet.</p>
          <a href="<?= base_url('payments?contribution=' . $contribution['id']) ?>" class="btn-primary">
            <i class="fas fa-plus"></i>
            Record First Payment
          </a>
        </div>
      <?php endif; ?>
    </div>

    <!-- Export Options -->
    <?php if (count($payments) > 0): ?>
    <div class="export-section">
      <h4><i class="fas fa-download"></i> Export Options</h4>
      <div class="export-buttons">
        <button class="btn-secondary" onclick="exportToCSV()">
          <i class="fas fa-file-csv"></i> Export CSV
        </button>
        <button class="btn-secondary" onclick="printReport()">
          <i class="fas fa-print"></i> Print Report
        </button>
      </div>
    </div>
    <?php endif; ?>

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

  <script>
    // Export to CSV functionality
    function exportToCSV() {
      console.log('Export to CSV triggered');

      try {
        const csvData = [];
        csvData.push(['Student Name', 'Student ID', 'Payment Date', 'Amount', 'Payment Method', 'Status', 'Verification Code']);
        
        // Add payment data
        <?php if (count($payments) > 0): ?>
        <?php foreach ($payments as $payment): ?>
        csvData.push([
          '<?= addslashes(esc($payment['student_name'])) ?>',
          '<?= esc($payment['student_id']) ?>',
          '<?= date('Y-m-d H:i:s', strtotime($payment['payment_date'])) ?>',
          '<?= number_format((float)($payment['amount_paid'] ?? $payment['amount'] ?? 0), 2) ?>',
          '<?= ucfirst(str_replace('_', ' ', $payment['payment_method'])) ?>',
          '<?= ucfirst($payment['payment_status']) ?>',
          '<?= esc($payment['verification_code']) ?>'
        ]);
        <?php endforeach; ?>
        <?php endif; ?>

        if (csvData.length <= 1) {
          alert('No payment data available to export.');
          return;
        }
        
        // Convert to CSV format
        const csvContent = csvData.map(row => 
          row.map(cell => {
            // Escape quotes and wrap in quotes if contains comma
            const escaped = String(cell).replace(/"/g, '""');
            return escaped.includes(',') ? `"${escaped}"` : escaped;
          }).join(',')
        ).join('\n');
        
        // Create and download file
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        
        // Create filename with contribution title and date
        const contributionTitle = '<?= preg_replace('/[^a-zA-Z0-9_-]/', '_', esc($contribution['title'])) ?>';
        const currentDate = new Date().toISOString().split('T')[0];
        const filename = `${contributionTitle}_payments_${currentDate}.csv`;
        
        link.href = url;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
        
        console.log('CSV export completed successfully');

      } catch (error) {
        console.error('Error exporting to CSV:', error);
        alert('An error occurred while exporting to CSV: ' + error.message);
      }
    }

    // Print report functionality
    function printReport() {
      console.log('Print report triggered');
      
      try {
        // Create a new window for printing
        const printWindow = window.open('', '_blank');
        
        if (!printWindow) {
          alert('Please allow popups to print the report');
          return;
        }
        
        // Generate print-friendly HTML
        const printContent = `
          <!DOCTYPE html>
          <html>
          <head>
            <title><?= esc($contribution['title']) ?> - Payment Report</title>
            <style>
              body { font-family: Arial, sans-serif; margin: 20px; }
              .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px; }
              .contribution-info { margin-bottom: 20px; }
              .stats { display: flex; justify-content: space-around; margin: 20px 0; }
              .stat { text-align: center; padding: 10px; border: 1px solid #ddd; }
              table { width: 100%; border-collapse: collapse; margin-top: 20px; }
              th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
              th { background-color: #f2f2f2; font-weight: bold; }
              .amount { text-align: right; font-weight: bold; }
              .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #666; }
              @media print {
                body { margin: 0; }
                .no-print { display: none; }
              }
            </style>
          </head>
          <body>
            <div class="header">
              <h1><?= esc($contribution['title']) ?></h1>
              <h2>Payment Report</h2>
              <p>Generated on ${new Date().toLocaleDateString()}</p>
            </div>
            
            <div class="contribution-info">
              <p><strong>Description:</strong> <?= esc($contribution['description']) ?></p>
              <p><strong>Amount:</strong> $<?= number_format($contribution['amount'], 2) ?></p>
              <p><strong>Category:</strong> <?= esc($contribution['category']) ?></p>
              <p><strong>Status:</strong> <?= ucfirst($contribution['status']) ?></p>
            </div>
            
            <div class="stats">
              <div class="stat">
                <h3><?= $stats['total_payments'] ?></h3>
                <p>Students Paid</p>
              </div>
              <div class="stat">
                <h3>$<?= number_format($stats['total_amount'], 2) ?></h3>
                <p>Total Collected</p>
              </div>
              <div class="stat">
                <h3>$<?= number_format($stats['average_amount'], 2) ?></h3>
                <p>Average Payment</p>
              </div>
            </div>
            
            <?php if (count($payments) > 0): ?>
            <table>
              <thead>
                <tr>
                  <th>Student Name</th>
                  <th>Student ID</th>
                  <th>Payment Date</th>
                  <th>Amount</th>
                  <th>Method</th>
                  <th>Status</th>
                  <th>Verification Code</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($payments as $payment): ?>
                <tr>
                  <td><?= esc($payment['student_name']) ?></td>
                  <td><?= esc($payment['student_id']) ?></td>
                  <td><?= date('M j, Y g:i A', strtotime($payment['payment_date'])) ?></td>
                  <td class="amount">$<?= number_format((float)($payment['amount_paid'] ?? $payment['amount'] ?? 0), 2) ?></td>
                  <td><?= ucfirst(str_replace('_', ' ', $payment['payment_method'])) ?></td>
                  <td><?= ucfirst($payment['payment_status']) ?></td>
                  <td><?= esc($payment['verification_code']) ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
            <?php else: ?>
            <p style="text-align: center; margin: 40px 0; font-style: italic;">No payments recorded yet.</p>
            <?php endif; ?>
            
            <div class="footer">
              <p>This report was generated automatically from the payment system.</p>
            </div>
          </body>
          </html>
        `;
        
        printWindow.document.write(printContent);
        printWindow.document.close();
        
        // Wait for content to load then print
        printWindow.onload = function() {
          printWindow.print();
          printWindow.close();
        };
        
        console.log('Print report completed successfully');
        
      } catch (error) {
        console.error('Error printing report:', error);
        alert('An error occurred while printing the report: ' + error.message);
      }
    }
  </script>

  

</div>

<script>
// Simple test
console.log('Modal script loaded');
window.addEventListener('DOMContentLoaded', function() {
  const modal = document.getElementById('paymentDetailsModal');
  console.log('Modal found:', !!modal);
});
</script>

  

  <!-- Payment Modal - Added at end -->
  <div id="paymentDetailsModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999;">
    <div style="background: white; margin: 5% auto; padding: 20px; width: 90%; max-width: 600px; border-radius: 8px; max-height: 80vh; overflow-y: auto;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
        <h3>Payment Details</h3>
        <span onclick="closePaymentModal()" style="cursor: pointer; font-size: 24px; font-weight: bold; color: #999;">&times;</span>
      </div>
      <div id="paymentDetailsContent">
        <div style="text-align: center; padding: 20px;">
          <div style="font-size: 20px; color: #007bff;">⏳</div>
          <p>Loading payment details...</p>
        </div>
      </div>
    </div>
  </div>

  <script>
    
    function displayPaymentDetails(data) {
      const content = document.getElementById('paymentDetailsContent');
      const payment = data.payment;
      const contribution = data.contribution;
      const qrImageUrl = data.qr_image_url;
      
      let html = '<div style="line-height: 1.6;">';
      html += '<h4>📄 Payment Information</h4>';
      html += '<p><strong>Payment ID:</strong> #' + payment.id + '</p>';
      html += '<p><strong>Student:</strong> ' + payment.student_name + ' (' + payment.student_id + ')</p>';
      html += '<p><strong>Contribution:</strong> ' + contribution.title + '</p>';
      html += '<p><strong>Amount:</strong> $' + parseFloat(payment.amount_paid).toFixed(2) + '</p>';
      html += '<p><strong>Method:</strong> ' + payment.payment_method + '</p>';
      html += '<p><strong>Date:</strong> ' + new Date(payment.payment_date).toLocaleDateString() + '</p>';
      html += '<p><strong>Status:</strong> ' + payment.payment_status + '</p>';
      html += '<p><strong>Verification Code:</strong> ' + payment.verification_code + '</p>';
      
      if (qrImageUrl) {
        html += '<hr style="margin: 20px 0;">';
        html += '<h4>📱 QR Receipt</h4>';
        html += '<div style="text-align: center;">';
        html += '<img src="' + qrImageUrl + '" alt="QR Code" style="max-width: 200px; border: 2px solid #007bff; border-radius: 8px; margin: 10px 0;">';
        html += '<br><small>Scan this QR code to verify payment</small>';
        html += '</div>';
      } else {
        html += '<hr style="margin: 20px 0;">';
        html += '<p style="text-align: center; color: #999;">⚠️ QR code not available for this payment</p>';
      }
      
      html += '</div>';
      content.innerHTML = html;
    }

    function closePaymentModal() {
      document.getElementById('paymentDetailsModal').style.display = 'none';
    }

    // Close modal when clicking outside
    document.addEventListener('click', function(e) {
      const modal = document.getElementById('paymentDetailsModal');
      if (e.target === modal) {
        modal.style.display = 'none';
      }
    });

    // Test that modal exists
    document.addEventListener('DOMContentLoaded', function() {
      console.log('DOM loaded - checking modal');
      const modal = document.getElementById('paymentDetailsModal');
      console.log('Modal found:', !!modal);
    });
  </script>

</body>
</html>