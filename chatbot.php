<?php

require_once __DIR__ . '/includes/layout.php';
require_login();

require_once __DIR__ . '/includes/groq-chatbot.php';

$userEmail = $_SESSION['user_email'];
$messages = [];
$error = null;

// চ্যাট মেসেজ সংরক্ষণ এবং পুনরুদ্ধার
$chatSessionFile = sys_get_temp_dir() . '/chat_' . md5($userEmail) . '.json';

// বিদ্যমান চ্যাট লোড করুন
if (file_exists($chatSessionFile)) {
    $messages = json_decode(file_get_contents($chatSessionFile), true) ?? [];
}

// নতুন বার্তা প্রক্রিয়া করুন
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = $_POST['message'] ?? '';
    
    if (empty($input)) {
        $error = 'দয়া করে একটি বার্তা লিখুন।';
    } else {
        // ব্যবহারকারীর বার্তা যোগ করুন
        $messages[] = [
            'role' => 'user',
            'content' => htmlspecialchars($input)
        ];
        
        // Groq API কল করুন
        try {
            $apiKey = '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';
            
            if (empty($apiKey)) {
                throw new Exception("⚠️ Groq API key সেট করা হয়নি। <br><br><strong>সেটআপ:</strong><br>1. <a href='https://groq.com' target='_blank'>groq.com</a> এ যান<br>2. বিনামূল্যে API key পান<br>3. আপনার সার্ভারে সেট করুন: <br><code>export GROQ_API_KEY='your-key'</code>");
            }
            
            $chatbot = new GroqChatbot($apiKey);
            
            // লাইব্রেরি প্রসঙ্গ সংগ্রহ করুন
            $context = [
                'library_name' => 'আমাদের লাইব্রেরি',
                'total_books' => fetch_one('SELECT COUNT(*) as count FROM books', [])['count'] ?? 0,
                'opening_hours' => '৯:০০ AM - ৬:০০ PM'
            ];
            
            $response = $chatbot->askAboutLibrary($input, $context);
            
            if ($response['success']) {
                $messages[] = [
                    'role' => 'assistant',
                    'content' => $response['message']
                ];
            } else {
                $error = "AI উত্তর দিতে পারেনি: " . ($response['error'] ?? 'অজানা ত্রুটি');
                // সর্বশেষ ব্যবহারকারী বার্তা সরান যদি AI ব্যর্থ হয়
                array_pop($messages);
            }
        } catch (Exception $e) {
            $error = "ত্রুটি: " . $e->getMessage();
            // সর্বশেষ ব্যবহারকারী বার্তা সরান যদি ব্যর্থ হয়
            array_pop($messages);
        }
        
        // কথোপকথন সংরক্ষণ করুন
        file_put_contents($chatSessionFile, json_encode($messages));
    }
    
    // AJAX অনুরোধের জন্য JSON রেসপন্স
    if (!empty($_POST['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => empty($error),
            'error' => $error,
            'messages' => $messages
        ]);
        exit;
    }
}

// চ্যাট ক্লিয়ার করুন
if (isset($_GET['clear'])) {
    if (file_exists($chatSessionFile)) {
        unlink($chatSessionFile);
    }
    redirect_to('chatbot.php');
}

render_header('চ্যাটবট - লাইব্রেরি সহায়ক');
?>

<div class="chatbot-container">
    <div class="chatbot-header">
        <h1>🤖 AI চ্যাটবট</h1>
        <p>আপনার লাইব্রেরি সহায়ক</p>
    </div>
    
    <div class="chat-messages" id="chatMessages">
        <?php if (empty($messages)): ?>
            <div class="no-messages">
                <div class="no-messages-icon">💬</div>
                <div class="empty-state-text">
                    <strong>স্বাগতম!</strong><br>
                    বই সম্পর্কে প্রশ্ন করুন বা লাইব্রেরি সম্পর্কে জানুন।<br>
                    <small style="color: #999;">উদা: "সবচেয়ে জনপ্রিয় বই কি?", "একটি ভালো উপন্যাস সুপারিশ করুন"</small>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($messages as $msg): ?>
                <div class="message <?php echo $msg['role']; ?>">
                    <div class="message-icon">
                        <?php echo $msg['role'] === 'user' ? '👤' : '🤖'; ?>
                    </div>
                    <div class="message-content">
                        <?php echo nl2br(htmlspecialchars($msg['content'])); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <?php if (!empty($error)): ?>
        <div class="error-message">
            <strong>⚠️ ত্রুটি:</strong> <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" class="chat-input-section" id="chatForm">
        <textarea name="message" placeholder="আপনার প্রশ্ন লিখুন..." required></textarea>
        <button type="submit">পাঠান <i class="fas fa-paper-plane"></i></button>
    </form>
    
    <div class="chatbot-actions" id="chatClearAction" style="<?php echo empty($messages) ? 'display: none;' : ''; ?>">
        <a href="chatbot.php?clear=1" onclick="return confirm('কথোপকথন মুছে দিতে চান?')">🗑️ কথোপকথন পরিষ্কার করুন</a>
    </div>
</div>

<script>
const chatMessages = document.getElementById('chatMessages');
chatMessages.scrollTop = chatMessages.scrollHeight;

const chatForm = document.getElementById('chatForm');
const textarea = chatForm.querySelector('textarea');
const chatClearAction = document.getElementById('chatClearAction');

chatForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const message = textarea.value.trim();
    if (!message) return;

    // Clear textarea
    textarea.value = '';
    textarea.style.height = 'auto';

    // Append user message immediately
    appendMessage('user', message);

    // Append typing indicator
    const typingDiv = appendTypingIndicator();

    // Send via AJAX
    const formData = new FormData();
    formData.append('message', message);
    formData.append('ajax', '1');

    fetch('chatbot.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // Remove typing indicator
        typingDiv.remove();

        if (data.success) {
            // Find the last assistant message
            const assistantMsg = data.messages[data.messages.length - 1];
            if (assistantMsg && assistantMsg.role === 'assistant') {
                appendMessage('assistant', assistantMsg.content);
            }
            if (chatClearAction) {
                chatClearAction.style.display = 'flex';
            }
        } else {
            appendErrorMessage(data.error || 'অজানা ত্রুটি ঘটেছে');
        }
    })
    .catch(err => {
        typingDiv.remove();
        appendErrorMessage('সার্ভারের সাথে সংযোগ স্থাপন করা সম্ভব হয়নি।');
        console.error(err);
    });
});

// Enter key sends message (Shift+Enter inserts new line)
textarea.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        chatForm.requestSubmit();
    }
});

// Auto-expand textarea height
textarea.addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = (this.scrollHeight) + 'px';
});

function appendMessage(role, content) {
    // Remove empty state if present
    const noMessages = chatMessages.querySelector('.no-messages');
    if (noMessages) {
        noMessages.remove();
    }

    const msgDiv = document.createElement('div');
    msgDiv.className = `message ${role}`;
    
    const iconDiv = document.createElement('div');
    iconDiv.className = 'message-icon';
    iconDiv.textContent = role === 'user' ? '👤' : '🤖';
    
    const contentDiv = document.createElement('div');
    contentDiv.className = 'message-content';
    contentDiv.innerHTML = escapeHtml(content).replace(/\n/g, '<br>');
    
    msgDiv.appendChild(iconDiv);
    msgDiv.appendChild(contentDiv);
    chatMessages.appendChild(msgDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function appendTypingIndicator() {
    const msgDiv = document.createElement('div');
    msgDiv.className = 'message assistant typing';
    
    const iconDiv = document.createElement('div');
    iconDiv.className = 'message-icon';
    iconDiv.textContent = '🤖';
    
    const contentDiv = document.createElement('div');
    contentDiv.className = 'message-content';
    contentDiv.innerHTML = '<div class="typing-indicator"><span></span><span></span><span></span></div>';
    
    msgDiv.appendChild(iconDiv);
    msgDiv.appendChild(contentDiv);
    chatMessages.appendChild(msgDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;
    return msgDiv;
}

function appendErrorMessage(msg) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.innerHTML = `<strong>⚠️ ত্রুটি:</strong> ${escapeHtml(msg)}`;
    chatMessages.appendChild(errorDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}
</script>

<?php render_footer(); ?>
