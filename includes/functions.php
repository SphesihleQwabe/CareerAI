<?php
/**
 * CareerAI - Reusable Helper Functions
 *
 * This file contains small utility functions used across the application.
 * As the project grows, more helpers will be added here.
 */

// Ensure config is loaded
require_once __DIR__ . '/../config/config.php';

/**
 * Build a full application URL.
 *
 * Usage:
 *   url('pages/login.php')  ->  http://localhost/CareerAI/pages/login.php
 *   url('index.php')        ->  http://localhost/CareerAI/index.php
 *
 * @param string $path  Relative path inside the project.
 * @return string       Full URL.
 */
function url($path = '')
{
    $path = ltrim($path, '/');
    return rtrim(SITE_URL, '/') . '/' . $path;
}

/**
 * Escape output for safe HTML rendering.
 *
 * Usage:
 *   echo e($userInput);  // Prevents XSS
 *
 * @param string $value
 * @return string
 */
function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}