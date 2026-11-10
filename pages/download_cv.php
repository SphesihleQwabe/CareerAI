<?php
/**
 * CareerAI - Secure CV Downloader
 * 
 * Verifies that the logged-in user owns the CV before serving it.
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

// Require login
if (empty($_SESSION['user_id'])) {
    die('Unauthorized access.');
}

$userId = $_SESSION['user_id'];
$cvId   = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($cvId <= 0) {
    die('Invalid file request.');
}

// Verify ownership
$stmt = $conn->prepare('SELECT original_filename, stored_filename, file_type FROM cvs WHERE id = ? AND user_id = ? LIMIT 1');
$stmt->bind_param('ii', $cvId, $userId);
$stmt->execute();
$result = $stmt->get_result();
$cv = $result->fetch_assoc();
$stmt->close();

if (!$cv) {
    die('File not found or you do not have permission to view it.');
}

$filePath = __DIR__ . '/../uploads/' . $cv['stored_filename'];

if (!file_exists($filePath)) {
    die('File does not exist on the server.');
}

// Set headers to force the browser to display or download the file
header('Content-Description: File Transfer');
header('Content-Type: ' . $cv['file_type']);
header('Content-Disposition: inline; filename="' . basename($cv['original_filename']) . '"'); // 'inline' means view in browser (for PDFs)
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filePath));

// Clear output buffer and read the file
ob_clean();
flush();
readfile($filePath);
exit;