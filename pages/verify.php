<?php
/**
 * CareerAI - Email Verification (OTP)
 *
 * The user lands here after registering. They enter the 6-digit
 * code from their inbox. On success, email_verified is set to 1
 * and the OTP is cleared.
 *
 * Security:
 *  - Attempt limiting (max 5 tries) to prevent brute-force guessing
 *  - Codes expire after 10 minutes
 *  - Constant-time comparison via hash_equals()
 *  - On success, the OTP is deleted so it cannot be reused
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/otp.php';

$errors  = [];
$success = false;
$email   = trim($_GET['email'] ?? $_POST['email'] ?? '');

// No email given → back to register
if ($email === '') {
    header('Location: ' . url('pages/register.php'));
    exit;
}

// Load the user (must exist and still be unverified)
$stmt = $conn->prepare(
    'SELECT id, first_name, email_verified FROM users WHERE email = ? LIMIT 1'
);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    header('Location: ' . url('pages/register.php'));
    exit;
}

if ((int)$user['email_verified'] === 1) {
    // Already verified — send to login
    header('Location: ' . url('pages/login.php'));
    exit;
}

// ---------------------------------------------------------
// HANDLE POST (verify code, or resend)
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? 'verify';

    // ---------- RESEND ----------
    if ($action === 'resend') {
        $otp = generate_otp();
        save_otp($conn, (int)$user['id'], $otp);

        $mailResult = send_otp_email($email, $user['first_name'], $otp);

        if ($mailResult === true) {
            $success = 'A new verification code has been sent to your email.';
        } else {
            $errors[] = 'We could not resend the code. Please try again later.';
        }
    }

    // ---------- VERIFY ----------
    else {
        $entered = trim($_POST['otp'] ?? '');

        if (!preg_match('/^[0-9]{6}$/', $entered)) {
            $errors[] = 'Please enter the 6-digit code from your email.';
        } else {
            // Load current OTP + expiry + attempts
            $stmt = $conn->prepare(
                'SELECT otp_code, otp_expires_at, otp_attempts
                 FROM users WHERE id = ? LIMIT 1'
            );
            $stmt->bind_param('i', $user['id']);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if (empty($row['otp_code'])) {
                $errors[] = 'No active code. Please click Resend to get a new one.';

            } elseif ((int)$row['otp_attempts'] >= OTP_MAX_ATTEMPTS) {
                // Too many failures — the code is now invalid
                $errors[] = 'Too many incorrect attempts. Please click Resend for a new code.';

            } elseif (strtotime($row['otp_expires_at']) < time()) {
                $errors[] = 'This code has expired. Please click Resend for a new one.';

            } elseif (!hash_equals($row['otp_code'], $entered)) {
                // Wrong code — increment attempts
                $stmt = $conn->prepare(
                    'UPDATE users SET otp_attempts = otp_attempts + 1 WHERE id = ? LIMIT 1'
                );
                $stmt->bind_param('i', $user['id']);
                $stmt->execute();
                $stmt->close();

                $remaining = OTP_MAX_ATTEMPTS - ((int)$row['otp_attempts'] + 1);
                if ($remaining > 0) {
                    $errors[] = 'Incorrect code. You have ' . $remaining
                              . ' attempt' . ($remaining === 1 ? '' : 's') . ' remaining.';
                } else {
                    $errors[] = 'Incorrect code. Too many failed attempts — please click Resend.';
                }

            } else {
                // Success — mark verified, clear OTP and attempts
                $stmt = $conn->prepare(
                    'UPDATE users
                     SET email_verified = 1,
                         otp_code = "",
                         otp_expires_at = NULL,
                         otp_attempts = 0
                     WHERE id = ? LIMIT 1'
                );
                $stmt->bind_param('i', $user['id']);
                $stmt->execute();
                $stmt->close();

                // Regenerate session id for safety
                session_regenerate_id(true);

                header('Location: ' . url('pages/login.php?verified=1'));
                exit;
            }
        }
    }
}

$page_title = 'Verify Email - ' . SITE_NAME;
$active_nav = '';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="auth-wrapper">
    <div class="auth-card">

        <div class="auth-header">
            <h1>Verify Your Email</h1>
            <p>We sent a 6-digit code to <strong><?php echo e($email); ?></strong></p>
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

        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <?php echo e($success); ?>
            </div>
        <?php endif; ?>

        <form class="auth-form" method="POST"
              action="<?php echo url('pages/verify.php'); ?>" novalidate>

            <input type="hidden" name="email" value="<?php echo e($email); ?>">
            <input type="hidden" name="action" value="verify">

            <div class="form-group">
                <label for="otp">Verification Code</label>
                <input type="text"
                       id="otp"
                       name="otp"
                       inputmode="numeric"
                       maxlength="6"
                       pattern="[0-9]{6}"
                       title="Enter the 6-digit code"
                       placeholder="000000"
                       autocomplete="one-time-code"
                       oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                       required>
            </div>

            <button type="submit" class="btn btn-primary btn-submit">Verify Email</button>
        </form>

        <form class="auth-form" method="POST"
              action="<?php echo url('pages/verify.php'); ?>"
              style="margin-top:14px;">
            <input type="hidden" name="email" value="<?php echo e($email); ?>">
            <input type="hidden" name="action" value="resend">
            <button type="submit" class="btn btn-outline btn-submit">
                Resend Code
            </button>
        </form>

        <p class="auth-footer-text">
            Wrong email?
            <a href="<?php echo url('pages/register.php'); ?>">Register again</a>
        </p>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>