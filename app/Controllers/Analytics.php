<?php

namespace App\Controllers;

use App\Models\ContributionModel;

class Analytics extends BaseController
{
    protected $contributionModel;
    protected $paymentModel;

    public function __construct()
    {
        $this->contributionModel = new ContributionModel();
        $this->paymentModel = new \App\Models\PaymentModel();
    }

    /**
     * Main analytics dashboard
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
            'title' => 'Analytics Dashboard',
            'overview' => $this->getOverviewStats(),
            'contributions' => $this->getContributionAnalytics(),
            'payments' => $this->getPaymentAnalytics(),
            'trends' => $this->getTrendAnalytics(),
            'charts' => $this->getChartData(),
            'profilePictureUrl' => $profilePictureUrl,
            'name' => $session->get('name'),
            'email' => $session->get('email')
        ];

        return view('analytics/dashboard', $data);
    }

    /**
     * Get overview statistics
     */
    private function getOverviewStats()
    {
        $db = \Config\Database::connect();
        
        // Total revenue (from payments)
        $totalRevenue = $db->table('payments')
                          ->selectSum('amount_paid')
                          ->whereIn('payment_status', ['completed', 'fully_paid'])
                          ->get()
                          ->getRow()
                          ->amount_paid ?? 0;

        // Total contributions
        $totalContributions = $this->contributionModel->countAll();

        // Active contributors (unique students who made payments)
        $activeContributors = $db->table('payments')
                                ->select('student_id')
                                ->whereIn('payment_status', ['completed', 'fully_paid'])
                                ->distinct()
                                ->countAllResults();

        // This month's revenue
        $thisMonthRevenue = $db->table('payments')
                              ->selectSum('amount_paid')
                              ->whereIn('payment_status', ['completed', 'fully_paid'])
                              ->where('MONTH(created_at)', date('m'))
                              ->where('YEAR(created_at)', date('Y'))
                              ->get()
                              ->getRow()
                              ->amount_paid ?? 0;

        // Last month's revenue for comparison
        $lastMonthRevenue = $db->table('payments')
                              ->selectSum('amount_paid')
                              ->whereIn('payment_status', ['completed', 'fully_paid'])
                              ->where('MONTH(created_at)', date('m', strtotime('-1 month')))
                              ->where('YEAR(created_at)', date('Y', strtotime('-1 month')))
                              ->get()
                              ->getRow()
                              ->amount_paid ?? 0;

        // Calculate month-over-month growth
        $monthlyGrowth = $lastMonthRevenue > 0 ? 
                        (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 : 0;

        // Total profit
        $profitData = $this->contributionModel->getProfitAnalytics();
        
        return [
            'total_revenue' => $totalRevenue,
            'total_contributions' => $totalContributions,
            'active_contributors' => $activeContributors,
            'monthly_revenue' => $thisMonthRevenue,
            'monthly_growth' => round($monthlyGrowth, 1),
            'total_profit' => $profitData['total_profit'] ?? 0,
            'avg_profit_margin' => round($profitData['avg_profit_margin'] ?? 0, 1)
        ];
    }

    /**
     * Get contribution analytics
     */
    private function getContributionAnalytics()
    {
        $profitAnalytics = $this->contributionModel->getProfitAnalytics();
        $topProfitable = $this->contributionModel->getTopProfitable(10);
        
        // Category breakdown
        $db = \Config\Database::connect();
        $categoryStats = $db->table('contributions')
                           ->select('category, COUNT(*) as count, SUM(amount) as total_amount, SUM(profit_amount) as total_profit')
                           ->where('status', 'active')
                           ->groupBy('category')
                           ->orderBy('total_amount', 'DESC')
                           ->get()
                           ->getResultArray();

        return [
            'summary' => $profitAnalytics,
            'top_profitable' => $topProfitable,
            'by_category' => $categoryStats
        ];
    }

    /**
     * Get payment analytics
     */
    private function getPaymentAnalytics()
    {
        $db = \Config\Database::connect();
        
        // Payment status breakdown
        $statusStats = $db->table('payments')
                         ->select('payment_status as status, COUNT(*) as count, SUM(amount_paid) as total_amount')
                         ->groupBy('payment_status')
                         ->get()
                         ->getResultArray();

        // Payment method breakdown
        $methodStats = $db->table('payments')
                         ->select('payment_method, COUNT(*) as count, SUM(amount_paid) as total_amount')
                         ->whereIn('payment_status', ['completed', 'fully_paid'])
                         ->groupBy('payment_method')
                         ->get()
                         ->getResultArray();

        // Recent payments
        $recentPayments = $db->table('payments p')
                            ->join('contributions c', 'p.contribution_id = c.id')
                            ->select('p.id, p.student_name, c.title as contribution_title, p.amount_paid as amount, p.payment_method, p.payment_status as status, p.created_at')
                            ->orderBy('p.created_at', 'DESC')
                            ->limit(10)
                            ->get()
                            ->getResultArray();

        // Average transaction value
        $avgTransaction = $db->table('payments')
                            ->selectAvg('amount_paid')
                            ->whereIn('payment_status', ['completed', 'fully_paid'])
                            ->get()
                            ->getRow()
                            ->amount_paid ?? 0;

        return [
            'by_status' => $statusStats,
            'by_method' => $methodStats,
            'recent_payments' => $recentPayments,
            'avg_transaction' => round($avgTransaction, 2)
        ];
    }

    /**
     * Get trend analytics
     */
    private function getTrendAnalytics()
    {
        $db = \Config\Database::connect();
        
        // Daily revenue for last 30 days
        $dailyRevenue = $db->table('payments')
                          ->select('DATE(created_at) as date, SUM(amount_paid) as total')
                          ->whereIn('payment_status', ['completed', 'fully_paid'])
                          ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-30 days')))
                          ->groupBy('DATE(created_at)')
                          ->orderBy('date', 'ASC')
                          ->get()
                          ->getResultArray();

        // Monthly revenue for last 12 months
        $monthlyRevenue = $db->table('payments')
                            ->select('YEAR(created_at) as year, MONTH(created_at) as month, SUM(amount_paid) as total')
                            ->whereIn('payment_status', ['completed', 'fully_paid'])
                            ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-12 months')))
                            ->groupBy('YEAR(created_at), MONTH(created_at)')
                            ->orderBy('year, month', 'ASC')
                            ->get()
                            ->getResultArray();

        // Transaction count trends
        $dailyTransactions = $db->table('payments')
                               ->select('DATE(created_at) as date, COUNT(*) as count')
                               ->whereIn('payment_status', ['completed', 'fully_paid'])
                               ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-30 days')))
                               ->groupBy('DATE(created_at)')
                               ->orderBy('date', 'ASC')
                               ->get()
                               ->getResultArray();

        return [
            'daily_revenue' => $dailyRevenue,
            'monthly_revenue' => $monthlyRevenue,
            'daily_transactions' => $dailyTransactions
        ];
    }

    /**
     * Get chart data for frontend
     */
    private function getChartData()
    {
        $trends = $this->getTrendAnalytics();
        
        // Format data for charts
        $revenueChart = [
            'labels' => array_column($trends['daily_revenue'], 'date'),
            'data' => array_column($trends['daily_revenue'], 'total')
        ];

        $transactionChart = [
            'labels' => array_column($trends['daily_transactions'], 'date'),
            'data' => array_column($trends['daily_transactions'], 'count')
        ];

        $monthlyChart = [
            'labels' => array_map(function($item) {
                return date('M Y', mktime(0, 0, 0, $item['month'], 1, $item['year']));
            }, $trends['monthly_revenue']),
            'data' => array_column($trends['monthly_revenue'], 'total')
        ];

        return [
            'daily_revenue' => $revenueChart,
            'daily_transactions' => $transactionChart,
            'monthly_revenue' => $monthlyChart
        ];
    }

    /**
     * Export analytics data
     */
    public function export($type = 'pdf')
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/');
        }

        $data = [
            'overview' => $this->getOverviewStats(),
            'contributions' => $this->getContributionAnalytics(),
            'payments' => $this->getPaymentAnalytics(),
            'trends' => $this->getTrendAnalytics(),
            'generated_at' => date('Y-m-d H:i:s')
        ];

        switch ($type) {
            case 'pdf':
                return $this->exportPDF($data);
            case 'csv':
                return $this->exportCSV($data);
            case 'excel':
                return $this->exportExcel($data);
            default:
                return redirect()->back()->with('error', 'Invalid export format');
        }
    }

    /**
     * Export to PDF
     */
    private function exportPDF($data)
    {
        // Load TCPDF library
        require_once ROOTPATH . 'vendor/tecnickcom/tcpdf/tcpdf.php';
        
        // Create new PDF document
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Set document information
        $pdf->SetCreator('ClearPay Analytics System');
        $pdf->SetAuthor('ClearPay Admin');
        $pdf->SetTitle('Analytics Report - ' . date('F j, Y'));
        $pdf->SetSubject('Payment System Analytics Report');
        $pdf->SetKeywords('analytics, payments, contributions, profit, report');
        
        // Set default header data
        $pdf->SetHeaderData('', 0, 'ClearPay Analytics Report', 'Generated on ' . date('F j, Y g:i A'));
        
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
        $html = $this->generatePDFContent($data);
        
        // Print text using writeHTMLCell()
        $pdf->writeHTML($html, true, false, true, false, '');
        
        // Set filename
        $filename = 'ClearPay_Analytics_Report_' . date('Y-m-d_H-i-s') . '.pdf';
        
        // Close and output PDF document
        // 'S' means return as string for download
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
    private function generatePDFContent($data)
    {
        $html = '
        <style>
            body { font-family: "DejaVu Sans", "Helvetica", sans-serif; font-size: 10px; color: #333; }
            h1 { color: #3b82f6; font-size: 20px; text-align: center; margin-bottom: 20px; }
            h2 { color: #3b82f6; font-size: 14px; margin-top: 20px; margin-bottom: 10px; border-bottom: 1px solid #e5e7eb; }
            h3 { color: #374151; font-size: 12px; margin-top: 15px; margin-bottom: 8px; }
            table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
            th, td { border: 1px solid #e5e7eb; padding: 6px; text-align: left; }
            th { background-color: #f3f4f6; font-weight: bold; color: #374151; }
            .stats-grid { margin-bottom: 20px; }
            .stat-item { margin-bottom: 8px; }
            .stat-label { font-weight: bold; color: #666; }
            .stat-value { font-size: 12px; color: #333; }
            .profit-high { color: #10b981; font-weight: bold; }
            .profit-medium { color: #f59e0b; font-weight: bold; }
            .profit-low { color: #ef4444; font-weight: bold; }
            .center { text-align: center; }
            .summary-section { background-color: #f9fafb; padding: 10px; margin: 15px 0; border: 1px solid #e5e7eb; }
        </style>

        <h1>ClearPay Analytics Report</h1>
        <p class="center"><strong>Generated:</strong> ' . date('F j, Y \a\t g:i A') . '</p>

        <div class="summary-section">
            <h2>Executive Summary</h2>
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-label">Total Revenue:</span> 
                    <span class="stat-value">PHP ' . number_format($data['overview']['total_revenue'], 2) . '</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Total Profit:</span> 
                    <span class="stat-value">PHP ' . number_format($data['overview']['total_profit'], 2) . '</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Profit Margin:</span> 
                    <span class="stat-value">' . $data['overview']['avg_profit_margin'] . '%</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Active Contributors:</span> 
                    <span class="stat-value">' . number_format($data['overview']['active_contributors']) . '</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Total Contributions:</span> 
                    <span class="stat-value">' . number_format($data['overview']['total_contributions']) . '</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Monthly Revenue:</span> 
                    <span class="stat-value">PHP ' . number_format($data['overview']['monthly_revenue'], 2) . '</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Monthly Growth:</span> 
                    <span class="stat-value">' . $data['overview']['monthly_growth'] . '%</span>
                </div>
            </div>
        </div>

        <h2>Top Profitable Contributions</h2>
        <table>
            <thead>
                <tr>
                    <th>Contribution Title</th>
                    <th>Revenue</th>
                    <th>Profit</th>
                    <th>Margin</th>
                    <th>Category</th>
                </tr>
            </thead>
            <tbody>';
        
        foreach (array_slice($data['contributions']['top_profitable'], 0, 15) as $contribution) {
            $marginClass = $contribution['profit_margin'] >= 30 ? 'profit-high' : 
                          ($contribution['profit_margin'] >= 15 ? 'profit-medium' : 'profit-low');
            
            $html .= '<tr>
                <td>' . htmlspecialchars($contribution['title']) . '</td>
                <td>PHP ' . number_format($contribution['amount'], 2) . '</td>
                <td>PHP ' . number_format($contribution['profit_amount'], 2) . '</td>
                <td class="' . $marginClass . '">' . number_format($contribution['profit_margin'], 1) . '%</td>
                <td>' . htmlspecialchars($contribution['category'] ?? 'General') . '</td>
            </tr>';
        }
        
        $html .= '</tbody></table>

        <h2>Performance by Category</h2>
        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Count</th>
                    <th>Revenue</th>
                    <th>Profit</th>
                    <th>Avg Margin</th>
                    <th>Rating</th>
                </tr>
            </thead>
            <tbody>';
        
        foreach ($data['contributions']['by_category'] as $category) {
            $avgMargin = $category['total_amount'] > 0 ? ($category['total_profit'] / $category['total_amount'] * 100) : 0;
            $rating = $avgMargin >= 30 ? 'Excellent' : ($avgMargin >= 15 ? 'Good' : 'Needs Improvement');
            $marginClass = $avgMargin >= 30 ? 'profit-high' : ($avgMargin >= 15 ? 'profit-medium' : 'profit-low');
            
            $html .= '<tr>
                <td>' . htmlspecialchars(ucfirst($category['category'])) . '</td>
                <td>' . number_format($category['count']) . '</td>
                <td>PHP ' . number_format($category['total_amount'], 2) . '</td>
                <td>PHP ' . number_format($category['total_profit'], 2) . '</td>
                <td class="' . $marginClass . '">' . number_format($avgMargin, 1) . '%</td>
                <td>' . $rating . '</td>
            </tr>';
        }
        
        $html .= '</tbody></table>

        <h2>Recent Payments</h2>
        <table>
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Contribution</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>';
        
        foreach (array_slice($data['payments']['recent_payments'], 0, 20) as $payment) {
            $html .= '<tr>
                <td>' . htmlspecialchars($payment['student_name']) . '</td>
                <td>' . htmlspecialchars($payment['contribution_title']) . '</td>
                <td>PHP ' . number_format($payment['amount'], 2) . '</td>
                <td>' . ucfirst($payment['payment_method'] ?? 'N/A') . '</td>
                <td>' . date('M j, Y', strtotime($payment['created_at'])) . '</td>
            </tr>';
        }
        
        $html .= '</tbody></table>

        <h2>Payment Methods Summary</h2>
        <table>
            <thead>
                <tr>
                    <th>Payment Method</th>
                    <th>Transaction Count</th>
                    <th>Total Amount</th>
                    <th>Average Transaction</th>
                    <th>Percentage of Total</th>
                </tr>
            </thead>
            <tbody>';
        
        $totalTransactions = array_sum(array_column($data['payments']['by_method'], 'count'));
        foreach ($data['payments']['by_method'] as $method) {
            $percentage = $totalTransactions > 0 ? round(($method['count'] / $totalTransactions) * 100, 1) : 0;
            $avgTransaction = $method['count'] > 0 ? $method['total_amount'] / $method['count'] : 0;
            
            $html .= '<tr>
                <td>' . htmlspecialchars(ucfirst($method['payment_method'])) . '</td>
                <td>' . number_format($method['count']) . '</td>
                <td>PHP ' . number_format($method['total_amount'], 2) . '</td>
                <td>PHP ' . number_format($avgTransaction, 2) . '</td>
                <td>' . $percentage . '%</td>
            </tr>';
        }
        
        $html .= '</tbody></table>

        <div class="summary-section">
            <h3>Report Summary</h3>
            <p><strong>Average Transaction Value:</strong> PHP ' . number_format($data['payments']['avg_transaction'], 2) . '</p>
            <p><strong>Total Active Payment Methods:</strong> ' . count($data['payments']['by_method']) . '</p>
            <p><strong>Total Active Categories:</strong> ' . count($data['contributions']['by_category']) . '</p>
            <p><strong>Overall Profit Performance:</strong> ' . ($data['overview']['avg_profit_margin'] >= 25 ? 'Excellent' : ($data['overview']['avg_profit_margin'] >= 15 ? 'Good' : 'Needs Improvement')) . '</p>
        </div>

        <p class="center" style="margin-top: 30px; font-size: 8px; color: #666;">
            This report was generated by ClearPay Analytics System on ' . $data['generated_at'] . '<br>
            Confidential information - Handle with appropriate security measures
        </p>';
        
        return $html;
    }

    /**
     * Export to CSV
     */
    private function exportCSV($data)
    {
        $filename = 'analytics_report_' . date('Y-m-d_H-i-s') . '.csv';
        
        // Set headers for CSV download
        $this->response->setHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $this->response->setHeader('Pragma', 'no-cache');
        $this->response->setHeader('Expires', '0');
        
        // Start output buffering
        ob_start();
        $output = fopen('php://output', 'w');
        
        // Add BOM for proper UTF-8 encoding in Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Overview Statistics
        fputcsv($output, ['OVERVIEW STATISTICS']);
        fputcsv($output, ['Metric', 'Value', 'Notes']);
        fputcsv($output, ['Total Revenue', '₱' . number_format($data['overview']['total_revenue'], 2), 'All completed payments']);
        fputcsv($output, ['Total Profit', '₱' . number_format($data['overview']['total_profit'], 2), 'From contributions with profit tracking']);
        fputcsv($output, ['Average Profit Margin', $data['overview']['avg_profit_margin'] . '%', 'Average across all profitable contributions']);
        fputcsv($output, ['Active Contributors', $data['overview']['active_contributors'], 'Unique students who made payments']);
        fputcsv($output, ['Total Contributions', $data['overview']['total_contributions'], 'Total contribution campaigns']);
        fputcsv($output, ['Monthly Revenue', '₱' . number_format($data['overview']['monthly_revenue'], 2), 'Current month total']);
        fputcsv($output, ['Monthly Growth', $data['overview']['monthly_growth'] . '%', 'Compared to previous month']);
        fputcsv($output, []);
        
        // Top Profitable Contributions
        if (!empty($data['contributions']['top_profitable'])) {
            fputcsv($output, ['TOP PROFITABLE CONTRIBUTIONS']);
            fputcsv($output, ['Contribution Title', 'Revenue', 'Cost Price', 'Profit Amount', 'Profit Margin %']);
            foreach ($data['contributions']['top_profitable'] as $contribution) {
                fputcsv($output, [
                    $contribution['title'],
                    '₱' . number_format($contribution['amount'], 2),
                    '₱' . number_format($contribution['cost_price'] ?? 0, 2),
                    '₱' . number_format($contribution['profit_amount'], 2),
                    number_format($contribution['profit_margin'], 2) . '%'
                ]);
            }
            fputcsv($output, []);
        }
        
        // Performance by Category
        if (!empty($data['contributions']['by_category'])) {
            fputcsv($output, ['PERFORMANCE BY CATEGORY']);
            fputcsv($output, ['Category', 'Count', 'Total Revenue', 'Total Profit', 'Average Margin %']);
            foreach ($data['contributions']['by_category'] as $category) {
                $avgMargin = $category['total_amount'] > 0 ? ($category['total_profit'] / $category['total_amount'] * 100) : 0;
                fputcsv($output, [
                    ucfirst($category['category']),
                    $category['count'],
                    '₱' . number_format($category['total_amount'], 2),
                    '₱' . number_format($category['total_profit'], 2),
                    number_format($avgMargin, 2) . '%'
                ]);
            }
            fputcsv($output, []);
        }
        
        // Payment Methods Breakdown
        if (!empty($data['payments']['by_method'])) {
            fputcsv($output, ['PAYMENT METHODS BREAKDOWN']);
            fputcsv($output, ['Payment Method', 'Transaction Count', 'Total Amount', 'Percentage']);
            $totalCount = array_sum(array_column($data['payments']['by_method'], 'count'));
            foreach ($data['payments']['by_method'] as $method) {
                $percentage = $totalCount > 0 ? ($method['count'] / $totalCount * 100) : 0;
                fputcsv($output, [
                    ucfirst($method['payment_method']),
                    $method['count'],
                    '₱' . number_format($method['total_amount'], 2),
                    number_format($percentage, 1) . '%'
                ]);
            }
            fputcsv($output, []);
        }
        
        // Recent Payments
        if (!empty($data['payments']['recent_payments'])) {
            fputcsv($output, ['RECENT PAYMENTS (Last 10)']);
            fputcsv($output, ['Date', 'Student Name', 'Contribution', 'Amount', 'Payment Method', 'Status']);
            foreach (array_slice($data['payments']['recent_payments'], 0, 10) as $payment) {
                fputcsv($output, [
                    date('Y-m-d H:i:s', strtotime($payment['created_at'])),
                    $payment['student_name'],
                    $payment['contribution_title'],
                    '₱' . number_format($payment['amount'], 2),
                    ucfirst($payment['payment_method']),
                    ucfirst($payment['status'])
                ]);
            }
            fputcsv($output, []);
        }
        
        // Daily Revenue Trends (Last 30 days)
        if (!empty($data['trends']['daily_revenue'])) {
            fputcsv($output, ['DAILY REVENUE TRENDS (Last 30 Days)']);
            fputcsv($output, ['Date', 'Revenue']);
            foreach ($data['trends']['daily_revenue'] as $day) {
                fputcsv($output, [
                    $day['date'],
                    '₱' . number_format($day['total'], 2)
                ]);
            }
            fputcsv($output, []);
        }
        
        // Monthly Revenue Trends (Last 12 months)
        if (!empty($data['trends']['monthly_revenue'])) {
            fputcsv($output, ['MONTHLY REVENUE TRENDS (Last 12 Months)']);
            fputcsv($output, ['Month', 'Revenue']);
            foreach ($data['trends']['monthly_revenue'] as $month) {
                $monthName = date('F Y', mktime(0, 0, 0, $month['month'], 1, $month['year']));
                fputcsv($output, [
                    $monthName,
                    '₱' . number_format($month['total'], 2)
                ]);
            }
            fputcsv($output, []);
        }
        
        // Footer
        fputcsv($output, ['Report Generated:', $data['generated_at']]);
        fputcsv($output, ['System:', 'ClearPay Analytics']);
        
        fclose($output);
        
        // Get the CSV content
        $csvContent = ob_get_clean();
        
        // Return response with CSV content
        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setHeader('Content-Length', strlen($csvContent))
            ->setBody($csvContent);
    }

    /**
     * Export to Excel
     */
    private function exportExcel($data)
    {
        // For now, we'll create a detailed Excel-compatible CSV
        // You can enhance this later with PhpSpreadsheet for true Excel format
        
        $filename = 'analytics_report_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        // Create an enhanced CSV that works well in Excel
        $this->response->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        
        // Start output buffering
        ob_start();
        $output = fopen('php://output', 'w');
        
        // Add BOM for proper UTF-8 encoding
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Create a more structured format for Excel
        fputcsv($output, ['ClearPay Analytics Report']);
        fputcsv($output, ['Generated:', $data['generated_at']]);
        fputcsv($output, []);
        
        // Summary Section
        fputcsv($output, ['EXECUTIVE SUMMARY']);
        fputcsv($output, ['Metric', 'Current Value', 'Growth/Change', 'Notes']);
        fputcsv($output, [
            'Total Revenue', 
            '₱' . number_format($data['overview']['total_revenue'], 2),
            ($data['overview']['monthly_growth'] >= 0 ? '+' : '') . $data['overview']['monthly_growth'] . '%',
            'Month-over-month growth'
        ]);
        fputcsv($output, [
            'Total Profit',
            '₱' . number_format($data['overview']['total_profit'], 2),
            $data['overview']['avg_profit_margin'] . '% avg margin',
            'From tracked contributions'
        ]);
        fputcsv($output, [
            'Active Contributors',
            $data['overview']['active_contributors'],
            'Growing',
            'Unique paying students'
        ]);
        fputcsv($output, [
            'Monthly Revenue',
            '₱' . number_format($data['overview']['monthly_revenue'], 2),
            'Current month',
            'This month performance'
        ]);
        fputcsv($output, []);
        
        // Detailed breakdown sections (same as CSV but formatted for Excel)
        // Top Performers
        if (!empty($data['contributions']['top_profitable'])) {
            fputcsv($output, ['TOP PERFORMING CONTRIBUTIONS']);
            fputcsv($output, ['Rank', 'Title', 'Revenue', 'Profit', 'Margin %', 'ROI']);
            $rank = 1;
            foreach ($data['contributions']['top_profitable'] as $contribution) {
                $roi = ($contribution['cost_price'] ?? 0) > 0 ? ($contribution['profit_amount'] / $contribution['cost_price'] * 100) : 0;
                fputcsv($output, [
                    $rank++,
                    $contribution['title'],
                    number_format($contribution['amount'], 2),
                    number_format($contribution['profit_amount'], 2),
                    number_format($contribution['profit_margin'], 2),
                    number_format($roi, 2) . '%'
                ]);
            }
            fputcsv($output, []);
        }
        
        fclose($output);
        $content = ob_get_clean();
        
        // Note: This creates an Excel-compatible CSV
        // For true .xlsx format, you'd need PhpSpreadsheet library
        return $this->response
            ->setHeader('Content-Type', 'application/vnd.ms-excel')
            ->setHeader('Content-Disposition', 'attachment; filename="' . str_replace('.xlsx', '.xls', $filename) . '"')
            ->setBody($content);
    }
}