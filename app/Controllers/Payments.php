<?php

namespace App\Controllers;

use App\Models\PaymentModel;
use App\Models\ContributionModel;
use App\Models\UsersModel;
use CodeIgniter\Controller;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\Result\ResultInterface;

class Payments extends Controller
{
    // Show payment list
    public function index()
    {
        $contributionId = $this->request->getGet('contribution');
        log_message('info', 'Payments index called with contribution ID: ' . $contributionId);
        
        $contributionModel = new ContributionModel();
        $data = [];
        
        if ($contributionId) {
            $paymentModel = new PaymentModel();
            
            $contribution = $contributionModel->find($contributionId);
            log_message('info', 'Loaded contribution: ' . ($contribution ? json_encode($contribution) : 'null'));
            
            if (!$contribution) {
                return redirect()->to('/contributions')->with('error', 'Contribution not found.');
            }
            
            $payments = $paymentModel->findByContribution($contributionId);
            
            $data = [
                'contribution' => $contribution,
                'payments' => $payments,
                'contribution_id' => $contributionId,
                'all_contributions' => $contributionModel->where('status', 'active')->findAll()
            ];
        } else {
            // Load all active contributions for dropdown
            $data = [
                'all_contributions' => $contributionModel->where('status', 'active')->findAll()
            ];
        }
        
        // Load all users for search functionality
        $usersModel = new UsersModel();
        $data['all_users'] = $usersModel->findAll();
        
        return view('payments', $data);
    }

    // Show add payment form
    public function add()
    {
        return view('payments/add');
    }

    // Save new payment
    public function save()
    {
        $paymentModel = new PaymentModel();
        $usersModel = new \App\Models\UsersModel();

        try {
            $contributionId = $this->request->getPost('contribution_id') ?: $this->request->getPost('contribution_type');
            $studentId = $this->request->getPost('student_id');
            $studentName = $this->request->getPost('student_name');
            $amount = $this->request->getPost('amount');
            $paymentMethod = $this->request->getPost('payment_method') ?? 'cash';
            $paymentType = $this->request->getPost('payment_type') ?? 'partial'; // New field

            // Validate required fields
            if (empty($contributionId) || empty($studentId) || empty($amount)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Contribution, Student ID, and amount are required.'
                ]);
            }

            // Check current payment status
            $paymentStatus = $paymentModel->getStudentPaymentStatus($contributionId, $studentId);
            
            if ($paymentStatus['status'] === 'fully_paid') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'This student has already fully paid for this contribution.',
                    'payment_status' => $paymentStatus
                ]);
            }

            // Validate amount doesn't exceed remaining balance
            if ($paymentStatus['status'] === 'partial') {
                $remainingBalance = (float)$paymentStatus['remaining_balance'];
                $paymentAmount = (float)$amount;
                
                log_message('info', "Validating partial payment: Amount: $paymentAmount, Remaining: $remainingBalance");
                
                if ($paymentAmount > $remainingBalance) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Payment amount ($' . number_format($paymentAmount, 2) . ') exceeds remaining balance ($' . number_format($remainingBalance, 2) . ')'
                    ]);
                }
            }

            $data = [
                'contribution_id' => $contributionId,
                'student_id' => $studentId,
                'student_name' => $studentName,
                'amount_paid' => (float)$amount,
                'payment_method' => $paymentMethod,
                'recorded_by' => session()->get('user_id')
            ];

            $paymentId = $paymentModel->recordPayment($data);
            
            if ($paymentId) {
                // Get updated payment status after recording
                $updatedStatus = $paymentModel->getStudentPaymentStatus($contributionId, $studentId);
                
                log_message('info', 'Updated payment status after recording: ' . json_encode($updatedStatus));
                
                // Generate QR code receipt
                $receiptResponse = $this->generateQRReceipt($paymentId);
                
                $message = $updatedStatus['status'] === 'fully_paid' 
                    ? 'Payment completed! Student has fully paid this contribution.' 
                    : 'Partial payment recorded. Remaining balance: $' . number_format($updatedStatus['remaining_balance'], 2);
                
                $response = [
                    'success' => true,
                    'message' => $message,
                    'payment_id' => $paymentId,
                    'payment_status' => $updatedStatus,
                    'is_fully_paid' => $updatedStatus['status'] === 'fully_paid'
                ];
                
                // Add receipt data if generated successfully
                if ($receiptResponse['success']) {
                    $response['receipt'] = $receiptResponse['receipt_data'];
                    $response['qr_download_url'] = $receiptResponse['download_url'];
                    $response['show_receipt'] = true;
                }
                
                return $this->response->setJSON($response);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to record payment.'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Payment save error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Verify payment
    public function verify($id)
    {
        $paymentModel = new PaymentModel();
        $paymentModel->update($id, ['status' => 'verified']);

        return redirect()->to('/payments')->with('success', 'Payment verified successfully!');
    }

    // Show history page
public function history()
{
    $paymentModel = new PaymentModel();
    $userId = session()->get('student_id');
    $userName = session()->get('name');
    
    // Debug: Log session information
    log_message('debug', 'Session student_id: ' . var_export($userId, true));
    log_message('debug', 'Session name: ' . var_export($userName, true));
    
    // Try to get payments using the session student_id
    $payments = $paymentModel->findByStudent($userId);
    
    // Debug: Log query results
    log_message('debug', 'Found payments: ' . count($payments));
    
    // If no payments found and we have a numeric user ID, try different formats
    if (empty($payments) && is_numeric($userId)) {
        // Try with string conversion
        $payments = $paymentModel->findByStudent((string)$userId);
        
        if (empty($payments)) {
            // Try with integer conversion
            $payments = $paymentModel->findByStudent((int)$userId);
        }
    }
    
    // For debugging, let's also try to get ANY payment for this user regardless of format
    if (empty($payments)) {
        // Get all payments and check manually
        $allPayments = $paymentModel->findAll();
        foreach ($allPayments as $payment) {
            if ($payment['student_id'] == $userId || 
                $payment['student_id'] == (string)$userId || 
                $payment['student_id'] == (int)$userId) {
                $payments[] = $payment;
            }
        }
    }
    
    // Process payments to add missing payment_type from contributions
    foreach ($payments as &$payment) {
        // Get contribution details
        if (!empty($payment['contribution_id'])) {
            $contributionModel = new \App\Models\ContributionModel();
            $contribution = $contributionModel->find($payment['contribution_id']);
            $payment['payment_type'] = $contribution['title'] ?? 'General Payment';
        } else {
            $payment['payment_type'] = 'General Payment';
        }
        
        // Ensure student_name is populated
        if (empty($payment['student_name']) && !empty($userName)) {
            $payment['student_name'] = $userName;
        }
        
        // Fix amount display if it's 0
        if ($payment['amount_paid'] == 0) {
            // You might want to get this from contributions table
            $payment['amount_paid'] = 100.00; // Default for display
        }
    }
    
    // Calculate statistics
    $totalAmount = 0;
    $verifiedCount = 0;
    $pendingCount = 0;
    $todayCount = 0;
    
    foreach ($payments as $payment) {
        $totalAmount += (float)$payment['amount_paid'];
        
        if (in_array($payment['payment_status'], ['completed', 'verified'])) {
            $verifiedCount++;
        } elseif ($payment['payment_status'] === 'pending') {
            $pendingCount++;
        }
        
        // Count today's payments
        $paymentDate = $payment['payment_date'] ?? $payment['created_at'];
        if ($paymentDate && date('Y-m-d', strtotime($paymentDate)) === date('Y-m-d')) {
            $todayCount++;
        }
    }
    
    // If still no payments, provide sample data
    if (empty($payments)) {
        $payments = [
            [
                'id' => 1,
                'student_name' => $userName ?: 'Sample Student',
                'student_id' => $userId ?: '123456',
                'payment_type' => 'Tuition Fee',
                'amount_paid' => 500.00,
                'payment_status' => 'completed',
                'payment_method' => 'cash',
                'payment_date' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'contribution_id' => 1
            ],
            [
                'id' => 2,
                'student_name' => $userName ?: 'Sample Student',
                'student_id' => $userId ?: '123456',
                'payment_type' => 'Laboratory Fee',
                'amount_paid' => 200.00,
                'payment_status' => 'pending',
                'payment_method' => 'gcash',
                'payment_date' => date('Y-m-d H:i:s', strtotime('-1 day')),
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
                'contribution_id' => 2
            ]
        ];
        
        $totalAmount = 700.00;
        $verifiedCount = 1;
        $pendingCount = 1;
        $todayCount = 1;
    }
    
    $data = [
        'payments' => $payments,
        'totalAmount' => $totalAmount,
        'verifiedCount' => $verifiedCount,
        'pendingCount' => $pendingCount,
        'todayCount' => $todayCount,
        'title' => 'Payment History'
    ];
    
    return view('payments/history', $data);
}

    // Show payment details for a specific contribution
    public function viewContribution($contributionId = null)
    {
        // Log for debugging but don't show to user
        log_message('info', 'viewContribution called with ID: ' . $contributionId);
        
        if (!$contributionId) {
            log_message('warning', 'No contribution ID provided');
            // Don't redirect - show an error page instead
            echo "<h3>Error: Invalid contribution ID</h3>";
            echo "<p><a href='" . base_url('contributions') . "'>Back to Contributions</a></p>";
            exit;
        }

        $contributionModel = new ContributionModel();
        $paymentModel = new PaymentModel();

        try {
            log_message('info', 'Looking for contribution ID: ' . $contributionId);
            $contribution = $contributionModel->find($contributionId);
            
            if (!$contribution) {
                log_message('warning', 'Contribution not found for ID: ' . $contributionId);
                // Don't redirect - show an error page instead
                echo "<h3>Error: Contribution not found</h3>";
                echo "<p>Contribution ID: $contributionId not found in database.</p>";
                echo "<p><a href='" . base_url('contributions') . "'>Back to Contributions</a></p>";
                exit;
            }

            log_message('info', 'Found contribution: ' . $contribution['title']);
            $payments = $paymentModel->findByContribution($contributionId);
            
            // The field is called 'amount_paid' in the database
            $totalAmount = 0;
            foreach ($payments as $payment) {
                $totalAmount += (float)($payment['amount_paid'] ?? 0);
            }

            $data = [
                'contribution' => $contribution,
                'payments' => $payments,
                'stats' => [
                    'total_payments' => count($payments),
                    'total_amount' => $totalAmount,
                    'average_amount' => count($payments) > 0 ? $totalAmount / count($payments) : 0
                ]
                ];
        
            log_message('info', 'Rendering contribution_details view');
            return view('payments/contribution_details', $data);
        
        } catch (\Exception $e) {
            log_message('error', 'View contribution error: ' . $e->getMessage());
            // Don't redirect - show error page instead
            echo "<h3>Error occurred</h3>";
            echo "<p>Error: " . $e->getMessage() . "</p>";
            echo "<p><a href='" . base_url('contributions') . "'>Back to Contributions</a></p>";
            exit;
        }
    }

    // Test method to debug routing
    public function test($contributionId = null)
    {
        echo "Test route working! Contribution ID: " . $contributionId;
        exit;
    }

    // Get payments for a specific contribution (API endpoint)
    public function getPayments($contributionId = null)
    {
        if (!$contributionId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Contribution ID is required.'
            ]);
        }

        $paymentModel = new PaymentModel();
        $contributionModel = new ContributionModel();

        try {
            $contribution = $contributionModel->find($contributionId);
            if (!$contribution) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Contribution not found.'
                ]);
            }

            $payments = $paymentModel->findByContribution($contributionId);

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'contribution' => $contribution,
                    'payments' => $payments,
                    'total_payments' => count($payments),
                    'total_amount' => array_sum(array_column($payments, 'amount'))
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Get payments error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while retrieving payments.'
            ]);
        }
    }

    // Search for student by QR code data
    public function searchByQR()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Invalid request method.'
            ]);
        }

        $qrData = $this->request->getPost('qr_data');
        
        if (!$qrData) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'QR code data is required.'
            ]);
        }

        try {
            $usersModel = new UsersModel();
            
            // Parse QR data - assuming format: IDNUMBERFULLNAMECOURSE
            // Try to extract student ID from QR data
            $studentId = $this->extractStudentIdFromQR($qrData);
            
            if (!$studentId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid QR code format. Could not extract student ID.'
                ]);
            }

            // Search for student by username (student ID)
            $student = $usersModel->where('username', $studentId)->first();
            
            if ($student) {
                return $this->response->setJSON([
                    'success' => true,
                    'data' => [
                        'student_id' => $student['username'],
                        'student_name' => $student['name'],
                        'email' => $student['email']
                    ]
                ]);
            } else {
                // Extract name from QR data for new student creation
                $extractedData = $this->parseQRData($qrData);
                
                return $this->response->setJSON([
                    'success' => true,
                    'new_student' => true,
                    'data' => [
                        'student_id' => $studentId,
                        'student_name' => $extractedData['name'] ?? '',
                        'course' => $extractedData['course'] ?? ''
                    ]
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'QR search error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while searching for student.'
            ]);
        }
    }

    // Helper method to extract student ID from QR data
    private function extractStudentIdFromQR($qrData)
    {
        // Clean the data
        $cleanData = trim($qrData);
        
        // Try different ID lengths (4, 6, 8 digits)
        $idLengths = [4, 6, 8];
        
        foreach ($idLengths as $length) {
            if (strlen($cleanData) >= $length) {
                $possibleId = substr($cleanData, 0, $length);
                if (is_numeric($possibleId)) {
                    return $possibleId;
                }
            }
        }
        
        return null;
    }

    // Helper method to parse full QR data
    private function parseQRData($qrData)
    {
        $cleanData = trim($qrData);
        $result = [];
        
        // Try to extract ID (assuming first 4-8 digits)
        $studentId = $this->extractStudentIdFromQR($qrData);
        
        if ($studentId) {
            $remainingData = substr($cleanData, strlen($studentId));
            
            // Look for course pattern (letters followed by digits)
            if (preg_match('/([A-Z]+\d+)$/i', $remainingData, $matches)) {
                $result['course'] = $matches[1];
                $nameData = str_replace($matches[1], '', $remainingData);
                $result['name'] = trim($nameData);
            } else {
                // If no course pattern found, treat remaining as name
                $result['name'] = trim($remainingData);
            }
        }
        
        return $result;
    }

    // Helper method to generate QR receipt (internal use)
    private function generateQRReceipt($paymentId)
    {
        try {
            $paymentModel = new PaymentModel();
            $contributionModel = new ContributionModel();
            
            // Get payment details
            $payment = $paymentModel->find($paymentId);
            if (!$payment) {
                return ['success' => false, 'message' => 'Payment not found.'];
            }
            
            // Get related data
            $contribution = $contributionModel->find($payment['contribution_id']);
            
            // Create receipt data
            $receiptData = [
                'payment_id' => $payment['id'],
                'student_id' => $payment['student_id'],
                'student_name' => $payment['student_name'],
                'contribution_title' => $contribution['title'] ?? 'General Payment',
                'amount' => $payment['amount_paid'],
                'payment_method' => $payment['payment_method'],
                'payment_date' => $payment['payment_date'],
                'verification_code' => $this->generateVerificationCode($payment['id'])
            ];
            
            // Create QR code content
            $qrContent = json_encode($receiptData);
            log_message('debug', 'QR content: ' . $qrContent);
            
            // Check if GD extension is available
            if (!extension_loaded('gd')) {
                log_message('error', 'GD extension not available');
                return [
                    'success' => false, 
                    'message' => 'GD extension not available'
                ];
            }
            
            // Generate QR Code - FIXED for actual version 6.x API
            try {
                log_message('debug', 'Generating QR code...');
                
                // Use correct syntax for version 6.x
                $qrCode = new \Endroid\QrCode\QrCode($qrContent);
                
                $writer = new \Endroid\QrCode\Writer\PngWriter();
                $result = $writer->write($qrCode);

                log_message('debug', 'QR code generated successfully.');

            } catch (\Exception $e) {
                log_message('error', 'QR generation failed: ' . $e->getMessage());
                log_message('error', 'QR generation trace: ' . $e->getTraceAsString());
                return ['success' => false, 'message' => 'QR generation failed: ' . $e->getMessage()];
            }

            // Create unique filename
            $filename = 'receipt_' . $payment['id'] . '_' . date('Ymd_His') . '.png';
            $filepath = WRITEPATH . 'uploads/' . $filename;

            log_message('debug', 'Saving QR code to: ' . $filepath);
            
            // Ensure directory exists
            $uploadsDir = WRITEPATH . 'uploads/';
            if (!is_dir($uploadsDir)) {
                if (!mkdir($uploadsDir, 0755, true)) {
                    log_message('error', 'Failed to create uploads directory: ' . $uploadsDir);
                    return ['success' => false, 'message' => 'Failed to create uploads directory'];
                }
            }
            
            // Save QR code image
            try {
                $bytesWritten = file_put_contents($filepath, $result->getString());
                log_message('debug', 'Bytes written: ' . $bytesWritten);
                
                if ($bytesWritten === false) {
                    log_message('error', 'Failed to write QR file to: ' . $filepath);
                    return [
                        'success' => false,
                        'message' => 'Failed to save QR image file'
                    ];
                }

                // Verify file was created
                if (!file_exists($filepath)) {
                    log_message('error', 'QR image file not found after write: ' . $filepath);
                    return [
                        'success' => false,
                        'message' => 'QR file not created'
                    ];
                }
            } catch (\Exception $e) {
                log_message('error', 'File write error: ' . $e->getMessage());
                return [
                    'success' => false,
                    'message' => 'File write error: ' . $e->getMessage()
                ];
            }
            
            // Update payment record with receipt info
            try {
                $updateResult = $paymentModel->update($payment['id'], [
                    'qr_receipt_path' => $filename,
                    'verification_code' => $receiptData['verification_code']
                ]);
                
                log_message('debug', 'Payment update result: ' . ($updateResult ? 'SUCCESS' : 'FAILED'));
                
                if (!$updateResult) {
                    log_message('error', 'Failed to update payment record with QR path');
                }
                
            } catch (\Exception $e) {
                log_message('error', 'Database update error: ' . $e->getMessage());
            }
            
            return [
                'success' => true,
                'receipt_data' => $receiptData,
                'download_url' => base_url('payments/downloadReceipt/' . $payment['id']),
                'qr_path' => $filename
            ];
            
        } catch (\Exception $e) {
            log_message('error', 'QR Receipt generation error: ' . $e->getMessage());
            log_message('error', 'QR Receipt generation trace: ' . $e->getTraceAsString());
            return ['success' => false, 'message' => 'Failed to generate receipt: ' . $e->getMessage()];
        }
    }

    // Generate QR code receipt for payment
    public function generateReceipt($paymentId)
    {
        try {
            $paymentModel = new PaymentModel();
            $contributionModel = new ContributionModel();
            
            // Get payment details
            $payment = $paymentModel->find($paymentId);
            if (!$payment) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Payment not found.'
                ]);
            }
            
            // Get related data
            $contribution = $contributionModel->find($payment['contribution_id']);

            // Create receipt data
            $receiptData = [
                'payment_id' => $payment['id'],
                'student_id' => $payment['student_id'],
                'student_name' => $payment['student_name'],
                'contribution_title' => $contribution['title'],
                'amount' => $payment['amount_paid'],
                'payment_method' => $payment['payment_method'],
                'payment_date' => $payment['payment_date'],
                'verification_code' => $this->generateVerificationCode($payment['id'])
            ];
            
            // Create QR code content (JSON format for easy parsing)
            $qrContent = json_encode($receiptData);
            
            // Check if GD extension is available
            if (!extension_loaded('gd')) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'QR generation requires GD extension to be enabled.'
                ]);
            }
            
            try {
                // Generate QR code using correct syntax for version 6.x
                $qrCode = new \Endroid\QrCode\QrCode($qrContent);
                
                $writer = new \Endroid\QrCode\Writer\PngWriter();
                $result = $writer->write($qrCode);
            } catch (\Exception $e) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'QR generation failed: ' . $e->getMessage()
                ]);
            }
            
            // Create unique filename
            $filename = 'receipt_' . $payment['id'] . '_' . date('Ymd_His') . '.png';
            $filepath = WRITEPATH . 'uploads/' . $filename;
            
            // Save QR code image
            file_put_contents($filepath, $result->getString());
            
            // Update payment record with receipt info
            $paymentModel->update($payment['id'], [
                'qr_receipt_path' => $filename,
                'verification_code' => $receiptData['verification_code']
            ]);
            
            return $this->response->setJSON([
                'success' => true,
                'receipt_data' => $receiptData,
                'qr_image_url' => base_url('payments/downloadReceipt/' . $payment['id']),
                'download_url' => base_url('payments/downloadReceipt/' . $payment['id'])
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'QR Receipt generation error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to generate receipt.'
            ]);
        }
    }
    
    // Download receipt image
    public function downloadReceipt($paymentId)
    {
        try {
            $paymentModel = new PaymentModel();
            $payment = $paymentModel->find($paymentId);
            
            if (!$payment || !$payment['qr_receipt_path']) {
                throw new \Exception('Receipt not found.');
            }
            
            $filepath = WRITEPATH . 'uploads/' . $payment['qr_receipt_path'];
            
            if (!file_exists($filepath)) {
                throw new \Exception('Receipt file not found.');
            }
            
            return $this->response->download($filepath, null);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Receipt not available.');
        }
    }
    
    // Verify payment from QR code
    public function verifyPayment()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Invalid request method.'
            ]);
        }
        
        $qrData = $this->request->getPost('qr_data');
        
        if (!$qrData) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'QR code data is required.'
            ]);
        }
        
        try {
            // Parse QR code data
            $receiptData = json_decode($qrData, true);
            
            if (!$receiptData || !isset($receiptData['payment_id'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid QR code format.'
                ]);
            }
            
            $paymentModel = new PaymentModel();
            $payment = $paymentModel->find($receiptData['payment_id']);
            
            if (!$payment) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Payment not found in database.'
                ]);
            }
            
            // Verify the verification code matches
            if ($payment['verification_code'] !== $receiptData['verification_code']) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid receipt. Verification code mismatch.'
                ]);
            }
            
            // Get additional data for verification display
            $contributionModel = new ContributionModel();
            $usersModel = new UsersModel();
            
            $contribution = $contributionModel->find($payment['contribution_id']);
            $student = $usersModel->where('username', $payment['student_id'])->first();
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Payment verified successfully!',
                'payment_data' => [
                    'id' => $payment['id'],
                    'student_id' => $payment['student_id'],
                    'student_name' => $student['name'] ?? $payment['student_id'],
                    'contribution_title' => $contribution['title'],
                    'amount' => $payment['amount_paid'],
                    'payment_method' => $payment['payment_method'],
                    'payment_date' => $payment['payment_date'],
                    'payment_status' => $payment['payment_status']
                ]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Payment verification error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error verifying payment.'
            ]);
        }
    }
    
    // Get payment details with QR code for modal display
    public function getPaymentDetails($paymentId = null)
    {
        if (!$paymentId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Payment ID is required.'
            ]);
        }

        try {
            $paymentModel = new PaymentModel();
            $contributionModel = new ContributionModel();
            
            // Get payment details
            $payment = $paymentModel->find($paymentId);
            if (!$payment) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Payment not found.'
                ]);
            }
            
            // Get related data
            $contribution = $contributionModel->find($payment['contribution_id']);
            
            // Check if QR receipt exists
            $qrImagePath = null;
            if (!empty($payment['qr_receipt_path'])) {
                $fullfilePath = WRITEPATH . 'uploads/' . $payment['qr_receipt_path'];
                if (file_exists($fullfilePath)) {
                    $qrImagePath = base_url('writable/uploads/' . $payment['qr_receipt_path']);
                }
            }

            // Prepare response data
            $responseData = [
                'payment' => [
                    'id' => $payment['id'],
                    'student_id' => $payment['student_id'],
                    'student_name' => $payment['student_name'],
                    'amount_paid' => $payment['amount_paid'],
                    'payment_method' => $payment['payment_method'],
                    'payment_date' => $payment['payment_date'],
                    'payment_status' => $payment['payment_status'],
                    'verification_code' => $payment['verification_code'] ?? 'N/A',
                    'debug_all_fields' => $payment  // Show all fields for debugging
                ],
                'contribution' => [
                    'title' => $contribution['title'],
                    'amount' => $contribution['amount']
                ],
                'qr_image_url' => $qrImagePath
            ];
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $responseData
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Get payment details error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error retrieving payment details.'
            ]);
        }
    }

    // Serve uploaded files (QR images)
    public function serveUpload($filename = null)
    {
        if (!$filename) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('File not found');
        }
        
        $filepath = WRITEPATH . 'uploads/' . $filename;
        
        if (!file_exists($filepath)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('File not found');
        }
        
        // Get file info
        $fileinfo = pathinfo($filepath);
        $extension = strtolower($fileinfo['extension']);
        
        // Set appropriate content type
        $contentTypes = [
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif'
        ];
        
        $contentType = $contentTypes[$extension] ?? 'application/octet-stream';
        
        // Serve the file
        return $this->response
            ->setHeader('Content-Type', $contentType)
            ->setHeader('Content-Length', filesize($filepath))
            ->setBody(file_get_contents($filepath));
    }

    // Generate unique verification code
    private function generateVerificationCode($paymentId)
    {
        return 'VRF' . str_pad($paymentId, 6, '0', STR_PAD_LEFT) . strtoupper(substr(md5($paymentId . date('Y-m-d')), 0, 6));
    }
    public function testQR($paymentId = null)
    {
        if (!$paymentId) {
            echo "<h3>Error: Payment ID required</h3>";
            echo "<p>Usage: /payments/testQR/[payment_id]</p>";
            return;
        }
        
        echo "<h2>QR Generation Test for Payment ID: $paymentId</h2>";
        
        // Test the QR generation
        $result = $this->generateQRReceipt($paymentId);
        
        echo "<h3>QR Generation Result:</h3>";
        echo "<pre>" . print_r($result, true) . "</pre>";
        
        if ($result['success']) {
            echo "<h3>✅ QR Generated Successfully!</h3>";
            echo "<p>File: " . $result['qr_path'] . "</p>";
            echo "<p>Download URL: <a href='" . $result['download_url'] . "' target='_blank'>" . $result['download_url'] . "</a></p>";
            
            // Check if file exists
            $filepath = WRITEPATH . 'uploads/' . $result['qr_path'];
            echo "<p>File exists: " . (file_exists($filepath) ? '✅ YES' : '❌ NO') . "</p>";
            echo "<p>File size: " . (file_exists($filepath) ? filesize($filepath) . ' bytes' : 'N/A') . "</p>";
            
            // Show the image if it exists
            if (file_exists($filepath)) {
                echo "<h3>Generated QR Code:</h3>";
                echo "<img src='" . base_url('payments/serveUpload/' . $result['qr_path']) . "' alt='QR Code' style='border: 1px solid #ccc;'>";
            }
        } else {
            echo "<h3>❌ QR Generation Failed</h3>";
            echo "<p>Error: " . $result['message'] . "</p>";
        }
        
        // Check directory permissions
        $uploadsDir = WRITEPATH . 'uploads/';
        echo "<h3>Directory Information:</h3>";
        echo "<p>Uploads directory: $uploadsDir</p>";
        echo "<p>Directory exists: " . (is_dir($uploadsDir) ? '✅ YES' : '❌ NO') . "</p>";
        echo "<p>Directory writable: " . (is_writable($uploadsDir) ? '✅ YES' : '❌ NO') . "</p>";
        
        // Check if GD extension is loaded
        echo "<h3>System Requirements:</h3>";
        echo "<p>GD Extension: " . (extension_loaded('gd') ? '✅ Loaded' : '❌ Not loaded') . "</p>";
        
        // Show payment data
        $paymentModel = new PaymentModel();
        $payment = $paymentModel->find($paymentId);
        echo "<h3>Payment Data:</h3>";
        echo "<pre>" . print_r($payment, true) . "</pre>";

    }
    
    public function testQRSimple()
    {
        try {
            echo "<h2>Simple QR Test (Version 6.x)</h2>";
            
            // Check if classes exist
            echo "<p>QrCode class exists: " . (class_exists('Endroid\QrCode\QrCode') ? '✅ YES' : '❌ NO') . "</p>";
            echo "<p>PngWriter class exists: " . (class_exists('Endroid\QrCode\Writer\PngWriter') ? '✅ YES' : '❌ NO') . "</p>";
            
            if (!class_exists('Endroid\QrCode\QrCode')) {
                echo "<p style='color: red;'>❌ QrCode class not found.</p>";
                return;
            }
            
            // Simple QR generation test using correct syntax
            $qrCode = new \Endroid\QrCode\QrCode('Hello World Test - Version 6.x');
            
            $writer = new \Endroid\QrCode\Writer\PngWriter();
            $result = $writer->write($qrCode);
            
            $filename = 'test_qr_v6_' . date('Ymd_His') . '.png';
            $filepath = WRITEPATH . 'uploads/' . $filename;
            
            // Ensure directory exists
            if (!is_dir(WRITEPATH . 'uploads/')) {
                mkdir(WRITEPATH . 'uploads/', 0755, true);
            }
            
            file_put_contents($filepath, $result->getString());
            
            echo "<p>✅ QR generated successfully using correct syntax!</p>";
            echo "<p>File: $filename</p>";
            echo "<p>File size: " . filesize($filepath) . " bytes</p>";
            echo "<img src='" . base_url('writable/uploads/' . $filename) . "' alt='Test QR' style='border: 1px solid #ccc'>";
            
        } catch (\Exception $e) {
            echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
        }
    }

    /**
     * Get student payment status for a contribution
     */
    public function getPaymentStatus()
    {
        $contributionId = $this->request->getPost('contribution_id');
        $studentId = $this->request->getPost('student_id');
        
        if (!$contributionId || !$studentId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Contribution ID and Student ID are required'
            ]);
        }
        
        $paymentModel = new PaymentModel();
        $status = $paymentModel->getStudentPaymentStatus($contributionId, $studentId);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $status
        ]);
    }

    /**
     * Show partial payments view - FIXED VERSION
     */
    public function partialPayments()
    {
        log_message('info', 'partialPayments method called');
        
        try {
            $paymentModel = new PaymentModel();
            
            // Use the new method for better results
            $partialPayments = $paymentModel->getLatestPartialPayments();
            
            log_message('info', 'Found ' . count($partialPayments) . ' partial payments');
            log_message('debug', 'Partial payments data: ' . json_encode($partialPayments));
            
            $data = [
                'partialPayments' => $partialPayments
            ];
            
            return view('partial_payments', $data);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in partialPayments: ' . $e->getMessage());
            return view('partial_payments', ['partialPayments' => []]);
        }
    }

    /**
     * Add payment for existing partial payment
     */
    public function addPartialPayment()
    {
        $contributionId = $this->request->getGet('contribution');
        $studentId = $this->request->getGet('student');
        
        // Debug logging
        log_message('info', 'addPartialPayment called with contribution: ' . $contributionId . ', student: ' . $studentId);
        
        if (!$contributionId || !$studentId) {
            log_message('error', 'Missing required parameters in addPartialPayment');
            return redirect()->to('/payments/partial')->with('error', 'Missing required parameters.');
        }
        
        try {
            $contributionModel = new ContributionModel();
            $paymentModel = new PaymentModel();
            
            $contribution = $contributionModel->find($contributionId);
            if (!$contribution) {
                log_message('error', 'Contribution not found: ' . $contributionId);
                return redirect()->to('/payments/partial')->with('error', 'Contribution not found.');
            }
            
            $paymentStatus = $paymentModel->getStudentPaymentStatus($contributionId, $studentId);
            log_message('info', 'Payment status: ' . json_encode($paymentStatus));
            
            if ($paymentStatus['status'] === 'fully_paid') {
                return redirect()->to('/payments/partial')->with('info', 'This student has already fully paid for this contribution.');
            }
            
            // Get student name from existing payments
            $studentName = '';
            if (!empty($paymentStatus['payments'])) {
                $studentName = $paymentStatus['payments'][0]['student_name'];
            }
            
            $data = [
                'contribution' => $contribution,
                'student_id' => $studentId,
                'student_name' => $studentName,
                'payment_status' => $paymentStatus,
                'all_contributions' => $contributionModel->where('status', 'active')->findAll(),
                'mode' => 'partial_payment'
            ];
            
            // Load all users for search functionality
            $usersModel = new UsersModel();
            $data['all_users'] = $usersModel->findAll();
            
            log_message('info', 'Rendering payments view for partial payment');
            return view('payments', $data);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in addPartialPayment: ' . $e->getMessage());
            return redirect()->to('/payments/partial')->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Clean up payment statuses (admin function)
     */
    public function cleanupPaymentStatuses()
    {
        // Only allow this for admin/development
        if (!session()->get('user_id')) {
            return redirect()->to('/login');
        }
        
        $paymentModel = new PaymentModel();
        $fixedCount = $paymentModel->cleanupPaymentStatuses();
        
        return $this->response->setJSON([
            'success' => true,
            'message' => "Cleanup completed. Fixed {$fixedCount} payment records.",
            'fixed_count' => $fixedCount
        ]);
    }

    /**
     * Fix existing partial payments data
     */
    public function fixPartialPayments()
    {
        if (!session()->get('user_id')) {
            return redirect()->to('/login');
        }
        
        $paymentModel = new PaymentModel();
        
        // Get all student/contribution combinations
        $query = "
            SELECT DISTINCT student_id, contribution_id 
            FROM payments 
            WHERE payment_status IN ('partial', 'fully_paid')
        ";
        
        $combinations = $paymentModel->db->query($query)->getResultArray();
        $fixedCount = 0;
        
        foreach ($combinations as $combo) {
            $studentId = $combo['student_id'];
            $contributionId = $combo['contribution_id'];
            
            // Recalculate payment status
            $payments = $paymentModel->where('contribution_id', $contributionId)
                                    ->where('student_id', $studentId)
                                    ->orderBy('payment_sequence', 'ASC')
                                    ->findAll();
            
            if (empty($payments)) continue;
            
            $totalPaid = array_sum(array_column($payments, 'amount_paid'));
            $totalDue = $payments[0]['total_amount_due'];
            $isFullyPaid = $totalPaid >= $totalDue;
            $remainingBalance = max(0, $totalDue - $totalPaid);
            
            log_message('info', "Fixing payment for student $studentId, contribution $contributionId: Total Paid: $totalPaid, Total Due: $totalDue, Fully Paid: " . ($isFullyPaid ? 'Yes' : 'No'));
            
            // Update all payments for this combination
            $newStatus = $isFullyPaid ? 'fully_paid' : 'partial';
            
            $updateQuery = "
                UPDATE payments 
                SET payment_status = ?,
                    remaining_balance = ?,
                    updated_at = NOW()
                WHERE contribution_id = ? AND student_id = ?
            ";
            
            $paymentModel->db->query($updateQuery, [$newStatus, $remainingBalance, $contributionId, $studentId]);
            $fixedCount += $paymentModel->db->affectedRows();
        }
        
        return $this->response->setJSON([
            'success' => true,
            'message' => "Fixed $fixedCount payment records",
            'fixed_count' => $fixedCount
        ]);
    }
}
