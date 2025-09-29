<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration

{
    public function up()
    {
        // Check if table already exists
        if ($this->db->tableExists('users')) {
            echo "Table 'users' already exists, skipping creation.\n";
            return;
        }

        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'auto_increment' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 255],
            'username'    => ['type' => 'VARCHAR', 'constraint' => 255],
            'email'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'email_verified' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'vefirication_token' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'verification_expires' => ['type' => 'DATETIME', 'null' => true],
            'reset_token' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'reset_expires' => ['type' => 'DATETIME', 'null' => true],
            'password'    => ['type' => 'VARCHAR', 'constraint' => 255],
            'created_at'  => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP'],
            'updated_at'  => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP', 'onUpdate' => 'CURRENT_TIMESTAMP'],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
