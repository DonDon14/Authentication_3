<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run()
    {
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
            [
                'title' => 'Partial Payment Policy Update',
                'content' => 'We have updated our partial payment policy to allow students more flexibility in managing their fees. Students can now make partial payments with a minimum of 25% of the total amount.',
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
                'title' => 'Admin Training Session',
                'content' => 'All administrators are required to attend the training session on the new features of the ClearPay system. The session will be held next Friday at 2:00 PM in the main conference room.',
                'type' => 'event',
                'priority' => 'high',
                'target_audience' => 'admins',
                'status' => 'published',
                'created_by' => 1,
                'published_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Server Migration Notice',
                'content' => 'This weekend we will be migrating to new, more powerful servers to improve system performance. Expected downtime is minimal, but please be aware that there may be brief interruptions.',
                'type' => 'maintenance',
                'priority' => 'high',
                'target_audience' => 'all',
                'status' => 'draft',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'expires_at' => date('Y-m-d H:i:s', strtotime('+7 days')),
            ],
            [
                'title' => 'Student Feedback Survey',
                'content' => 'We value your opinion! Please take a few minutes to complete our student feedback survey about the ClearPay system. Your feedback helps us improve our services.',
                'type' => 'general',
                'priority' => 'low',
                'target_audience' => 'students',
                'status' => 'published',
                'created_by' => 1,
                'published_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'expires_at' => date('Y-m-d H:i:s', strtotime('+30 days')),
            ],
        ];

        // Insert the data
        $this->db->table('announcements')->insertBatch($data);
    }
}