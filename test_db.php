<?php
// Load CodeIgniter bootstrap
require_once 'public/index.php';

// Get the CodeIgniter app instance
$app = \Config\Services::codeigniter();

// Test database connection
try {
    $db = \Config\Database::connect();
    
    echo "✅ CodeIgniter database connection successful!\n\n";
    
    // Check if table exists
    if ($db->tableExists('contributions')) {
        echo "✅ Contributions table exists!\n\n";
        
        // Get current data
        $query = $db->query("SELECT COUNT(*) as count FROM contributions");
        $result = $query->getRow();
        echo "Current contributions count: " . $result->count . "\n\n";
        
        // Test inserting data
        echo "Testing data insertion...\n";
        $testData = [
            'title' => 'Test Contribution',
            'description' => 'This is a test contribution',
            'amount' => 50.00,
            'category' => 'Test',
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        if ($db->table('contributions')->insert($testData)) {
            echo "✅ Test data inserted successfully!\n";
            $insertId = $db->insertID();
            echo "Inserted ID: $insertId\n\n";
            
            // Verify the data was inserted
            $query = $db->query("SELECT * FROM contributions WHERE id = ?", [$insertId]);
            $inserted = $query->getRow();
            if ($inserted) {
                echo "✅ Data verification successful:\n";
                echo "Title: " . $inserted->title . "\n";
                echo "Amount: " . $inserted->amount . "\n";
                echo "Category: " . $inserted->category . "\n";
                
                // Clean up test data
                $db->query("DELETE FROM contributions WHERE id = ?", [$insertId]);
                echo "✅ Test data cleaned up\n";
            } else {
                echo "❌ Could not verify inserted data\n";
            }
        } else {
            echo "❌ Failed to insert test data\n";
            echo "Error: " . $db->error() . "\n";
        }
        
    } else {
        echo "❌ Contributions table does not exist!\n";
    }
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}
?>