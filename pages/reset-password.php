<?php
/**
 * CareerAI - Reset Password
 *
 * User arrives from the emailed link with ?token=...&email=...
 * We validate the token and expiry, then show a new-password form.
 * On submit, we update the password hash and clear the token.
 *
 * Enforces the same password rules as registration (uppercase,
 * lowercase, digit, special char, min 8 chars).
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

$errors     = [];
$email      = trim($_GET['email'] ?? $_POST['email'] ?? '');
$token      = trim($_GET['token'] ?? $_POST['token'] ?? '');
$validToken = false;
$user       = null;

// ----- Validate the token -----
if ($email === '' || $token === '') {
    $errors[] = 'This reset link is invalid or incomplete.';
} else {
    $stmt = $conn->prepare(
        'SELECT id, first_name, reset_token, reset_expires_at
         FROM users WHERE email = ? LIMIT 1'
    );
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$user || empty($user['reset_token'])) {
        $errors[] = 'This reset link is invalid. Please request a new one.';
    } elseif (!hash_equals($user['reset_token'], $token)) {
        $errors[] = 'This reset link is invalid. Please request a new one.';
    } elseif (strtotime($user['reset_expires_at']) < time()) {
        $errors[] = 'This reset link has expired. Please request a new one.';
    } else {
        $validToken = true;
    }
}

// ----- Handle new password submission -----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validToken) {

    $password = $_POST['password']         ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if ($password === '') {
        $errors[] = 'Password is required.';
    } else {
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters.';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter.';
        }
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter.';
        }
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number.';
        }
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = 'Password must contain at least one special character.';
        }
    }

    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    // ----- Update password and clear token -----
    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare(
            'UPDATE users
             SET password_hash = ?, reset_token = "", reset_expires_at = NULL
             WHERE id = ? LIMIT 1'
        );
        $stmt->bind_param('si', $hash, $user['id']);
        $stmt->execute();
        $stmt->close();

        header('Location: ' . url('pages/login.php?reset=1'));
        exit;
    }
}

$page_title = 'Reset Password - ' . SITE_NAME;
$active_nav = '';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="auth-wrapper">
    <div class="auth-card">

        <div class="auth-header">
            <h1>Choose a New Password</h1>
            <p>Enter a strong new password for your account</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $err): ?>
                        <li><?php echo e($err); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($validToken): ?>
        <form class="auth-form" method="POST"
              action="<?php echo url('pages/reset-password.php'); ?>" novalidate>

            <input type="hidden" name="email" value="<?php echo e($email); ?>">
            <input type="hidden" name="token" value="<?php echo e($token); ?>">

            <div class="form-group">
                <label for="password">New Password</label>
                <div class="password-field">
                    <input type="password" id="password" name="password"
                           autocomplete="new-password" required>
                    <button type="button" class="password-toggle"
                            data-target="password" aria-label="Show password">Show</button>
                </div>

                <ul class="password-rules">
                    <li id="rule-length">At least 8 characters</li>
                    <li id="rule-upper">One uppercase letter</li>
                    <li id="rule-lower">One lowercase letter</li>
                    <li id="rule-digit">One number</li>
                    <li id="rule-special">One special character</li>
                </ul>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <div class="password-field">
                    <input type="password" id="confirm_password" name="confirm_password"
                           autocomplete="new-password" required>
                    <button type="button" class="password-toggle"
                            data-target="confirm_password" aria-label="Show password">Show</button>
                </div>
            </div>

            <button type="submit" id="register-submit"
                    class="btn btn-primary btn-submit" disabled>Update Password</button>
        </form>
        <?php else: ?>
            <p class="auth-footer-text">
                <a href="<?php echo url('pages/forgot-password.php'); ?>">Request a new reset link</a>
            </p>
        <?php endif; ?>

        <p class="auth-footer-text">
            <a href="<?php echo url('pages/login.php'); ?>">Back to login</a>
        </p>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>