<?php
// Start the session
session_start();

// Database connection
$host = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "urbanloom";

// Create connection
$conn = new mysqli($host, $dbUsername, $dbPassword, $dbName);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle login (this is a simplified version)
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $sql = "SELECT user_id, username, password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        // Verify password (in a real app, use password_verify with hashed passwords)
        if ($password == $user['password']) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
        }
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Handle getting users
if (isset($_GET['action']) && $_GET['action'] == 'getUsers') {
    $query = "SELECT user_id, username, created_at FROM users WHERE user_id != ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    
    header('Content-Type: application/json');
    echo json_encode($users);
    exit();
}

// Handle message sending
if (isset($_POST['action']) && $_POST['action'] == 'sendMessage') {
    $from_user = $_SESSION['user_id'];
    $to_user = $_POST['to_user'];
    $message = $_POST['message'];
    
    $query = "INSERT INTO messages (from_user_id, to_user_id, message_text, sent_at) 
              VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iis", $from_user, $to_user, $message);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }
    exit();
}

// Handle getting messages
if (isset($_GET['action']) && $_GET['action'] == 'getMessages') {
    $user1 = $_SESSION['user_id'];
    $user2 = $_GET['user_id'];
    
    $query = "SELECT * FROM messages 
              WHERE (from_user_id = ? AND to_user_id = ?) 
              OR (from_user_id = ? AND to_user_id = ?) 
              ORDER BY sent_at ASC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiii", $user1, $user2, $user2, $user1);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
    
    header('Content-Type: application/json');
    echo json_encode($messages);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Application</title>
    <link rel="stylesheet" href="..\style\chatting.css">
</head>
<body>
    <?php if (!isset($_SESSION['user_id'])): ?>
    <!-- Login Form -->
    <div class="login-container">
        <h2>Login to Chat</h2>
        <form method="post" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" name="login" class="btn">Login</button>
        </form>
    </div>
    <?php else: ?>
         <!-- Navbar -->
<div class="navbar">
    <div class="logo"><img src="..\elements\STK-20250209-WA0001.webp" alt=""></div>
    <div class="menu">
        <a href="dashboad.php">Home</a>
        <a href="browse.php">Browse</a>
        <a href="wishlist.php">Wishlist</a>
        <a href="library.php">Library</a>
        <a href="chatting.php">Friends</a>
        <a href="users_profile.php">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 48 48">
                <path d="M24,4C12.972,4,4,12.972,4,24s8.972,20,20,20s20-8.972,20-20S35.028,4,24,4z M24,13c2.761,0,5,2.239,5,5 c0,2.761-2.239,5-5,5s-5-2.239-5-5C19,15.239,21.239,13,24,13z M33,29.538C33,32.397,29.353,35,24,35s-9-2.603-9-5.462v-0.676 C15,27.834,15.834,27,16.862,27h14.276C32.166,27,33,27.834,33,28.862V29.538z"></path>
            </svg>
        </a>
        <?php
        // Start session
        // session_start();

        // Assuming user_id is stored in the session after login
        $user_id = $_SESSION['user_id'] ?? null;

        if ($user_id) {
            // Query to fetch the username
            $sql = "SELECT username FROM users WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                echo '<span class="username" style="margin-left: 10px; font-size: 1rem; color: #555;">' . htmlspecialchars($row['username']) . '</span>';
            } else {
                echo '<span class="username" style="margin-left: 10px; font-size: 1rem; color: #555;">Guest</span>';
            }
        } else {
            echo '<span class="username" style="margin-left: 10px; font-size: 1rem; color: #555;">Guest</span>';
        }
        ?>
    </div>
</div>
    <!-- Chat Application -->
    <div class="container">
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Urban Loom</h2>
                <div class="user-profile">
                    <!-- <a href="?logout=1" class="logout-btn">Logout</a> -->
                </div>
            </div>
            <div class="search-container">
                <input type="text" id="userSearch" placeholder="Search users...">
            </div>
            <div class="users-list" id="usersList">
                <!-- Users will be loaded here via JavaScript -->
                <div class="loading-users">Loading users...</div>
            </div>
        </div>
        <div class="chat-container">
            <div class="chat-header" id="chatHeader">
                <h3>Select a user to start chatting</h3>
            </div>
            <div class="messages" id="messageArea">
                <div class="welcome-message">
                    <h3>Welcome, <?php echo $_SESSION['username']; ?></h3>
                    <p>Select a user from the list to start a conversation</p>
                </div>
            </div>
            <div class="chat-input-container">
                <textarea id="messageInput" placeholder="Type a message..." disabled></textarea>
                <button id="sendButton" disabled>↑</button>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let selectedUserId = null;
            const usersList = document.getElementById('usersList');
            const chatHeader = document.getElementById('chatHeader');
            const messageArea = document.getElementById('messageArea');
            const messageInput = document.getElementById('messageInput');
            const sendButton = document.getElementById('sendButton');
            const userSearch = document.getElementById('userSearch');
            
            // Load users
            loadUsers();
            
            // Search users
            userSearch.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const userItems = usersList.querySelectorAll('.user-item');
                
                userItems.forEach(item => {
                    const userName = item.querySelector('.user-name').textContent.toLowerCase();
                    if (userName.includes(searchTerm)) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
            
            // Send message
            sendButton.addEventListener('click', sendMessage);
            messageInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });
            
            function loadUsers() {
                fetch('?action=getUsers')
                    .then(response => response.json())
                    .then(users => {
                        usersList.innerHTML = '';
                        
                        users.forEach(user => {
                            const userItem = document.createElement('div');
                            userItem.className = 'user-item';
                            userItem.dataset.userId = user.user_id;
                            
                            const initials = user.username.charAt(0).toUpperCase();
                            const joinDate = formatDate(user.created_at);
                            
                            userItem.innerHTML = `
                                <div class="user-avatar">${initials}</div>
                                <div class="user-info">
                                    <div class="user-name">${user.username}</div>
                                    <div class="user-joined">Joined: ${joinDate}</div>
                                </div>
                            `;
                            
                            userItem.addEventListener('click', function() {
                                // Remove active class from all users
                                document.querySelectorAll('.user-item').forEach(item => {
                                    item.classList.remove('active');
                                });
                                
                                // Add active class to clicked user
                                this.classList.add('active');
                                
                                // Set selected user
                                selectedUserId = this.dataset.userId;
                                
                                // Update chat header
                                const userName = this.querySelector('.user-name').textContent;
                                chatHeader.innerHTML = `<h3>${userName}</h3>`;
                                
                                // Enable message input
                                messageInput.disabled = false;
                                sendButton.disabled = false;
                                messageInput.focus();
                                
                                // Load messages
                                loadMessages(selectedUserId);
                            });
                            
                            usersList.appendChild(userItem);
                        });
                    })
                    .catch(error => console.error('Error loading users:', error));
            }
            
            function loadMessages(userId) {
                messageArea.innerHTML = '<div class="loading-messages">Loading messages...</div>';
                
                fetch(`?action=getMessages&user_id=${userId}`)
                    .then(response => response.json())
                    .then(messages => {
                        messageArea.innerHTML = '';
                        
                        if (messages.length === 0) {
                            messageArea.innerHTML = '<div class="welcome-message"><p>No messages yet. Start a conversation!</p></div>';
                            return;
                        }
                        
                        messages.forEach(msg => {
                            const messageDiv = document.createElement('div');
                            const isOutgoing = msg.from_user_id == <?php echo $_SESSION['user_id']; ?>;
                            messageDiv.className = `message ${isOutgoing ? 'outgoing' : 'incoming'}`;
                            
                            const time = formatTime(msg.sent_at);
                            
                            messageDiv.innerHTML = `
                                <p>${msg.message_text}</p>
                                <div class="time">${time}</div>
                            `;
                            
                            messageArea.appendChild(messageDiv);
                        });
                        
                        // Scroll to bottom
                        messageArea.scrollTop = messageArea.scrollHeight;
                    })
                    .catch(error => console.error('Error loading messages:', error));
            }
            
            function sendMessage() {
                const message = messageInput.value.trim();
                
                if (!message || !selectedUserId) return;
                
                const formData = new FormData();
                formData.append('action', 'sendMessage');
                formData.append('to_user', selectedUserId);
                formData.append('message', message);
                
                fetch('', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Clear input
                        messageInput.value = '';
                        
                        // Reload messages
                        loadMessages(selectedUserId);
                    }
                })
                .catch(error => console.error('Error sending message:', error));
            }
            
            function formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString("en-US", { 
                    year: 'numeric', 
                    month: 'short', 
                    day: 'numeric' 
                });
            }
            
            function formatTime(dateString) {
                const date = new Date(dateString);
                return date.toLocaleTimeString("en-US", { 
                    hour: '2-digit', 
                    minute: '2-digit' 
                });
            }
            
            // Refresh messages periodically (every 5 seconds)
            setInterval(() => {
                if (selectedUserId) {
                    loadMessages(selectedUserId);
                }
            }, 5000);
        });
    </script>
    <?php endif; ?>
</body>
</html>