<?php
// Simple CLI script to create migrations table
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'codeigniter_auth';

try {
    // Connect to MySQL
    $mysqli = new mysqli($host, $username, $password);
    
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }
    
    // Create database if it doesn't exist
    $result = $mysqli->query("CREATE DATABASE IF NOT EXISTS `$database`");
    if ($result) {
        echo "✅ Database '$database' created or already exists\n";
    }
    
    // Select the database
    $mysqli->select_db($database);
    
    // Create migrations table
    $sql = "CREATE TABLE IF NOT EXISTS `migrations` (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `version` varchar(255) NOT NULL,
        `class` varchar(255) NOT NULL,
        `group` varchar(255) NOT NULL,
        `namespace` varchar(255) NOT NULL,
        `time` int(11) NOT NULL,
        `batch` int(11) unsigned NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    
    if ($mysqli->query($sql)) {
        echo "✅ Migrations table created successfully\n";
    } else {
        echo "❌ Error creating migrations table: " . $mysqli->error . "\n";
    }
    
    // Check if migrations table exists and show structure
    $result = $mysqli->query("SHOW TABLES LIKE 'migrations'");
    if ($result && $result->num_rows > 0) {
        echo "\n✅ Migrations table confirmed to exist\n";
        
        $result = $mysqli->query("DESCRIBE migrations");
        if ($result && $result->num_rows > 0) {
            echo "\n📋 Migrations table structure:\n";
            while ($row = $result->fetch_assoc()) {
                echo "- {$row['Field']} ({$row['Type']})\n";
            }
        }
    } else {
        echo "\n❌ Migrations table was not created successfully\n";
        echo "Let me try a different approach...\n";
        
        // Try creating with simpler syntax
        $simpleSql = "CREATE TABLE `migrations` (
            `id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `version` varchar(255) NOT NULL,
            `class` varchar(255) NOT NULL,
            `group` varchar(255) NOT NULL,
            `namespace` varchar(255) NOT NULL,
            `time` int NOT NULL,
            `batch` int unsigned NOT NULL,
            PRIMARY KEY (`id`)
        )";
        
        if ($mysqli->query($simpleSql)) {
            echo "✅ Migrations table created with simpler syntax\n";
        } else {
            echo "❌ Still failed: " . $mysqli->error . "\n";
        }
    }
    
    $mysqli->close();
    echo "\n✅ Setup complete! You can now run 'php spark migrate'\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>