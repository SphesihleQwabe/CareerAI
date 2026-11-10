<?php
/**
 * CareerAI - Secure Session Bootstrap
 *
 * Starts a PHP session with secure settings.
 * Include this at the top of every page that needs a session.
 */

// Only start the session if it is not already active
if (session_status() === PHP_SESSION_NONE) {

    // Secure session cookie settings
    session_set_cookie_params([
        'lifetime' => 0,          // Session ends when browser closes
        'path'     => '/',
        'domain'   => '',
        'secure'   => false,      // Set to true when using HTTPS in production
        'httponly' => true,       // JavaScript cannot read the cookie (prevents XSS theft)
        'samesite' => 'Lax',      // Basic CSRF protection
    ]);

    session_start();
}