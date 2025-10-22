<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddContactFieldsToPayments extends Migration
{
    public function up()
    {
        // Add the new columns to the payments table
        $fields = [
            'contact_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'comment'    => 'Contact number of the person making the payment',
                'after'      => 'student_name'
            ],
            'email_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Email address for payment receipts and notifications',
                'after'      => 'contact_number'
            ]
        ];

        $this->forge->addColumn('payments', $fields);
        
        // Add index for email_address for faster lookups
        $this->forge->addKey('email_address');
    }

    public function down()
    {
        // Remove the columns if rolling back
        $this->forge->dropColumn('payments', ['contact_number', 'email_address']);
    }
}