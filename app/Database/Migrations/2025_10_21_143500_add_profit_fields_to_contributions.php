<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProfitFieldsToContributions extends Migration
{
    public function up()
    {
        $fields = [
            'cost_price' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
                'comment' => 'Cost/expense for this contribution'
            ],
            'profit_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
                'comment' => 'Calculated profit (amount - cost_price)'
            ],
            'profit_margin' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 0.00,
                'comment' => 'Profit margin percentage'
            ],
            'profit_calculated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'comment' => 'When profit was last calculated'
            ]
        ];

        $this->forge->addColumn('contributions', $fields);

        // Update existing records to have 0 cost and calculate profit
        $db = \Config\Database::connect();
        $db->query("UPDATE contributions 
                   SET cost_price = 0.00, 
                       profit_amount = amount, 
                       profit_margin = 100.00,
                       profit_calculated_at = NOW() 
                   WHERE cost_price IS NULL");
    }

    public function down()
    {
        $this->forge->dropColumn('contributions', ['cost_price', 'profit_amount', 'profit_margin', 'profit_calculated_at']);
    }
}