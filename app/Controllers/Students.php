<?php

namespace App\Controllers;

use App\Models\PaymentModel;

class Students extends BaseController
{
    protected $paymentModel;

    public function __construct()
    {
        $this->paymentModel = new PaymentModel();
    }

    /**
     * Students dashboard
     */
    public function index()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/');
        }

        // Add profile picture for sidebar and header
        $session = session();
        $userId = $session->get('user_id');
        $usersModel = new \App\Models\UsersModel();
        $user = $usersModel->find($userId);
        
        $profilePictureUrl = '';
        if (!empty($user['profile_picture'])) {
            $filename = basename($user['profile_picture']);
            $profilePictureUrl = base_url('test-profile-picture/' . $filename);
        }

        $data = [
            'title' => 'Students Management',
            'students' => $this->getStudentsData(),
            'profilePictureUrl' => $profilePictureUrl,
            'name' => $session->get('name'),
            'email' => $session->get('email')
        ];

        return view('students/index', $data);
    }

    /**
     * Get students data from payments
     */
    private function getStudentsData()
    {
        $db = \Config\Database::connect();
        
        // Get unique students with their payment summary
        $query = "
            SELECT 
                student_id,
                student_name,
                COUNT(*) as total_payments,
                SUM(amount_paid) as total_paid,
                MAX(created_at) as last_payment,
                COUNT(DISTINCT contribution_id) as contributions_count
            FROM payments 
            WHERE payment_status IN ('completed', 'fully_paid', 'partial')
            GROUP BY student_id, student_name
            ORDER BY total_paid DESC
        ";
        
        return $db->query($query)->getResultArray();
    }

    /**
     * Get student details
     */
    public function details($studentId)
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/');
        }

        $student = $this->getStudentDetails($studentId);
        
        if (!$student) {
            return redirect()->back()->with('error', 'Student not found');
        }

        $payments = $this->getStudentPayments($studentId);

        $data = [
            'title' => 'Student Details - ' . $student['student_name'],
            'student' => $student,
            'payments' => $payments
        ];

        return view('students/details', $data);
    }

    /**
     * Update student information
     */
    public function update()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized access']);
        }

        // Get JSON input
        $json = $this->request->getJSON(true);
        
        if (!$json) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request data']);
        }

        $studentId = $json['student_id'] ?? '';
        $studentName = trim($json['student_name'] ?? '');
        $studentEmail = trim($json['student_email'] ?? '') ?: null;
        $studentPhone = trim($json['student_phone'] ?? '') ?: null;
        $studentNotes = trim($json['student_notes'] ?? '') ?: null;

        // Validate required fields
        if (empty($studentId) || empty($studentName)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Student ID and name are required']);
        }

        try {
            $db = \Config\Database::connect();
            
            // Start transaction
            $db->transStart();
            
            // Update all payment records for this student
            $updateQuery = "
                UPDATE payments 
                SET student_name = ?
                WHERE student_id = ?
            ";
            
            $db->query($updateQuery, [$studentName, $studentId]);
            
            // Check if student metadata table exists, if not create it
            $this->ensureStudentMetadataTable($db);
            
            // Insert or update student metadata
            $metadataQuery = "
                INSERT INTO student_metadata (student_id, student_name, email, phone, notes, updated_at) 
                VALUES (?, ?, ?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE 
                    student_name = VALUES(student_name),
                    email = VALUES(email),
                    phone = VALUES(phone),
                    notes = VALUES(notes),
                    updated_at = NOW()
            ";
            
            $db->query($metadataQuery, [$studentId, $studentName, $studentEmail, $studentPhone, $studentNotes]);
            
            // Complete transaction
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                return $this->response->setJSON(['success' => false, 'message' => 'Failed to update student information']);
            }
            
            // Log the update
            log_message('info', 'Student updated: ' . $studentId . ' by user ' . session()->get('username'));
            
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Student information updated successfully',
                'data' => [
                    'student_id' => $studentId,
                    'student_name' => $studentName,
                    'email' => $studentEmail,
                    'phone' => $studentPhone,
                    'notes' => $studentNotes
                ]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Student update error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    }

    /**
     * Get student metadata
     */
    public function getStudentMetadata($studentId)
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized access']);
        }

        try {
            $db = \Config\Database::connect();
            
            $query = "
                SELECT student_id, student_name, email, phone, notes, created_at, updated_at
                FROM student_metadata 
                WHERE student_id = ?
            ";
            
            $result = $db->query($query, [$studentId])->getRowArray();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $result
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Get student metadata error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to get student information']);
        }
    }

    /**
     * Ensure student metadata table exists
     */
    private function ensureStudentMetadataTable($db)
    {
        $createTableQuery = "
            CREATE TABLE IF NOT EXISTS student_metadata (
                id INT AUTO_INCREMENT PRIMARY KEY,
                student_id VARCHAR(50) NOT NULL UNIQUE,
                student_name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NULL,
                phone VARCHAR(20) NULL,
                notes TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_student_id (student_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        $db->query($createTableQuery);
    }

    /**
     * Export student payment data to PDF
     */
    public function exportStudentData($studentId)
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/');
        }

        try {
            $student = $this->getStudentDetails($studentId);
            
            if (!$student) {
                return redirect()->back()->with('error', 'Student not found');
            }

            $payments = $this->getStudentPayments($studentId);

            // Generate PDF
            return $this->generateStudentPDF($student, $payments);
            
        } catch (\Exception $e) {
            log_message('error', 'Student export error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

    /**
     * Generate PDF report for student
     */
    private function generateStudentPDF($student, $payments)
    {
        // Load TCPDF library
        require_once ROOTPATH . 'vendor/tecnickcom/tcpdf/tcpdf.php';
        
        // Create new PDF document
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Set document information
        $pdf->SetCreator('ClearPay Student Management System');
        $pdf->SetAuthor('ClearPay Admin');
        $pdf->SetTitle('Student Payment Report - ' . $student['student_name']);
        $pdf->SetSubject('Individual Student Payment History');
        $pdf->SetKeywords('student, payments, report, history');
        
        // Set default header data
        $pdf->SetHeaderData('', 0, 'Student Payment Report', $student['student_name'] . ' - Generated on ' . date('F j, Y g:i A'));
        
        // Set header and footer fonts
        $pdf->setHeaderFont(Array('dejavusans', '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array('dejavusans', '', PDF_FONT_SIZE_DATA));
        
        // Set default monospaced font
        $pdf->SetDefaultMonospacedFont('dejavusansmono');
        
        // Set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        
        // Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        
        // Set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        
        // Add a page
        $pdf->AddPage();
        
        // Set font to DejaVu Sans for better UTF-8 support
        $pdf->SetFont('dejavusans', '', 12);
        
        // Generate PDF content
        $html = $this->generateStudentPDFContent($student, $payments);
        
        // Print text using writeHTMLCell()
        $pdf->writeHTML($html, true, false, true, false, '');
        
        // Set filename
        $filename = 'Student_Report_' . $student['student_id'] . '_' . date('Y-m-d_H-i-s') . '.pdf';
        
        // Close and output PDF document
        $pdfContent = $pdf->Output($filename, 'S');
        
        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setHeader('Cache-Control', 'private, max-age=0, must-revalidate')
            ->setHeader('Pragma', 'public')
            ->setBody($pdfContent);
    }

    /**
     * Generate PDF content in HTML format for TCPDF
     */
    private function generateStudentPDFContent($student, $payments)
    {
        // Calculate additional statistics
        $totalAmount = array_sum(array_column($payments, 'amount_paid'));
        $avgPayment = count($payments) > 0 ? $totalAmount / count($payments) : 0;
        $paymentMethods = array_count_values(array_column($payments, 'payment_method'));
        $contributionTypes = array_count_values(array_filter(array_column($payments, 'contribution_title')));
        
        // Calculate monthly breakdown
        $monthlyData = [];
        foreach ($payments as $payment) {
            $month = date('Y-m', strtotime($payment['created_at']));
            if (!isset($monthlyData[$month])) {
                $monthlyData[$month] = ['count' => 0, 'amount' => 0];
            }
            $monthlyData[$month]['count']++;
            $monthlyData[$month]['amount'] += $payment['amount_paid'];
        }
        ksort($monthlyData);

        // Status breakdown
        $statusBreakdown = array_count_values(array_column($payments, 'payment_status'));

        $html = '
        <style>
            body { 
                font-family: "DejaVu Sans", "Helvetica", sans-serif; 
                font-size: 9px; 
                color: #2d3748; 
                line-height: 1.5; 
                margin: 0; 
                padding: 0;
            }
            h1 { 
                color: #1a365d; 
                font-size: 22px; 
                text-align: center; 
                margin: 0 0 25px 0; 
                padding: 20px 0; 
                border-bottom: 3px solid #3182ce;
                background: linear-gradient(135deg, #ebf8ff 0%, #bee3f8 100%);
            }
            h2 { 
                color: #2b6cb0; 
                font-size: 14px; 
                margin: 25px 0 12px 0; 
                padding: 8px 12px; 
                background: #ebf8ff; 
                border-left: 4px solid #3182ce;
                border-radius: 4px;
            }
            h3 { 
                color: #4a5568; 
                font-size: 11px; 
                margin: 18px 0 8px 0; 
                padding-bottom: 3px;
                border-bottom: 1px solid #e2e8f0;
            }
            table { 
                width: 100%; 
                border-collapse: collapse; 
                margin-bottom: 18px; 
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            }
            th, td { 
                border: 1px solid #e2e8f0; 
                padding: 10px; 
                text-align: left; 
                vertical-align: top; 
            }
            th { 
                background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%); 
                font-weight: bold; 
                color: #2d3748; 
                font-size: 10px;
            }
            .header-section {
                background: linear-gradient(135deg, #bee3f8 0%, #90cdf4 100%);
                padding: 20px;
                margin: 0 0 25px 0;
                border-radius: 8px;
                border: 2px solid #3182ce;
            }
            .info-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 15px;
                margin-bottom: 20px;
            }
            .info-card {
                background: #f7fafc;
                padding: 15px;
                border-radius: 6px;
                border: 1px solid #e2e8f0;
            }
            .stat-large {
                font-size: 24px;
                font-weight: bold;
                color: #2b6cb0;
                text-align: center;
                margin: 10px 0;
            }
            .stat-label {
                font-weight: bold;
                color: #4a5568;
                font-size: 10px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            .stat-value {
                font-size: 12px;
                color: #1a202c;
                font-weight: 600;
            }
            .status-completed, .completed { 
                color: #38a169; 
                background: #f0fff4; 
                padding: 3px 8px; 
                border-radius: 12px; 
                font-weight: bold;
                font-size: 8px;
            }
            .status-pending, .pending { 
                color: #d69e2e; 
                background: #fffbeb; 
                padding: 3px 8px; 
                border-radius: 12px; 
                font-weight: bold;
                font-size: 8px;
            }
            .status-partial, .partial { 
                color: #e53e3e; 
                background: #fed7d7; 
                padding: 3px 8px; 
                border-radius: 12px; 
                font-weight: bold;
                font-size: 8px;
            }
            .center { text-align: center; }
            .right { text-align: right; }
            .highlight { 
                background: linear-gradient(135deg, #fef5e7 0%, #fed7aa 100%); 
                padding: 8px 12px; 
                border-radius: 6px;
                font-weight: bold;
            }
            .amount-large {
                font-size: 16px;
                font-weight: bold;
                color: #2b6cb0;
            }
            .progress-bar {
                background: #e2e8f0;
                height: 8px;
                border-radius: 4px;
                overflow: hidden;
                margin: 5px 0;
            }
            .progress-fill {
                background: linear-gradient(90deg, #48bb78 0%, #38a169 100%);
                height: 100%;
                border-radius: 4px;
            }
            .summary-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 15px;
                margin: 20px 0;
            }
            .summary-card {
                background: #f7fafc;
                padding: 15px;
                border-radius: 8px;
                border: 1px solid #e2e8f0;
                text-align: center;
            }
            .payment-row { background: #fafafa; }
            .payment-row:nth-child(even) { background: #f1f5f9; }
            .amount-cell {
                font-weight: bold;
                font-size: 11px;
            }
            .date-cell {
                font-size: 9px;
                color: #4a5568;
            }
            .method-badge {
                background: #e6fffa;
                color: #319795;
                padding: 3px 8px;
                border-radius: 10px;
                font-size: 8px;
                font-weight: bold;
            }
            .footer-section {
                background: #f7fafc;
                padding: 20px;
                margin: 25px 0 0 0;
                border: 1px solid #e2e8f0;
                border-radius: 8px;
            }
            .watermark {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%) rotate(-45deg);
                font-size: 72px;
                color: rgba(59, 130, 246, 0.05);
                z-index: -1;
                font-weight: bold;
            }
        </style>

        <div class="watermark">CLEARPAY</div>

        <div class="header-section">
            <h1>Student Payment Report</h1>
            <table style="margin: 0; border: none;">
                <tr>
                    <td style="border: none; padding: 5px 15px;"><span class="stat-label">Student Name:</span><br><span class="stat-large">' . htmlspecialchars($student['student_name']) . '</span></td>
                    <td style="border: none; padding: 5px 15px;"><span class="stat-label">Student ID:</span><br><span class="stat-large">' . htmlspecialchars($student['student_id']) . '</span></td>
                    <td style="border: none; padding: 5px 15px;"><span class="stat-label">Report Date:</span><br><span class="stat-value">' . date('F j, Y g:i A') . '</span></td>
                </tr>
            </table>
        </div>

        <div class="summary-grid">
            <div class="summary-card">
                <div class="stat-large">₱' . number_format($student['total_paid'], 2) . '</div>
                <div class="stat-label">Total Paid</div>
                <div class="progress-bar"><div class="progress-fill" style="width: 100%;"></div></div>
            </div>
            <div class="summary-card">
                <div class="stat-large">' . number_format($student['total_payments']) . '</div>
                <div class="stat-label">Total Transactions</div>
                <div class="stat-value">Over ' . $this->calculateDaysBetween($student['first_payment'], $student['last_payment']) . ' days</div>
            </div>
            <div class="summary-card">
                <div class="stat-large">₱' . number_format($avgPayment, 2) . '</div>
                <div class="stat-label">Average Payment</div>
                <div class="stat-value">' . $this->calculatePaymentFrequency($student['total_payments'], $student['first_payment'], $student['last_payment']) . '</div>
            </div>
        </div>

        <h2>📊 Payment Analytics</h2>
        <table>
            <tr>
                <td width="25%"><span class="stat-label">Payment Period:</span></td>
                <td width="25%"><span class="stat-value">' . date('M j, Y', strtotime($student['first_payment'])) . ' to ' . date('M j, Y', strtotime($student['last_payment'])) . '</span></td>
                <td width="25%"><span class="stat-label">Active Period:</span></td>
                <td width="25%"><span class="stat-value">' . $this->calculateDaysBetween($student['first_payment'], $student['last_payment']) . ' days</span></td>
            </tr>
            <tr>
                <td><span class="stat-label">Payment Frequency:</span></td>
                <td><span class="stat-value">' . $this->calculatePaymentFrequency($student['total_payments'], $student['first_payment'], $student['last_payment']) . '</span></td>
                <td><span class="stat-label">Contribution Types:</span></td>
                <td><span class="stat-value">' . $student['contributions_count'] . ' different campaigns</span></td>
            </tr>
        </table>';

        // Payment Status Breakdown
        if (!empty($statusBreakdown)) {
            $html .= '<h2>📈 Payment Status Breakdown</h2>
            <table>
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Count</th>
                        <th>Percentage</th>
                        <th>Total Amount</th>
                    </tr>
                </thead>
                <tbody>';
            
            foreach ($statusBreakdown as $status => $count) {
                $percentage = round(($count / count($payments)) * 100, 1);
                $statusAmount = array_sum(array_column(array_filter($payments, function($p) use ($status) {
                    return $p['payment_status'] === $status;
                }), 'amount_paid'));
                
                $html .= '<tr>
                    <td><span class="status-' . strtolower($status) . '">' . ucfirst($status) . '</span></td>
                    <td class="center">' . $count . '</td>
                    <td class="center">' . $percentage . '%</td>
                    <td class="right amount-cell">₱' . number_format($statusAmount, 2) . '</td>
                </tr>';
            }
            $html .= '</tbody></table>';
        }

        // Payment Methods Analysis
        if (!empty($paymentMethods)) {
            $html .= '<h2>💳 Payment Methods Analysis</h2>
            <table>
                <thead>
                    <tr>
                        <th>Payment Method</th>
                        <th>Usage Count</th>
                        <th>Percentage</th>
                        <th>Total Amount</th>
                        <th>Avg Transaction</th>
                    </tr>
                </thead>
                <tbody>';
            
            foreach ($paymentMethods as $method => $count) {
                $percentage = round(($count / count($payments)) * 100, 1);
                $methodPayments = array_filter($payments, function($p) use ($method) {
                    return $p['payment_method'] === $method;
                });
                $methodAmount = array_sum(array_column($methodPayments, 'amount_paid'));
                $avgTransaction = $count > 0 ? $methodAmount / $count : 0;
                
                $html .= '<tr>
                    <td><span class="method-badge">' . ucfirst(str_replace('_', ' ', $method)) . '</span></td>
                    <td class="center">' . $count . '</td>
                    <td class="center">' . $percentage . '%</td>
                    <td class="right amount-cell">₱' . number_format($methodAmount, 2) . '</td>
                    <td class="right amount-cell">₱' . number_format($avgTransaction, 2) . '</td>
                </tr>';
            }
            $html .= '</tbody></table>';
        }

        // Monthly Payment Trends
        if (!empty($monthlyData)) {
            $html .= '<h2>📅 Monthly Payment Trends</h2>
            <table>
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Transactions</th>
                        <th>Total Amount</th>
                        <th>Average per Transaction</th>
                        <th>Trend</th>
                    </tr>
                </thead>
                <tbody>';
            
            $previousAmount = 0;
            foreach ($monthlyData as $month => $data) {
                $avgPerTransaction = $data['count'] > 0 ? $data['amount'] / $data['count'] : 0;
                $trend = '';
                if ($previousAmount > 0) {
                    $growth = (($data['amount'] - $previousAmount) / $previousAmount) * 100;
                    $trend = $growth >= 0 ? '↗️ +' . number_format($growth, 1) . '%' : '↘️ ' . number_format($growth, 1) . '%';
                }
                
                $html .= '<tr>
                    <td>' . date('F Y', strtotime($month . '-01')) . '</td>
                    <td class="center">' . $data['count'] . '</td>
                    <td class="right amount-cell">₱' . number_format($data['amount'], 2) . '</td>
                    <td class="right amount-cell">₱' . number_format($avgPerTransaction, 2) . '</td>
                    <td class="center">' . $trend . '</td>
                </tr>';
                
                $previousAmount = $data['amount'];
            }
            $html .= '</tbody></table>';
        }

        // Contribution Types Detailed Analysis
        if (!empty($contributionTypes)) {
            $html .= '<h2>🎯 Contribution Campaign Analysis</h2>
            <table>
                <thead>
                    <tr>
                        <th>Campaign Name</th>
                        <th>Payments</th>
                        <th>Total Amount</th>
                        <th>Avg Payment</th>
                        <th>Participation %</th>
                    </tr>
                </thead>
                <tbody>';
            
            foreach ($contributionTypes as $type => $count) {
                $percentage = round(($count / count($payments)) * 100, 1);
                $typePayments = array_filter($payments, function($p) use ($type) {
                    return $p['contribution_title'] === $type;
                });
                $typeAmount = array_sum(array_column($typePayments, 'amount_paid'));
                $avgPayment = $count > 0 ? $typeAmount / $count : 0;
                
                $html .= '<tr>
                    <td>' . htmlspecialchars($type ?: 'General Payment') . '</td>
                    <td class="center">' . $count . '</td>
                    <td class="right amount-cell">₱' . number_format($typeAmount, 2) . '</td>
                    <td class="right amount-cell">₱' . number_format($avgPayment, 2) . '</td>
                    <td class="center">' . $percentage . '%</td>
                </tr>';
            }
            $html .= '</tbody></table>';
        }

        // Detailed Payment History
        $html .= '<h2>📋 Complete Payment History</h2>
        <table>
            <thead>
                <tr>
                    <th width="6%">ID</th>
                    <th width="12%">Date & Time</th>
                    <th width="22%">Contribution Campaign</th>
                    <th width="12%">Amount</th>
                    <th width="12%">Method</th>
                    <th width="10%">Status</th>
                    <th width="26%">Reference & Notes</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($payments as $index => $payment) {
            $statusClass = 'status-' . strtolower($payment['payment_status']);
            $rowClass = $index % 2 == 0 ? 'payment-row' : '';
            
            $html .= '<tr class="' . $rowClass . '">
                <td class="center"><strong>#' . str_pad($payment['id'], 4, '0', STR_PAD_LEFT) . '</strong></td>
                <td class="date-cell">
                    <strong>' . date('M j, Y', strtotime($payment['created_at'])) . '</strong><br>
                    <span style="font-size: 8px; color: #718096;">' . date('g:i A', strtotime($payment['created_at'])) . '</span>
                </td>
                <td>
                    <strong>' . htmlspecialchars($payment['contribution_title'] ?: 'General Payment') . '</strong><br>
                    <span style="font-size: 8px; color: #718096;">' . htmlspecialchars($payment['category'] ?: 'No category') . '</span>
                </td>
                <td class="right amount-cell">
                    <span class="amount-large">₱' . number_format($payment['amount_paid'], 2) . '</span>
                </td>
                <td class="center">
                    <span class="method-badge">' . ucfirst(str_replace('_', ' ', $payment['payment_method'])) . '</span>
                </td>
                <td class="center">
                    <span class="' . $statusClass . '">' . ucfirst($payment['payment_status']) . '</span>
                </td>
                <td style="font-size: 8px;">
                    ' . ($payment['reference_number'] ? '<strong>Ref:</strong> ' . htmlspecialchars($payment['reference_number']) . '<br>' : '') . '
                    ' . ($payment['notes'] ? '<strong>Notes:</strong> ' . htmlspecialchars(substr($payment['notes'], 0, 80)) . (strlen($payment['notes']) > 80 ? '...' : '') : '<em>No notes</em>') . '
                </td>
            </tr>';
        }

        $html .= '</tbody></table>';

        // Enhanced Footer Summary
        $html .= '<div class="footer-section">
            <h2>📊 Report Summary & Insights</h2>
            <table>
                <tr>
                    <td width="25%"><span class="stat-label">Total Transactions:</span></td>
                    <td width="25%"><span class="highlight">' . count($payments) . ' payments</span></td>
                    <td width="25%"><span class="stat-label">Grand Total:</span></td>
                    <td width="25%"><span class="highlight">₱' . number_format($totalAmount, 2) . '</span></td>
                </tr>
                <tr>
                    <td><span class="stat-label">Payment Consistency:</span></td>
                    <td><span class="stat-value">' . $this->getPaymentConsistencyRating($student['total_payments'], $student['first_payment'], $student['last_payment']) . '</span></td>
                    <td><span class="stat-label">Preferred Method:</span></td>
                    <td><span class="stat-value">' . ucfirst(array_keys($paymentMethods, max($paymentMethods))[0]) . '</span></td>
                </tr>
                <tr>
                    <td><span class="stat-label">Report Generated:</span></td>
                    <td><span class="stat-value">' . date('F j, Y \a\t g:i A') . '</span></td>
                    <td><span class="stat-label">Generated By:</span></td>
                    <td><span class="stat-value">' . (session()->get('name') ?? 'System Administrator') . '</span></td>
                </tr>
            </table>
            
            <div style="margin-top: 20px; padding: 15px; background: #e6fffa; border-left: 4px solid #319795; border-radius: 4px;">
                <h3 style="margin: 0 0 10px 0; color: #234e52;">💡 Payment Insights</h3>
                <p style="margin: 0; font-size: 9px; line-height: 1.4;">
                    <strong>Performance Rating:</strong> ' . $this->getStudentPerformanceRating($student, $payments) . '<br>
                    <strong>Recommendation:</strong> ' . $this->getPaymentRecommendation($student, $payments) . '
                </p>
            </div>
        </div>

        <p style="text-align: center; margin-top: 30px; font-size: 7px; color: #a0aec0; border-top: 1px solid #e2e8f0; padding-top: 15px;">
            This report contains confidential student financial information. Handle in accordance with data protection policies.<br>
            <strong>Generated by ClearPay Student Management System</strong> | Report ID: RPT-' . strtoupper(uniqid()) . ' | Page 1 of 1
        </p>';

        return $html;
    }

    /**
     * Get payment consistency rating
     */
    private function getPaymentConsistencyRating($totalPayments, $startDate, $endDate)
    {
        $days = $this->calculateDaysBetween($startDate, $endDate);
        if ($days == 0) return 'Single Payment';
        
        $frequency = $days / max($totalPayments, 1);
        
        if ($frequency <= 7) return 'Excellent (Weekly+)';
        if ($frequency <= 30) return 'Good (Monthly)';
        if ($frequency <= 90) return 'Regular (Quarterly)';
        return 'Occasional';
    }

    /**
     * Get student performance rating
     */
    private function getStudentPerformanceRating($student, $payments)
    {
        $completedPayments = array_filter($payments, function($p) {
            return in_array($p['payment_status'], ['completed', 'fully_paid']);
        });
        
        $completionRate = count($payments) > 0 ? (count($completedPayments) / count($payments)) * 100 : 0;
        $consistencyScore = $this->getConsistencyScore($student, $payments);
        
        $overallScore = ($completionRate * 0.6) + ($consistencyScore * 0.4);
        
        if ($overallScore >= 90) return '⭐⭐⭐⭐⭐ Excellent';
        if ($overallScore >= 75) return '⭐⭐⭐⭐ Very Good';
        if ($overallScore >= 60) return '⭐⭐⭐ Good';
        if ($overallScore >= 40) return '⭐⭐ Fair';
        return '⭐ Needs Improvement';
    }

    /**
     * Get consistency score
     */
    private function getConsistencyScore($student, $payments)
    {
        $days = $this->calculateDaysBetween($student['first_payment'], $student['last_payment']);
        if ($days == 0) return 100;
        
        $frequency = $days / max($student['total_payments'], 1);
        
        if ($frequency <= 7) return 100;
        if ($frequency <= 30) return 80;
        if ($frequency <= 90) return 60;
        return 40;
    }

    /**
     * Get payment recommendation
     */
    private function getPaymentRecommendation($student, $payments)
    {
        $avgPayment = array_sum(array_column($payments, 'amount_paid')) / max(count($payments), 1);
        $frequency = $this->calculateDaysBetween($student['first_payment'], $student['last_payment']) / max($student['total_payments'], 1);
        
        if ($frequency <= 7) {
            return 'Maintain excellent payment consistency. Consider setting up automatic payments for convenience.';
        } elseif ($frequency <= 30) {
            return 'Good payment pattern. Consider increasing frequency for better cash flow management.';
        } else {
            return 'Consider establishing a more regular payment schedule to improve financial tracking.';
        }
    }

    private function getStudentDetails($studentId)
    {
        $db = \Config\Database::connect();
        
        $query = "
            SELECT 
                student_id,
                student_name,
                COUNT(*) as total_payments,
                SUM(amount_paid) as total_paid,
                MAX(created_at) as last_payment,
                MIN(created_at) as first_payment,
                COUNT(DISTINCT contribution_id) as contributions_count
            FROM payments 
            WHERE student_id = ? AND payment_status IN ('completed', 'fully_paid', 'partial')
            GROUP BY student_id, student_name
        ";
        
        return $db->query($query, [$studentId])->getRowArray();
    }

    private function getStudentPayments($studentId)
    {
        $db = \Config\Database::connect();
        
        $query = "
            SELECT 
                p.*,
                c.title as contribution_title,
                c.category
            FROM payments p
            LEFT JOIN contributions c ON p.contribution_id = c.id
            WHERE p.student_id = ?
            ORDER BY p.created_at DESC
        ";
        
        return $db->query($query, [$studentId])->getResultArray();
    }

    /**
     * Calculate days between two dates
     */
    private function calculateDaysBetween($startDate, $endDate)
    {
        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        $diff = $start->diff($end);
        return $diff->days;
    }

    /**
     * Calculate payment frequency description
     */
    private function calculatePaymentFrequency($totalPayments, $startDate, $endDate)
    {
        $days = $this->calculateDaysBetween($startDate, $endDate);
        if ($days == 0) return 'Single day';
        
        $frequency = $days / max($totalPayments, 1);
        
        if ($frequency <= 1) return 'Daily';
        if ($frequency <= 7) return 'Weekly';
        if ($frequency <= 30) return 'Monthly';
        return 'Occasional';
    }
}