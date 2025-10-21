<?php
// Test the payment save endpoint directly
header('Content-Type: application/json');

// Simulate a payment request
$testData = [
    'contribution_id' => '1', // Make sure this exists in your contributions table
    'student_id' => 'TEST001',
    'student_name' => 'Test Student',
    'amount' => '50.00',
    'payment_method' => 'cash'
];

echo "<h1>Payment Endpoint Test</h1>";
echo "<h2>Test Data:</h2>";
echo "<pre>" . json_encode($testData, JSON_PRETTY_PRINT) . "</pre>";

// Check if we can make the request
$baseUrl = 'http://localhost' . str_replace('\\', '/', substr(__DIR__, strlen($_SERVER['DOCUMENT_ROOT'])));
$endpoint = $baseUrl . '/payments/save';

echo "<h2>Testing Endpoint: $endpoint</h2>";

// Create a curl request to test the endpoint
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $endpoint);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($testData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded',
    'X-Requested-With: XMLHttpRequest'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "<h2>Response (HTTP $httpCode):</h2>";
if ($error) {
    echo "<p style='color: red;'>CURL Error: $error</p>";
} else {
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    
    // Try to decode JSON response
    $decoded = json_decode($response, true);
    if ($decoded) {
        echo "<h3>Decoded Response:</h3>";
        echo "<pre>" . json_encode($decoded, JSON_PRETTY_PRINT) . "</pre>";
        
        // Check for QR receipt data
        if (isset($decoded['show_receipt'])) {
            echo "<h3>QR Receipt Analysis:</h3>";
            echo "<p>show_receipt: " . ($decoded['show_receipt'] ? 'TRUE' : 'FALSE') . "</p>";
            echo "<p>receipt data present: " . (isset($decoded['receipt']) ? 'YES' : 'NO') . "</p>";
            echo "<p>download URL present: " . (isset($decoded['qr_download_url']) ? 'YES' : 'NO') . "</p>";
            
            if (isset($decoded['receipt'])) {
                echo "<h4>Receipt Data:</h4>";
                echo "<pre>" . json_encode($decoded['receipt'], JSON_PRETTY_PRINT) . "</pre>";
            }
        } else {
            echo "<p style='color: orange;'>No 'show_receipt' field in response</p>";
        }
    } else {
        echo "<p style='color: red;'>Response is not valid JSON</p>";
    }
}

// Also test database connection
echo "<h2>Database Connection Test:</h2>";
try {
    $mysqli = new mysqli('localhost', 'root', '', 'codeigniter_auth_new');
    
    if ($mysqli->connect_error) {
        echo "<p style='color: red;'>Database connection failed: " . $mysqli->connect_error . "</p>";
    } else {
        echo "<p style='color: green;'>Database connection successful</p>";
        
        // Check if contributions table has data
        $result = $mysqli->query("SELECT COUNT(*) as count FROM contributions WHERE status = 'active'");
        $row = $result->fetch_assoc();
        echo "<p>Active contributions: " . $row['count'] . "</p>";
        
        // Check if we have the QR-related columns in payments
        $result = $mysqli->query("DESCRIBE payments");
        $qrColumns = [];
        while ($row = $result->fetch_assoc()) {
            if (strpos($row['Field'], 'qr') !== false || strpos($row['Field'], 'verification') !== false) {
                $qrColumns[] = $row['Field'] . ' (' . $row['Type'] . ')';
            }
        }
        echo "<p>QR-related columns in payments table: " . implode(', ', $qrColumns) . "</p>";
    }
    
    $mysqli->close();
} catch (Exception $e) {
    echo "<p style='color: red;'>Database error: " . $e->getMessage() . "</p>";
}
?>