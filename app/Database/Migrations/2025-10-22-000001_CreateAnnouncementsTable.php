<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAnnouncementsTable extends Migration
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
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'content' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['general', 'urgent', 'maintenance', 'event', 'deadline'],
                'default'    => 'general',
                'null'       => false,
            ],
            'priority' => [
                'type'       => 'ENUM',
                'constraint' => ['low', 'medium', 'high', 'critical'],
                'default'    => 'medium',
                'null'       => false,
            ],
            'target_audience' => [
                'type'       => 'ENUM',
                'constraint' => ['all', 'students', 'admins', 'staff'],
                'default'    => 'all',
                'null'       => false,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['draft', 'published', 'archived'],
                'default'    => 'draft',
                'null'       => false,
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 1,
                'null'       => false,
            ],
            'published_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'expires_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);

        // Add primary key
        $this->forge->addKey('id', true);

        // Add indexes for better performance
        $this->forge->addKey('status');
        $this->forge->addKey('priority');
        $this->forge->addKey('target_audience');
        $this->forge->addKey('published_at');
        $this->forge->addKey('expires_at');
        $this->forge->addKey('created_by');

        // Create the table
        $this->forge->createTable('announcements');

        // Insert sample data
        $data = [
            [
                'title' => 'Welcome to ClearPay System',
                'content' => 'We are excited to announce the launch of our new ClearPay payment system. This platform will make it easier for students to manage their payments and for administrators to track transactions.',
                'type' => 'general',
                'priority' => 'high',
                'target_audience' => 'all',
                'status' => 'published',
                'created_by' => 1,
                'published_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Payment Deadline Reminder',
                'content' => 'This is a reminder that the deadline for semester fee payments is approaching. Please ensure all outstanding payments are completed by the end of this month to avoid late fees.',
                'type' => 'deadline',
                'priority' => 'high',
                'target_audience' => 'students',
                'status' => 'published',
                'created_by' => 1,
                'published_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'System Maintenance Scheduled',
                'content' => 'The ClearPay system will undergo routine maintenance this weekend from 2:00 AM to 6:00 AM. During this time, the system may be temporarily unavailable.',
                'type' => 'maintenance',
                'priority' => 'medium',
                'target_audience' => 'all',
                'status' => 'published',
                'created_by' => 1,
                'published_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'New QR Code Feature',
                'content' => 'We have added a new QR code feature for payment receipts. Students can now download QR codes for their payment confirmations, making verification easier.',
                'type' => 'general',
                'priority' => 'medium',
                'target_audience' => 'students',
                'status' => 'published',
                'created_by' => 1,
                'published_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Holiday Schedule',
                'content' => 'Please note that our offices will be closed during the upcoming holiday period. Online payments will still be available, but manual processing may be delayed.',
                'type' => 'event',
                'priority' => 'low',
                'target_audience' => 'all',
                'status' => 'published',
                'created_by' => 1,
                'published_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Critical Security Update',
                'content' => 'We have implemented important security enhancements to protect your payment information. Please log out and log back in to ensure you have the latest security features.',
                'type' => 'urgent',
                'priority' => 'critical',
                'target_audience' => 'all',
                'status' => 'published',
                'created_by' => 1,
                'published_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        // Insert the sample data
        $this->db->table('announcements')->insertBatch($data);
    }

    public function down()
    {
        // Drop the announcements table
        $this->forge->dropTable('announcements');
    }
}