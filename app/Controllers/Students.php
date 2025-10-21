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

        $data = [
            'title' => 'Students Management',
            'students' => $this->getStudentsData()
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
}