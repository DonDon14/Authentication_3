<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt</title>
    <!-- Include FontAwesome with print media -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" media="all">
    <!-- Main Styles -->
    <style>
        /* Base styles */
        body {
            margin: 0;
            padding: 20px;
            background: #f4f6f8;
            font-family: Arial, sans-serif;
            color: #333;
        }
        
        /* Receipt Container */
        .receipt-wrapper {
            background: white;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Print styles */
        @page {
            margin: 0.5cm;
            size: auto;
        }

        @media print {
            html, body {
                width: 210mm;
                height: 297mm;
                margin: 0;
                padding: 0;
                background: white !important;
            }

            body * {
                visibility: hidden;
            }

            .receipt-wrapper, .receipt-wrapper * {
                visibility: visible;
            }

            .receipt-wrapper {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 20px;
                margin: 0;
                box-shadow: none;
            }

            .test-controls, 
            .btn, 
            .receipt-actions,
            .receipt-footer,
            .close-btn {
                display: none !important;
            }

            /* Content visibility */
            .payment-receipt,
            .receipt-title,
            .student-info,
            .payment-summary,
            .transaction-details,
            .amount-box {
                display: block !important;
                visibility: visible !important;
                margin-bottom: 20px !important;
                page-break-inside: avoid !important;
            }

            /* Colors and borders */
            .amount-box {
                border: 1px solid #000 !important;
                background-color: white !important;
                padding: 10px !important;
            }

            /* Text colors */
            .receipt-title,
            .student-info h4,
            .amount-box .value,
            .transaction-details h5 {
                color: black !important;
            }

            .receipt-subtitle,
            .student-info p,
            .amount-box .label {
                color: #666 !important;
            }

            /* Icons */
            .fa, .fas, .far, .fab {
                font-family: 'Font Awesome 6 Free' !important;
                color: black !important;
                visibility: visible !important;
            }

            /* Force print backgrounds and colors */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            
            /* QR Code visibility */
            img.qr-code {
                display: block !important;
                visibility: visible !important;
                max-width: 150px !important;
                height: auto !important;
            }
        }
        
        :root {
            --bg-primary: #ffffff;
            --bg-secondary: #f8f9fa;
            --bg-tertiary: #e9ecef;
            --text-primary: #212529;
            --text-secondary: #6c757d;
            --text-tertiary: #adb5bd;
            --primary-color: #007bff;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --border-color: #dee2e6;
            --radius-md: 0.375rem;
            --radius-lg: 0.5rem;
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            margin: 0;
            padding: 2rem;
            background: #f0f2f5;
        }
        body.printing {
            padding: 0;
            background: white;
        }
        .test-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .test-controls {
            margin-bottom: 2rem;
            padding: 1rem;
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-xl);
        }
    </style>
</head>
<body>
    <div class="test-controls">
        <h2>Receipt Test Page</h2>
        <p>This page demonstrates the payment receipt partial view with test data.</p>
    </div>

    <div class="receipt-wrapper">
            <?php
            // Use the payment data passed from the controller
            if (!isset($payment)) {
                $payment = [
                    'student_name' => 'John Doe',
                    'student_id' => '2023001',
                    'payment_date' => date('Y-m-d H:i:s'),
                    'amount_paid' => 1000.00,
                    'remaining' => 500.00,
                    'payment_type' => 'Tuition Fee',
                    'payment_method' => 'Cash',
                    'transaction_id' => 'TXN'.time(),
                    'verification_status' => 'Verified',
                    'qr_code' => 'test_qr_v6_20250929_164135.png', // Use an existing QR code as fallback
                    'receipt_number' => 'RCP'.time(),
                    'notes' => 'Test payment for demonstration',
                    'recorded_by' => 'Admin User',
                    'payment_sequence' => 1
                ];
            }
            
            // Include the payment receipt partial
            echo view('partials/payment_receipt', [
                'payment' => $payment
            ]);
            ?>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Simple print function
            window.PaymentReceipt = {
                printReceipt: function() {
                    window.print();
                },
                init: function(config) {
                    // Store payment data for potential future use
                    this.payment = config.payment;
                }
            };

            // Initialize with the payment data
            PaymentReceipt.init({
                payment: <?= json_encode($payment) ?>
            });
        });
    </script>
</body>
</html>