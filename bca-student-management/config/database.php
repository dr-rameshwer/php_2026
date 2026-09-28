<?php
/**
 * BCA Student Management System
 * Database Configuration & PDO Initialization File
 * 
 * Purpose: Establishes a persistent, secure, exception-enabled PDO connection
 * to the MySQL database.
 */

declare(strict_types=1);

// Prevent direct browser access to config file if requested directly
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    header("HTTP/1.1 403 Forbidden");
    exit("Direct access to configuration files is prohibited.");
}

// Database Credentials (Standard XAMPP / WAMP defaults)
$dbHost     = "127.0.0.1";
$dbPort     = "3306";
$dbName     = "bca_student_management";
$dbUser     = "root";
$dbPassword = "";
$dbCharset  = "utf8mb4";

// Construct Data Source Name (DSN)
$dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset={$dbCharset}";

// Robust PDO Configuration Options
$pdoOptions = [
    // Throw standard PHP PDOException instances on any query error
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    
    // Always return rows as associative arrays (column_name => value)
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    
    // Disable emulation of prepared statements to enforce genuine server-side prepares
    PDO::ATTR_EMULATE_PREPARES   => false,
    
    // Ensure column names maintain exact casing as defined in MySQL schema
    PDO::ATTR_CASE               => PDO::CASE_NATURAL,
];

try {
    // Instantiate PDO instance
    $pdo = new PDO($dsn, $dbUser, $dbPassword, $pdoOptions);
} catch (PDOException $e) {
    // Log technical error internally on the server
    error_log("[Database Connection Error] " . $e->getMessage());
    
    // Display friendly, non-technical error to visitors without leaking credentials
    http_response_code(500);
    die("
        <div style='font-family: Arial, sans-serif; padding: 2rem; max-width: 600px; margin: 3rem auto; border: 1px solid #f5c6cb; background-color: #f8d7da; color: #721c24; border-radius: 8px;'>
            <h3 style='margin-top: 0;'>Database Connection Failed</h3>
            <p>The application could not connect to the MySQL database. Please verify:</p>
            <ul>
                <li>MySQL service is started in your XAMPP Control Panel.</li>
                <li>The database <code>bca_student_management</code> has been imported using <code>database/schema.sql</code>.</li>
                <li>Database credentials in <code>config/database.php</code> match your local environment.</li>
            </ul>
        </div>
    ");
}
