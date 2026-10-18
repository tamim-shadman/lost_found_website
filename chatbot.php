<?php
declare(strict_types=1);

require_once __DIR__ . '/src/autoload.php';

use App\Services\AuthService;
use App\Services\ChatbotService;
use App\Utils\Security;

Security::initSession();
$auth = new AuthService();
$currentUser = $auth->getCurrentUser();

$chatbot = new ChatbotService();
$sessionId = session_id();
$history = $chatbot->getHistory($sessionId);
$suggestions = $chatbot->getSuggestions();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="images/logo.png">
    <title>AI Recovery Assistant — Lost & Found Bangladesh</title>
    <meta name="description" content="Instant 24/7 AI-powered assistance for lost items recovery, police GD procedures, and claim verification in Bangladesh.">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/design-system.css">
    <style>
      .chatbot-page {
        padding-top: 130px;
        padding-bottom: 60px;
      }
      .chat-window {
        height: 520px;
        display: flex;
        flex-direction: column;
        background: var(--bg-surface-elevated);
        border: 1px solid var(--border-medium);
        border-radius: var(--radius-xl);
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
      }
      .chat-messages-area {
        flex: 1;
        overflow-y: auto;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
      }
      .chat-bubble {
        max-width: 80%;
        padding: 0.85rem 1.15rem;
        border-radius: var(--radius-lg);
        font-size: 0.925rem;
        line-height: 1.5;
        position: relative;
        word-break: break-word;
      }
      .bubble-bot {
        align-self: flex-start;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid var(--border-subtle);
        color: var(--text-primary);
        border-bottom-left-radius: 4px;
      }
      .bubble-user {
        align-self: flex-end;
        background: linear-gradient(135deg, var(--color-emerald-600), var(--color-emerald-500));
        color: #ffffff;
        border-bottom-right-radius: 4px;
        box-shadow: 0 4px 14px var(--color-emerald-glow);
      }
      .chat-input-bar {
        padding: 1rem 1.25rem;
        border-top: 1px solid var(--border-subtle);
        background: rgba(7, 9, 14, 0.6);
      }
      .suggestion-chip {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--border-subtle);
        color: var(--text-secondary);
        font-size: 0.8rem;
        padding: 0.4rem 0.85rem;
        border-radius: var(--radius-pill);
        cursor: pointer;
        transition: all var(--transition-fast);
        white-space: nowrap;
      }
      .suggestion-chip:hover {
        background: rgba(16, 185, 129, 0.15);
        color: var(--color-emerald-400);
        border-color: rgba(16, 185, 129, 0.35);
      }
      .typing-indicator span {
        display: inline-block;
        width: 6px;
        height: 6px;
        background: var(--text-muted);
        border-radius: 50%;
        margin-right: 3px;
        animation: typingBounce 1.4s infinite both;
      }
      .typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
      .typing-indicator span:nth-child(3) { animation-delay: 0.4s; }
      @keyframes typingBounce {
        0%, 80%, 100% { transform: scale(0); }
        40% { transform: scale(1); }
      }
    </style>
</head>
<body>
    <?php include __DIR__ . '/components/navbar.php'; ?>

    <main class="container mx-auto px-3 chatbot-page" style="max-width: 900px;">
        <div class="text-center mb-4">
            <span class="brand-badge mb-2"><i class="bi bi-robot me-1"></i> 24/7 AI-Powered Intelligence</span>
            <h1 class="text-light fw-bold mb-1" style="font-size: 2.2rem;">Lost & Found Assistant</h1>
            <p class="text-secondary">Instant answers on claim verification, police GD guidance, and recovery workflows.</p>
        </div>

        <!-- Suggestions Carousel / Bar -->
        <div class="d-flex gap-2 overflow-x-auto pb-3 mb-3">
            <?php foreach ($suggestions as $sug): ?>
                <button type="button" class="suggestion-chip" onclick="askPreset('<?= addslashes(htmlspecialchars($sug, ENT_QUOTES, 'UTF-8')) ?>')">
                    <?= htmlspecialchars($sug, ENT_QUOTES, 'UTF-8') ?>
                </button>
            <?php endforeach; ?>
        </div>

        <!-- Chat Window -->
        <div class="chat-window">
            <!-- Messages Container -->
            <div class="chat-messages-area" id="chatMessagesArea">
                <!-- Welcome Initial Message -->
                <div class="d-flex align-items-start gap-2 mb-2">
                    <img src="images/bot.png" alt="Bot" width="32" height="32" class="rounded-circle border border-emerald">
                    <div class="chat-bubble bubble-bot">
                        Hello! I am your <strong>Lost & Found Bangladesh</strong> assistant. How can I help you recover an item or file an emergency alert today?
                    </div>
                </div>

                <!-- Previous session history if any -->
                <?php foreach ($history as $h): ?>
                    <?php if ($h['sender'] === 'user'): ?>
                        <div class="chat-bubble bubble-user">
                            <?= htmlspecialchars($h['message'], ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    <?php else: ?>
                        <div class="d-flex align-items-start gap-2 mb-2">
                            <img src="images/bot.png" alt="Bot" width="32" height="32" class="rounded-circle border border-emerald">
                            <div class="chat-bubble bubble-bot">
                                <?= htmlspecialchars($h['message'], ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <!-- Typing indicator -->
            <div id="typingIndicator" class="px-4 py-2" style="display: none;">
                <div class="d-flex align-items-center gap-2 text-muted small">
                    <span class="typing-indicator"><span></span><span></span><span></span></span>
                    <span>Assistant is typing...</span>
                </div>
            </div>

            <!-- Input Bar -->
            <div class="chat-input-bar">
                <form id="chatForm" onsubmit="handleChatSubmit(event)" class="d-flex gap-2">
                    <input type="text" class="form-control flex-grow-1" id="chatInputMessage" placeholder="Ask a question (e.g. How to report lost wallet?)..." autocomplete="off">
                    <button type="submit" class="btn btn-primary px-4" id="chatSendBtn">
                        <span>Send</span>
                        <i class="bi bi-send-fill ms-1"></i>
                    </button>
                </form>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/components/footer.php'; ?>

    <script>
      const chatArea = document.getElementById('chatMessagesArea');
      const chatInput = document.getElementById('chatInputMessage');
      const typingInd = document.getElementById('typingIndicator');

      function scrollToBottom() {
        chatArea.scrollTop = chatArea.scrollHeight;
      }
      scrollToBottom();

      function askPreset(text) {
        chatInput.value = text;
        handleChatSubmit(new Event('submit'));
      }

      function handleChatSubmit(e) {
        if (e) e.preventDefault();
        const text = chatInput.value.trim();
        if (!text) return;

        // Append user bubble
        const userDiv = document.createElement('div');
        userDiv.className = 'chat-bubble bubble-user animate-fade-in';
        userDiv.textContent = text;
        chatArea.appendChild(userDiv);

        chatInput.value = '';
        scrollToBottom();

        // Show typing indicator
        typingInd.style.display = 'block';

        fetch('api/chatbot.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify({ action: 'ask', message: text })
        })
        .then(r => r.json())
        .then(res => {
          typingInd.style.display = 'none';
          const botDiv = document.createElement('div');
          botDiv.className = 'd-flex align-items-start gap-2 mb-2 animate-fade-in';
          botDiv.innerHTML = `
            <img src="images/bot.png" alt="Bot" width="32" height="32" class="rounded-circle border border-emerald">
            <div class="chat-bubble bubble-bot">${res.data ? res.data.reply : 'I am here to assist with lost and found queries.'}</div>
          `;
          chatArea.appendChild(botDiv);
          scrollToBottom();
        })
        .catch(() => {
          typingInd.style.display = 'none';
          const errDiv = document.createElement('div');
          errDiv.className = 'chat-bubble bubble-bot text-danger';
          errDiv.textContent = 'Connection error. Please try asking again.';
          chatArea.appendChild(errDiv);
          scrollToBottom();
        });
      }
    </script>
</body>
</html>