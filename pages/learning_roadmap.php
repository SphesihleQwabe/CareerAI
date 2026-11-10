<?php
/**
 * CareerAI - AI Learning Roadmap
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/ai.php';

// Require login
if (empty($_SESSION['user_id'])) {
    header('Location: ' . url('pages/login.php'));
    exit;
}

$userId = $_SESSION['user_id'];
$error = '';

// 1. Fetch User Profile
$stmt = $conn->prepare('SELECT * FROM profiles WHERE user_id = ? LIMIT 1');
$stmt->bind_param('i', $userId);
$stmt->execute();
$profile = $stmt->get_result()->fetch_assoc();
$stmt->close();

// If no profile, they cannot generate a roadmap
if (!$profile) {
    $page_title = 'Learning Roadmap - ' . SITE_NAME;
    $active_nav = '';
    $active_dash = 'roadmap';
    require_once __DIR__ . '/../includes/header.php';
    ?>
    <div class="dashboard-wrapper">
        <?php require_once __DIR__ . '/../includes/dashboard_sidebar.php'; ?>
        <main class="dash-content" id="main-content">
            <div class="dash-header"><h1>Learning Roadmap</h1></div>
            <div class="dash-card" style="text-align: center; padding: 60px 20px;">
                <h2 style="color: var(--dark-navy); margin-bottom: 12px;">Profile Required</h2>
                <p style="color: var(--secondary-text); margin-bottom: 24px;">To generate a personalized learning roadmap, our AI needs to know your current skills and career goals.</p>
                <a href="<?php echo url('pages/profile.php'); ?>" class="btn btn-primary">Complete Your Profile</a>
            </div>
        </main>
    </div>
    <?php
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// 2. Fetch existing roadmap
$stmt = $conn->prepare('SELECT * FROM learning_roadmaps WHERE user_id = ? LIMIT 1');
$stmt->bind_param('i', $userId);
$stmt->execute();
$roadmap = $stmt->get_result()->fetch_assoc();
$stmt->close();

// 3. Handle "Generate Roadmap" click
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate'])) {
    
    $profileText = "Status: " . $profile['current_status'] . "\n"
                 . "Headline: " . $profile['headline'] . "\n"
                 . "Bio/Skills: " . $profile['bio'] . "\n";
    
    // Call AI
    $aiResult = generate_roadmap_with_ai($profileText, $profile['career_goal']);

    if (strpos($aiResult, 'Error:') === 0 || strpos($aiResult, 'cURL Error') === 0 || strpos($aiResult, 'AI API Error') === 0) {
        $error = $aiResult;
    } else {
        // Save or Update Roadmap
        if ($roadmap) {
            $stmt = $conn->prepare('UPDATE learning_roadmaps SET career_goal = ?, roadmap_content = ? WHERE user_id = ?');
            $stmt->bind_param('ssi', $profile['career_goal'], $aiResult, $userId);
        } else {
            $stmt = $conn->prepare('INSERT INTO learning_roadmaps (user_id, career_goal, roadmap_content) VALUES (?, ?, ?)');
            $stmt->bind_param('iss', $userId, $profile['career_goal'], $aiResult);
        }
        $stmt->execute();
        $stmt->close();

        // Redirect to prevent form resubmission
        header('Location: ' . url('pages/learning_roadmap.php'));
        exit;
    }
}

$page_title = 'Learning Roadmap - ' . SITE_NAME;
$active_nav = '';
$active_dash = 'roadmap';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-wrapper">
    <?php require_once __DIR__ . '/../includes/dashboard_sidebar.php'; ?>
    <main class="dash-content" id="main-content">
        
        <div class="dash-header">
            <h1>Learning Roadmap</h1>
            <p>A personalized step-by-step path to achieve your goal as a <strong><?php echo e($profile['career_goal']); ?></strong>.</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error" role="alert" style="max-width: 800px;"><?php echo e($error); ?></div>
        <?php endif; ?>

        <?php if ($roadmap): ?>
            <!-- Display Generated Roadmap -->
            <div class="dash-card" style="max-width: 100%;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 16px;">
                    <h2 style="font-size: 20px; margin: 0;">Your Career Path</h2>
                    <span style="font-size: 13px; color: var(--secondary-text);">Generated on: <?php echo date('d M Y', strtotime($roadmap['created_at'])); ?></span>
                </div>
                
                <div class="ai-output" style="line-height: 1.8; color: var(--main-text);">
                    <?php echo $roadmap['roadmap_content']; ?>
                </div>

                <form method="POST" style="margin-top: 32px; border-top: 1px solid var(--border-color); padding-top: 20px;">
                    <p style="font-size: 13px; color: var(--secondary-text); margin-bottom: 12px;">Skills change fast. Click below to generate an updated roadmap based on your latest profile.</p>
                    <button type="submit" name="generate" class="btn btn-outline">Regenerate Roadmap</button>
                </form>
            </div>

        <?php else: ?>
            <!-- Show "Generate" Prompt -->
            <div class="dash-card" style="text-align: center; padding: 60px 20px;">
                <h2 style="color: var(--dark-navy); margin-bottom: 12px;">Ready to map your future?</h2>
                <p style="color: var(--secondary-text); margin-bottom: 24px; max-width: 500px; margin-left: auto; margin-right: auto;">
                    Our AI will analyze your current skills and compare them to industry requirements for a <strong><?php echo e($profile['career_goal']); ?></strong>. It will then build a step-by-step learning path just for you.
                </p>
                <form method="POST">
                    <button type="submit" name="generate" class="btn btn-primary btn-large">Generate My Roadmap</button>
                </form>
            </div>
        <?php endif; ?>

    </main>
</div>

<!-- Simple CSS to style the HTML returned by the AI -->
<style>
    .ai-output h3 { margin-top: 28px; margin-bottom: 12px; color: var(--primary-blue); font-size: 18px; border-bottom: 1px solid var(--border-color); padding-bottom: 6px; }
    .ai-output ul { margin-bottom: 20px; padding-left: 20px; list-style-type: disc; }
    .ai-output li { margin-bottom: 8px; }
    .ai-output strong { color: var(--dark-navy); }
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>