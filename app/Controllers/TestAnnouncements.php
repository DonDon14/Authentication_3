<?php

namespace App\Controllers;

use App\Models\AnnouncementModel;

class TestAnnouncements extends BaseController
{
    public function testCreate()
    {
        try {
            log_message('debug', 'Test create announcement started');
            
            // Simple test data
            $data = [
                'title' => 'Test Announcement',
                'content' => 'This is a test announcement with enough content',
                'type' => 'general',
                'priority' => 'low',
                'target_audience' => 'all',
                'status' => 'published',
                'created_by' => 1
            ];
            
            log_message('debug', 'Test data: ' . json_encode($data));
            
            $model = new AnnouncementModel();
            log_message('debug', 'Model created successfully');
            
            $id = $model->createAnnouncement($data);
            log_message('debug', 'Create result: ' . $id);
            
            if ($id) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Test announcement created successfully',
                    'id' => $id
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to create test announcement',
                    'errors' => $model->errors()
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Test create error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}