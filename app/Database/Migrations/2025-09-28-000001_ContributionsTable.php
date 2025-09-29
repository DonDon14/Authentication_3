<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ContributionsTable extends Migration
{
    public function up()
    {
        // Check if table already exists
        if ($this->db->tableExists('contributions')) {
            echo "Table 'contributions' already exists, skipping creation.\n";
            return;
        }

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => false,
                'default'    => '0.00',
            ],
            'category' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default'    => 'active',
                'null'       => false,
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
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
        $this->forge->addKey(['status', 'category']);
        $this->forge->addKey('created_by');
        $this->forge->addKey('created_at');
        
        // Add foreign key constraint (if users table exists)
        // $this->forge->addForeignKey('created_by', 'users', 'id', 'SET NULL', 'CASCADE');

        // Create the table
        $this->forge->createTable('contributions');
    }

    public function down()
    {
        $this->forge->dropTable('contributions');
    }
}