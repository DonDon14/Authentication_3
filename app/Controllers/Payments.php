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

            // Validate required fields
            if (empty($contributionId) || empty($studentId) || empty($amount)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Contribution, Student ID, and amount are required.'
                ]);
            }

            // Store student information if provided and doesn't exist
            if (!empty($studentName)) {
                $existingStudent = $usersModel->where('username', $studentId)->first();
                if (!$existingStudent) {
                    $studentData = [
                        'name' => $studentName,
                        'username' => $studentId,
                        'email' => $studentId . '@student.school.edu', // Default email
                        'password' => password_hash('student123', PASSWORD_DEFAULT),
                        'role' => 'student',
                        'is_verified' => 1,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    $usersModel->insert($studentData);
                }
            }

            // Check if student has already paid for this contribution
            if ($paymentModel->hasStudentPaid($contributionId, $studentId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Student has already paid for this contribution.'
                ]);
            }

            $data = [
                'contribution_id' => $contributionId,
                'student_id' => $studentId,
                'amount_paid' => (float)$amount,  // Fixed: use amount_paid instead of amount
                'payment_method' => $paymentMethod,
                'payment_status' => 'completed',
                'payment_date' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $paymentId = $paymentModel->recordPayment($data);
            
            if ($paymentId) {
                // Generate QR code receipt
                $receiptResponse = $this->generateQRReceipt($paymentId);
                
                $response = [
                    'success' => true,
                    'message' => 'Payment recorded successfully!',
                    'payment_id' => $paymentId
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
                'message' => 'An error occurred while recording the payment.'
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
        return view('payments/history');
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
            
            // Load student names for each payment
            $usersModel = new UsersModel();
            foreach ($payments as &$payment) {
                $student = $usersModel->where('username', $payment['student_id'])->first();
                $payment['student_name'] = $student ? $student['name'] : $payment['student_id'];
            }
            
            log_message('info', 'Found ' . count($payments) . ' payments');
            
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
            $usersModel = new UsersModel();
            
            // Get payment details
            $payment = $paymentModel->find($paymentId);
            if (!$payment) {
                return ['success' => false, 'message' => 'Payment not found.'];
            }
            
            // Get related data
            $contribution = $contributionModel->find($payment['contribution_id']);
            $student = $usersModel->where('username', $payment['student_id'])->first();
            
            // Create receipt data
            $receiptData = [
                'payment_id' => $payment['id'],
                'student_id' => $payment['student_id'],
                'student_name' => $student['name'] ?? $payment['student_id'],
                'contribution_title' => $contribution['title'],
                'amount' => $payment['amount_paid'],
                'payment_method' => $payment['payment_method'],
                'payment_date' => $payment['payment_date'],
                'verification_code' => $this->generateVerificationCode($payment['id'])
            ];
            
            // Create QR code content
            $qrContent = json_encode($receiptData);
            
            // Check if GD extension is available
            if (!extension_loaded('gd')) {
                log_message('error', 'GD extension not available, skipping QR generation');
                return [
                    'success' => false, 
                    'message' => 'QR generation requires GD extension. Payment saved successfully without QR receipt.'
                ];
            }
            
            try {
                // Generate QR code using simple approach
                $qrCode = new QrCode($qrContent);
                $writer = new PngWriter();
                $result = $writer->write($qrCode);
            } catch (\Exception $e) {
                log_message('error', 'QR Code generation failed: ' . $e->getMessage());
                return [
                    'success' => false,
                    'message' => 'QR generation failed: ' . $e->getMessage()
                ];
            }
            
            // Create unique filename
            $filename = 'receipt_' . $payment['id'] . '_' . date('Ymd_His') . '.png';
            $filepath = WRITEPATH . 'uploads/' . $filename;
            
            // Ensure directory exists
            if (!is_dir(WRITEPATH . 'uploads/')) {
                mkdir(WRITEPATH . 'uploads/', 0755, true);
            }
            
            // Save QR code image
            file_put_contents($filepath, $result->getString());
            
            // Update payment record with receipt info
            $paymentModel->update($payment['id'], [
                'qr_receipt_path' => $filename,
                'verification_code' => $receiptData['verification_code']
            ]);
            
            return [
                'success' => true,
                'receipt_data' => $receiptData,
                'download_url' => base_url('payments/downloadReceipt/' . $payment['id'])
            ];
            
        } catch (\Exception $e) {
            log_message('error', 'QR Receipt generation error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to generate receipt.'];
        }
    }

    // Generate QR code receipt for payment
    public function generateReceipt($paymentId)
    {
        try {
            $paymentModel = new PaymentModel();
            $contributionModel = new ContributionModel();
            $usersModel = new UsersModel();
            
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
            $student = $usersModel->where('username', $payment['student_id'])->first();
            
            // Create receipt data
            $receiptData = [
                'payment_id' => $payment['id'],
                'student_id' => $payment['student_id'],
                'student_name' => $student['name'] ?? $payment['student_id'],
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
                // Generate QR code using simple approach
                $qrCode = new QrCode($qrContent);
                $writer = new PngWriter();
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
            $usersModel = new UsersModel();
            
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
            $student = $usersModel->where('username', $payment['student_id'])->first();
            
            // Check if QR receipt exists
            $qrImagePath = null;
            if (!empty($payment['qr_receipt_path'])) {
                // Debug: Log the stored path
                log_message('info', 'Stored QR path: ' . $payment['qr_receipt_path']);
                
                // Construct full file path from filename
                $fullFilePath = WRITEPATH . 'uploads/' . $payment['qr_receipt_path'];
                log_message('info', 'Full file path: ' . $fullFilePath);
                
                if (file_exists($fullFilePath)) {
                    // Convert file path to web-accessible URL using our custom route
                    $qrImagePath = base_url('writable/uploads/' . $payment['qr_receipt_path']);
                    log_message('info', 'Generated QR URL: ' . $qrImagePath);
                } else {
                    log_message('info', 'QR file does not exist at: ' . $fullFilePath);
                }
            } else {
                log_message('info', 'No QR receipt path stored for payment: ' . $paymentId);
            }
            
            // Debug: Log payment data to see what's in the database
            log_message('info', 'Payment data: ' . json_encode($payment));
            
            // Prepare response data
            $responseData = [
                'payment' => [
                    'id' => $payment['id'],
                    'student_id' => $payment['student_id'],
                    'student_name' => $student['name'] ?? $payment['student_id'],
                    'amount_paid' => $payment['amount_paid'] ?? $payment['amount'] ?? 0,
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
}
