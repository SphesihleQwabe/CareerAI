<?php
/**
 * CareerAI - Shared Header
 */

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/../config/session.php';

if (!isset($page_title)) {
    $page_title = SITE_NAME . ' - ' . SITE_TAGLINE;
}

if (!isset($active_nav)) {
    $active_nav = '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($page_title); ?></title>
    <meta name="description" content="CareerAI is an AI-powered career development platform. Analyze your CV, discover job matches, prepare for interviews, and build your career with intelligent tools.">
    <link rel="icon" type="image/svg+xml" href="<?php echo url('assets/images/logo-icon.svg'); ?>">
    <link rel="stylesheet" href="<?php echo url('assets/css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo url('assets/css/auth.css?v=2'); ?>">
    <link rel="stylesheet" href="<?php echo url('assets/css/dashboard.css'); ?>">
</head>
<body>

    <!-- Skip link: hidden until focused via keyboard -->
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <header class="site-header" role="banner">
        <nav class="navbar" aria-label="Main navigation">
            <div class="nav-container">

                <a href="<?php echo url('index.php'); ?>" class="nav-logo" aria-label="<?php echo e(SITE_NAME); ?> home">
                    <img src="<?php echo url('assets/images/logo-icon.svg'); ?>"
                         alt=""
                         aria-hidden="true"
                         class="logo-mark">
                    <span class="logo-text"><?php echo e(SITE_NAME); ?></span>
                </a>

                <ul class="nav-links" id="main-nav">
                    <li><a href="<?php echo url('index.php'); ?>" class="<?php echo $active_nav === 'home' ? 'active' : ''; ?>">Home</a></li>
                    <li><a href="<?php echo url('index.php#features'); ?>" class="<?php echo $active_nav === 'features' ? 'active' : ''; ?>">Features</a></li>
                    <li><a href="<?php echo url('index.php#how-it-works'); ?>" class="<?php echo $active_nav === 'how-it-works' ? 'active' : ''; ?>">How It Works</a></li>
                    <li><a href="<?php echo url('index.php#about'); ?>" class="<?php echo $active_nav === 'about' ? 'active' : ''; ?>">About</a></li>
                </ul>

                <button class="nav-toggle" aria-label="Toggle navigation" aria-expanded="false" aria-controls="main-nav">
                    <span class="nav-toggle-bar"></span>
                    <span class="nav-toggle-bar"></span>
                    <span class="nav-toggle-bar"></span>
                </button>

                <div class="nav-actions">
                    <!-- LIVE CLOCK -->
                    <span id="live-clock" style="font-size: 13px; font-weight: 600; color: var(--secondary-text); margin-right: 16px; white-space: nowrap;"></span>

                    <?php if (!empty($_SESSION['user_id'])): ?>
                        <?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?>
                            <span style="background-color: #FDECEC; color: #B42424; border: 1px solid #F5C2C2; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 800; letter-spacing: 0.5px; margin-right: 8px;">ADMIN MODE</span>
                            <a href="<?php echo url('admin/index.php'); ?>" class="btn btn-link" style="color: var(--primary-blue); font-weight: 700;">Admin Panel</a>
                        <?php endif; ?>
                        
                        <a href="<?php echo url('pages/dashboard.php'); ?>" class="btn btn-link">Dashboard</a>
                        <a href="<?php echo url('pages/logout.php'); ?>" class="btn btn-primary">Log Out</a>
                    <?php else: ?>
                        <a href="<?php echo url('pages/login.php'); ?>" class="btn btn-link">Login</a>
                        <a href="<?php echo url('pages/register.php'); ?>" class="btn btn-primary">Get Started</a>
                    <?php endif; ?>
                </div>

            </div>
        </nav>
    </header>

    <main id="main-content" role="main">