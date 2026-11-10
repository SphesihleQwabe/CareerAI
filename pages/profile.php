<?php
/**
 * CareerAI - User Profile
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

// Require login
if (empty($_SESSION['user_id'])) {
    header('Location: ' . url('pages/login.php'));
    exit;
}

$userId = $_SESSION['user_id'];
$errors = [];
$success = false;

// Fetch existing profile if it exists
$stmt = $conn->prepare('SELECT * FROM profiles WHERE user_id = ? LIMIT 1');
$stmt->bind_param('i', $userId);
$stmt->execute();
$profile = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Set form variables (defaults if no profile yet)
$current_status   = $profile['current_status'] ?? 'university';
$headline         = $profile['headline'] ?? '';
$bio              = $profile['bio'] ?? '';
$location         = $profile['location'] ?? '';
$career_goal      = $profile['career_goal'] ?? '';
$years_experience = $profile['years_experience'] ?? 0;
$matric_subjects  = $profile['matric_subjects'] ?? '';
$matric_aps       = $profile['matric_aps'] ?? 0;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $current_status   = $_POST['current_status'] ?? 'university';
    $headline         = trim($_POST['headline'] ?? '');
    $bio              = trim($_POST['bio'] ?? '');
    $location         = trim($_POST['location'] ?? '');
    $career_goal      = trim($_POST['career_goal'] ?? '');
    $years_experience = (int)($_POST['years_experience'] ?? 0);
    $matric_subjects  = trim($_POST['matric_subjects'] ?? '');
    $matric_aps       = (int)($_POST['matric_aps'] ?? 0);

    // Basic validation
    if ($headline === '') {
        $errors[] = 'Professional headline is required.';
    }
    if ($career_goal === '') {
        $errors[] = 'Career goal is required.';
    }

    if (empty($errors)) {
        if ($profile) {
            // Update existing profile
            $stmt = $conn->prepare(
                'UPDATE profiles 
                 SET current_status=?, headline=?, bio=?, location=?, career_goal=?, years_experience=?, matric_subjects=?, matric_aps=? 
                 WHERE user_id=?'
            );
            $stmt->bind_param('sssssisisii', $current_status, $headline, $bio, $location, $career_goal, $years_experience, $matric_subjects, $matric_aps, $userId);
        } else {
            // Create new profile
            $stmt = $conn->prepare(
                'INSERT INTO profiles (current_status, headline, bio, location, career_goal, years_experience, matric_subjects, matric_aps, user_id) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->bind_param('sssssisii', $current_status, $headline, $bio, $location, $career_goal, $years_experience, $matric_subjects, $matric_aps, $userId);
        }

        if ($stmt->execute()) {
            $success = true;
            // Refresh profile data
            $stmt->close();
            $stmt = $conn->prepare('SELECT * FROM profiles WHERE user_id = ? LIMIT 1');
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            $profile = $stmt->get_result()->fetch_assoc();
            $stmt->close();
        } else {
            $errors[] = 'Failed to save profile. Please try again.';
        }
    }
}

$page_title = 'My Profile - ' . SITE_NAME;
$active_nav = '';
$active_dash = 'profile';

require_once __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-wrapper">
    
    <?php require_once __DIR__ . '/../includes/dashboard_sidebar.php'; ?>

    <main class="dash-content" id="main-content">
        
        <div class="dash-header">
            <h1>My Profile</h1>
            <p>Update your career details so the AI can give you the best advice.</p>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success" role="alert" style="max-width: 800px;">
                Your profile has been saved successfully.
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error" role="alert" style="max-width: 800px;">
                <ul>
                    <?php foreach ($errors as $err): ?>
                        <li><?php echo e($err); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="dash-card">
            <form method="POST" action="<?php echo url('pages/profile.php'); ?>">
                
                <div class="form-row-2">
                    <div class="form-group">
                        <label for="headline">Professional Headline</label>
                        <input type="text" id="headline" name="headline" value="<?php echo e($headline); ?>" placeholder="e.g. Aspiring Software Developer" required>
                    </div>
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" id="location" name="location" value="<?php echo e($location); ?>" placeholder="e.g. Johannesburg, SA">
                    </div>
                </div>

                <div class="form-row-2">
                    <div class="form-group">
                        <label for="current_status">Current Status</label>
                        <select id="current_status" name="current_status">
                            <option value="high_school" <?php echo $current_status === 'high_school' ? 'selected' : ''; ?>>High School Student</option>
                            <option value="university" <?php echo $current_status === 'university' ? 'selected' : ''; ?>>University Student</option>
                            <option value="graduate" <?php echo $current_status === 'graduate' ? 'selected' : ''; ?>>Graduate</option>
                            <option value="professional" <?php echo $current_status === 'professional' ? 'selected' : ''; ?>>Professional</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="years_experience">Years of Experience</label>
                        <input type="number" id="years_experience" name="years_experience" value="<?php echo e($years_experience); ?>" min="0" max="50">
                    </div>
                </div>

                <div class="form-group">
                    <label for="career_goal">Target Career / Job Role</label>
                    <input type="text" id="career_goal" name="career_goal" value="<?php echo e($career_goal); ?>" placeholder="e.g. Junior PHP Developer" required>
                </div>

                <div class="form-group">
                    <label for="bio">Short Bio / Skills</label>
                    <textarea id="bio" name="bio" placeholder="Tell us a bit about your background, your skills (e.g. PHP, MySQL, JavaScript), and what you want to achieve..."><?php echo e($bio); ?></textarea>
                </div>

                <!-- Matric Section (Will be shown/hidden by JS based on status) -->
                <div id="matric-section" style="display: <?php echo $current_status === 'high_school' ? 'block' : 'none'; ?>; padding-top: 20px; border-top: 1px solid var(--border-color); margin-bottom: 20px;">
                    <h3 style="font-size: 18px; margin-bottom: 16px;">Matric Details</h3>
                    <div class="form-row-2">
                        <div class="form-group">
                            <label for="matric_aps">APS Score</label>
                            <input type="number" id="matric_aps" name="matric_aps" value="<?php echo e($matric_aps); ?>" min="0" max="42">
                        </div>
                        <div class="form-group">
                            <label for="matric_subjects">Subjects (comma separated)</label>
                            <input type="text" id="matric_subjects" name="matric_subjects" value="<?php echo e($matric_subjects); ?>" placeholder="e.g. Maths, Physical Science, English">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Save Profile</button>
            </form>
        </div>

    </main>
</div>

<script>
    // Show/hide matric fields based on status selection
    document.getElementById('current_status').addEventListener('change', function() {
        var matricSection = document.getElementById('matric-section');
        if (this.value === 'high_school') {
            matricSection.style.display = 'block';
        } else {
            matricSection.style.display = 'none';
        }
    });
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>