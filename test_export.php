<?php
// Simple test file to debug export functionality
require_once 'vendor/autoload.php';

echo "<h2>Export Test</h2>";
echo "<p>Base URL test: " . base_url('payments/export') . "</p>";

// Test if we can access the controller
try {
    $controller = new \App\Controllers\Payments();
    echo "<p>Controller loaded successfully</p>";
    
    // Test export method exists
    if (method_exists($controller, 'exportPayments')) {
        echo "<p>exportPayments method exists</p>";
    } else {
        echo "<p>ERROR: exportPayments method does not exist</p>";
    }
    
} catch (Exception $e) {
    echo "<p>ERROR loading controller: " . $e->getMessage() . "</p>";
}

echo "<br><a href='payments/export' target='_blank'>Test Export Link</a>";
?>