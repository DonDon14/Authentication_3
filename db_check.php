<?php
// Simple database check
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'codeigniter_auth';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Database connection successful!\n\n";
    
    // Check if contributions table exists and show structure
    $result = $pdo->query("SHOW TABLES LIKE 'contributions'");
    if ($result->rowCount() > 0) {
        echo "✅ Contributions table exists!\n\n";
        
        // Get table structure
        $stmt = $pdo->query("DESCRIBE contributions");
        $fields = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Table Structure:\n";
        echo "================\n";
        foreach ($fields as $field) {
            echo "Field: {$field['Field']}\n";
            echo "Type: {$field['Type']}\n";
            echo "Nullable: {$field['Null']}\n";
            echo "Default: " . ($field['Default'] ?? 'NULL') . "\n";
            echo "Extra: {$field['Extra']}\n";
            echo "---\n";
        }
        
        // Check current data
        echo "\nCurrent Data:\n";
        echo "=============\n";
        $stmt = $pdo->query("SELECT * FROM contributions ORDER BY created_at DESC");
        $contributions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($contributions)) {
            echo "No data found in contributions table.\n";
        } else {
            foreach ($contributions as $contribution) {
                echo "ID: {$contribution['id']}\n";
                echo "Title: {$contribution['title']}\n";
                echo "Amount: {$contribution['amount']}\n";
                echo "Category: {$contribution['category']}\n";
                echo "Status: {$contribution['status']}\n";
                echo "Created: {$contribution['created_at']}\n";
                echo "---\n";
            }
        }
    } else {
        echo "❌ Contributions table does not exist!\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}
?>