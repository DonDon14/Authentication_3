<?php
// Simple test file - no CodeIgniter routing
?>
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
            max-height: 300px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="test-container">
        <h1><i class="fas fa-receipt"></i> QR Receipt Modal Test</h1>
        <p>Click the button below to test the QR receipt modal functionality:</p>
        
        <button class="test-btn" onclick="testModal()">
            <i class="fas fa-qrcode"></i> Test QR Receipt Modal
        </button>
        
        <div class="debug-info" id="debugInfo">Ready to test modal...
        </div>
    </div>

    <script>
        function testModal() {
            const debugInfo = document.getElementById('debugInfo');
            debugInfo.textContent = 'Creating test modal...\n';
            
            // Create modal directly
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
                                <strong>Student:</strong> <span>Test Student</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                <strong>Student ID:</strong> <span>TEST001</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                <strong>Contribution:</strong> <span>Uniform</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                <strong>Amount:</strong> <span>$1000.00</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                <strong>Verification Code:</strong> 
                                <span style="font-family: monospace; background: #e9ecef; padding: 2px 5px; border-radius: 3px;">
                                    QR123456
                                </span>
                            </div>
                        </div>
                        
                        <div style="display: flex; gap: 15px; justify-content: center;">
                            <button onclick="alert('Download would work in real app')" style="
                                background: linear-gradient(45deg, #28a745, #20c997);
                                color: white;
                                padding: 12px 20px;
                                border: none;
                                border-radius: 8px;
                                cursor: pointer;
                                display: inline-flex;
                                align-items: center;
                                gap: 8px;
                            ">
                                <i class="fas fa-download"></i> Download QR Receipt
                            </button>
                            <button onclick="closeModal()" style="
                                background: #6c757d;
                                color: white;
                                padding: 12px 20px;
                                border: none;
                                border-radius: 8px;
                                cursor: pointer;
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
                            <strong>Note:</strong> This is how the QR receipt modal should appear after a successful payment.
                        </div>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', modalHTML);
            document.body.style.overflow = 'hidden';
            
            debugInfo.textContent += 'Modal created and displayed!\n';
        }
        
        function closeModal() {
            const modal = document.getElementById('testQrReceiptModal');
            if (modal) {
                modal.remove();
                document.body.style.overflow = '';
                document.getElementById('debugInfo').textContent += 'Modal closed.\n';
            }
        }
    </script>
</body>
</html>