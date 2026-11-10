<?php
/**
 * CareerAI - Global Configuration
 *
 * This file defines site-wide constants and paths.
 * It should be included at the top of every page.
 *
 * NOTE: In a later phase, sensitive values (database credentials,
 * AI API keys) will be moved into a .env file for security.
 * For now, this file only holds non-sensitive configuration.
 */

// Prevent direct browser access to this file
if (!defined('CAREERAI_APP')) {
    define('CAREERAI_APP', true);
}

// =============================
// SITE INFORMATION
// =============================
define('SITE_NAME', 'CareerAI');
define('SITE_TAGLINE', 'Your Future, Powered by AI');
define('SITE_URL', 'http://localhost/CareerAI');

// =============================
// FILESYSTEM PATHS
// =============================
// BASE_PATH is the absolute path on disk to the project root.
define('BASE_PATH', dirname(__DIR__));

// Path to the uploads directory (for CVs, later)
define('UPLOAD_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'uploads');

// =============================
// APPLICATION SETTINGS
// =============================
// Debug mode: set to false in production.
// When true, PHP errors are displayed. When false, they are hidden.
define('APP_DEBUG', true);

// Timezone
date_default_timezone_set('Africa/Johannesburg');

// Error reporting based on debug mode
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}