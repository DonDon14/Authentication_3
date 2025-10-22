<?php
// Database connection
$mysqli = new mysqli('localhost', 'root', '', 'codeigniter_auth_new');
if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

// Get user profile picture path
$result = $mysqli->query('SELECT id, username, profile_picture FROM users WHERE username = "Floro" LIMIT 1');
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo "User found:\n";
    echo "Username: " . $user['username'] . "\n";
    echo "Profile picture path: " . $user['profile_picture'] . "\n";
    
    // Check if file exists
    $filepath = __DIR__ . '/writable/uploads/' . $user['profile_picture'];
    echo "\nChecking file at: " . $filepath . "\n";
    echo "File exists: " . (file_exists($filepath) ? 'YES' : 'NO') . "\n";
    
    if (file_exists($filepath)) {
        echo "File size: " . filesize($filepath) . " bytes\n";
        echo "File permissions: " . substr(sprintf('%o', fileperms($filepath)), -4) . "\n";
        echo "File readable: " . (is_readable($filepath) ? 'YES' : 'NO') . "\n";
    }
} else {
    echo "User not found";
}

$mysqli->close();
?>