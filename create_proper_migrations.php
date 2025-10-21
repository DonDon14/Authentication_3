<?php
// Create proper migrations table for CodeIgniter
$mysqli = new mysqli('localhost', 'root', '', 'codeigniter_auth_new');

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Drop and recreate migrations table with correct structure
$mysqli->query("DROP TABLE IF EXISTS migrations");

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
    echo "✅ Migrations table created successfully with correct structure!\n";
} else {
    echo "❌ Error: " . $mysqli->error . "\n";
}

$mysqli->close();
?>