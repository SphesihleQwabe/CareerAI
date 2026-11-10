<?php
/**
 * CareerAI - Admin Dashboard
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/auth.php'; // Security guard

// Fetch Statistics
$totalUsers = 0;
$res = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'");
if ($res) $totalUsers = $res->fetch_assoc()['count'];

$totalCvs = 0;
$res = $conn->query("SELECT COUNT(*) as count FROM cvs");
if ($res) $totalCvs = $res->fetch_assoc()['count'];

$totalChats = 0;
$res = $conn->query("SELECT COUNT(*) as count FROM chat_messages WHERE role = 'user'");
if ($res) $totalChats = $res->fetch_assoc()['count'];

// Fetch Recent Users
$recentUsers = $conn->query("SELECT first_name, last_name, email, created_at FROM users WHERE role = 'user' ORDER BY created_at DESC LIMIT 5");

$page_title = 'Admin Dashboard - ' . SITE_NAME;
$active_nav = '';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-wrapper">
    <!-- Admin Sidebar -->
    <aside class="dash-sidebar">
        <nav class="dash-menu" aria-label="Admin navigation">
            <a href="<?php echo url('admin/index.php'); ?>" class="active">Dashboard</a>
            <a href="<?php echo url('admin/users.php'); ?>">Manage Users</a>
            <a href="<?php echo url('pages/dashboard.php'); ?>">Back to User App</a>
        </nav>
    </aside>

    <main class="dash-content" id="main-content">
        <div class="dash-header">
            <h1>Admin Overview</h1>
            <p>Welcome to the system administration panel.</p>
        </div>

        <!-- Stats Grid -->
        <div class="preview-stats" style="grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 32px; max-width: 900px;">
            <div class="preview-stat-card" style="padding: 24px; text-align: left;">
                <p class="stat-label" style="font-size: 14px;">Total Users</p>
                <p class="stat-value" style="font-size: 32px;"><?php echo $totalUsers; ?></p>
            </div>
            <div class="preview-stat-card" style="padding: 24px; text-align: left;">
                <p class="stat-label" style="font-size: 14px;">CVs Uploaded</p>
                <p class="stat-value" style="font-size: 32px;"><?php echo $totalCvs; ?></p>
            </div>
            <div class="preview-stat-card" style="padding: 24px; text-align: left;">
                <p class="stat-label" style="font-size: 14px;">AI Chats</p>
                <p class="stat-value" style="font-size: 32px;"><?php echo $totalChats; ?></p>
            </div>
        </div>

        <!-- Recent Users Table -->
        <div class="dash-card" style="max-width: 900px;">
            <h2 style="font-size: 18px; margin-bottom: 16px;">Recently Registered Users</h2>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--border-color);">
                            <th style="padding: 12px 8px; color: var(--secondary-text); font-size: 13px;">Name</th>
                            <th style="padding: 12px 8px; color: var(--secondary-text); font-size: 13px;">Email</th>
                            <th style="padding: 12px 8px; color: var(--secondary-text); font-size: 13px;">Date & Time Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($recentUsers && $recentUsers->num_rows > 0) {
                            while($row = $recentUsers->fetch_assoc()): ?>
                                <tr style="border-bottom: 1px solid var(--border-color);">
                                    <td style="padding: 12px 8px; font-size: 14px;"><?php echo e($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                    <td style="padding: 12px 8px; font-size: 14px;"><?php echo e($row['email']); ?></td>
                                    <td style="padding: 12px 8px; font-size: 14px; color: var(--secondary-text);">
                                        <!-- THIS IS THE UPDATED LINE -->
                                        <?php echo date('d M Y, H:i', strtotime($row['created_at'])); ?>
                                    </td>
                                </tr>
                            <?php endwhile; 
                        } else {
                            echo '<tr><td colspan="3" style="padding: 16px; text-align: center; color: var(--secondary-text);">No users found.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>