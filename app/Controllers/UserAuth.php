<?php

namespace App\Controllers;

use App\Models\UsersModel;
use App\Models\PaymentModel;
use App\Models\ContributionModel;
use CodeIgniter\Controller;

class UserAuth extends Controller
{
    protected $usersModel;
    protected $paymentModel;
    protected $contributionModel;

    public function __construct()
    {
        $this->usersModel = new UsersModel();
        $this->paymentModel = new PaymentModel();
        $this->contributionModel = new ContributionModel();
    }

    /**
     * Show user login form
     */
    public function login()
    {
        return view('user_login');  // Changed from 'user/login'
    }

    /**
     * Process user login
     */
    public function processLogin()
    {
        try {
            $studentName = trim($this->request->getPost('student_name'));
            $studentId = trim($this->request->getPost('student_id'));

            log_message('debug', "User login attempt - Name: '$studentName', ID: '$studentId'");

            if (empty($studentName) || empty($studentId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Please enter both your name and student ID'
                ]);
            }

            // Check if student exists in payments table (has made payments before)
            $existingPayment = $this->paymentModel->where('student_name', $studentName)
                                                  ->where('student_id', $studentId)
                                                  ->first();

            if (!$existingPayment) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No payment records found for this name and ID combination'
                ]);
            }

            // Start user session
            $session = session();
            $session->set([
                'user_student_id' => $studentId,
                'user_student_name' => $studentName,
                'user_logged_in' => true,
                'user_type' => 'student'
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Login successful! Welcome ' . $studentName,
                'redirect' => '/user/dashboard'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'User login error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Login error. Please try again.'
            ]);
        }
    }

    /**
     * User dashboard
     */
    public function dashboard()
    {
        if (!$this->isUserLoggedIn()) {
            return redirect()->to('/user/login');
        }

        $session = session();
        $studentId = $session->get('user_student_id');
        $studentName = $session->get('user_student_name');

        try {
            // Get all payments for this student
            $payments = $this->paymentModel->where('student_id', $studentId)
                                          ->where('student_name', $studentName)
                                          ->orderBy('payment_date', 'DESC')
                                          ->findAll();

            // Get contribution details for each payment
            foreach ($payments as &$payment) {
                if ($payment['contribution_id']) {
                    $contribution = $this->contributionModel->find($payment['contribution_id']);
                    $payment['contribution_title'] = $contribution['title'] ?? 'General Payment';
                    $payment['contribution_category'] = $contribution['category'] ?? 'General';
                }
            }

            // Calculate stats
            $totalPaid = array_sum(array_column($payments, 'amount_paid'));
            $completedPayments = count(array_filter($payments, function($p) {
                return in_array($p['payment_status'], ['completed', 'verified', 'fully_paid']);
            }));
            $pendingPayments = count(array_filter($payments, function($p) {
                return $p['payment_status'] === 'pending';
            }));

            // Get recent payments (last 5)
            $recentPayments = array_slice($payments, 0, 5);

            // Get active contributions
            $activeContributions = $this->contributionModel->where('status', 'active')->findAll();

            $data = [
                'student_name' => $studentName,
                'student_id' => $studentId,
                'payments' => $payments,
                'recent_payments' => $recentPayments,
                'active_contributions' => $activeContributions,
                'stats' => [
                    'total_paid' => $totalPaid,
                    'completed_payments' => $completedPayments,
                    'pending_payments' => $pendingPayments,
                    'total_payments' => count($payments)
                ]
            ];

            return view('user_dashboard', $data);  // Changed from 'user/dashboard'

        } catch (\Exception $e) {
            log_message('error', 'User dashboard error: ' . $e->getMessage());
            return redirect()->to('/user/login')->with('error', 'Error loading dashboard');
        }
    }

    /**
     * User payment history
     */
    public function paymentHistory()
    {
        if (!$this->isUserLoggedIn()) {
            return redirect()->to('/user/login');
        }

        $session = session();
        $studentId = $session->get('user_student_id');
        $studentName = $session->get('user_student_name');

        $payments = $this->paymentModel->select('
                payments.*, 
                contributions.title as contribution_title,
                contributions.category,
                contributions.amount as contribution_amount
            ')
            ->join('contributions', 'contributions.id = payments.contribution_id', 'left')
            ->where('payments.student_id', $studentId)
            ->where('payments.student_name', $studentName)
            ->orderBy('payments.payment_date', 'DESC')
            ->findAll();

        $data = [
            'student_name' => $studentName,
            'student_id' => $studentId,
            'payments' => $payments
        ];

        return view('user_payment_history', $data);  // Changed from 'user/payment_history'
    }

    /**
     * User logout
     */
    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to('/user/login')->with('message', 'You have been logged out successfully');
    }

    /**
     * Check if user is logged in
     */
    private function isUserLoggedIn()
    {
        $session = session();
        return $session->get('user_logged_in') === true && $session->get('user_type') === 'student';
    }

    /**
     * Check payment status for a contribution
     */
    public function checkPaymentStatus($contributionId)
    {
        if (!$this->isUserLoggedIn()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not logged in']);
        }

        $session = session();
        $studentId = $session->get('user_student_id');
        $studentName = $session->get('user_student_name');

        try {
            // Check if student has paid for this contribution
            $payment = $this->paymentModel
                ->where('student_id', $studentId)
                ->where('student_name', $studentName)
                ->where('contribution_id', $contributionId)
                ->first();

            if ($payment) {
                return $this->response->setJSON([
                    'success' => true,
                    'status' => $payment['payment_status'],
                    'message' => 'Payment found: ' . ucfirst($payment['payment_status']),
                    'payment_data' => $payment
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => true,
                    'status' => 'not_paid',
                    'message' => 'No payment found for this contribution'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Check payment status error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error checking payment status'
            ]);
        }
    }
}