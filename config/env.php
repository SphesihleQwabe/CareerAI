<?php
/**
 * CareerAI - Environment Variable Loader
 *
 * Reads key=value pairs from the .env file in the project root
 * and makes them available through the env() helper function.
 *
 * Usage:
 *   env('GMAIL_USERNAME')
 *   env('GMAIL_APP_PASSWORD', 'default_value')
 */

/**
 * Load the .env file once and cache the values in memory.
 */
function load_env()
{
    static $loaded = false;
    static $vars = [];

    if ($loaded) {
        return $vars;
    }

    $envFile = __DIR__ . '/../.env';

    if (!is_readable($envFile)) {
        $loaded = true;
        return $vars;
    }

    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $line = trim($line);

        // Skip comments and empty lines
        if ($line === '' || $line[0] === '#') {
            continue;
        }

        // Split into key=value at the first "=" character
        if (strpos($line, '=') === false) {
            continue;
        }

        list($key, $value) = explode('=', $line, 2);
        $key   = trim($key);
        $value = trim($value);

        // Strip surrounding quotes if present
        if (strlen($value) >= 2 &&
            ($value[0] === '"' || $value[0] === "'") &&
            $value[0] === substr($value, -1)) {
            $value = substr($value, 1, -1);
        }

        $vars[$key] = $value;
    }

    $loaded = true;
    return $vars;
}

/**
 * Get an environment variable.
 *
 * @param string $key
 * @param mixed  $default
 * @return mixed
 */
function env($key, $default = null)
{
    $vars = load_env();
    return $vars[$key] ?? $default;
}