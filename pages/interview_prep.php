<?php
/**
 * CareerAI - Interview Preparation
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

if (!$profile) {
    $page_title = 'Interview Prep - ' . SITE_NAME;
    $active_nav = '';
    $active_dash = 'interview';
    require_once __DIR__ . '/../includes/header.php';
    ?>
    <div class="dashboard-wrapper">
        <?php require_once __DIR__ . '/../includes/dashboard_sidebar.php'; ?>
        <main class="dash-content" id="main-content">
            <div class="dash-header"><h1>Interview Preparation</h1></div>
            <div class="dash-card" style="text-align: center; padding: 60px 20px;">
                <h2 style="color: var(--dark-navy); margin-bottom: 12px;">Profile Required</h2>
                <p style="color: var(--secondary-text); margin-bottom: 24px;">To generate interview questions, our AI needs to know your target career goal.</p>
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
             . "Bio/Skills: " . $profile['bio'] . "\n";

// 2. Handle "Generate Questions" click
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate'])) {
    
    // Delete old questions for a fresh start
    $stmt = $conn->prepare('DELETE FROM interview_questions WHERE user_id = ?');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $stmt->close();

    // Call AI
    $questions = generate_interview_questions($profile['career_goal'], $profileText);

    if (is_array($questions) && !empty($questions)) {
        // Save new questions to DB
        $stmt = $conn->prepare('INSERT INTO interview_questions (user_id, career_goal, question) VALUES (?, ?, ?)');
        foreach ($questions as $q) {
            $stmt->bind_param('iss', $userId, $profile['career_goal'], $q);
            $stmt->execute();
        }
        $stmt->close();
        
        header('Location: ' . url('pages/interview_prep.php'));
        exit;
    } else {
        $error = is_array($questions) ? 'AI returned empty results.' : $questions;
    }
}

// 3. Fetch existing questions (including previous answers and feedback)
$stmt = $conn->prepare('SELECT * FROM interview_questions WHERE user_id = ? ORDER BY id ASC');
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
$questions = [];
while ($row = $result->fetch_assoc()) {
    $questions[] = $row;
}
$stmt->close();

$page_title = 'Interview Prep - ' . SITE_NAME;
$active_nav = '';
$active_dash = 'interview';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-wrapper">
    <?php require_once __DIR__ . '/../includes/dashboard_sidebar.php'; ?>
    <main class="dash-content" id="main-content">
        
        <div class="dash-header">
            <h1>Interview Preparation</h1>
            <p>Practice realistic interview questions for <strong><?php echo e($profile['career_goal']); ?></strong>.</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error" role="alert" style="max-width: 800px;"><?php echo e($error); ?></div>
        <?php endif; ?>

        <?php if (empty($questions)): ?>
            <div class="dash-card" style="text-align: center; padding: 60px 20px; max-width: 800px;">
                <h2 style="color: var(--dark-navy); margin-bottom: 12px;">Ready to practice?</h2>
                <p style="color: var(--secondary-text); margin-bottom: 24px; max-width: 500px; margin-left: auto; margin-right: auto;">
                    Our AI will generate 5 realistic interview questions based on your target role and skills.
                </p>
                <form method="POST">
                    <button type="submit" name="generate" class="btn btn-primary btn-large">Generate Interview Questions</button>
                </form>
            </div>
        <?php else: ?>
            
            <div style="display: grid; gap: 20px; max-width: 900px;">
                <?php foreach ($questions as $index => $q): ?>
                    <div class="dash-card" style="padding: 24px;">
                        <h3 style="font-size: 16px; color: var(--dark-navy); margin-bottom: 16px; line-height: 1.5;">
                            Question <?php echo $index + 1; ?>: <?php echo e($q['question']); ?>
                        </h3>
                        
                        <!-- Pre-fill the textarea if they answered before -->
                        <textarea id="answer-<?php echo $q['id']; ?>" rows="3" placeholder="Type your answer here..." style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); font-family: inherit; font-size: 14px; resize: vertical;"><?php echo e($q['user_answer']); ?></textarea>
                        
                        <div style="margin-top: 12px; display: flex; gap: 12px;">
                            <button type="button" class="btn btn-primary" onclick="submitAnswer(<?php echo $q['id']; ?>)">Submit Answer for AI Feedback</button>
                        </div>
                        
                        <!-- Show previous feedback if it exists, otherwise hide -->
                        <div id="feedback-<?php echo $q['id']; ?>" style="display: <?php echo empty($q['ai_feedback']) ? 'none' : 'block'; ?>; margin-top: 16px; padding: 16px; background: var(--very-light-bg); border-left: 4px solid var(--primary-blue); border-radius: var(--radius-sm); font-size: 14px; line-height: 1.6;">
                            <?php echo $q['ai_feedback']; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div style="margin-top: 32px; padding-bottom: 40px;">
                <form method="POST">
                    <button type="submit" name="generate" class="btn btn-outline">Regenerate Questions</button>
                </form>
            </div>
            
        <?php endif; ?>
    </main>
</div>

<script>
async function submitAnswer(questionId) {
    const textarea = document.getElementById('answer-' + questionId);
    const feedbackDiv = document.getElementById('feedback-' + questionId);
    const answer = textarea.value.trim();
    
    if (!answer) {
        alert('Please type an answer first.');
        return;
    }
    
    feedbackDiv.style.display = 'block';
    feedbackDiv.innerHTML = '<em style="color: var(--primary-blue);">AI is evaluating your answer... Please wait.</em>';
    
    try {
        const response = await fetch('<?php echo url("api/evaluate_answer.php"); ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ question_id: questionId, answer: answer })
        });
        
        const data = await response.json();
        
        if (data.feedback) {
            feedbackDiv.innerHTML = data.feedback;
        } else {
            feedbackDiv.innerHTML = '<span style="color:#B42424;">Error: ' + (data.error || 'Could not get feedback') + '</span>';
        }
    } catch (error) {
        feedbackDiv.innerHTML = '<span style="color:#B42424;">Network error. Please try again.</span>';
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>