<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PaymentsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'contribution_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'student_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
                'comment'    => 'Student ID from QR code scan',
            ],
            'student_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'amount_paid' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => false,
                'default'    => '0.00',
            ],
            'payment_method' => [
                'type'       => 'ENUM',
                'constraint' => ['cash', 'card', 'bank_transfer', 'gcash', 'mobile_payment', 'other'],
                'default'    => 'cash',
                'null'       => false,
            ],
            'payment_status' => [
                'type'       => 'ENUM',
                'constraint' => ['completed', 'pending', 'failed', 'refunded', 'partial', 'fully_paid'],
                'default'    => 'completed',
                'null'       => false,
            ],
            // NEW COLUMNS FOR PARTIAL PAYMENTS
            'is_partial_payment' => [
                'type'       => 'BOOLEAN',
                'default'    => false,
                'null'       => false,
                'comment'    => 'Whether this is a partial payment',
            ],
            'remaining_balance' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '0.00',
                'null'       => false,
                'comment'    => 'Remaining balance after this payment',
            ],
            'total_amount_due' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '0.00',
                'null'       => false,
                'comment'    => 'Total amount due for the contribution',
            ],
            'parent_payment_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'References the first payment in a series of partial payments',
            ],
            'payment_sequence' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1,
                'null'       => false,
                'comment'    => 'Sequence number for partial payments (1st, 2nd, 3rd payment)',
            ],
            // EXISTING COLUMNS
            'reference_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Transaction reference number',
            ],
            'receipt_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'comment'    => 'Receipt number for tracking',
            ],
            // QR RECEIPT COLUMNS
            'qr_receipt_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Path to generated QR receipt image',
            ],
            'verification_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Verification code for QR receipt',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Additional payment notes',
            ],
            'recorded_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'User ID who recorded the payment',
            ],
            'payment_date' => [
                'type'    => 'DATETIME',
                'null'    => false,
                'comment' => 'When the payment was made',
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
        ]);

        // Add primary key
        $this->forge->addKey('id', true);
        
        // Add indexes for better performance
        $this->forge->addKey(['contribution_id', 'student_id']);
        $this->forge->addKey('student_id');
        $this->forge->addKey('payment_status');
        $this->forge->addKey('payment_date');
        $this->forge->addKey('recorded_by');
        
        // NEW INDEXES FOR PARTIAL PAYMENTS
        $this->forge->addKey('parent_payment_id');
        $this->forge->addKey(['student_id', 'contribution_id']);
        $this->forge->addKey(['payment_status', 'is_partial_payment']);
        
        // Add foreign key constraints
        $this->forge->addForeignKey('contribution_id', 'contributions', 'id', 'CASCADE', 'CASCADE');
        
        // Self-referencing foreign key for parent payments
        $this->forge->addForeignKey('parent_payment_id', 'payments', 'id', 'SET NULL', 'CASCADE');
        
        // Note: We can't add FK to users.id for student_id since student_id is from QR scan
        // But we can add FK for recorded_by if users table exists
        // $this->forge->addForeignKey('recorded_by', 'users', 'id', 'SET NULL', 'CASCADE');

        // Create the table
        $this->forge->createTable('payments');
    }

    public function down()
    {
        $this->forge->dropTable('payments');
    }
}