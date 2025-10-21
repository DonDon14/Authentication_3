<!DOCTYPE html>
<html>
<head>
    <title>QR Receipt Modal Test</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            font-family: Arial, sans-serif; 
            padding: 20px; 
            background: #f0f0f0;
        }
        .test-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .test-btn {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px;
            transition: all 0.3s ease;
        }
        .test-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .debug-info {
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-top: 20px;
            white-space: pre-wrap;
            font-family: monospace;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="test-container">
        <h1><i class="fas fa-receipt"></i> QR Receipt Modal Test</h1>
        <p>Click the button below to test the QR receipt modal functionality:</p>
        
        <button class="test-btn" onclick="testQRReceipt()">
            <i class="fas fa-qrcode"></i> Test QR Receipt Modal
        </button>
        
        <button class="test-btn" onclick="testPaymentFlow()">
            <i class="fas fa-credit-card"></i> Test Full Payment Flow
        </button>
        
        <div class="debug-info" id="debugInfo">Debug information will appear here...</div>
    </div>

    <script>
        // Test data
        const testReceiptData = {
            payment_id: 123,
            student_id: "TEST001",
            student_name: "John Doe",
            contribution_title: "Test Contribution",
            amount: "50.00",
            payment_method: "cash",
            payment_date: "2025-10-17 10:30:00",
            verification_code: "QR123456"
        };
        
        const testDownloadUrl = "<?= base_url('payments/downloadReceipt/123') ?>";
        
        function debug(message) {
            const debugInfo = document.getElementById('debugInfo');
            debugInfo.textContent += new Date().toLocaleTimeString() + ': ' + message + '\n';
            console.log(message);
        }
        
        function testQRReceipt() {
            debug('Testing QR Receipt Modal...');
            
            // Check if function exists
            if (typeof showQRReceipt === 'function') {
                debug('showQRReceipt function found, calling...');
                showQRReceipt(testReceiptData, testDownloadUrl);
            } else {
                debug('ERROR: showQRReceipt function not found!');
                debug('Available functions: ' + Object.getOwnPropertyNames(window).filter(name => typeof window[name] === 'function').join(', '));
                
                // Create the modal directly as a fallback
                debug('Creating modal directly...');
                createTestModal();
            }
        }
        
        function testPaymentFlow() {
            debug('Testing complete payment flow...');
            
            // Simulate successful payment response
            const mockResponse = {
                success: true,
                message: "Payment recorded successfully!",
                show_receipt: true,
                receipt: testReceiptData,
                qr_download_url: testDownloadUrl
            };
            
            debug('Mock response: ' + JSON.stringify(mockResponse, null, 2));
            
            if (mockResponse.show_receipt && mockResponse.receipt) {
                debug('Conditions met, showing QR receipt...');
                if (typeof showQRReceipt === 'function') {
                    showQRReceipt(mockResponse.receipt, mockResponse.qr_download_url);
                } else {
                    debug('ERROR: showQRReceipt function not available');
                    createTestModal();
                }
            } else {
                debug('ERROR: show_receipt conditions not met');
            }
        }
        
        function createTestModal() {
            debug('Creating test modal manually...');
            
            const modalHTML = `
                <div id="testQrReceiptModal" style="
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
                    <div style="
                        background: white;
                        border-radius: 15px;
                        padding: 30px;
                        max-width: 500px;
                        width: 90%;
                        text-align: center;
                        box-shadow: 0 15px 35px rgba(0,0,0,0.2);
                    ">
                        <h2 style="color: #667eea; margin-bottom: 10px;">
                            <i class="fas fa-receipt"></i> Payment Receipt
                        </h2>
                        <p style="color: #666; margin-bottom: 20px;">QR Code Generated Successfully</p>
                        
                        <div style="
                            background: #f8f9fa;
                            border-radius: 10px;
                            padding: 20px;
                            margin-bottom: 20px;
                            text-align: left;
                        ">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                <strong>Student:</strong> <span>${testReceiptData.student_name}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                <strong>Student ID:</strong> <span>${testReceiptData.student_id}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                <strong>Contribution:</strong> <span>${testReceiptData.contribution_title}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                <strong>Amount:</strong> <span>$${parseFloat(testReceiptData.amount).toFixed(2)}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                <strong>Verification Code:</strong> 
                                <span style="font-family: monospace; background: #e9ecef; padding: 2px 5px; border-radius: 3px;">
                                    ${testReceiptData.verification_code}
                                </span>
                            </div>
                        </div>
                        
                        <div style="display: flex; gap: 15px; justify-content: center;">
                            <a href="${testDownloadUrl}" style="
                                background: linear-gradient(45deg, #28a745, #20c997);
                                color: white;
                                padding: 12px 20px;
                                border-radius: 8px;
                                text-decoration: none;
                                display: inline-flex;
                                align-items: center;
                                gap: 8px;
                            ">
                                <i class="fas fa-download"></i> Download QR Receipt
                            </a>
                            <button onclick="closeTestModal()" style="
                                background: #6c757d;
                                color: white;
                                padding: 12px 20px;
                                border: none;
                                border-radius: 8px;
                                cursor: pointer;
                                display: inline-flex;
                                align-items: center;
                                gap: 8px;
                            ">
                                <i class="fas fa-times"></i> Close
                            </button>
                        </div>
                        
                        <div style="
                            margin-top: 20px;
                            padding: 15px;
                            background: #e3f2fd;
                            border-radius: 8px;
                            font-size: 0.9em;
                            color: #1565c0;
                        ">
                            <i class="fas fa-info-circle"></i>
                            <strong>Note:</strong> This is a test modal. In the real application, this would show after a successful payment.
                        </div>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', modalHTML);
            document.body.style.overflow = 'hidden';
            
            debug('Test modal created and displayed');
        }
        
        function closeTestModal() {
            const modal = document.getElementById('testQrReceiptModal');
            if (modal) {
                modal.remove();
                document.body.style.overflow = '';
                debug('Test modal closed');
            }
        }
        
        // Load the actual payments.js if available
        window.addEventListener('load', function() {
            debug('Page loaded, checking for payments.js functions...');
            
            if (typeof showQRReceipt === 'function') {
                debug('✓ showQRReceipt function is available');
            } else {
                debug('✗ showQRReceipt function is NOT available');
            }
            
            if (typeof closeQRReceiptModal === 'function') {
                debug('✓ closeQRReceiptModal function is available');
            } else {
                debug('✗ closeQRReceiptModal function is NOT available');
            }
        });
    </script>
    
    <!-- Try to load the actual payments.js -->
    <script src="<?= base_url('js/payments.js') ?>"></script>
</body>
</html>