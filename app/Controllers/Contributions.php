<?php

namespace App\Controllers;

use App\Models\ContributionModel;
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
        $category = $this->request->getPost('category');

        log_message('info', "Title: $title, Amount: $amount, Category: $category");

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

        $data = [
            'title' => trim($title),
            'description' => trim($this->request->getPost('description') ?? ''),
            'amount' => floatval($amount),
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
        $contributionModel->delete($id);
        
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
}