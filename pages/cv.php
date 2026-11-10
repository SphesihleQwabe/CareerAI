<?php
/**
 * CareerAI - User CV Upload
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

// Fetch existing CV if one exists
$stmt = $conn->prepare('SELECT * FROM cvs WHERE user_id = ? ORDER BY uploaded_at DESC LIMIT 1');
$stmt->bind_param('i', $userId);
$stmt->execute();
$existingCv = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_FILES['cv_file']) && $_FILES['cv_file']['error'] === UPLOAD_ERR_OK) {
        
        $file = $_FILES['cv_file'];
        
        // 1. File Size Limit (5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            $errors[] = 'File is too large. Maximum size is 5MB.';
        }
        
        // 2. File Type Validation (MIME type)
        $allowedMimes = [
            'application/pdf' => 'pdf',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/msword' => 'doc'
        ];
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!array_key_exists($mime, $allowedMimes)) {
            $errors[] = 'Invalid file type. Only PDF and Word documents (.docx, .doc) are allowed.';
        }
        
        // 3. Process the file if no errors
        if (empty($errors)) {
            $extension = $allowedMimes[$mime];
            // Generate a random secure filename
            $newFilename = bin2hex(random_bytes(16)) . '.' . $extension;
            
            $uploadDir = __DIR__ . '/../uploads/';
            $destination = $uploadDir . $newFilename;
            
            // Move the file from temp folder to uploads folder
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                
                // Delete old CV if replacing
                if ($existingCv) {
                    $oldPath = $uploadDir . $existingCv['stored_filename'];
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                    $stmt = $conn->prepare('DELETE FROM cvs WHERE id = ?');
                    $stmt->bind_param('i', $existingCv['id']);
                    $stmt->execute();
                    $stmt->close();
                }
                
                // Save to database
                $originalName = $file['name'];
                $stmt = $conn->prepare(
                    'INSERT INTO cvs (user_id, original_filename, stored_filename, file_size, file_type) 
                     VALUES (?, ?, ?, ?, ?)'
                );
                $stmt->bind_param('issss', $userId, $originalName, $newFilename, $file['size'], $mime);
                
                if ($stmt->execute()) {
                    $success = true;
                    // Refresh existing CV data
                    $stmt->close();
                    $stmt = $conn->prepare('SELECT * FROM cvs WHERE user_id = ? ORDER BY uploaded_at DESC LIMIT 1');
                    $stmt->bind_param('i', $userId);
                    $stmt->execute();
                    $existingCv = $stmt->get_result()->fetch_assoc();
                    $stmt->close();
                } else {
                    $errors[] = 'Failed to save file details to the database.';
                }
            } else {
                $errors[] = 'Failed to move the uploaded file. Check folder permissions.';
            }
        }
    } else {
        $errors[] = 'Please select a file to upload.';
    }
}

$page_title = 'My CV - ' . SITE_NAME;
$active_nav = '';
$active_dash = 'cv';

require_once __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-wrapper">
    
    <?php require_once __DIR__ . '/../includes/dashboard_sidebar.php'; ?>

    <main class="dash-content" id="main-content">
        
        <div class="dash-header">
            <h1>My CV</h1>
            <p>Upload your CV so our AI can analyze it and match you with jobs.</p>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success" role="alert" style="max-width: 800px;">
                Your CV has been uploaded successfully!
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
            <?php if ($existingCv): ?>
                <!-- Existing CV Details -->
                <div style="margin-bottom: 24px; padding: 20px; background: var(--very-light-bg); border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                    <p style="margin-bottom: 8px; font-size: 16px;"><strong>Current CV:</strong> <?php echo e($existingCv['original_filename']); ?></p>
                    <p style="font-size: 13px; color: var(--secondary-text); margin-bottom: 16px;">
                        Uploaded on: <?php echo date('d M Y, H:i', strtotime($existingCv['uploaded_at'])); ?> 
                        (Size: <?php echo round($existingCv['file_size'] / 1024, 1); ?> KB)
                    </p>
                    <!-- Button to securely view/download the file -->
                    <a href="<?php echo url('pages/download_cv.php?id=' . $existingCv['id']); ?>" target="_blank" class="btn btn-outline" style="padding: 8px 16px; font-size: 14px;">
                        View / Download CV
                    </a>
                </div>
                <h3 style="font-size: 18px; margin-bottom: 12px;">Upload a New CV (Replace Existing)</h3>
            <?php else: ?>
                <h3 style="font-size: 18px; margin-bottom: 12px;">Upload Your CV</h3>
            <?php endif; ?>

            <!-- Upload Form -->
            <form method="POST" action="<?php echo url('pages/cv.php'); ?>" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="cv_file">Select File (PDF or Word Document, Max 5MB)</label>
                    <input type="file" id="cv_file" name="cv_file" accept=".pdf,.doc,.docx" required style="padding: 10px; border: 1px dashed var(--border-color); width: 100%; border-radius: var(--radius-sm);">
                </div>
                
                <button type="submit" class="btn btn-primary" style="margin-top: 16px;">Upload CV</button>
            </form>
        </div>

    </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>