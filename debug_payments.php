<?php
// Debug the payments page data
echo "<h1>Payments Debug</h1>";

// Check database connection
try {
    $mysqli = new mysqli('localhost', 'root', '', 'codeigniter_auth_new');
    
    if ($mysqli->connect_error) {
        echo "<p style='color: red;'>Database connection failed: " . $mysqli->connect_error . "</p>";
        exit;
    }
    
    echo "<p style='color: green;'>✅ Database connection successful</p>";
    
    // Check contributions
    $result = $mysqli->query("SELECT * FROM contributions WHERE status = 'active'");
    
    if ($result) {
        echo "<h2>Active Contributions:</h2>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Title</th><th>Amount</th><th>Status</th><th>Created</th></tr>";
        
        $contributions = [];
        while ($row = $result->fetch_assoc()) {
            $contributions[] = $row;
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . htmlspecialchars($row['title']) . "</td>";
            echo "<td>$" . number_format($row['amount'], 2) . "</td>";
            echo "<td>" . $row['status'] . "</td>";
            echo "<td>" . $row['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        if (empty($contributions)) {
            echo "<p style='color: orange;'>⚠️ No active contributions found!</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Error querying contributions: " . $mysqli->error . "</p>";
    }
    
    // Check users
    $result = $mysqli->query("SELECT COUNT(*) as count FROM users");
    $row = $result->fetch_assoc();
    echo "<h2>Users in Database: " . $row['count'] . "</h2>";
    
    // Test the actual payments controller
    echo "<h2>Testing Payments Controller:</h2>";
    
    // Create a simple test URL
    echo "<p><a href='/Authentication_3/payments' target='_blank'>Open Payments Page</a></p>";
    
    // Show what the dropdown should look like
    if (!empty($contributions)) {
        echo "<h3>Expected Dropdown Options:</h3>";
        echo "<select>";
        echo "<option value=''>Select contribution type</option>";
        foreach ($contributions as $contrib) {
            echo "<option value='" . $contrib['id'] . "' data-amount='" . $contrib['amount'] . "'>";
            echo htmlspecialchars($contrib['title']) . " - $" . number_format($contrib['amount'], 2);
            echo "</option>";
        }
        echo "</select>";
    }
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>