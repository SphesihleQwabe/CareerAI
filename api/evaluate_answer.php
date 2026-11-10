<?php
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
$questionId = (int)($input['question_id'] ?? 0);
$userAnswer = trim($input['answer'] ?? '');

if ($questionId === 0 || $userAnswer === '') {
    echo json_encode(['error' => 'Invalid data']);
    exit;
}

// Fetch the question and career goal
$stmt = $conn->prepare('SELECT question, career_goal FROM interview_questions WHERE id = ? AND user_id = ? LIMIT 1');
$stmt->bind_param('ii', $questionId, $userId);
$stmt->execute();
$qData = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$qData) {
    echo json_encode(['error' => 'Question not found']);
    exit;
}

// Call AI
$result = evaluate_interview_answer($qData['career_goal'], $qData['question'], $userAnswer);

if (is_array($result) && isset($result['score'])) {
    // Save to DB
    $feedbackHTML = "<div style='margin-bottom: 8px;'><strong>Score: " . $result['score'] . "/100</strong></div>";
    $feedbackHTML .= "<p style='margin-bottom: 12px;'>" . $result['feedback'] . "</p>";
    $feedbackHTML .= "<div style='background: white; padding: 12px; border: 1px solid #DCE7F2; border-radius: 6px;'><strong>Ideal Answer:</strong><br>" . $result['ideal_answer'] . "</div>";
    
    $stmt = $conn->prepare('UPDATE interview_questions SET user_answer = ?, ai_feedback = ? WHERE id = ? AND user_id = ?');
    $stmt->bind_param('ssii', $userAnswer, $feedbackHTML, $questionId, $userId);
    $stmt->execute();
    $stmt->close();

    echo json_encode(['feedback' => $feedbackHTML]);
} else {
    echo json_encode(['error' => $result['error'] ?? 'AI error']);
}