<?php

namespace App\Controllers;

use App\Models\ContributionModel;
use App\Models\PaymentModel;
use App\Models\ActivityModel;
use CodeIgniter\Controller;

class Contributions extends Controller
{
    // Show contributions list
    public function index()
    {
        $contributionModel = new ContributionModel();
        
        $data = [
            'contributions' => $contributionModel->findAll(),
            'stats' => $contributionModel->getStats()
        ];
        
        return view('contributions', $data);
    }

    // Add new contribution
    public function add()
    {
        // Debug logging
        log_message('info', 'Contributions::add() called');
        log_message('info', 'POST data: ' . json_encode($this->request->getPost()));

        $contributionModel = new ContributionModel();

        // Validate input
        $title = $this->request->getPost('title');
        $amount = $this->request->getPost('amount');
        $costPrice = $this->request->getPost('cost_price') ?? 0;
        $category = $this->request->getPost('category');

        log_message('info', "Title: $title, Amount: $amount, Cost Price: $costPrice, Category: $category");

        if (!$title || !$amount || !$category) {
            log_message('warning', 'Validation failed: missing required fields');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'All required fields must be filled'
            ]);
        }

        if (!is_numeric($amount) || $amount < 0) {
            log_message('warning', 'Validation failed: invalid amount');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Amount must be a valid positive number'
            ]);
        }

        if (!is_numeric($costPrice) || $costPrice < 0) {
            log_message('warning', 'Validation failed: invalid cost price');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cost price must be a valid positive number'
            ]);
        }

        // Calculate profit
        $profit = $contributionModel->calculateProfit(floatval($amount), floatval($costPrice));

        $data = [
            'title' => trim($title),
            'description' => trim($this->request->getPost('description') ?? ''),
            'amount' => floatval($amount),
            'cost_price' => floatval($costPrice),
            'profit_amount' => $profit['profit_amount'],
            'profit_margin' => $profit['profit_margin'],
            'profit_calculated_at' => date('Y-m-d H:i:s'),
            'category' => trim($category),
            'status' => 'active',
            'created_by' => session()->get('user_id') ?? null,
            'created_at' => date('Y-m-d H:i:s')
        ];

        log_message('info', 'Data to insert: ' . json_encode($data));

        try {
            $result = $contributionModel->insert($data);
            log_message('info', 'Insert result: ' . ($result ? 'success' : 'failed'));
            
            if ($result) {
                $insertId = $contributionModel->getInsertID();
                log_message('info', 'Inserted ID: ' . $insertId);
                
                // Log activity
                $activityModel = new ActivityModel();
                $activityModel->logActivity(
                    session()->get('user_id'),
                    ActivityModel::ACTIVITY_CONTRIBUTION_CREATED,
                    "Created contribution: {$title}",
                    'contribution',
                    $insertId,
                    ['amount' => $amount, 'category' => $category]
                );
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Contribution added successfully!',
                    'id' => $insertId
                ]);
            } else {
                log_message('error', 'Insert failed - no exception thrown but result is false');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to insert data'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Contribution add error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to add contribution. Error: ' . $e->getMessage()
            ]);
        }
    }

    // Update contribution
    public function update($id)
    {
        $contributionModel = new ContributionModel();

        // Check if contribution exists
        $existingContribution = $contributionModel->find($id);
        if (!$existingContribution) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Contribution not found'
            ]);
        }

        // Validate input
        $title = $this->request->getPost('title');
        $amount = $this->request->getPost('amount');
        $category = $this->request->getPost('category');

        if (!$title || !$amount || !$category) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'All required fields must be filled'
            ]);
        }

        if (!is_numeric($amount) || $amount < 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Amount must be a valid positive number'
            ]);
        }

        $data = [
            'title' => trim($title),
            'description' => trim($this->request->getPost('description') ?? ''),
            'amount' => floatval($amount),
            'category' => trim($category),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        try {
            $contributionModel->update($id, $data);
            
            // Log activity
            $activityModel = new ActivityModel();
            $activityModel->logActivity(
                session()->get('user_id'),
                ActivityModel::ACTIVITY_CONTRIBUTION_UPDATED,
                "Updated contribution: {$title}",
                'contribution',
                $id,
                ['amount' => $amount, 'category' => $category]
            );
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Contribution updated successfully!'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Contribution update error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update contribution. Please try again.'
            ]);
        }
    }

    // Toggle contribution status
    public function toggle($id)
    {
        $contributionModel = new ContributionModel();
        $contribution = $contributionModel->find($id);
        
        if ($contribution) {
            $newStatus = $contribution['status'] === 'active' ? 'inactive' : 'active';
            $contributionModel->update($id, ['status' => $newStatus]);
            
            // Log activity
            $activityModel = new ActivityModel();
            $activityModel->logActivity(
                session()->get('user_id'),
                ActivityModel::ACTIVITY_CONTRIBUTION_UPDATED,
                "Changed contribution '{$contribution['title']}' status to {$newStatus}",
                'contribution',
                $id,
                ['old_status' => $contribution['status'], 'new_status' => $newStatus]
            );
            
            return $this->response->setJSON([
                'success' => true,
                'status' => $newStatus,
                'message' => 'Contribution status updated!'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Contribution not found!'
        ]);
    }

    // Delete contribution
    public function delete($id)
    {
        $contributionModel = new ContributionModel();
        
        // Get contribution details before deletion for logging
        $contribution = $contributionModel->find($id);
        $contributionTitle = $contribution ? $contribution['title'] : "Contribution ID: $id";
        
        $contributionModel->delete($id);
        
        // Log activity
        $activityModel = new ActivityModel();
        $activityModel->logActivity(
            session()->get('user_id'),
            ActivityModel::ACTIVITY_CONTRIBUTION_DELETED,
            "Deleted contribution: {$contributionTitle}",
            'contribution',
            $id,
            null
        );
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Contribution deleted successfully!'
        ]);
    }

    // Get contribution data for editing
    public function get($id)
    {
        $contributionModel = new ContributionModel();
        $contribution = $contributionModel->find($id);
        
        if ($contribution) {
            return $this->response->setJSON([
                'success' => true,
                'data' => $contribution
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Contribution not found!'
        ]);
    }

    // View contribution details
    public function viewContribution($contributionId)
    {
        $contributionModel = new ContributionModel();
        $paymentModel = new PaymentModel();
        
        // Get contribution details
        $contribution = $contributionModel->find($contributionId);
        if (!$contribution) {
            return redirect()->to('/contributions')->with('error', 'Contribution not found.');
        }
        
        // Get all payments for this contribution - grouped by student
        $allPayments = $paymentModel->findByContribution($contributionId);
        
        // Group payments by student and calculate totals
        $studentPayments = [];
        foreach ($allPayments as $payment) {
            $studentId = $payment['student_id'];
            
            if (!isset($studentPayments[$studentId])) {
                $studentPayments[$studentId] = [
                    'id' => $payment['id'], // Use first payment ID for modal reference
                    'student_id' => $payment['student_id'],
                    'student_name' => $payment['student_name'],
                    'contribution_id' => $payment['contribution_id'],
                    'total_paid' => 0,
                    'payment_count' => 1,
                    'first_payment_date' => $payment['payment_date'],
                    'last_payment_date' => $payment['payment_date'],
                    'payment_status' => $payment['payment_status'], // Will be updated based on total
                    'payment_method' => $payment['payment_method'], // Last payment method
                    'verification_code' => $payment['verification_code']
                ];
            }
            
            // Accumulate payment data
            $studentPayments[$studentId]['total_paid'] += $payment['amount_paid'];
            $studentPayments[$studentId]['payment_count']++;
            
            // Update dates
            if (strtotime($payment['payment_date']) < strtotime($studentPayments[$studentId]['first_payment_date'])) {
                $studentPayments[$studentId]['first_payment_date'] = $payment['payment_date'];
            }
            if (strtotime($payment['payment_date']) > strtotime($studentPayments[$studentId]['last_payment_date'])) {
                $studentPayments[$studentId]['last_payment_date'] = $payment['payment_date'];
                $studentPayments[$studentId]['payment_method'] = $payment['payment_method'];
            }
            
            // Determine overall status
            if ($payment['payment_status'] === 'partial') {
                $studentPayments[$studentId]['payment_status'] = 'partial';
            }
        }
        
        // Convert back to array and determine final payment status
        $payments = array_values($studentPayments);
        foreach ($payments as &$payment) {
            if (!isset($payment['payment_count'])) {
                $payment['payment_count'] = 1;
            }
            if (!isset($payment['total_paid'])) {
                $payment['total_paid'] = 0;
            }
            
            $contributionAmount = (float)$contribution['amount'];
            $totalPaid = $payment['total_paid'];
            
            if ($totalPaid >= $contributionAmount) {
                $payment['payment_status'] = 'fully_paid';
                $payment['remaining_balance'] = 0;
            } else {
                $payment['payment_status'] = 'partial';
                $payment['remaining_balance'] = $contributionAmount - $totalPaid;
            }
            
            // Add amount_paid for compatibility
            $payment['amount_paid'] = $payment['total_paid'];
            $payment['payment_date'] = $payment['last_payment_date'];
        }

        foreach ($payments as $p) {
            log_message('debug', 'Student: ' . $p['student_id'] . ' payment_count: ' . (isset($p['payment_count']) ? $p['payment_count'] : 'NOT SET'));
        }
                
        // Calculate statistics
        $stats = [
            'total_payments' => count($payments),
            'total_amount' => array_sum(array_column($payments, 'total_paid')),
            'average_amount' => count($payments) > 0 ? array_sum(array_column($payments, 'total_paid')) / count($payments) : 0,
            'unique_students' => count($payments)
        ];
        
        $data = [
            'contribution' => $contribution,
            'payments' => $payments,
            'stats' => $stats
        ];
        
        return view('payments/contribution_details', $data);
    }

    /**
     * Get complete payment history for a student and contribution
     */
    public function getStudentPaymentHistory($contributionId, $studentId)
    {
        $paymentModel = new PaymentModel();
        
        try {
            $payments = $paymentModel->where([
                'contribution_id' => $contributionId,
                'student_id' => $studentId
            ])->orderBy('payment_date', 'DESC')->findAll();

            if (!$payments) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No payment history found'
                ]);
            }

            // Calculate totals
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

    // Show profit analytics
    public function analytics()
    {
        $contributionModel = new ContributionModel();
        
        $data = [
            'profit_analytics' => $contributionModel->getProfitAnalytics(),
            'top_profitable' => $contributionModel->getTopProfitable(10),
            'contributions' => $contributionModel->select('id, title, amount, cost_price, profit_amount, profit_margin, category, status')
                                                 ->orderBy('profit_margin', 'DESC')
                                                 ->findAll()
        ];
        
        return view('contribution_analytics', $data);
    }
}