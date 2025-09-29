<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'contribution_id',
        'student_id', 
        'student_name',
        'amount_paid', 
        'payment_method',
        'payment_status',
        'reference_number',
        'receipt_number',
        'qr_receipt_path',
        'verification_code',
        'notes',
        'recorded_by',
        'payment_date',
        'created_at', 
        'updated_at'
    ];
    
    protected $useTimestamps = false;

    /**
     * Find payments by contribution ID
     */
    public function findByContribution($contributionId)
    {
        return $this->where('contribution_id', $contributionId)
                   ->orderBy('payment_date', 'DESC')
                   ->findAll();
    }

    /**
     * Find payments by student ID
     */
    public function findByStudent($studentId)
    {
        return $this->where('student_id', $studentId)
                   ->orderBy('payment_date', 'DESC')
                   ->findAll();
    }

    /**
     * Find payments by status
     */
    public function findByStatus($status)
    {
        return $this->where('payment_status', $status)->findAll();
    }

    /**
     * Check if student has already paid for a contribution
     */
    public function hasStudentPaid($contributionId, $studentId)
    {
        return $this->where('contribution_id', $contributionId)
                   ->where('student_id', $studentId)
                   ->where('payment_status', 'completed')
                   ->countAllResults() > 0;
    }

    /**
     * Get payments with contribution details
     */
    public function getPaymentsWithContributions($limit = null)
    {
        $builder = $this->db->table($this->table)
                           ->select('payments.*, contributions.title as contribution_title, contributions.category')
                           ->join('contributions', 'contributions.id = payments.contribution_id', 'left')
                           ->orderBy('payments.payment_date', 'DESC');
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        return $builder->get()->getResultArray();
    }

    /**
     * Get contribution payment statistics
     */
    public function getContributionStats($contributionId)
    {
        $payments = $this->where('contribution_id', $contributionId)
                         ->where('payment_status', 'completed')
                         ->findAll();
        
        return [
            'total_payments' => count($payments),
            'total_amount' => array_sum(array_column($payments, 'amount_paid')),
            'unique_students' => count(array_unique(array_column($payments, 'student_id')))
        ];
    }

    /**
     * Get overall payment statistics
     */
    public function getStats()
    {
        return [
            'total_amount' => $this->selectSum('amount_paid')->where('payment_status', 'completed')->first()['amount_paid'] ?? 0,
            'total_payments' => $this->where('payment_status', 'completed')->countAllResults(),
            'pending_payments' => $this->where('payment_status', 'pending')->countAllResults(),
            'today_payments' => $this->where('DATE(payment_date)', date('Y-m-d'))->where('payment_status', 'completed')->countAllResults()
        ];
    }

    /**
     * Create new payment record
     */
    public function recordPayment($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['payment_date'] = $data['payment_date'] ?? date('Y-m-d H:i:s');
        $data['payment_status'] = $data['payment_status'] ?? 'completed';
        $data['payment_method'] = $data['payment_method'] ?? 'cash';
        
        return $this->insert($data);
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus($id, $status, $notes = null)
    {
        $updateData = [
            'payment_status' => $status, 
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        if ($notes) {
            $updateData['notes'] = $notes;
        }
        
        return $this->update($id, $updateData);
    }

    /**
     * Search payments by student name, ID, or contribution
     */
    public function searchPayments($query)
    {
        return $this->db->table($this->table)
                       ->select('payments.*, contributions.title as contribution_title')
                       ->join('contributions', 'contributions.id = payments.contribution_id', 'left')
                       ->like('payments.student_name', $query)
                       ->orLike('payments.student_id', $query)
                       ->orLike('contributions.title', $query)
                       ->orderBy('payments.payment_date', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    /**
     * Get recent payments for dashboard
     */
    public function getRecentPayments($limit = 10)
    {
        return $this->getPaymentsWithContributions($limit);
    }
}