<?php

namespace App\Models;

use CodeIgniter\Model;

class ContributionModel extends Model
{
    protected $table = 'contributions';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'title', 
        'description', 
        'amount', 
        'category', 
        'status', 
        'created_at', 
        'created_by',
        'updated_at'
    ];
    
    protected $useTimestamps = false;

    /**
     * Find active contributions
     */
    public function findActive()
    {
        return $this->where('status', 'active')->findAll();
    }

    /**
     * Find contributions by category
     */
    public function findByCategory($category)
    {
        return $this->where('category', $category)->findAll();
    }

    /**
     * Get contribution statistics
     */
    public function getStats()
    {
        return [
            'active' => $this->where('status', 'active')->countAllResults(),
            'total' => $this->countAllResults(),
            'inactive' => $this->where('status', 'inactive')->countAllResults()
        ];
    }

    /**
     * Toggle contribution status
     */
    public function toggleStatus($id)
    {
        $contribution = $this->find($id);
        if ($contribution) {
            $newStatus = $contribution['status'] === 'active' ? 'inactive' : 'active';
            return $this->update($id, ['status' => $newStatus, 'updated_at' => date('Y-m-d H:i:s')]);
        }
        return false;
    }

    /**
     * Create new contribution
     */
    public function createContribution($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['status'] = $data['status'] ?? 'active';
        
        return $this->insert($data);
    }

    /**
     * Update contribution
     */
    public function updateContribution($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->update($id, $data);
    }
}