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
        'is_partial_payment',        // Add this
        'remaining_balance',         // Add this
        'total_amount_due',          // Add this
        'parent_payment_id',         // Add this
        'payment_sequence',          // Add this
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
        if (!$studentId) {
            return [];
        }
        
        return $this->select('
                payments.*, 
                contributions.title as contribution_title,
                contributions.category
            ')
            ->join('contributions', 'contributions.id = payments.contribution_id', 'left')
            ->where('payments.student_id', $studentId)
            ->orderBy('payments.payment_date', 'DESC')
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
     * Get student payment status for a contribution
     */
    public function getStudentPaymentStatus($contributionId, $studentId)
    {
        $payments = $this->where('contribution_id', $contributionId)
                        ->where('student_id', $studentId)
                        ->whereIn('payment_status', ['completed', 'partial', 'fully_paid'])
                        ->orderBy('created_at', 'ASC')
                        ->findAll();
        
        if (empty($payments)) {
            return [
                'status' => 'not_paid',
                'total_paid' => 0,
                'remaining_balance' => 0,
                'total_amount_due' => 0,
                'payments' => []
            ];
        }
        
        $totalPaid = array_sum(array_column($payments, 'amount_paid'));
        $totalDue = $payments[0]['total_amount_due'] ?? 0;
        $remainingBalance = $totalDue - $totalPaid;
        
        $status = $remainingBalance <= 0 ? 'fully_paid' : 'partial';
        
        return [
            'status' => $status,
            'total_paid' => $totalPaid,
            'remaining_balance' => max(0, $remainingBalance),
            'total_amount_due' => $totalDue,
            'payments' => $payments
        ];
    }

    /**
     * Record a new payment (partial or full) - COMPLETELY FIXED VERSION
     */
    public function recordPayment($data)
    {
        log_message('info', 'recordPayment called with data: ' . json_encode($data));
        
        $contributionModel = new \App\Models\ContributionModel();
        $contribution = $contributionModel->find($data['contribution_id']);
        
        if (!$contribution) {
            throw new \Exception('Contribution not found');
        }
        
        // Check existing payments for this student and contribution
        $paymentStatus = $this->getStudentPaymentStatus($data['contribution_id'], $data['student_id']);
        log_message('info', 'Current payment status: ' . json_encode($paymentStatus));
        
        if ($paymentStatus['status'] === 'fully_paid') {
            throw new \Exception('This contribution has already been fully paid by this student');
        }
        
        $totalAmountDue = (float)$contribution['amount'];
        $amountPaid = (float)$data['amount_paid'];
        $previouslyPaid = (float)$paymentStatus['total_paid'];
        $newTotalPaid = $previouslyPaid + $amountPaid;
        
        log_message('info', "Payment calculation: Total Due: $totalAmountDue, Amount Paid: $amountPaid, Previously Paid: $previouslyPaid, New Total: $newTotalPaid");
        
        // Validate payment amount
        if ($newTotalPaid > $totalAmountDue) {
            throw new \Exception('Payment amount exceeds remaining balance of $' . number_format($paymentStatus['remaining_balance'], 2));
        }
        
        // Determine payment status and sequence
        $isFullyPaid = $newTotalPaid >= $totalAmountDue;
        $paymentSequence = count($paymentStatus['payments']) + 1;
        $newRemainingBalance = max(0, $totalAmountDue - $newTotalPaid);
        
        log_message('info', "Payment determination: Is Fully Paid: " . ($isFullyPaid ? 'Yes' : 'No') . ", Sequence: $paymentSequence, New Remaining: $newRemainingBalance");
        
        // Prepare payment data
        $paymentData = [
            'contribution_id' => $data['contribution_id'],
            'student_id' => $data['student_id'],
            'student_name' => $data['student_name'],
            'amount_paid' => $amountPaid,
            'payment_method' => $data['payment_method'] ?? 'cash',
            'payment_status' => $isFullyPaid ? 'fully_paid' : 'partial',
            'is_partial_payment' => !$isFullyPaid,
            'remaining_balance' => $newRemainingBalance,
            'total_amount_due' => $totalAmountDue,
            'parent_payment_id' => $paymentSequence > 1 ? $paymentStatus['payments'][0]['id'] : null,
            'payment_sequence' => $paymentSequence,
            'payment_date' => $data['payment_date'] ?? date('Y-m-d H:i:s'),
            'recorded_by' => $data['recorded_by'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
            'notes' => $data['notes'] ?? null
        ];
        
        log_message('info', 'Inserting payment data: ' . json_encode($paymentData));
        
        // Start transaction to ensure data consistency
        $this->db->transStart();
        
        try {
            // Insert the new payment
            $paymentId = $this->insert($paymentData);
            log_message('info', 'New payment ID: ' . $paymentId);
            
            if ($paymentId && $isFullyPaid) {
                // Update ALL previous partial payments for this student/contribution to 'fully_paid'
                $updateQuery = "
                    UPDATE payments 
                    SET payment_status = 'fully_paid',
                        remaining_balance = 0,
                        notes = CONCAT(COALESCE(notes, ''), ' | Completed with final payment #$paymentId'),
                        updated_at = NOW()
                    WHERE contribution_id = ? 
                    AND student_id = ? 
                    AND payment_status = 'partial'
                    AND id != ?
                ";
                
                $this->db->query($updateQuery, [$data['contribution_id'], $data['student_id'], $paymentId]);
                $affectedRows = $this->db->affectedRows();
                
                log_message('info', 'Updated ' . $affectedRows . ' previous partial payments to fully_paid status');
                
                // Also update the remaining balance in all payments for this combination
                $updateAllQuery = "
                    UPDATE payments 
                    SET remaining_balance = 0
                    WHERE contribution_id = ? 
                    AND student_id = ?
                ";
                
                $this->db->query($updateAllQuery, [$data['contribution_id'], $data['student_id']]);
            }
            
            // Commit transaction
            $this->db->transCommit();
            
            log_message('info', 'Payment recorded successfully. ID: ' . $paymentId . ', Fully Paid: ' . ($isFullyPaid ? 'Yes' : 'No'));
            
            return $paymentId;
            
        } catch (\Exception $e) {
            // Rollback transaction on error
            $this->db->transRollback();
            log_message('error', 'Payment recording failed: ' . $e->getMessage());
            throw $e;
        }
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

    /**
     * Get payment history for a student and contribution
     */
    public function getPaymentHistory($contributionId, $studentId)
    {
        return $this->where('contribution_id', $contributionId)
                   ->where('student_id', $studentId)
                   ->orderBy('payment_sequence', 'ASC')
                   ->findAll();
    }

    /**
     * Get all partial payments summary - COMPLETELY FIXED VERSION
     */
    public function getPartialPaymentsSummary()
    {
        // Get the latest partial payment record for each student/contribution combination
        $query = "
            SELECT 
                p1.id,
                p1.contribution_id,
                p1.student_id,
                p1.student_name,
                p1.amount_paid,
                p1.payment_method,
                p1.payment_status,
                p1.is_partial_payment,
                p1.remaining_balance,
                p1.total_amount_due,
                p1.payment_sequence,
                p1.payment_date,
                p1.created_at,
                c.title as contribution_title,
                c.category,
                (SELECT COUNT(*) FROM payments p2 
                 WHERE p2.student_id = p1.student_id 
                 AND p2.contribution_id = p1.contribution_id 
                 AND p2.payment_status IN ('partial', 'fully_paid')) as payment_count,
                (SELECT SUM(p3.amount_paid) FROM payments p3 
                 WHERE p3.student_id = p1.student_id 
                 AND p3.contribution_id = p1.contribution_id 
                 AND p3.payment_status IN ('partial', 'fully_paid')) as total_paid_amount
            FROM payments p1
            INNER JOIN contributions c ON c.id = p1.contribution_id
            WHERE p1.payment_status = 'partial'
            AND p1.remaining_balance > 0
            AND p1.payment_sequence = (
                SELECT MAX(p4.payment_sequence) 
                FROM payments p4 
                WHERE p4.student_id = p1.student_id 
                AND p4.contribution_id = p1.contribution_id
                AND p4.payment_status = 'partial'
            )
            ORDER BY p1.created_at DESC
        ";
        
        return $this->db->query($query)->getResultArray();
    }

    /**
     * Get most recent partial payment for each student/contribution combination
     */
    public function getLatestPartialPayments()
    {
        $query = "
            SELECT DISTINCT
                p1.contribution_id,
                p1.student_id,
                p1.student_name,
                c.title as contribution_title,
                p1.total_amount_due,
                (p1.total_amount_due - COALESCE(totals.total_paid, 0)) as remaining_balance,
                COALESCE(totals.total_paid, 0) as total_paid,
                totals.payment_count,
                p1.created_at,
                p1.payment_method
            FROM payments p1
            INNER JOIN contributions c ON c.id = p1.contribution_id
            LEFT JOIN (
                SELECT 
                    contribution_id,
                    student_id,
                    SUM(amount_paid) as total_paid,
                    COUNT(*) as payment_count,
                    MAX(created_at) as latest_payment
                FROM payments 
                WHERE payment_status IN ('partial', 'fully_paid')
                GROUP BY contribution_id, student_id
            ) totals ON totals.contribution_id = p1.contribution_id 
                    AND totals.student_id = p1.student_id
            WHERE p1.payment_status = 'partial'
            AND (p1.total_amount_due - COALESCE(totals.total_paid, 0)) > 0
            AND p1.created_at = totals.latest_payment
            ORDER BY p1.created_at DESC
        ";
        
        return $this->db->query($query)->getResultArray();
    }

    /**
     * Clean up payment statuses - fix any inconsistent data
     */
    public function cleanupPaymentStatuses()
    {
        // Get all unique student/contribution combinations
        $combinations = $this->select('DISTINCT student_id, contribution_id')
                             ->findAll();
        
        $fixedCount = 0;
        
        foreach ($combinations as $combo) {
            $status = $this->getStudentPaymentStatus($combo['contribution_id'], $combo['student_id']);
            
            if ($status['status'] === 'fully_paid') {
                // Update all payments for this combination to 'fully_paid'
                $updated = $this->where('contribution_id', $combo['contribution_id'])
                               ->where('student_id', $combo['student_id'])
                               ->where('payment_status', 'partial')
                               ->set([
                                   'payment_status' => 'fully_paid',
                                   'notes' => 'Status corrected by cleanup process',
                                   'updated_at' => date('Y-m-d H:i:s')
                               ])
                               ->update();
                
                if ($this->db->affectedRows() > 0) {
                    $fixedCount += $this->db->affectedRows();
                    log_message('info', "Fixed payment status for student {$combo['student_id']} contribution {$combo['contribution_id']}");
                }
            }
        }
        
        return $fixedCount;
    }
}

