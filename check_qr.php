<?php
// Test QR generation functionality
echo "<h1>QR Generation Debug Test</h1>";

try {
    // Check if required extensions are loaded
    echo "<h2>Extension Check:</h2>";
    
    if (extension_loaded('gd')) {
        echo "<p style='color: green;'>✅ GD extension is loaded</p>";
    } else {
        echo "<p style='color: red;'>❌ GD extension is NOT loaded - QR generation will fail!</p>";
    }
    
    if (class_exists('Endroid\QrCode\QrCode')) {
        echo "<p style='color: green;'>✅ QR Code library is available</p>";
    } else {
        echo "<p style='color: red;'>❌ QR Code library is NOT available</p>";
        
        // Check if Composer autoload exists
        if (file_exists(__DIR__ . '/vendor/autoload.php')) {
            echo "<p style='color: blue;'>ℹ️ Composer autoload file exists</p>";
            require_once __DIR__ . '/vendor/autoload.php';
            
            if (class_exists('Endroid\QrCode\QrCode')) {
                echo "<p style='color: green;'>✅ QR Code library loaded after autoload</p>";
            } else {
                echo "<p style='color: red;'>❌ QR Code library still not available after autoload</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ Composer autoload file not found</p>";
        }
    }
    
    // Check uploads directory
    echo "<h2>Uploads Directory Check:</h2>";
    $uploadsDir = __DIR__ . '/writable/uploads/';
    
    if (is_dir($uploadsDir)) {
        echo "<p style='color: green;'>✅ Uploads directory exists: $uploadsDir</p>";
        
        if (is_writable($uploadsDir)) {
            echo "<p style='color: green;'>✅ Uploads directory is writable</p>";
        } else {
            echo "<p style='color: red;'>❌ Uploads directory is NOT writable</p>";
        }
        
        // List existing files
        $files = scandir($uploadsDir);
        $files = array_filter($files, function($file) {
            return !in_array($file, ['.', '..']);
        });
        
        if (!empty($files)) {
            echo "<p>Files in uploads directory:</p>";
            echo "<ul>";
            foreach ($files as $file) {
                echo "<li>$file</li>";
            }
            echo "</ul>";
        } else {
            echo "<p style='color: orange;'>⚠️ No files in uploads directory</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Uploads directory does NOT exist: $uploadsDir</p>";
        
        // Try to create it
        if (mkdir($uploadsDir, 0755, true)) {
            echo "<p style='color: green;'>✅ Uploads directory created successfully</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to create uploads directory</p>";
        }
    }
    
    // Test basic QR generation
    echo "<h2>Basic QR Generation Test:</h2>";
    
    if (extension_loaded('gd') && class_exists('Endroid\QrCode\QrCode')) {
        try {
            $testData = [
                'payment_id' => 999,
                'student_id' => 'TEST999',
                'student_name' => 'QR Test Student',
                'amount' => '100.00',
                'verification_code' => 'TEST123'
            ];
            
            $qrContent = json_encode($testData);
            echo "<p>QR Content: " . htmlspecialchars($qrContent) . "</p>";
            
            // Generate QR Code
            $qrCode = new \Endroid\QrCode\QrCode($qrContent);
            $writer = new \Endroid\QrCode\Writer\PngWriter();
            $result = $writer->write($qrCode);
            
            // Save test QR code
            $filename = 'test_qr_' . date('Ymd_His') . '.png';
            $filepath = $uploadsDir . $filename;
            
            $bytesWritten = file_put_contents($filepath, $result->getString());
            
            if ($bytesWritten !== false && file_exists($filepath)) {
                echo "<p style='color: green;'>✅ Test QR code generated successfully!</p>";
                echo "<p>File: $filename</p>";
                echo "<p>Size: $bytesWritten bytes</p>";
                echo "<p><a href='/Authentication_3/writable/uploads/$filename' target='_blank'>View QR Code</a></p>";
            } else {
                echo "<p style='color: red;'>❌ Failed to save QR code file</p>";
            }
            
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ QR Generation Error: " . $e->getMessage() . "</p>";
            echo "<p>Stack trace:</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
        }
    } else {
        echo "<p style='color: red;'>❌ Cannot test QR generation - missing requirements</p>";
    }
    
    // Check database for existing payments
    echo "<h2>Database Payments Check:</h2>";
    
    $mysqli = new mysqli('localhost', 'root', '', 'codeigniter_auth_new');
    
    if (!$mysqli->connect_error) {
        $result = $mysqli->query("SELECT id, student_id, student_name, amount_paid, qr_receipt_path, verification_code FROM payments ORDER BY id DESC LIMIT 5");
        
        if ($result) {
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>ID</th><th>Student ID</th><th>Student Name</th><th>Amount</th><th>QR Path</th><th>Verification Code</th></tr>";
            
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . htmlspecialchars($row['student_id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['student_name']) . "</td>";
                echo "<td>$" . $row['amount_paid'] . "</td>";
                echo "<td style='color: " . ($row['qr_receipt_path'] ? 'green' : 'red') . ";'>" . 
                     ($row['qr_receipt_path'] ?: 'NULL') . "</td>";
                echo "<td style='color: " . ($row['verification_code'] ? 'green' : 'red') . ";'>" . 
                     ($row['verification_code'] ?: 'NULL') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
        $mysqli->close();
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Fatal Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='/Authentication_3/payments'>Back to Payments</a></p>";
?>