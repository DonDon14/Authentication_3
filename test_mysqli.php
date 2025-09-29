<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "PHP MySQLi Test\n";
echo "===============\n\n";

// Database connection details
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'codeigniter_auth';

// Test mysqli extension
if (!extension_loaded('mysqli')) {
    echo "❌ MySQLi extension is not loaded\n";
    exit(1);
}

echo "✅ MySQLi extension is loaded\n\n";

// Connect to MySQL server (without database first)
$mysqli = new mysqli($host, $username, $password);

if ($mysqli->connect_error) {
    echo "❌ Connection failed: " . $mysqli->connect_error . "\n";
    exit(1);
}

echo "✅ Connected to MySQL server\n\n";

// Check if database exists
$result = $mysqli->query("SHOW DATABASES LIKE '$database'");
if ($result->num_rows > 0) {
    echo "✅ Database '$database' exists\n\n";
} else {
    echo "❌ Database '$database' does not exist\n";
    
    // Show all databases
    echo "Available databases:\n";
    $result = $mysqli->query("SHOW DATABASES");
    while ($row = $result->fetch_row()) {
        echo "- " . $row[0] . "\n";
    }
    exit(1);
}

// Connect to specific database
$mysqli->select_db($database);

// Check if contributions table exists
$result = $mysqli->query("SHOW TABLES LIKE 'contributions'");
if ($result->num_rows > 0) {
    echo "✅ Table 'contributions' exists\n\n";
    
    // Show table structure
    echo "Table structure:\n";
    $result = $mysqli->query("DESCRIBE contributions");
    while ($row = $result->fetch_assoc()) {
        echo "- {$row['Field']} ({$row['Type']})\n";
    }
    
    echo "\n";
    
    // Check current data
    $result = $mysqli->query("SELECT COUNT(*) as count FROM contributions");
    $row = $result->fetch_assoc();
    echo "Current records: " . $row['count'] . "\n\n";
    
    // Try to insert test data
    echo "Testing data insertion...\n";
    $title = 'Test Contribution';
    $description = 'Test Description';
    $amount = 25.50;
    $category = 'Test';
    $status = 'active';
    $created_at = date('Y-m-d H:i:s');
    
    $stmt = $mysqli->prepare("INSERT INTO contributions (title, description, amount, category, status, created_at) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdsss", $title, $description, $amount, $category, $status, $created_at);
    
    if ($stmt->execute()) {
        $insertId = $mysqli->insert_id;
        echo "✅ Test data inserted successfully! ID: $insertId\n";
        
        // Verify insertion
        $result = $mysqli->query("SELECT * FROM contributions WHERE id = $insertId");
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo "✅ Verification successful:\n";
            echo "  Title: {$row['title']}\n";
            echo "  Amount: {$row['amount']}\n";
            echo "  Category: {$row['category']}\n";
            
            // Clean up
            $mysqli->query("DELETE FROM contributions WHERE id = $insertId");
            echo "✅ Test data cleaned up\n";
        }
    } else {
        echo "❌ Failed to insert test data: " . $stmt->error . "\n";
    }
    
} else {
    echo "❌ Table 'contributions' does not exist\n";
    
    // Show all tables
    echo "Available tables:\n";
    $result = $mysqli->query("SHOW TABLES");
    while ($row = $result->fetch_row()) {
        echo "- " . $row[0] . "\n";
    }
}

$mysqli->close();
echo "\nDatabase test completed.\n";
?>