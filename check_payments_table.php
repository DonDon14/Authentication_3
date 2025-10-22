<?php
// First check what databases exist
$mysqli = new mysqli('localhost', 'root', '');
$result = $mysqli->query('SHOW DATABASES');
echo "Available databases:\n";
while ($row = $result->fetch_assoc()) {
    echo $row['Database'] . "\n";
}
echo "\n";

// Try different possible database names
$possible_dbs = ['auth_system', 'authentication_3', 'clearpay', 'payment_system'];
$correct_db = null;

foreach ($possible_dbs as $db) {
    $mysqli->select_db($db);
    if ($mysqli->error === '') {
        $tables_result = $mysqli->query('SHOW TABLES');
        if ($tables_result && $tables_result->num_rows > 0) {
            $correct_db = $db;
            break;
        }
    }
}

if ($correct_db) {
    echo "Using database: $correct_db\n\n";
    $mysqli->select_db($correct_db);
} else {
    die('Could not find a database with payment tables');
}

$result = $mysqli->query('DESCRIBE payments');
if ($result) {
    echo "Payments table structure:\n";
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
} else {
    echo 'Error describing payments table: ' . $mysqli->error;
}

$mysqli->close();
?>