document.addEventListener('DOMContentLoaded', function() {
    console.log('Payment details script loaded');
});

function showPaymentDetails(paymentId) {
    console.log('Showing payment details for:', paymentId);
    
    fetch(`/payments/getPaymentDetails/${paymentId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                displayPaymentDetails(data);
            } else {
                throw new Error(data.message || 'Failed to load payment details');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading payment details: ' + error.message);
        });
}

function createDynamicModal() {
    const modal = document.createElement('div');
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
    return modal;
}

function displayPaymentDetails(data, container) {
    const payment = data.payment;
    const contribution = data.contribution;

    container.innerHTML = `
        <div style="line-height: 1.6;">
            <div style="margin-bottom: 15px;">
                <h4 style="margin-bottom: 10px;">Payment Information</h4>
                <p><strong>Student Name:</strong> ${payment.student_name}</p>
                <p><strong>Student ID:</strong> ${payment.student_id}</p>
                <p><strong>Amount Paid:</strong> ₱${parseFloat(payment.amount_paid).toFixed(2)}</p>
                <p><strong>Payment Date:</strong> ${new Date(payment.payment_date).toLocaleString()}</p>
                <p><strong>Payment Method:</strong> ${payment.payment_method.toUpperCase()}</p>
                <p><strong>Status:</strong> ${payment.payment_status.toUpperCase()}</p>
                <p><strong>Verification Code:</strong> ${payment.verification_code || 'N/A'}</p>
            </div>
            
            <div style="margin-bottom: 15px;">
                <h4 style="margin-bottom: 10px;">Contribution Details</h4>
                <p><strong>Title:</strong> ${contribution.title}</p>
                <p><strong>Amount:</strong> ₱${parseFloat(contribution.amount).toFixed(2)}</p>
                <p><strong>Due Date:</strong> ${new Date(contribution.due_date).toLocaleDateString()}</p>
            </div>
            
            ${data.qr_image_url ? `
                <div style="text-align: center; margin-top: 20px;">
                    <img src="${data.qr_image_url}" alt="QR Code" style="max-width: 200px; margin-bottom: 10px;">
                    <br>
                    <button onclick="downloadQR('${data.qr_image_url}', 'payment_qr_${payment.id}.png')" 
                            style="padding: 8px 16px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        Download QR
                    </button>
                </div>
            ` : ''}
        </div>
    `;
}

function downloadQR(qrUrl, filename) {
    fetch(qrUrl)
        .then(response => response.blob())
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(url);
        })
        .catch(error => {
            console.error('Error downloading QR:', error);
            alert('Error downloading QR code');
        });
}

function closeDynamicModal() {
    const modal = document.getElementById('dynamicPaymentModal');
    if (modal) {
        modal.style.display = 'none';
    }
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

// Close modal when clicking outside
window.onclick = function(event) {
  const modal = document.getElementById('paymentDetailsModal');
  if (event.target === modal) {
    modal.style.display = 'none';
  }
};

window.addEventListener('DOMContentLoaded', function() {
  const modal = document.getElementById('paymentDetailsModal');
  console.log('Modal found:', !!modal);
});

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

    // Export to CSV functionality
    function exportToCSV(payments, contributionTitle) {
        console.log('Export to CSV triggered');

        try {
            const csvData = [];
            csvData.push(['Student Name', 'Student ID', 'Payment Date', 'Amount', 'Payment Method', 'Status', 'Verification Code']);
            
            // Add payment data if available
            if (payments && payments.length > 0) {
                payments.forEach(payment => {
                    csvData.push([
                        payment.student_name,
                        payment.student_id,
                        new Date(payment.payment_date).toLocaleString(),
                        parseFloat(payment.amount_paid || payment.amount || 0).toFixed(2),
                        payment.payment_method.charAt(0).toUpperCase() + 
                            payment.payment_method.slice(1).replace('_', ' '),
                        payment.payment_status.charAt(0).toUpperCase() + 
                            payment.payment_status.slice(1),
                        payment.verification_code
                    ]);
                });
            }

            if (csvData.length <= 1) {
                alert('No payment data available to export.');
                return;
            }
            
            // Convert to CSV format
            const csvContent = csvData.map(row => 
                row.map(cell => {
                    const escaped = String(cell).replace(/"/g, '""');
                    return escaped.includes(',') ? `"${escaped}"` : escaped;
                }).join(',')
            ).join('\n');
            
            // Create and download file
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            
            // Create filename with sanitized contribution title and date
            const sanitizedTitle = contributionTitle.replace(/[^a-zA-Z0-9_-]/g, '_');
            const currentDate = new Date().toISOString().split('T')[0];
            const filename = `${sanitizedTitle}_payments_${currentDate}.csv`;
            
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


    // Simple test
    console.log('Modal script loaded');
    window.addEventListener('DOMContentLoaded', function() {
      const modal = document.getElementById('paymentDetailsModal');
      console.log('Modal found:', !!modal);
    });

    function displayPaymentDetails(data) {
      const payment = data.payment;
      const contribution = data.contribution;
      let modal = document.getElementById('paymentDetailsModal');
      
      if (!modal) {
        modal = createPaymentModal();
      }
      
      const content = modal.querySelector('.modal-content');
      content.innerHTML = `
        <div class="modal-header">
            <h4>Payment Details</h4>
            <span class="close">&times;</span>
        </div>
        <div class="modal-body">
            <div class="payment-summary-box">
                <div class="qr-section">
                    <img src="/payments/getQRCode/${payment.student_id}/${contribution.id}" 
                         alt="Payment QR" 
                         class="payment-qr">
                    <button onclick="downloadQR('/payments/getQRCode/${payment.student_id}/${contribution.id}', 'payment_qr.png')"
                            class="btn btn-sm btn-primary">
                        <i class="fas fa-download"></i> Download QR
                    </button>
                </div>
                <div class="summary-details">
                    <div class="total-section">
                        <h5>Total Amount Due</h5>
                        <p class="amount">₱${parseFloat(contribution.amount).toFixed(2)}</p>
                    </div>
                    <div class="payment-progress">
                        <div class="progress-row">
                            <span>Total Paid:</span>
                            <span class="value">₱${parseFloat(payment.total_paid || 0).toFixed(2)}</span>
                        </div>
                        <div class="progress-row">
                            <span>Remaining:</span>
                            <span class="value">₱${parseFloat(contribution.amount - (payment.total_paid || 0)).toFixed(2)}</span>
                        </div>
                        <div class="status-badge ${payment.payment_status}">
                            ${payment.payment_status.replace('_', ' ').toUpperCase()}
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="payment-details-section">
                <h5>Student Information</h5>
                <div class="details-grid">
                    <div class="detail-item">
                        <label>Student Name:</label>
                        <span>${payment.student_name}</span>
                    </div>
                    <div class="detail-item">
                        <label>Student ID:</label>
                        <span>${payment.student_id}</span>
                    </div>
                </div>

                <h5>Payment History</h5>
                <div class="payment-history-list">
                    ${payment.history.map(p => `
                        <div class="history-item">
                            <div class="payment-date">
                                ${new Date(p.payment_date).toLocaleDateString('en-US', {
                                    year: 'numeric',
                                    month: 'short',
                                    day: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit'
                                })}
                            </div>
                            <div class="payment-info">
                                <span class="amount">₱${parseFloat(p.amount_paid).toFixed(2)}</span>
                                <span class="method">${p.payment_method.replace('_', ' ')}</span>
                            </div>
                            <div class="verification">
                                <span class="code">Verification: ${p.verification_code}</span>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        </div>
    `;
    
    modal.style.display = 'block';
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
    
    function showStudentPaymentHistory(contributionId, studentId) {
    console.log('Showing payment history for:', contributionId, studentId);
    
    fetch(`/payments/studentPaymentHistory/${contributionId}/${studentId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                displayPaymentHistoryModal(data);
            } else {
                throw new Error(data.message || 'Failed to load payment history');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading payment history: ' + error.message);
        });
}

function createPaymentHistoryModal() {
    const modal = document.createElement('div');
    modal.id = 'paymentHistoryModal';
    modal.className = 'modal';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="payment-history-container">
                <div class="modal-header">
                    <h4>Payment History</h4>
                    <button type="button" class="close" onclick="closePaymentModal()">&times;</button>
                </div>
                <div id="paymentHistoryContent">
                    <div class="loading">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p>Loading payment history...</p>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Add CSS if not already present
    if (!document.getElementById('payment-modal-styles')) {
        const styles = document.createElement('style');
        styles.id = 'payment-modal-styles';
        styles.textContent = `
            .modal {
                display: none;
                position: fixed;
                z-index: 1000;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0,0,0,0.5);
            }
            .modal-content {
                background-color: #fefefe;
                margin: 15% auto;
                padding: 20px;
                border: 1px solid #888;
                width: 80%;
                max-width: 600px;
                border-radius: 8px;
            }
            .loading {
                text-align: center;
                padding: 20px;
            }
            .close {
                float: right;
                font-size: 28px;
                font-weight: bold;
                cursor: pointer;
            }
            .close:hover {
                color: #000;
            }
        `;
        document.head.appendChild(styles);
    }

    document.body.appendChild(modal);
    return modal;
}

function displayPaymentHistoryModal(data) {
    if (!data || !data.payments) {
        console.error('Invalid payment data received');
        return;
    }

    let modal = document.getElementById('paymentHistoryModal');
    if (!modal) {
        modal = createPaymentHistoryModal();
    }

    const content = modal.querySelector('.modal-content');
    const payments = data.payments;
    const summary = data.summary || {};

    let html = `
        <div class="payment-history-container">
            <div class="modal-header">
                <h4 class="modal-title">Payment History</h4>
                <button type="button" class="close" onclick="closePaymentModal()">&times;</button>
            </div>
            
            <div class="payment-summary">
                <div class="summary-details">
                    <div class="total-section">
                        <h5>Total Paid:</h5>
                        <p class="amount">₱${parseFloat(summary.total_paid || 0).toFixed(2)}</p>
                    </div>
                    <div class="total-section">
                        <h5>Remaining Balance:</h5>
                        <p class="amount">₱${parseFloat(summary.remaining_balance || 0).toFixed(2)}</p>
                    </div>
                    <div class="total-section">
                        <h5>Payment Status:</h5>
                        <p class="status-badge ${summary.remaining_balance <= 0 ? 'fully-paid' : 'partial'}">
                            ${summary.remaining_balance <= 0 ? 'FULLY PAID' : 'PARTIAL PAYMENT'}
                        </p>
                    </div>
                </div>
            </div>

            <div class="payment-history-list">
                <h5>Payment Transactions (${payments.length})</h5>
                ${payments.map(payment => `
                    <div class="payment-record">
                        <div class="payment-header">
                            <div class="payment-date">
                                <i class="fas fa-calendar"></i>
                                ${new Date(payment.payment_date).toLocaleString()}
                            </div>
                            <div class="payment-amount">₱${parseFloat(payment.amount_paid || 0).toFixed(2)}</div>
                        </div>
                        <div class="payment-details">
                            <p><strong>Method:</strong> ${(payment.payment_method || '').toUpperCase()}</p>
                            <p><strong>Verification:</strong> <span class="verification-code">${payment.verification_code || 'N/A'}</span></p>
                            ${payment.qr_receipt_path ? `
                                <div class="qr-section">
                                    <img src="/writable/uploads/${payment.qr_receipt_path}" 
                                         alt="Payment QR" 
                                         class="payment-qr">
                                    <button onclick="downloadQR('${payment.qr_receipt_path}')" 
                                            class="btn btn-sm btn-primary">
                                        <i class="fas fa-download"></i> Download QR
                                    </button>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                `).join('')}
            </div>
        </div>
    `;

    content.innerHTML = html;
    modal.style.display = 'block';
}

// Helper function to safely close the modal
function closePaymentModal() {
    const modal = document.getElementById('paymentHistoryModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// Helper function to download QR code
function downloadQR(qrPath) {
    if (!qrPath) {
        console.error('No QR path provided');
        return;
    }

    const filename = `payment_qr_${Date.now()}.png`;
    fetch(`/writable/uploads/${qrPath}`)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.blob();
        })
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        })
        .catch(error => {
            console.error('Error downloading QR:', error);
            alert('Error downloading QR code');
        });
}

