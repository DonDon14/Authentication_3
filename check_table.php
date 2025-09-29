<?php
require_once 'vendor/autoload.php';

// Load CodeIgniter
$app = require_once FCPATH . '../app/Config/Boot.php';

// Get database instance
$db = \Config\Database::connect();

// Check if contributions table exists and show structure
if ($db->tableExists('contributions')) {
    echo "✅ Contributions table exists!\n\n";
    
    // Get table fields
    $fields = $db->getFieldData('contributions');
    
    echo "Table Structure:\n";
    echo "================\n";
    foreach ($fields as $field) {
        echo "Field: {$field->name}\n";
        echo "Type: {$field->type}\n";
        echo "Max Length: " . ($field->max_length ?? 'N/A') . "\n";
        echo "Nullable: " . ($field->nullable ? 'YES' : 'NO') . "\n";
        echo "Default: " . ($field->default ?? 'NULL') . "\n";
        echo "---\n";
    }
} else {
    echo "❌ Contributions table does not exist!\n";
}
?>