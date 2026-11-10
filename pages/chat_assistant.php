<?php
/**
 * CareerAI - AI Chat Assistant
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

if (empty($_SESSION['user_id'])) {
    header('Location: ' . url('pages/login.php'));
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch existing chat history
$stmt = $conn->prepare('SELECT role, message, created_at FROM chat_messages WHERE user_id = ? ORDER BY id ASC');
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}
$stmt->close();

$page_title = 'AI Career Assistant - ' . SITE_NAME;
$active_nav = '';
$active_dash = 'chat';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-wrapper">
    <?php require_once __DIR__ . '/../includes/dashboard_sidebar.php'; ?>
    
    <main class="dash-content" id="main-content" style="display: flex; flex-direction: column; height: calc(100vh - 72px); padding-bottom: 0;">
        
        <div class="dash-header" style="margin-bottom: 16px;">
            <h1>AI Career Assistant</h1>
            <p>Ask any career-related questions based on your profile.</p>
        </div>

        <!-- Chat Box -->
        <div class="dash-card" style="flex: 1; display: flex; flex-direction: column; overflow: hidden; margin-bottom: 24px; padding: 0;">
            
            <!-- Messages Area -->
            <div id="chat-messages" style="flex: 1; overflow-y: auto; padding: 24px; display: flex; flex-direction: column; gap: 16px; background: var(--very-light-bg);">
                
                <?php if (empty($messages)): ?>
                    <div style="text-align: center; color: var(--secondary-text); margin-top: 40px;">
                        <p>Hello <?php echo e($_SESSION['user_name']); ?>! How can I help you today?</p>
                        <p style="font-size: 13px; margin-top: 8px;">Try asking: "What skills should I learn to become a PHP developer?"</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($messages as $msg): ?>
                        <?php if ($msg['role'] === 'user'): ?>
                            <div style="align-self: flex-end; background: var(--primary-blue); color: white; padding: 12px 16px; border-radius: 12px 12px 0 12px; max-width: 80%; font-size: 14px; line-height: 1.5;">
                                <?php echo nl2br(e($msg['message'])); ?>
                            </div>
                        <?php else: ?>
                            <div style="align-self: flex-start; background: var(--white); border: 1px solid var(--border-color); color: var(--main-text); padding: 12px 16px; border-radius: 12px 12px 12px 0; max-width: 80%; font-size: 14px; line-height: 1.6;">
                                <?php echo nl2br(e($msg['message'])); ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Loading Indicator (Hidden by default) -->
                <div id="typing-indicator" style="display: none; align-self: flex-start; background: var(--white); border: 1px solid var(--border-color); padding: 12px 16px; border-radius: 12px; color: var(--secondary-text); font-size: 14px;">
                    AI is typing...
                </div>
            </div>

            <!-- Input Area -->
            <div style="padding: 16px; background: var(--white); border-top: 1px solid var(--border-color);">
                <form id="chat-form" style="display: flex; gap: 12px;">
                    <input type="text" id="chat-input" placeholder="Type your message here..." autocomplete="off" required style="flex: 1; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); font-size: 15px;">
                    <button type="submit" class="btn btn-primary" id="send-btn">Send</button>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('chat-form');
    const input = document.getElementById('chat-input');
    const messagesContainer = document.getElementById('chat-messages');
    const typingIndicator = document.getElementById('typing-indicator');
    const sendBtn = document.getElementById('send-btn');

    // Scroll to bottom on load
    messagesContainer.scrollTop = messagesContainer.scrollHeight;

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        const message = input.value.trim();
        if (!message) return;

        // 1. Show user message in UI
        appendMessage('user', message);
        input.value = '';
        messagesContainer.scrollTop = messagesContainer.scrollHeight;

        // 2. Disable input and show typing indicator
        input.disabled = true;
        sendBtn.disabled = true;
        sendBtn.textContent = '...';
        typingIndicator.style.display = 'block';
        messagesContainer.scrollTop = messagesContainer.scrollHeight;

        try {
            // 3. Send to API
            const response = await fetch('<?php echo url("api/chat.php"); ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: message })
            });
            const data = await response.json();

            // 4. Hide typing and show AI response
            typingIndicator.style.display = 'none';
            
            if (data.reply) {
                appendMessage('assistant', data.reply);
            } else {
                appendMessage('assistant', 'Error: ' + (data.error || 'Could not get response.'));
            }
        } catch (error) {
            typingIndicator.style.display = 'none';
            appendMessage('assistant', 'Network error. Please try again.');
        } finally {
            // 5. Re-enable input
            input.disabled = false;
            sendBtn.disabled = false;
            sendBtn.textContent = 'Send';
            input.focus();
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    });

    function appendMessage(role, text) {
        const div = document.createElement('div');
        if (role === 'user') {
            div.style.cssText = 'align-self: flex-end; background: var(--primary-blue); color: white; padding: 12px 16px; border-radius: 12px 12px 0 12px; max-width: 80%; font-size: 14px; line-height: 1.5;';
        } else {
            div.style.cssText = 'align-self: flex-start; background: var(--white); border: 1px solid var(--border-color); color: var(--main-text); padding: 12px 16px; border-radius: 12px 12px 12px 0; max-width: 80%; font-size: 14px; line-height: 1.6;';
        }
        
        // Convert newlines to breaks safely
        text.split('\n').forEach((line, index) => {
            if (index > 0) div.appendChild(document.createElement('br'));
            div.appendChild(document.createTextNode(line));
        });

        messagesContainer.insertBefore(div, typingIndicator);
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>