<?php
/**
 * Database Connection Module
 * Modified to read credentials from environment variables for containerized deployments.
 * Falls back to sensible defaults for local development.
 */

// Check if a database connection handle ($con) is already active
if (!isset($con)) {
    // Read configuration from environment variables (set these in your Docker Compose or environment)
    $host     = getenv('DB_HOST') !== false ? getenv('DB_HOST') : 'localhost';
    $user     = getenv('DB_USER') !== false ? getenv('DB_USER') : 'root';
    $password = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
    $database = getenv('DB_NAME') !== false ? getenv('DB_NAME') : 'projects';

    // Attempt establishing connection to MySQL server
    $con = @mysqli_connect($host, $user, $password, $database);

    // Validate database connection success
    if (!$con) {
        // Log connection failure to error log for server debugging
        error_log("Database Connection Failure: " . mysqli_connect_error());

        // If request is an AJAX/API JSON call, return structured JSON error payload
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Database Connection Failed']);
            exit();
        } else {
            // Render user-friendly alert for standard web browser views
            echo '<script>alert("Database Connection Unsuccessful! Please check database server.");</script>';
        }
    } else {
        // Set charset to utf8mb4 for full UTF-8 support (emojis, special characters) and optimized performance
        mysqli_set_charset($con, "utf8mb4");
    }
}
?>
