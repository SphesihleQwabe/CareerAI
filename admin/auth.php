<?php
/**
 * CareerAI - Admin Security Check
 * Include this file at the top of EVERY page inside the /admin/ folder.
 */

require_once __DIR__ . '/../config/session.php';

// Check if user is logged in AND has the admin role
if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    // If not an admin, redirect them to the normal user dashboard
    header('Location: ' . url('pages/dashboard.php'));
    exit;
}