<?php
/**
 * CareerAI - Chat API
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/ai.php';

header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'];
$input = json_decode(file_get_contents('php://input'), true);
$message = trim($input['message'] ?? '');

if ($message === '') {
    echo json_encode(['error' => 'Message is empty']);
    exit;
}

// 1. Save user message to DB
$stmt = $conn->prepare('INSERT INTO chat_messages (user_id, role, message) VALUES (?, ?, ?)');
$role = 'user';
$stmt->bind_param('iss', $userId, $role, $message);
$stmt->execute();
$stmt->close();

// 2. Fetch full chat history for context
$stmt = $conn->prepare('SELECT role, message FROM chat_messages WHERE user_id = ? ORDER BY id ASC LIMIT 20');
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
$history = [];
while ($row = $result->fetch_assoc()) {
    $history[] = $row;
}
$stmt->close();

// 3. Get user profile for AI context
$stmt = $conn->prepare('SELECT career_goal, bio FROM profiles WHERE user_id = ? LIMIT 1');
$stmt->bind_param('i', $userId);
$stmt->execute();
$profile = $stmt->get_result()->fetch_assoc();
$stmt->close();

$profileText = "Career Goal: " . ($profile['career_goal'] ?? 'Not set') . "\nSkills/Bio: " . ($profile['bio'] ?? 'Not set');

// 4. Call AI
$aiResponse = chat_with_ai($history, $profileText);

// 5. Save AI response to DB
$stmt = $conn->prepare('INSERT INTO chat_messages (user_id, role, message) VALUES (?, ?, ?)');
$role = 'assistant';
$stmt->bind_param('iss', $userId, $role, $aiResponse);
$stmt->execute();
$stmt->close();

echo json_encode(['reply' => $aiResponse]);