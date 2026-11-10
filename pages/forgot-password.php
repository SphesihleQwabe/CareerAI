<?php
/**
 * CareerAI - Forgot Password
 *
 * User enters their email. If an account exists, we generate a
 * secure random token, store it with a 1-hour expiry, and email
 * a password reset link.
 *
 * SECURITY: We always show the same message regardless of
 * whether the email exists. This prevents attackers from
 * discovering which emails are registered.
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/mailer.php';

$errors  = [];
$notice  = '';
$email   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if (empty($errors)) {

        // Look up the user (if any)
        $stmt = $conn->prepare('SELECT id, first_name FROM users WHERE email = ? LIMIT 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        // Always show the same message
        $notice = 'If an account exists with that email, a password reset link has been sent.';

        if ($user) {
            // Generate a strong token (32 bytes = 64 hex chars)
            $token      = bin2hex(random_bytes(32));
            $expiresAt  = date('Y-m-d H:i:s', time() + 3600); // 1 hour

            // Save token + expiry
            $stmt = $conn->prepare(
                'UPDATE users SET reset_token = ?, reset_expires_at = ? WHERE id = ? LIMIT 1'
            );
            $stmt->bind_param('ssi', $token, $expiresAt, $user['id']);
            $stmt->execute();
            $stmt->close();

            // Build the reset link
            $link = url('pages/reset-password.php')
                  . '?token=' . urlencode($token)
                  . '&email=' . urlencode($email);

            // Email body
            $safeName = htmlspecialchars($user['first_name'] ?: 'there', ENT_QUOTES, 'UTF-8');
            $body = '
                <div style="font-family:Arial,Helvetica,sans-serif;color:#263B53;max-width:520px;margin:0 auto;padding:24px;border:1px solid #DCE7F2;border-radius:10px;">
                    <h2 style="color:#0B2545;margin-top:0;">Reset your CareerAI password</h2>
                    <p>Hi ' . $safeName . ',</p>
                    <p>We received a request to reset your password. Click the button below to choose a new one. This link expires in 1 hour.</p>

                    <p style="text-align:center;margin:28px 0;">
                        <a href="' . $link . '"
                           style="display:inline-block;background-color:#1769E0;color:#ffffff;text-decoration:none;padding:12px 24px;border-radius:8px;font-weight:600;">
                            Reset Password
                        </a>
                    </p>

                    <p style="color:#6C7D90;font-size:13px;">
                        If the button does not work, copy this link into your browser:<br>
                        <a href="' . $link . '" style="color:#1769E0;word-break:break-all;">' . $link . '</a>
                    </p>

                    <p>If you did not request a password reset, you can safely ignore this email.</p>
                    <p style="color:#6C7D90;font-size:13px;margin-bottom:0;">&copy; ' . date('Y') . ' CareerAI</p>
                </div>
            ';

            send_mail($email, $user['first_name'], 'Reset your CareerAI password', $body);
        }
    }
}

$page_title = 'Forgot Password - ' . SITE_NAME;
$active_nav = '';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="auth-wrapper">
    <div class="auth-card">

        <div class="auth-header">
            <h1>Forgot Your Password?</h1>
            <p>Enter your email and we will send you a reset link</p>
        </div>

        <?php if ($notice !== ''): ?>
            <div class="alert alert-success"><?php echo e($notice); ?></div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $err): ?>
                        <li><?php echo e($err); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($notice === ''): ?>
        <form class="auth-form" method="POST"
              action="<?php echo url('pages/forgot-password.php'); ?>" novalidate>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email"
                       autocomplete="email"
                       value="<?php echo e($email); ?>" required>
            </div>

            <button type="submit" class="btn btn-primary btn-submit">Send Reset Link</button>
        </form>
        <?php endif; ?>

        <p class="auth-footer-text">
            Remembered your password?
            <a href="<?php echo url('pages/login.php'); ?>">Back to login</a>
        </p>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>