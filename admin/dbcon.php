<?php
/**
 * admin/dbcon.php
 * Environment-variable-based DB connection with safe defaults for local/docker development.
 */

if (!isset($con)) {
    // Read from environment variables (preferred). Set these in your hosting panel or CI environment.
    $host     = getenv('DB_HOST') !== false ? getenv('DB_HOST') : 'db';
    $user     = getenv('DB_USER') !== false ? getenv('DB_USER') : 'user';
    $password = getenv('DB_PASS') !== false ? getenv('DB_PASS') : 'password';
    $database = getenv('DB_NAME') !== false ? getenv('DB_NAME') : 'projects';

    // Try to connect (suppress warnings, handle error below)
    $con = @mysqli_connect($host, $user, $password, $database);

    if (!$con) {
        // Log the error to server logs
        error_log("Database Connection Failure: " . mysqli_connect_error());

        // If AJAX/XHR, return JSON
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Database Connection Failed']);
            exit();
        } else {
            // Friendly alert for browser
            echo '<script>alert("Database Connection Unsuccessful! Please check database credentials.");</script>';
            // Do not exit to allow pages to show a friendly message, but you can uncomment exit() for stricter behavior
            // exit();
        }
    } else {
        // Set proper charset
        mysqli_set_charset($con, "utf8mb4");
    }
}
?>