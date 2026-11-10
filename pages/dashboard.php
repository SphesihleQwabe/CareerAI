<?php
/**
 * CareerAI - User Dashboard
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

if (empty($_SESSION['user_id'])) {
    header('Location: ' . url('pages/login.php'));
    exit;
}

$userId = $_SESSION['user_id'];

// 1. Fetch Profile
$stmt = $conn->prepare('SELECT * FROM profiles WHERE user_id = ? LIMIT 1');
$stmt->bind_param('i', $userId);
$stmt->execute();
$profile = $stmt->get_result()->fetch_assoc();
$stmt->close();

// 2. Calculate CV Score (Extract the exact score from the AI analysis text)
$cvScore = 0;
$stmt = $conn->prepare('SELECT ai_response FROM cv_analysis WHERE user_id = ? ORDER BY created_at DESC LIMIT 1');
$stmt->bind_param('i', $userId);
$stmt->execute();
$analysis = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($analysis) {
    // Look for patterns like "52 / 100", "Score: 52", or "52/100"
    if (preg_match('/(\d+)\s*\/\s*100/i', $analysis['ai_response'], $matches)) {
        $cvScore = (int)$matches[1];
    } elseif (preg_match('/(?:Score|Rating)[^\d]*(\d+)/i', $analysis['ai_response'], $matches)) {
        $cvScore = (int)$matches[1];
    }
}

// 3. Calculate Average Job Match (Real mathematical average from database)
$skillsMatch = 0;
$stmt = $conn->prepare('SELECT AVG(match_score) as avg_score FROM job_matches WHERE user_id = ?');
$stmt->bind_param('i', $userId);
$stmt->execute();
$matchData = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($matchData && $matchData['avg_score']) {
    $skillsMatch = round($matchData['avg_score']);
}

// 4. Calculate Career Readiness (Based strictly on completed steps)
$readiness = 20; // Base score for registering
if ($profile) $readiness += 20; // Has profile
if ($cvScore > 0) $readiness += 20; // Has analyzed CV
if ($skillsMatch > 0) $readiness += 20; // Has matched with jobs

$roadmapCheck = $conn->query("SELECT id FROM learning_roadmaps WHERE user_id = $userId LIMIT 1");
if ($roadmapCheck->num_rows > 0) $readiness += 20; // Has roadmap

$page_title = 'Dashboard - ' . SITE_NAME;
$active_nav = '';
$active_dash = 'dashboard';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-wrapper">
    <?php require_once __DIR__ . '/../includes/dashboard_sidebar.php'; ?>
    <main class="dash-content" id="main-content">
        
        <div class="dash-header">
            <h1>Welcome back, <?php echo e($_SESSION['user_name']); ?></h1>
            <p>Here is an overview of your career progress.</p>
        </div>

        <?php if (!$profile): ?>
            <div class="dash-card" style="text-align: center; padding: 60px 20px;">
                <h2 style="color: var(--dark-navy); margin-bottom: 12px;">Complete Your Profile</h2>
                <p style="color: var(--secondary-text); margin-bottom: 24px;">To get accurate AI career recommendations, we need to know more about you.</p>
                <a href="<?php echo url('pages/profile.php'); ?>" class="btn btn-primary btn-large">Create Profile Now</a>
            </div>
        <?php else: ?>
            
            <!-- STATS GRID -->
            <div class="preview-stats" style="grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 32px; max-width: 900px;">
                
                <!-- CV Score Card -->
                <div class="preview-stat-card" style="padding: 24px; text-align: left; display: flex; flex-direction: column;">
                    <p class="stat-label" style="font-size: 14px;">CV Score</p>
                    <p class="stat-value" style="font-size: 32px; margin-bottom: 8px;"><?php echo $cvScore; ?>%</p>
                    <p style="font-size: 12px; color: var(--secondary-text); margin-top: auto;">Based on your latest AI analysis.</p>
                </div>

                <!-- Skills Match Card -->
                <div class="preview-stat-card" style="padding: 24px; text-align: left; display: flex; flex-direction: column;">
                    <p class="stat-label" style="font-size: 14px;">Skills Match</p>
                    <p class="stat-value" style="font-size: 32px; margin-bottom: 8px;"><?php echo $skillsMatch; ?>%</p>
                    <p style="font-size: 12px; color: var(--secondary-text); margin-top: auto;">Average of all job matches.</p>
                </div>

                <!-- Career Readiness Card -->
                <div class="preview-stat-card" style="padding: 24px; text-align: left; display: flex; flex-direction: column;">
                    <p class="stat-label" style="font-size: 14px;">Career Readiness</p>
                    <p class="stat-value" style="font-size: 32px; color: var(--primary-blue); margin-bottom: 8px;"><?php echo $readiness; ?>%</p>
                    <p style="font-size: 12px; color: var(--secondary-text); margin-top: auto;">Based on completed platform steps.</p>
                </div>

            </div>

            <!-- GOAL SUMMARY -->
            <div class="dash-card" style="max-width: 900px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3>Career Goal Summary</h3>
                    <a href="<?php echo url('pages/profile.php'); ?>" class="btn btn-outline">Edit Profile</a>
                </div>
                <p style="margin-bottom: 10px;"><strong>Current Status:</strong> <?php echo ucfirst(str_replace('_', ' ', e($profile['current_status']))); ?></p>
                <p style="margin-bottom: 10px;"><strong>Target Role:</strong> <?php echo e($profile['career_goal']); ?></p>
                <p style="margin-bottom: 10px;"><strong>Location:</strong> <?php echo e($profile['location']); ?></p>
            </div>
        <?php endif; ?>

    </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>