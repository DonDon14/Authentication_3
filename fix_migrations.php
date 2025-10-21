<?php
// Script to properly clean up and create migrations table
$mysqli = new mysqli('localhost', 'root', '', 'codeigniter_auth');

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

echo "Cleaning up existing migrations table...\n";

// Force drop table
$mysqli->query("SET FOREIGN_KEY_CHECKS = 0");
$mysqli->query("DROP TABLE IF EXISTS migrations");
$mysqli->query("SET FOREIGN_KEY_CHECKS = 1");

echo "Creating new migrations table...\n";

$sql = "CREATE TABLE migrations (
    id bigint unsigned NOT NULL AUTO_INCREMENT,
    version varchar(255) NOT NULL,
    class varchar(255) NOT NULL,
    `group` varchar(255) NOT NULL,
    namespace varchar(255) NOT NULL,
    time int NOT NULL,
    batch int unsigned NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($mysqli->query($sql)) {
    echo "✅ Migrations table created successfully!\n";
    
    // Verify it exists
    $result = $mysqli->query("SHOW TABLES LIKE 'migrations'");
    if ($result->num_rows > 0) {
        echo "✅ Verified: migrations table exists\n";
    }
} else {
    echo "❌ Error creating migrations table: " . $mysqli->error . "\n";
}

$mysqli->close();
echo "\nNow try running: php spark migrate\n";
?>