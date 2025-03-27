<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UrbanLoom Support</title>
    <link rel="stylesheet" href="..\style\game_issue_bot.css">
</head>
<body>
    <div class="chatbot-container">
        <div class="chatbot-header">
            <span>🎮</span>
            <span>UrbanLoom Support</span>
        </div>
        
        <div class="chatbot-messages" id="chatMessages">
            <!-- Messages will be added here by JavaScript -->
        </div>
        
        <div class="chatbot-input">
            <input type="text" id="userInput" placeholder="Describe your issue or ask a question...">
            <button id="sendMessage">Send</button>
        </div>
    </div>
    
    <script>
        // Common customer support issues
        const supportIssues = {
            "payment": "We support credit/debit cards, PayPal, and store gift cards. If you're having payment issues, please try refreshing the page or using a different payment method. For persistent issues, we can escalate to our billing team.",
            
            "refund": "Our refund policy allows returns within 14 days of purchase for digital games that have been played less than 2 hours. Please provide your order number, and we'll check your eligibility for a refund.",
            
            "download": "If you're having download issues, please try: 1) Checking your internet connection, 2) Restarting the download, 3) Clearing your browser cache, or 4) Using our desktop app instead of the browser.",
            
            "account": "For account-related issues, we can help with password resets, updating personal information, or recovering access. For security reasons, we may need to verify your identity with the email associated with your account.",
            
            "game": "If you're experiencing technical issues with a game, please let us know which game and what platform you're playing on. Many issues can be resolved by updating your graphics drivers or verifying your game files.",
            
            "order": "For order status inquiries, please provide your order number or the email address used during checkout. We can check if your order has been processed and provide tracking information if applicable."
        };
        
        // Chatbot functionality
        const chatMessages = document.getElementById('chatMessages');
        const userInput = document.getElementById('userInput');
        const sendButton = document.getElementById('sendMessage');
        
        // Initial welcome message
        setTimeout(() => {
            addBotMessage("👋 Welcome to UrbanLoom Support! I'm here to help with any issues you're having. How can I assist you today?");
            
            // Add suggestion chips after welcome message
            addSuggestionChips([
                "Payment issue",
                "Refund request",
                "Download problem",
                "Account help",
                "Game not working",
                "Order status"
            ]);
        }, 500);
        
        // Event listeners
        sendButton.addEventListener('click', processUserInput);
        userInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                processUserInput();
            }
        });
        
        function processUserInput() {
            const message = userInput.value.trim();
            if (message === '') return;
            
            // Add user message to chat
            addUserMessage(message);
            userInput.value = '';
            
            // Show typing animation
            showTypingAnimation();
            
            // Process the response after a delay (simulating thinking)
            setTimeout(() => {
                hideTypingAnimation();
                respondToUser(message);
            }, 1000 + Math.random() * 1000);
        }
        
        function addUserMessage(message) {
            const messageElement = document.createElement('div');
            messageElement.className = 'message user-message';
            messageElement.textContent = message;
            chatMessages.appendChild(messageElement);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
        
        function addBotMessage(message) {
            const messageElement = document.createElement('div');
            messageElement.className = 'message bot-message';
            messageElement.textContent = message;
            chatMessages.appendChild(messageElement);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
        
        function showTypingAnimation() {
            const typingElement = document.createElement('div');
            typingElement.className = 'bot-typing';
            typingElement.id = 'botTyping';
            
            for (let i = 0; i < 3; i++) {
                const dot = document.createElement('div');
                dot.className = 'typing-dot';
                typingElement.appendChild(dot);
            }
            
            chatMessages.appendChild(typingElement);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
        
        function hideTypingAnimation() {
            const typingElement = document.getElementById('botTyping');
            if (typingElement) {
                typingElement.remove();
            }
        }
        
        function addSuggestionChips(suggestions) {
            const chipsContainer = document.createElement('div');
            chipsContainer.className = 'suggestion-chips';
            
            suggestions.forEach(suggestion => {
                const chip = document.createElement('div');
                chip.className = 'suggestion-chip';
                chip.textContent = suggestion;
                chip.addEventListener('click', () => {
                    userInput.value = suggestion;
                    processUserInput();
                });
                chipsContainer.appendChild(chip);
            });
            
            chatMessages.appendChild(chipsContainer);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
        
        function respondToUser(message) {
            message = message.toLowerCase();
            
            // Check for standard greetings
            if (message.includes('hi') || message.includes('hello') || message.includes('hey')) {
                addBotMessage("Hello! I'm here to help with any UrbanLoom issues. What seems to be the problem today?");
                return;
            }
            
            // Check for keywords related to common issues
            if (message.includes('payment') || message.includes('pay') || message.includes('card') || message.includes('checkout')) {
                addBotMessage(supportIssues.payment);
                addSuggestionChips(["Still having payment issues", "Contact billing team", "Try another payment method"]);
                return;
            }
            
            if (message.includes('refund') || message.includes('return') || message.includes('money back')) {
                addBotMessage(supportIssues.refund);
                addSuggestionChips(["My order number is", "Been more than 14 days", "Haven't played the game"]);
                return;
            }
            
            if (message.includes('download') || message.includes('install') || message.includes('loading')) {
                addBotMessage(supportIssues.download);
                addSuggestionChips(["Still can't download", "How to verify files", "Using the desktop app"]);
                return;
            }
            
            if (message.includes('account') || message.includes('login') || message.includes('password') || message.includes('sign in')) {
                addBotMessage(supportIssues.account);
                addSuggestionChips(["Reset password", "Can't login", "Update account info"]);
                return;
            }
            
            if (message.includes('game') || message.includes('play') || message.includes('crash') || message.includes('bug') || message.includes('not working')) {
                addBotMessage(supportIssues.game);
                addSuggestionChips(["Game crashes", "Graphics issues", "Sound problems", "Game won't launch"]);
                return;
            }
            
            if (message.includes('order') || message.includes('purchase') || message.includes('bought') || message.includes('status')) {
                addBotMessage(supportIssues.order);
                addSuggestionChips(["Check order status", "Missing purchase", "Order delay"]);
                return;
            }
            
            if (message.includes('thank')) {
                addBotMessage("You're welcome! Is there anything else I can help you with today?");
                addSuggestionChips(["Yes, another question", "No, that's all"]);
                return;
            }
            
            if (message.includes('bye') || message.includes('goodbye') || message.includes("that's all") || message.includes("no more questions")) {
                addBotMessage("Thank you for contacting UrbanLoom Support! If you need further assistance, don't hesitate to reach out again. Have a great day!");
                return;
            }
            
            if (message.includes('human') || message.includes('agent') || message.includes('person') || message.includes('representative')) {
                addBotMessage("I understand you'd like to speak with a human representative. I'll connect you with one of our support agents. Please provide a brief description of your issue and the best email to reach you, and someone will contact you within 24 hours.");
                return;
            }
            
            // Default response for unrecognized queries
            addBotMessage("I'm not sure I understand the specific issue. Could you provide more details about what you're experiencing? Or you can select one of these common support categories:");
            
            // Add suggestion chips for common questions
            addSuggestionChips([
                "Payment issue",
                "Refund request",
                "Download problem",
                "Account help",
                "Game not working",
                "Order status",
                "Speak to human agent"
            ]);
        }
    </script>
</body>
</html>