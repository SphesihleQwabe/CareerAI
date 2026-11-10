<?php
/**
 * CareerAI - User Login
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

// If already logged in, go to the correct dashboard based on role
if (!empty($_SESSION['user_id'])) {
    if (($_SESSION['user_role'] ?? '') === 'admin') {
        header('Location: ' . url('admin/index.php'));
    } else {
        header('Location: ' . url('pages/dashboard.php'));
    }
    exit;
}

// Optional success messages passed in the URL
$notice = '';
if (isset($_GET['verified']) && $_GET['verified'] === '1') {
    $notice = 'Your email has been verified. You can now log in.';
} elseif (isset($_GET['reset']) && $_GET['reset'] === '1') {
    $notice = 'Your password has been reset. You can now log in.';
}

$errors = [];
$email  = '';

// ---------------------------------------------------------
// HANDLE POST
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // ----- Validate basic input -----
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if ($password === '') {
        $errors[] = 'Password is required.';
    }

    // ----- Check credentials -----
    if (empty($errors)) {
        $stmt = $conn->prepare(
            'SELECT id, first_name, last_name, password_hash, role, is_active, email_verified
             FROM users WHERE email = ? LIMIT 1'
        );
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if ((int)$user['is_active'] !== 1) {
                $errors[] = 'Your account is not active. Please contact support.';

            } elseif (!password_verify($password, $user['password_hash'])) {
                $errors[] = 'Invalid email or password.';

            } elseif ((int)$user['email_verified'] !== 1) {
                $stmt->close();
                header('Location: ' . url('pages/verify.php?email=' . urlencode($email)));
                exit;

            } else {
                // Success — save minimal data in session
                $_SESSION['user_id']   = (int)$user['id'];
                $_SESSION['user_name'] = $user['first_name'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['logged_in'] = true;

                // Prevent session fixation
                session_regenerate_id(true);

                // REDIRECT BASED ON ROLE
                if ($user['role'] === 'admin') {
                    header('Location: ' . url('admin/index.php'));
                } else {
                    header('Location: ' . url('pages/dashboard.php'));
                }
                exit;
            }
        } else {
            $errors[] = 'Invalid email or password.';
        }

        $stmt->close();
    }
}

$page_title = 'Login - ' . SITE_NAME;
$active_nav = '';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="auth-wrapper">
    <div class="auth-card">

        <div class="auth-header">
            <h1>Welcome Back</h1>
            <p>Log in to continue your career journey</p>
        </div>

        <?php if ($notice !== ''): ?>
            <div class="alert alert-success" role="alert">
                <?php echo e($notice); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error" role="alert">
                <ul>
                    <?php foreach ($errors as $err): ?>
                        <li><?php echo e($err); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form class="auth-form" method="POST"
              action="<?php echo url('pages/login.php'); ?>" novalidate>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email"
                       autocomplete="email"
                       value="<?php echo e($email); ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-field">
                    <input type="password" id="password" name="password"
                           autocomplete="current-password" required>
                    <button type="button" class="password-toggle"
                            data-target="password" aria-label="Show password">Show</button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-submit">Log In</button>

        </form>

        <p class="auth-footer-text" style="margin-top:14px;">
            <a href="<?php echo url('pages/forgot-password.php'); ?>">Forgot your password?</a>
        </p>

        <p class="auth-footer-text">
            Do not have an account?
            <a href="<?php echo url('pages/register.php'); ?>">Create one</a>
        </p>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>