<?php
/**
 * CareerAI - User Registration
 *
 * Flow:
 *  1. Validate form input
 *  2. Check email is not already registered
 *  3. Insert the user with email_verified = 0
 *  4. Generate a 6-digit OTP and save it with a 10-minute expiry
 *  5. Email the OTP to the user
 *  6. Redirect to verify.php so the user can enter the code
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/otp.php';

$errors = [];
$first_name = '';
$last_name  = '';
$email      = '';
$phone      = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $first_name = trim($_POST['first_name'] ?? '');
    $last_name  = trim($_POST['last_name']  ?? '');
    $email      = trim($_POST['email']      ?? '');
    $phone      = trim($_POST['phone']      ?? '');
    $password   = $_POST['password']         ?? '';
    $confirm    = $_POST['confirm_password'] ?? '';

    // ----- Names -----
    if ($first_name === '') {
        $errors[] = 'First name is required.';
    } elseif (strlen($first_name) > 50) {
        $errors[] = 'First name must be 50 characters or less.';
    }
    if ($last_name === '') {
        $errors[] = 'Last name is required.';
    } elseif (strlen($last_name) > 50) {
        $errors[] = 'Last name must be 50 characters or less.';
    }

    // ----- Email -----
    if ($email === '') {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    } elseif (strlen($email) > 150) {
        $errors[] = 'Email must be 150 characters or less.';
    }

    // ----- Phone -----
    if ($phone === '') {
        $errors[] = 'Phone number is required.';
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $errors[] = 'Phone number must be exactly 10 digits (numbers only).';
    }

    // ----- Password -----
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

    // ----- Duplicate email -----
    if (empty($errors)) {
        $stmt = $conn->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = 'An account with this email already exists.';
        }
        $stmt->close();
    }

    // ----- Insert user, generate OTP, send email, redirect -----
    if (empty($errors)) {

        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare(
            'INSERT INTO users (first_name, last_name, email, phone, password_hash, email_verified)
             VALUES (?, ?, ?, ?, ?, 0)'
        );
        $stmt->bind_param('sssss', $first_name, $last_name, $email, $phone, $password_hash);

        if ($stmt->execute()) {
            $newUserId = $conn->insert_id;
            $stmt->close();

            // Generate and save OTP
            $otp = generate_otp();
            save_otp($conn, $newUserId, $otp);

            // Send OTP email (failure is not fatal here —
            // the user can request a new one from verify.php)
            send_otp_email($email, $first_name, $otp);

            // Redirect to the verification page
            header('Location: ' . url('pages/verify.php?email=' . urlencode($email)));
            exit;
        } else {
            $errors[] = 'Registration failed. Please try again.';
            $stmt->close();
        }
    }
}

$page_title = 'Create Account - ' . SITE_NAME;
$active_nav = '';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="auth-wrapper">
    <div class="auth-card">

        <div class="auth-header">
            <h1>Create Your Account</h1>
            <p>Start building your career with CareerAI</p>
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

        <form class="auth-form" method="POST"
              action="<?php echo url('pages/register.php'); ?>" novalidate>

            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name"
                           autocomplete="given-name"
                           value="<?php echo e($first_name); ?>" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name"
                           autocomplete="family-name"
                           value="<?php echo e($last_name); ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email"
                       autocomplete="email"
                       value="<?php echo e($email); ?>" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text"
                       id="phone"
                       name="phone"
                       value="<?php echo e($phone); ?>"
                       placeholder="e.g. 0821234567"
                       inputmode="numeric"
                       maxlength="10"
                       pattern="[0-9]{10}"
                       title="Phone number must be exactly 10 digits."
                       autocomplete="tel"
                       oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                       required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
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
                <label for="confirm_password">Confirm Password</label>
                <div class="password-field">
                    <input type="password" id="confirm_password" name="confirm_password"
                           autocomplete="new-password" required>
                    <button type="button" class="password-toggle"
                            data-target="confirm_password" aria-label="Show password">Show</button>
                </div>
            </div>

            <button type="submit" id="register-submit"
                    class="btn btn-primary btn-submit" disabled>Create Account</button>

        </form>

        <p class="auth-footer-text">
            Already have an account?
            <a href="<?php echo url('pages/login.php'); ?>">Log in</a>
        </p>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>