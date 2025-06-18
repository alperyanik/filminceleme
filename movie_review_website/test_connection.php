<?php
// Display PHP and SQL Server information
echo "PHP Version: " . PHP_VERSION . "\n";
echo "PHP Architecture: " . (PHP_INT_SIZE * 8) . " bit\n\n";

// Check SQL Server extensions
echo "SQL Server Extensions:\n";
echo "sqlsrv extension loaded: " . (extension_loaded('sqlsrv') ? 'Yes' : 'No') . "\n";
echo "pdo_sqlsrv extension loaded: " . (extension_loaded('pdo_sqlsrv') ? 'Yes' : 'No') . "\n\n";

// Try to connect to the database
require_once 'config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    if ($conn) {
        echo "Database Connection: SUCCESS\n";
        
        // Test query to check database access
        $query = "SELECT @@VERSION as version";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "\nSQL Server Version:\n" . $row['version'] . "\n";
        
        // Check if our database exists
        $query = "SELECT name FROM sys.databases WHERE name = 'movie_review_db'";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "\nDatabase 'movie_review_db' exists: " . ($row ? 'Yes' : 'No') . "\n";
        
        if ($row) {
            // Check tables
            $query = "USE movie_review_db; SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE'";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            echo "\nTables in database:\n";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "- " . $row['TABLE_NAME'] . "\n";
            }
        }
    } else {
        echo "Database Connection: FAILED\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?> 