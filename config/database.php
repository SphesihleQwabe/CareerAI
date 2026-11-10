<?php
/**
 * CareerAI - Database Connection
 *
 * Creates a single MySQLi connection that is reused across the application.
 * Uses prepared statements and strict error handling.
 *
 * SECURITY NOTE:
 * Never store credentials directly in source code in production.
 * Later we will move these to a .env file.
 */

// Load global configuration first
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../includes/functions.php';

// =============================
// DATABASE CREDENTIALS
// =============================
// Default XAMPP credentials. Change these for production.
$db_host = '127.0.0.1';
$db_user = 'root';
$db_pass = '';
$db_name = 'careerai';
$db_port = 3306;

// =============================
// CREATE CONNECTION
// =============================
// Suppress default warnings so we can throw a clean error.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);

    // Set the character set so accented characters and Unicode work correctly.
    $conn->set_charset('utf8mb4');

} catch (mysqli_sql_exception $e) {
    // Log the real error internally but do not show details to the user.
    // In debug mode, we show the error; otherwise, a generic message.
    if (APP_DEBUG) {
        die('Database connection failed: ' . $e->getMessage());
    } else {
        die('We are experiencing technical difficulties. Please try again later.');
    }
}