<?php
/**
 * Database Setup Script for User Activities Table
 * Run this after migration to populate sample data
 */

// Define paths
define('ROOTPATH', __DIR__ . DIRECTORY_SEPARATOR);
define('APPPATH', ROOTPATH . 'app' . DIRECTORY_SEPARATOR);
define('SYSTEMPATH', ROOTPATH . 'system' . DIRECTORY_SEPARATOR);
define('FCPATH', ROOTPATH . 'public' . DIRECTORY_SEPARATOR);
define('WRITEPATH', ROOTPATH . 'writable' . DIRECTORY_SEPARATOR);

require_once 'vendor/autoload.php';

// Load CodeIgniter framework
require_once APPPATH . 'Config/Boot/production.php';

use Config\Database;
use App\Models\ActivityModel;

echo "Setting up User Activities Table...\n";

try {
    // Get database connection
    $db = Database::connect();
    
    // Check if table exists
    if (!$db->tableExists('user_activities')) {
        echo "Error: user_activities table does not exist. Please run migrations first.\n";
        exit(1);
    }
    
    echo "Table exists. Checking for existing data...\n";
    
    // Check if there's already data
    $existingCount = $db->table('user_activities')->countAllResults();
    
    if ($existingCount > 0) {
        echo "Found {$existingCount} existing activities. Skipping sample data insertion.\n";
    } else {
        echo "No existing data found. Creating sample activities...\n";
        
        // Get a sample user ID
        $usersTable = $db->table('users');
        $user = $usersTable->limit(1)->get()->getRowArray();
        
        if (!$user) {
            echo "No users found. Please create a user first.\n";
            exit(1);
        }
        
        $userId = $user['id'];
        echo "Using user ID: {$userId}\n";
        
        // Sample activities
        $activities = [
            [
                'user_id' => $userId,
                'activity_type' => 'login',
                'description' => 'User logged into the system',
                'entity_type' => null,
                'entity_id' => null,
                'metadata' => json_encode(['ip' => '127.0.0.1', 'browser' => 'Chrome']),
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours'))
            ],
            [
                'user_id' => $userId,
                'activity_type' => 'payment_created',
                'description' => 'Created payment for School Fund contribution',
                'entity_type' => 'payment',
                'entity_id' => 1,
                'metadata' => json_encode(['amount' => 1000, 'method' => 'mobile_money']),
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 hour'))
            ],
            [
                'user_id' => $userId,
                'activity_type' => 'qr_generated',
                'description' => 'Generated QR code for payment receipt',
                'entity_type' => 'payment',
                'entity_id' => 1,
                'metadata' => json_encode(['qr_type' => 'receipt', 'format' => 'png']),
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => date('Y-m-d H:i:s', strtotime('-30 minutes'))
            ],
            [
                'user_id' => $userId,
                'activity_type' => 'contribution_created',
                'description' => 'Created new contribution: Sports Equipment Fund',
                'entity_type' => 'contribution',
                'entity_id' => 1,
                'metadata' => json_encode(['amount' => 5000, 'category' => 'Sports']),
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => date('Y-m-d H:i:s', strtotime('-15 minutes'))
            ]
        ];
        
        // Insert sample data
        $activityModel = new ActivityModel();
        foreach ($activities as $activity) {
            $activityModel->insert($activity);
            echo "Created activity: {$activity['description']}\n";
        }
        
        echo "Sample activities created successfully!\n";
    }
    
    // Display current activities
    echo "\nCurrent activities in database:\n";
    echo "--------------------------------\n";
    
    $activities = $db->table('user_activities')
        ->select('user_activities.*, users.firstname, users.lastname')
        ->join('users', 'users.id = user_activities.user_id', 'left')
        ->orderBy('created_at', 'DESC')
        ->limit(10)
        ->get()
        ->getResultArray();
    
    foreach ($activities as $activity) {
        $userName = ($activity['firstname'] && $activity['lastname']) 
            ? $activity['firstname'] . ' ' . $activity['lastname'] 
            : 'System';
        
        echo sprintf(
            "%s | %s | %s | %s\n",
            $activity['created_at'],
            $userName,
            $activity['activity_type'],
            $activity['description']
        );
    }
    
    echo "\nSetup completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}