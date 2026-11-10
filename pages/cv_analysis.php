<?php
/**
 * CareerAI - AI CV Analysis
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/ai.php';
require_once __DIR__ . '/../vendor/autoload.php'; // Loads the PDF Parser

use Smalot\PdfParser\Parser;

// Require login
if (empty($_SESSION['user_id'])) {
    header('Location: ' . url('pages/login.php'));
    exit;
}

$userId = $_SESSION['user_id'];
$error = '';
$isAnalyzing = false;

// Fetch the user's CV
$stmt = $conn->prepare('SELECT * FROM cvs WHERE user_id = ? ORDER BY uploaded_at DESC LIMIT 1');
$stmt->bind_param('i', $userId);
$stmt->execute();
$cv = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$cv) {
    header('Location: ' . url('pages/cv.php'));
    exit;
}

// Fetch existing analysis
$stmt = $conn->prepare('SELECT * FROM cv_analysis WHERE user_id = ? AND cv_id = ? LIMIT 1');
$stmt->bind_param('ii', $userId, $cv['id']);
$stmt->execute();
$analysis = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Handle "Analyze" request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['analyze'])) {
    $isAnalyzing = true;
    $filePath = __DIR__ . '/../uploads/' . $cv['stored_filename'];

    if (file_exists($filePath)) {
        $text = '';
        
        try {
            // 1. Extract text from PDF
            if ($cv['file_type'] === 'application/pdf') {
                $parser = new Parser();
                $pdf = $parser->parseFile($filePath);
                $text = $pdf->getText();
            } else {
                $error = 'Currently, only PDF files can be analyzed. Please upload a PDF version of your CV.';
            }

            // 2. Check if text extraction was successful
            if (empty($error) && empty(trim((string)$text))) {
                $error = 'Could not extract text from the CV. It might be a scanned image (picture). Please upload a PDF with selectable text.';
            }

            // 3. Send to AI if no errors
            if (empty($error)) {
                // Limit text to 6000 characters to avoid massive API costs and timeouts
                $cleanText = substr(trim($text), 0, 6000); 
                $aiResult = analyze_cv_with_ai($cleanText);

                if (strpos($aiResult, 'Error:') === 0 || strpos($aiResult, 'cURL Error') === 0 || strpos($aiResult, 'AI API Error') === 0) {
                    $error = $aiResult;
                } else {
                    // 4. Save result to database
                    $stmt = $conn->prepare('INSERT INTO cv_analysis (user_id, cv_id, ai_response) VALUES (?, ?, ?)');
                    $stmt->bind_param('iis', $userId, $cv['id'], $aiResult);
                    $stmt->execute();
                    $stmt->close();

                    // Refresh page to show the saved result
                    header('Location: ' . url('pages/cv_analysis.php'));
                    exit;
                }
            }
        } catch (Exception $e) {
            $error = 'Error reading PDF: ' . $e->getMessage();
        }
    } else {
        $error = 'CV file not found on the server. Please re-upload.';
    }
}

$page_title = 'AI CV Analysis - ' . SITE_NAME;
$active_nav = '';
$active_dash = 'analysis';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-wrapper">
    
    <?php require_once __DIR__ . '/../includes/dashboard_sidebar.php'; ?>

    <main class="dash-content" id="main-content">
        
        <div class="dash-header">
            <h1>AI CV Analysis</h1>
            <p>Our AI will analyze your CV and provide professional feedback.</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error" role="alert" style="max-width: 800px;">
                <?php echo e($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($analysis): ?>
            <!-- Display Analysis Result -->
            <div class="dash-card" style="max-width: 100%;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 16px;">
                    <h2 style="font-size: 20px; margin: 0;">Analysis Results</h2>
                    <span style="font-size: 13px; color: var(--secondary-text);">Analyzed on: <?php echo date('d M Y', strtotime($analysis['created_at'])); ?></span>
                </div>
                
                <div class="ai-output" style="line-height: 1.8; color: var(--main-text);">
                    <?php echo $analysis['ai_response']; ?>
                </div>

                <!-- Re-analyze button -->
                <form method="POST" style="margin-top: 32px; border-top: 1px solid var(--border-color); padding-top: 20px;">
                    <p style="font-size: 13px; color: var(--secondary-text); margin-bottom: 12px;">Updated your CV? You can run a new analysis.</p>
                    <button type="submit" name="analyze" class="btn btn-outline">Re-Analyze CV</button>
                </form>
            </div>

        <?php else: ?>
            <!-- Show "Start Analysis" Prompt -->
            <div class="dash-card" style="text-align: center; padding: 60px 20px;">
                <?php if ($isAnalyzing): ?>
                    <h2 style="color: var(--primary-blue); margin-bottom: 12px;">Analyzing your CV...</h2>
                    <p style="color: var(--secondary-text);">Our AI is reading your file and generating feedback. This takes about 10 seconds...</p>
                <?php else: ?>
                    <h2 style="color: var(--dark-navy); margin-bottom: 12px;">Ready to analyze your CV?</h2>
                    <p style="color: var(--secondary-text); margin-bottom: 24px; max-width: 450px; margin-left: auto; margin-right: auto;">
                        We will securely extract the text from <strong><?php echo e($cv['original_filename']); ?></strong> and send it to Google Gemini to get professional ATS feedback.
                    </p>
                    <form method="POST">
                        <button type="submit" name="analyze" class="btn btn-primary btn-large">Start AI Analysis</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </main>
</div>

<!-- Simple CSS to style the HTML returned by the AI -->
<style>
    .ai-output h3 { margin-top: 24px; margin-bottom: 12px; color: var(--dark-navy); font-size: 18px; }
    .ai-output ul { margin-bottom: 16px; padding-left: 20px; list-style-type: disc; }
    .ai-output li { margin-bottom: 6px; }
    .ai-output strong { color: var(--dark-navy); }
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>