<?php
$mysqli = new mysqli('localhost', 'root', '', 'codeigniter_auth_new');

echo "=== USER DETAILS ===\n";
$result = $mysqli->query('SELECT * FROM users WHERE id = 1');
if ($user = $result->fetch_assoc()) {
    echo "User ID 1 found:\n";
    foreach ($user as $key => $value) {
        echo "  $key: $value\n";
    }
} else {
    echo "No user found with ID 1\n";
}

echo "\n=== RECENT ACTIVITIES ===\n";
$result = $mysqli->query('
    SELECT 
        ua.created_at, 
        ua.activity_type, 
        ua.description,
        ua.user_id
    FROM user_activities ua
    ORDER BY ua.created_at DESC
    LIMIT 5
');

while ($activity = $result->fetch_assoc()) {
    echo sprintf(
        "%s | User %s | %s | %s\n",
        $activity['created_at'],
        $activity['user_id'],
        $activity['activity_type'],
        $activity['description']
    );
}

$mysqli->close();