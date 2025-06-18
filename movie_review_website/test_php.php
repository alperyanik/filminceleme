<?php
// Display all loaded extensions
echo "Loaded Extensions:\n";
print_r(get_loaded_extensions());

// Check specifically for SQL Server extensions
echo "\nSQL Server Extensions:\n";
echo "sqlsrv extension loaded: " . (extension_loaded('sqlsrv') ? 'Yes' : 'No') . "\n";
echo "pdo_sqlsrv extension loaded: " . (extension_loaded('pdo_sqlsrv') ? 'Yes' : 'No') . "\n";

// Display PHP version and architecture
echo "\nPHP Version: " . PHP_VERSION . "\n";
echo "PHP Architecture: " . (PHP_INT_SIZE * 8) . " bit\n";

// Display extension directory
echo "\nExtension Directory: " . ini_get('extension_dir') . "\n";
?> 