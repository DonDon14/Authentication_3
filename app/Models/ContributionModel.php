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
        'cost_price',
        'profit_amount',
        'profit_margin',
        'profit_calculated_at',
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
     * Calculate profit for a contribution
     */
    public function calculateProfit($amount, $costPrice)
    {
        $profitAmount = $amount - $costPrice;
        $profitMargin = $amount > 0 ? ($profitAmount / $amount) * 100 : 0;
        
        return [
            'profit_amount' => round($profitAmount, 2),
            'profit_margin' => round($profitMargin, 2)
        ];
    }

    /**
     * Update contribution with profit calculation
     */
    public function updateWithProfit($id, $data)
    {
        if (isset($data['amount']) && isset($data['cost_price'])) {
            $profit = $this->calculateProfit($data['amount'], $data['cost_price']);
            $data['profit_amount'] = $profit['profit_amount'];
            $data['profit_margin'] = $profit['profit_margin'];
            $data['profit_calculated_at'] = date('Y-m-d H:i:s');
        }
        
        return $this->update($id, $data);
    }

    /**
     * Get profit analytics
     */
    public function getProfitAnalytics()
    {
        $query = $this->select('
            COUNT(*) as total_contributions,
            SUM(amount) as total_revenue,
            SUM(cost_price) as total_costs,
            SUM(profit_amount) as total_profit,
            AVG(profit_margin) as avg_profit_margin,
            MAX(profit_margin) as max_profit_margin,
            MIN(profit_margin) as min_profit_margin
        ')->where('status', 'active');
        
        return $query->first();
    }

    /**
     * Get top profitable contributions
     */
    public function getTopProfitable($limit = 5)
    {
        return $this->select('id, title, amount, cost_price, profit_amount, profit_margin, category')
                    ->where('status', 'active')
                    ->orderBy('profit_amount', 'DESC')
                    ->limit($limit)
                    ->findAll();
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