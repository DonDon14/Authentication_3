<?php
/**
 * Simple Database Setup Script for User Activities Table
 */

// Database connection settings - adjust these to match your setup
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'codeigniter_auth_new'; // Update this to your actual database name

echo "Setting up User Activities Table...\n";

try {
    // Create connection
    $mysqli = new mysqli($host, $username, $password, $database);
    
    // Check connection
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }
    
    echo "Connected to database successfully.\n";
    
    // Check if table exists
    $result = $mysqli->query("SHOW TABLES LIKE 'user_activities'");
    if ($result->num_rows == 0) {
        echo "Error: user_activities table does not exist. Please run migrations first.\n";
        exit(1);
    }
    
    echo "Table exists. Checking for existing data...\n";
    
    // Check if there's already data
    $result = $mysqli->query("SELECT COUNT(*) as count FROM user_activities");
    $row = $result->fetch_assoc();
    $existingCount = $row['count'];
    
    if ($existingCount > 0) {
        echo "Found {$existingCount} existing activities. Skipping sample data insertion.\n";
    } else {
        echo "No existing data found. Creating sample activities...\n";
        
        // Get a sample user ID
        $result = $mysqli->query("SELECT id FROM users LIMIT 1");
        if ($result->num_rows == 0) {
            echo "No users found. Please create a user first.\n";
            exit(1);
        }
        
        $user = $result->fetch_assoc();
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
        
        // Prepare insert statement
        $stmt = $mysqli->prepare("INSERT INTO user_activities (user_id, activity_type, description, entity_type, entity_id, metadata, ip_address, user_agent, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        // Insert sample data
        foreach ($activities as $activity) {
            $stmt->bind_param(
                "issssisss",
                $activity['user_id'],
                $activity['activity_type'],
                $activity['description'],
                $activity['entity_type'],
                $activity['entity_id'],
                $activity['metadata'],
                $activity['ip_address'],
                $activity['user_agent'],
                $activity['created_at']
            );
            
            if ($stmt->execute()) {
                echo "Created activity: {$activity['description']}\n";
            } else {
                echo "Error creating activity: " . $stmt->error . "\n";
            }
        }
        
        $stmt->close();
        echo "Sample activities created successfully!\n";
    }
    
    // Display current activities
    echo "\nCurrent activities in database:\n";
    echo "--------------------------------\n";
    
    $result = $mysqli->query("
        SELECT 
            ua.created_at, 
            ua.activity_type, 
            ua.description,
            CONCAT(COALESCE(u.firstname, ''), ' ', COALESCE(u.lastname, '')) as user_name
        FROM user_activities ua
        LEFT JOIN users u ON u.id = ua.user_id
        ORDER BY ua.created_at DESC
        LIMIT 10
    ");
    
    while ($activity = $result->fetch_assoc()) {
        $userName = trim($activity['user_name']) ?: 'System';
        
        echo sprintf(
            "%s | %s | %s | %s\n",
            $activity['created_at'],
            $userName,
            $activity['activity_type'],
            $activity['description']
        );
    }
    
    echo "\nSetup completed successfully!\n";
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}