<?php

// Simple script to create the user_activities table
// Run this from your browser: http://localhost/Authentication_3/create_activities_table.php

require_once 'vendor/autoload.php';

$config = new \Config\Database();
$db = \CodeIgniter\Database\Config::connect();

// Check if table exists
$query = $db->query("SHOW TABLES LIKE 'user_activities'");
if ($query->getNumRows() > 0) {
    echo "Table 'user_activities' already exists.<br>";
} else {
    // Create the table
    $sql = "
    CREATE TABLE `user_activities` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `user_id` int(11) unsigned DEFAULT NULL,
        `activity_type` varchar(50) NOT NULL,
        `description` text NOT NULL,
        `entity_type` varchar(50) DEFAULT NULL,
        `entity_id` int(11) unsigned DEFAULT NULL,
        `metadata` json DEFAULT NULL,
        `ip_address` varchar(45) DEFAULT NULL,
        `user_agent` text DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_user_id` (`user_id`),
        KEY `idx_activity_type` (`activity_type`),
        KEY `idx_created_at` (`created_at`),
        KEY `idx_entity` (`entity_type`, `entity_id`),
        CONSTRAINT `fk_user_activities_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    try {
        $db->query($sql);
        echo "Table 'user_activities' created successfully!<br>";
        
        // Insert some sample activities for testing
        $sampleActivities = [
            [
                'user_id' => 1, // Assuming admin user has ID 1
                'activity_type' => 'login',
                'description' => 'Administrator logged into the system',
                'entity_type' => 'user',
                'entity_id' => 1,
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours'))
            ],
            [
                'user_id' => 1,
                'activity_type' => 'payment_created',
                'description' => 'New payment recorded for John Doe - $25.00',
                'entity_type' => 'payment',
                'entity_id' => 1,
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 hour'))
            ],
            [
                'user_id' => 1,
                'activity_type' => 'qr_generated',
                'description' => 'QR receipt generated for payment #1',
                'entity_type' => 'payment',
                'entity_id' => 1,
                'created_at' => date('Y-m-d H:i:s', strtotime('-30 minutes'))
            ]
        ];
        
        foreach ($sampleActivities as $activity) {
            $db->table('user_activities')->insert($activity);
        }
        
        echo "Sample activities inserted successfully!<br>";
        echo "<br><strong>Setup Complete!</strong> You can now visit your dashboard to see the activity timeline in action.";
        
    } catch (Exception $e) {
        echo "Error creating table: " . $e->getMessage() . "<br>";
    }
}

echo "<br><br><a href='" . base_url('dashboard') . "'>← Back to Dashboard</a>";
?>