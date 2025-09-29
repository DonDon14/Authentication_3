<?php
// Check if GD extension is loaded
if (extension_loaded('gd')) {
    echo "GD extension is enabled\n";
    print_r(gd_info());
} else {
    echo "GD extension is NOT enabled\n";
}

// Check available extensions
echo "\nAvailable extensions:\n";
$extensions = get_loaded_extensions();
sort($extensions);
foreach ($extensions as $ext) {
    echo "- $ext\n";
}
?>