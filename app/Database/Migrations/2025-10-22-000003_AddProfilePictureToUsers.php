<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProfilePictureToUsers extends Migration
{
    public function up()
    {
        // Add profile picture column to users table
        $fields = [
            'profile_picture' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Path to user profile picture',
                'after'      => 'email'
            ]
        ];

        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        // Remove profile picture column if rolling back
        $this->forge->dropColumn('users', 'profile_picture');
    }
}