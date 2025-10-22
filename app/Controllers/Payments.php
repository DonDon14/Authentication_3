<?php

namespace App\Controllers;

use App\Models\PaymentModel;
use App\Models\ContributionModel;
use App\Models\UsersModel;
use CodeIgniter\Controller;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\Result\ResultInterface;
use Endroid\QrCode\Color\Color;

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
                // Log payment creation activity
                try {
                    $activityModel = new \App\Models\ActivityModel();
                    $session = session();
                    $activityModel->logActivity(
                        $session->get('user_id'),
                        \App\Models\ActivityModel::ACTIVITY_PAYMENT_CREATED,
                        'New payment recorded for ' . $studentName . ' - $' . number_format((float)$amount, 2),
                        'payment',
                        $paymentId,
                        [
                            'student_id' => $studentId,
                            'student_name' => $studentName,
                            'contribution_id' => $contributionId,
                            'amount' => (float)$amount,
                            'payment_method' => $paymentMethod
                        ]
                    );
                } catch (\Exception $activityError) {
                    log_message('warning', 'Failed to log payment creation activity: ' . $activityError->getMessage());
                }
                
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
                    // Log QR receipt generation activity
                    try {
                        $activityModel = new \App\Models\ActivityModel();
                        $session = session();
                        $activityModel->logActivity(
                            $session->get('user_id'),
                            \App\Models\ActivityModel::ACTIVITY_QR_GENERATED,
                            'QR receipt generated for payment #' . $paymentId,
                            'payment',
                            $paymentId,
                            ['receipt_generated' => true]
                        );
                    } catch (\Exception $activityError) {
                        log_message('warning', 'Failed to log QR generation activity: ' . $activityError->getMessage());
                    }
                    
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
    
    // Get all payments instead of filtering by session student_id
    // Since this appears to be an admin/staff interface, show all payments
    $payments = $paymentModel->select('
            payments.*, 
            contributions.title as payment_type,
            contributions.category
        ')
        ->join('contributions', 'contributions.id = payments.contribution_id', 'left')
        ->orderBy('payments.created_at', 'DESC')
        ->findAll();
    
    log_message('debug', 'Found total payments: ' . count($payments));
    
    // Process payments to ensure all fields are populated
    foreach ($payments as &$payment) {
        // Ensure payment_type is populated
        if (empty($payment['payment_type'])) {
            $payment['payment_type'] = 'General Payment';
        }
        
        // Ensure proper amount formatting
        $payment['amount_paid'] = (float)($payment['amount_paid'] ?? 0);
        
        // Ensure payment status is set
        if (empty($payment['payment_status'])) {
            $payment['payment_status'] = 'completed';
        }
    }
    
    // Calculate statistics
    $totalAmount = 0;
    $verifiedCount = 0;
    $pendingCount = 0;
    $todayCount = 0;
    
    foreach ($payments as $payment) {
        $totalAmount += (float)$payment['amount_paid'];
        
        if (in_array($payment['payment_status'], ['completed', 'verified', 'fully_paid'])) {
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

/**
 * Test export functionality
 */
public function testExport()
{
    echo "Export test page working!<br>";
    echo "Date: " . date('Y-m-d H:i:s') . "<br>";
    echo "Session user: " . (session()->get('name') ?? 'No session') . "<br>";
    echo "<a href='" . base_url('payments/export') . "'>Try actual export</a>";
    exit;
}

/**
 * Export payments to CSV
 */
public function exportPayments()
{
    // Debug: First check if method is being called
    log_message('info', 'Export payments method called');
    
    try {
        $paymentModel = new PaymentModel();
        
        // Get all payments with joins for export
        $payments = $paymentModel->select('
                payments.id,
                payments.student_id,
                payments.student_name,
                payments.amount_paid,
                payments.payment_method,
                payments.payment_status,
                payments.reference_number,
                payments.payment_date,
                payments.created_at,
                payments.notes,
                contributions.title as payment_type,
                contributions.category,
                contributions.amount as contribution_amount
            ')
            ->join('contributions', 'contributions.id = payments.contribution_id', 'left')
            ->orderBy('payments.created_at', 'DESC')
            ->findAll();

        log_message('info', 'Found ' . count($payments) . ' payments for export');

        // Set headers for CSV download
        $filename = 'payments_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        // Clear any output that might interfere
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        header('Pragma: public');

        // Open output stream
        $output = fopen('php://output', 'w');

        // Add BOM for UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // CSV Header
        $headers = [
            'Payment ID',
            'Student ID',
            'Student Name',
            'Payment Type',
            'Category',
            'Amount Paid',
            'Contribution Amount',
            'Payment Method',
            'Payment Status',
            'Reference Number',
            'Payment Date',
            'Created Date',
            'Notes'
        ];
        fputcsv($output, $headers);

        // CSV Data
        foreach ($payments as $payment) {
            $row = [
                $payment['id'] ?? '',
                $payment['student_id'] ?? '',
                $payment['student_name'] ?? '',
                $payment['payment_type'] ?? 'General Payment',
                $payment['category'] ?? '',
                number_format((float)($payment['amount_paid'] ?? 0), 2),
                number_format((float)($payment['contribution_amount'] ?? 0), 2),
                ucfirst(str_replace('_', ' ', $payment['payment_method'] ?? '')),
                ucfirst($payment['payment_status'] ?? ''),
                $payment['reference_number'] ?? '',
                $payment['payment_date'] ?? '',
                $payment['created_at'] ?? '',
                $payment['notes'] ?? ''
            ];
            fputcsv($output, $row);
        }

        // Add summary at the end
        fputcsv($output, []); // Empty row
        fputcsv($output, ['SUMMARY']);
        fputcsv($output, ['Total Payments', count($payments)]);
        fputcsv($output, ['Total Amount', '₱' . number_format(array_sum(array_column($payments, 'amount_paid')), 2)]);
        fputcsv($output, ['Export Date', date('Y-m-d H:i:s')]);
        fputcsv($output, ['Generated By', session()->get('name') ?? 'Admin']);

        fclose($output);
        exit;
        
    } catch (\Exception $e) {
        log_message('error', 'Export error: ' . $e->getMessage());
        // Return error page or redirect with error
        return redirect()->to('/payments/history')->with('error', 'Export failed: ' . $e->getMessage());
    }
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
                    'average_amount' => count($payments) > 0 ? $totalAmount / count($payments) : 0,
                    'payment_count' => count($payments)
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
    public function getPaymentDetails($paymentId)
    {
        $paymentModel = new PaymentModel();
        $contributionModel = new ContributionModel();

        try {
            // Check if user is logged in
            if (!session()->get('logged_in')) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ]);
            }

            $payment = $paymentModel->find($paymentId);
            if (!$payment) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Payment not found'
                ]);
            }

            $contribution = $contributionModel->find($payment['contribution_id']);
            
            // Add contribution title to payment data for easier access
            if ($contribution) {
                $payment['contribution_title'] = $contribution['title'];
                $payment['contribution_category'] = $contribution['category'];
            }

            return $this->response->setJSON([
                'success' => true,
                'payment' => $payment,
                'contribution' => $contribution
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error getting payment details: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error retrieving payment details'
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

    /**
     * Get student payment history for a contribution
     */
    public function getStudentPaymentHistory($studentId, $contributionId)
    {
        $paymentModel = new PaymentModel();
        
        try {
            $payments = $paymentModel->select('
                payments.*, 
                qr_receipt_path
            ')
            ->where('student_id', $studentId)
            ->where('contribution_id', $contributionId)
            ->orderBy('payment_date', 'DESC')
            ->findAll();

            $totalPaid = array_sum(array_column($payments, 'amount_paid'));
            $contribution = (new ContributionModel())->find($contributionId);
            $remainingBalance = $contribution['amount'] - $totalPaid;

            return $this->response->setJSON([
                'success' => true,
                'payments' => $payments,
                'summary' => [
                    'total_paid' => $totalPaid,
                    'remaining_balance' => $remainingBalance,
                    'payment_count' => count($payments)
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Payment history error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error retrieving payment history'
            ]);
        }
    }

    /**
     * Get student payment history for a specific contribution
     */
    public function getStudentHistory($contributionId, $studentId)
    {
        try {
            // Check if user is logged in
            if (!session()->get('logged_in')) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ]);
            }

            log_message('info', "Getting student history for contribution: {$contributionId}, student: {$studentId}");

            $paymentModel = new PaymentModel();
            $contributionModel = new ContributionModel();
            
            // Get contribution details
            $contribution = $contributionModel->find($contributionId);
            if (!$contribution) {
                log_message('error', "Contribution not found: {$contributionId}");
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Contribution not found'
                ]);
            }

            // Get student payments for this contribution
            $db = \Config\Database::connect();
            $query = "
                SELECT 
                    p.*,
                    c.title as contribution_title,
                    c.category
                FROM payments p
                LEFT JOIN contributions c ON p.contribution_id = c.id
                WHERE p.contribution_id = ? AND p.student_id = ?
                ORDER BY p.created_at DESC
            ";
            
            $payments = $db->query($query, [$contributionId, $studentId])->getResultArray();
            log_message('info', "Found " . count($payments) . " payments for student");

            // Get student basic info from the first payment
            $studentInfo = [
                'id' => $studentId,
                'name' => !empty($payments) ? $payments[0]['student_name'] : 'Unknown Student'
            ];

            return $this->response->setJSON([
                'success' => true,
                'payments' => $payments,
                'student' => $studentInfo,
                'contribution' => $contribution
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Student history error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error retrieving student payment history: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Generate QR code for payment
     */
    public function generateQR($paymentId, $studentId, $contributionId)
    {
        try {
            // Check if user is logged in
            if (!session()->get('logged_in')) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ]);
            }

            // Get payment details for QR data
            $paymentModel = new PaymentModel();
            $payment = $paymentModel->find($paymentId);
            
            if (!$payment) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Payment not found'
                ]);
            }

            // Create QR code data
            $qrData = [
                'type' => 'payment_receipt',
                'payment_id' => $paymentId,
                'student_id' => $studentId,
                'contribution_id' => $contributionId,
                'amount' => $payment['amount_paid'],
                'status' => $payment['payment_status'],
                'timestamp' => time(),
                'verification_url' => base_url("payments/verifyQR/{$paymentId}")
            ];

            // Generate QR code using correct API
            $writer = new PngWriter();
            $qrCode = new QrCode(json_encode($qrData));
            $qrCode->setSize(300);
            $qrCode->setMargin(10);
            $qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0]);
            $qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255]);
            
            $result = $writer->write($qrCode);
            
            // Convert to base64 for easy display
            $qrCodeBase64 = 'data:image/png;base64,' . base64_encode($result->getString());

            return $this->response->setJSON([
                'success' => true,
                'qrCode' => $qrCodeBase64,
                'qrData' => $qrData
            ]);

        } catch (\Exception $e) {
            log_message('error', 'QR generation error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error generating QR code'
            ]);
        }
    }

    /**
     * Verify payment via QR code scan
     */
    public function verifyQR($paymentId)
    {
        try {
            $paymentModel = new PaymentModel();
            $payment = $paymentModel->find($paymentId);
            
            if (!$payment) {
                return view('payments/verify_error', ['message' => 'Payment not found']);
            }

            $contributionModel = new ContributionModel();
            $contribution = $contributionModel->find($payment['contribution_id']);

            $data = [
                'payment' => $payment,
                'contribution' => $contribution,
                'verified' => true
            ];

            return view('payments/verify_success', $data);

        } catch (\Exception $e) {
            log_message('error', 'Payment verification error: ' . $e->getMessage());
            return view('payments/verify_error', ['message' => 'Verification failed']);
        }
    }

    public function getQRCode($studentId, $contributionId) 
    {
        $qrData = json_encode([
            'student_id' => $studentId,
            'contribution_id' => $contributionId,
            'timestamp' => time()
        ]);
        
        $writer = new PngWriter();
        $qrCode = new QrCode($qrData);
        $qrCode->setSize(300);
        $qrCode->setMargin(10);
        $qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0]);
        $qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255]);
        
        $result = $writer->write($qrCode);
        
        return $this->response
            ->setHeader('Content-Type', $result->getMimeType())
            ->setHeader('Content-Disposition', 'inline; filename="payment_qr.png"')
            ->setBody($result->getString());
    }
}
