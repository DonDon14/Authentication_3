<?php
$mysqli = new mysqli('localhost', 'root', '', 'codeigniter_auth_new');

echo "=== CONTRIBUTIONS TABLE DATA ===\n";
$result = $mysqli->query('SELECT * FROM contributions ORDER BY id');
while ($row = $result->fetch_assoc()) {
    echo "ID: {$row['id']}\n";
    echo "Title: {$row['title']}\n";
    echo "Amount: {$row['amount']}\n";
    echo "Category: {$row['category']}\n";
    echo "Status: {$row['status']}\n";
    echo "Description: {$row['description']}\n";
    echo "Created: {$row['created_at']}\n";
    echo "-------------------\n";
}

echo "\n=== TABLE STRUCTURE ===\n";
$result = $mysqli->query('DESCRIBE contributions');
while ($row = $result->fetch_assoc()) {
    echo "{$row['Field']} - {$row['Type']} - {$row['Null']} - {$row['Default']}\n";
}

$mysqli->close();