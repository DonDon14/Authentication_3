<?php
$mysqli = new mysqli('localhost', 'root', '', 'codeigniter_auth_new');

echo "=== CHECKING CONTRIBUTION STATUS ===\n";
$result = $mysqli->query('SELECT * FROM contributions WHERE id = 1');
if ($row = $result->fetch_assoc()) {
    echo "ID: " . $row['id'] . "\n";
    echo "Title: " . $row['title'] . "\n";
    echo "Status: " . $row['status'] . "\n";
    echo "Amount: " . $row['amount'] . "\n";
} else {
    echo "No contribution found with ID 1\n";
}

$mysqli->close();