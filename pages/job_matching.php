<?php
/**
 * CareerAI - Job Matching (Himalayas + Direct Search)
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/ai.php';

if (empty($_SESSION['user_id'])) {
    header('Location: ' . url('pages/login.php'));
    exit;
}

$userId = $_SESSION['user_id'];
$error = '';

$stmt = $conn->prepare('SELECT * FROM profiles WHERE user_id = ? LIMIT 1');
$stmt->bind_param('i', $userId);
$stmt->execute();
$profile = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$profile) {
    $page_title = 'Job Matching - ' . SITE_NAME;
    $active_nav = '';
    $active_dash = 'jobs';
    require_once __DIR__ . '/../includes/header.php';
    ?>
    <div class="dashboard-wrapper">
        <?php require_once __DIR__ . '/../includes/dashboard_sidebar.php'; ?>
        <main class="dash-content" id="main-content">
            <div class="dash-header"><h1>Job Matching</h1></div>
            <div class="dash-card" style="text-align: center; padding: 60px 20px;">
                <h2 style="color: var(--dark-navy); margin-bottom: 12px;">Profile Required</h2>
                <p style="color: var(--secondary-text); margin-bottom: 24px;">To match you with real jobs, our AI needs to know your skills and career goals.</p>
                <a href="<?php echo url('pages/profile.php'); ?>" class="btn btn-primary">Complete Your Profile</a>
            </div>
        </main>
    </div>
    <?php
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$profileText = "Status: " . $profile['current_status'] . "\n"
             . "Headline: " . $profile['headline'] . "\n"
             . "Career Goal: " . $profile['career_goal'] . "\n"
             . "Bio/Skills: " . $profile['bio'] . "\n";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['external_job_id'])) {
    $jobId    = $_POST['external_job_id'];
    $jobTitle = $_POST['job_title'];
    $jobReq   = $_POST['job_desc'];
    
    $aiResult = match_job_with_ai($profileText, $jobTitle, $jobReq);
    
    if (is_array($aiResult) && isset($aiResult['score'])) {
        if (!isset($_SESSION['external_matches'])) { $_SESSION['external_matches'] = []; }
        $_SESSION['external_matches'][$jobId] = $aiResult;
    } else {
        $error = 'The AI could not process this match. ' . (is_array($aiResult) ? ($aiResult['error'] ?? 'Unknown error') : 'Unknown error');
    }
}

$externalJobs = fetch_himalayas_jobs($profile['career_goal'] ?: 'software developer');

$page_title = 'Job Matching - ' . SITE_NAME;
$active_nav = '';
$active_dash = 'jobs';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-wrapper">
    <?php require_once __DIR__ . '/../includes/dashboard_sidebar.php'; ?>
    <main class="dash-content" id="main-content">
        <div class="dash-header">
            <h1>Live Job Matching (South Africa)</h1>
            <p>Real jobs for <strong><?php echo e($profile['career_goal']); ?></strong> located in South Africa.</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error" role="alert" style="max-width: 800px;"><?php echo e($error); ?></div>
        <?php endif; ?>

        <div style="display: grid; gap: 20px; max-width: 900px;">
            <?php if (empty($externalJobs)): ?>
                <div class="dash-card" style="padding: 40px; text-align: center;">
                    <p>No jobs found right now. Try changing your career goal in your profile.</p>
                </div>
            <?php else: ?>
                <?php foreach ($externalJobs as $job): ?>
                    <?php 
                        $jobId = $job['guid'] ?? md5(($job['title'] ?? '') . ($job['companyName'] ?? '')); 
                        $match = $_SESSION['external_matches'][$jobId] ?? null; 
                        $cleanDesc = substr(strip_tags($job['description'] ?? ''), 0, 2000); 
                        
                        // Get Original Link from API
                        $applyLink = $job['applicationLink'] ?? '#';

                        // GENERATE DIRECT SEARCH LINKS (This guarantees they find the actual job)
                        $titleEncoded = urlencode($job['title'] ?? '');
                        $companyEncoded = urlencode($job['companyName'] ?? '');
                        $linkedinSearch = "https://www.linkedin.com/jobs/search/?keywords={$titleEncoded}%20{$companyEncoded}&location=South%20Africa";
                        $indeedSearch   = "https://www.indeed.com/jobs?q={$titleEncoded}%20{$companyEncoded}&l=South%20Africa";
                    ?>
                    <div class="dash-card" style="padding: 24px;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                            <div>
                                <h3 style="font-size: 18px; color: var(--dark-navy); margin-bottom: 4px;"><?php echo e($job['title'] ?? 'Job Title'); ?></h3>
                                <p style="font-size: 14px; font-weight: 600; color: var(--primary-blue);"><?php echo e($job['companyName'] ?? 'Company'); ?></p>
                                <p style="font-size: 13px; color: var(--secondary-text); margin-top: 4px;">
                                    Location: <?php echo e(isset($job['locationRestrictions']) && is_array($job['locationRestrictions']) ? implode(', ', $job['locationRestrictions']) : 'South Africa'); ?>
                                </p>
                            </div>
                            <?php if ($match): ?>
                                <div style="text-align: center; background: var(--light-blue); padding: 12px 16px; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                                    <p style="font-size: 11px; font-weight: 700; color: var(--primary-blue); text-transform: uppercase;">Match Score</p>
                                    <p style="font-size: 28px; font-weight: 800; color: var(--dark-navy); margin: 4px 0;"><?php echo (int)$match['score']; ?>%</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div style="margin-bottom: 20px; font-size: 14px; color: var(--main-text); line-height: 1.6;">
                            <?php echo substr(strip_tags($job['description'] ?? ''), 0, 250); ?>...
                        </div>
                        <?php if ($match): ?>
                            <div style="background: var(--very-light-bg); border-left: 4px solid var(--primary-blue); padding: 16px; border-radius: 0 var(--radius-sm) var(--radius-sm) 0; margin-bottom: 16px;">
                                <h4 style="font-size: 14px; color: var(--dark-navy); margin-bottom: 8px;">AI Feedback:</h4>
                                <div style="font-size: 14px; color: var(--main-text); line-height: 1.6;"><?php echo $match['feedback']; ?></div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- APPLY BUTTONS -->
                        <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap; border-top: 1px solid var(--border-color); padding-top: 20px;">
                            <a href="<?php echo e($applyLink); ?>" target="_blank" class="btn btn-primary" style="background-color: var(--dark-navy); border-color: var(--dark-navy);">View Original Post</a>
                            
                            <a href="<?php echo $linkedinSearch; ?>" target="_blank" class="btn btn-primary" style="background-color: #0A66C2; border-color: #0A66C2;">Find on LinkedIn</a>
                            
                            <a href="<?php echo $indeedSearch; ?>" target="_blank" class="btn btn-primary" style="background-color: #2557A7; border-color: #2557A7;">Find on Indeed</a>
                            
                            <!-- AI Match Form -->
                            <form method="POST" style="margin: 0; margin-left: auto;">
                                <input type="hidden" name="external_job_id" value="<?php echo e($jobId); ?>">
                                <input type="hidden" name="job_title" value="<?php echo e($job['title'] ?? ''); ?>">
                                <input type="hidden" name="job_desc" value="<?php echo e($cleanDesc); ?>">
                                <button type="submit" class="btn btn-outline"><?php echo $match ? 'Re-check Match' : 'Check AI Match'; ?></button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>