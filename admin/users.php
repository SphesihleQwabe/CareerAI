<?php
/**
 * CareerAI - Admin Manage Users
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/auth.php'; // <--- The security guard

// Handle Actions (Delete, Toggle Active)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['target_id'])) {
    $targetId = (int)$_POST['target_id'];
    
    // Prevent admin from deleting or disabling themselves
    if ($targetId !== (int)$_SESSION['user_id']) {
        if ($_POST['action'] === 'delete') {
            // Deleting the user will CASCADE and delete their profile, cv, etc.
            $stmt = $conn->prepare('DELETE FROM users WHERE id = ?');
            $stmt->bind_param('i', $targetId);
            $stmt->execute();
            $stmt->close();
        } elseif ($_POST['action'] === 'toggle_active') {
            $stmt = $conn->prepare('UPDATE users SET is_active = NOT is_active WHERE id = ?');
            $stmt->bind_param('i', $targetId);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    // Refresh to prevent form resubmission
    header('Location: ' . url('admin/users.php'));
    exit;
}

// Fetch all users
$users = $conn->query("SELECT id, first_name, last_name, email, phone, role, is_active, created_at FROM users ORDER BY created_at DESC");

$page_title = 'Manage Users - ' . SITE_NAME;
$active_nav = '';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-wrapper">
    <aside class="dash-sidebar">
        <nav class="dash-menu" aria-label="Admin navigation">
            <a href="<?php echo url('admin/index.php'); ?>">Dashboard</a>
            <a href="<?php echo url('admin/users.php'); ?>" class="active">Manage Users</a>
            <a href="<?php echo url('pages/dashboard.php'); ?>">Back to User App</a>
        </nav>
    </aside>

    <main class="dash-content" id="main-content">
        <div class="dash-header">
            <h1>Manage Users</h1>
            <p>View, disable, or delete registered users.</p>
        </div>

        <div class="dash-card" style="max-width: 100%; overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; min-width: 800px;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border-color);">
                        <th style="padding: 12px 8px; color: var(--secondary-text); font-size: 13px;">User Details</th>
                        <th style="padding: 12px 8px; color: var(--secondary-text); font-size: 13px;">Contact</th>
                        <th style="padding: 12px 8px; color: var(--secondary-text); font-size: 13px;">Status</th>
                        <th style="padding: 12px 8px; color: var(--secondary-text); font-size: 13px;">Role</th>
                        <th style="padding: 12px 8px; color: var(--secondary-text); font-size: 13px; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($u = $users->fetch_assoc()): ?>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 16px 8px;">
                                <strong style="color: var(--dark-navy);"><?php echo e($u['first_name'] . ' ' . $u['last_name']); ?></strong><br>
                                <!-- THIS IS THE UPDATED LINE -->
                                <span style="font-size: 12px; color: var(--secondary-text);">Joined: <?php echo date('d M Y, H:i', strtotime($u['created_at'])); ?></span>
                            </td>
                            <td style="padding: 16px 8px; font-size: 14px;">
                                <?php echo e($u['email']); ?><br>
                                <span style="font-size: 12px; color: var(--secondary-text);"><?php echo e($u['phone']); ?></span>
                            </td>
                            <td style="padding: 16px 8px;">
                                <?php if ($u['is_active']): ?>
                                    <span style="background: #E8F8F0; color: #0F7A47; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Active</span>
                                <?php else: ?>
                                    <span style="background: #FDECEC; color: #B42424; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Disabled</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 16px 8px;">
                                <span style="font-size: 13px; font-weight: 600; text-transform: capitalize; color: <?php echo $u['role'] === 'admin' ? 'var(--primary-blue)' : 'var(--main-text)'; ?>;">
                                    <?php echo $u['role']; ?>
                                </span>
                            </td>
                            <td style="padding: 16px 8px; text-align: right;">
                                <?php if ($u['id'] == $_SESSION['user_id']): ?>
                                    <span style="font-size: 13px; color: var(--secondary-text); font-style: italic;">Current User</span>
                                <?php else: ?>
                                    <form method="POST" style="display: inline-block;">
                                        <input type="hidden" name="target_id" value="<?php echo $u['id']; ?>">
                                        <input type="hidden" name="action" value="toggle_active">
                                        <button type="submit" class="btn btn-outline" style="padding: 6px 12px; font-size: 12px; margin-bottom: 4px;">
                                            <?php echo $u['is_active'] ? 'Disable' : 'Enable'; ?>
                                        </button>
                                    </form>
                                    
                                    <form method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to permanently delete this user? All their data will be lost.');">
                                        <input type="hidden" name="target_id" value="<?php echo $u['id']; ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <button type="submit" class="btn btn-primary" style="padding: 6px 12px; font-size: 12px; background-color: #B42424; border-color: #B42424;">
                                            Delete
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>