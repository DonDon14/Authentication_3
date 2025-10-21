<?php
$mysqli = new mysqli('localhost', 'root', '', 'codeigniter_auth_new');
if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

echo "Users table structure:\n";
$result = $mysqli->query('DESCRIBE users');
while ($row = $result->fetch_assoc()) {
    echo $row['Field'] . ' - ' . $row['Type'] . "\n";
}

echo "\nSample users data:\n";
$result = $mysqli->query('SELECT * FROM users LIMIT 1');
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    foreach ($user as $key => $value) {
        echo "$key: $value\n";
    }
}

$mysqli->close();
?>