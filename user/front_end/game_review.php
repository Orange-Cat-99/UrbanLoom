<?php


session_start();
// Database connection parameters for XAMPP default settings
$servername = "localhost";
$username = "root";  
$password = "";      
$dbname = "urbanloom";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process form submission
$message = '';
$messageType = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $user_id = $_POST["user_id"];
    $game_id = $_POST["game_id"];
    $rating = $_POST["rating"];
    $comment = $_POST["comment"];
    
    // Basic validation
    if (empty($user_id) || empty($game_id) || empty($rating) || empty($comment)) {
        $message = "Please fill in all required fields";
        $messageType = "error";
    } else {
        // Prepare and bind the SQL statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO reviews_1 (user_id, game_id, rating, comment, review_date) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssis", $user_id, $game_id, $rating, $comment);
        
        // Execute the statement
        if ($stmt->execute()) {
            $message = "Your review has been submitted successfully!";
            $messageType = "success";
        } else {
            $message = "There was an error submitting your review: " . $conn->error;
            $messageType = "error";
        }
        
        // Close statement
        $stmt->close();
    }
}

// Fetch games for selection
$gamesQuery = "SELECT game_id, title, genre, platform, price FROM games ORDER BY title ASC";
$gamesResult = $conn->query($gamesQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Review System - UrbanLoom</title>
    <link rel="stylesheet" href="..\style\game_review.css">
</head>
<body>
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

    <div class="container">
        <?php
        // Display messages if any
        if (!empty($message)) {
            echo '<div class="message ' . $messageType . '">' . $message . '</div>';
        }
        ?>

        <section class="games-container">
            <h2>Select a Game to Review</h2>
            <input type="text" id="gameSearch" class="search-box" placeholder="Search for games...">
            
            <div class="games-slider">
                <?php
                if ($gamesResult && $gamesResult->num_rows > 0) {
                    while ($game = $gamesResult->fetch_assoc()) {
                        echo '<div class="game-card" data-game-id="' . htmlspecialchars($game['game_id']) . 
                             '" data-title="' . htmlspecialchars($game['title']) . 
                             '" data-genre="' . htmlspecialchars($game['genre']) . 
                             '" data-platform="' . htmlspecialchars($game['platform']) . '">';
                        echo htmlspecialchars($game['title']);
                        echo '</div>';
                    }
                } else {
                    echo '<div>No games available</div>';
                }
                ?>
            </div>
        </section>

        <section class="review-form-container">
            <h2>Submit Your Review</h2>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="reviewForm">
                <div class="form-group">
                    <label for="user_id">User ID:</label>
                    <input type="text" id="user_id" name="user_id" required>
                </div>

                <div class="form-group">
                    <label for="selected_game">Selected Game:</label>
                    <input type="text" id="selected_game" readonly disabled>
                    <input type="hidden" id="game_id" name="game_id">
                </div>

                <div class="form-group">
                    <label>Rating:</label>
                    <div class="rating-select">
                        <?php for($i = 1; $i <= 5; $i++): ?>
                            <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" <?php echo ($i == 5) ? 'checked' : ''; ?>>
                            <label for="star<?php echo $i; ?>"><?php echo $i; ?></label>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="comment">Your Review:</label>
                    <textarea id="comment" name="comment" required placeholder="Share your thoughts about this game..."></textarea>
                </div>

                <button type="submit">Submit Review</button>
            </form>
        </section>

        <section class="reviews-container">
            <h2>Recent Reviews</h2>
            
            <?php
            // Join reviews with games to get game titles
            $sql = "SELECT r.*, g.title as game_title 
                    FROM reviews_1 r 
                    LEFT JOIN games g ON r.game_id = g.game_id 
                    ORDER BY r.review_date DESC LIMIT 10";
            $result = $conn->query($sql);
            
            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '<div class="review">';
                    echo '<div class="review-header">';
                    echo '<span class="review-game">' . (isset($row["game_title"]) ? htmlspecialchars($row["game_title"]) : 'Game ID: ' . htmlspecialchars($row["game_id"])) . '</span>';
                    echo '<div class="star-rating">';
                    
                    // Display star rating
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= $row["rating"]) {
                            echo '★';
                        } else {
                            echo '☆';
                        }
                    }
                    
                    echo '</div>';
                    echo '</div>';
                    echo '<p class="review-comment">' . htmlspecialchars($row["comment"]) . '</p>';
                    echo '<div class="review-date">Reviewed by User ' . htmlspecialchars($row["user_id"]) . ' on ' . date('F j, Y', strtotime($row["review_date"])) . '</div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="no-reviews">No reviews yet. Be the first to submit a review!</div>';
            }
            
            // Close connection
            $conn->close();
            ?>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('reviewForm');
            const gameCards = document.querySelectorAll('.game-card');
            const selectedGameInput = document.getElementById('selected_game');
            const gameIdInput = document.getElementById('game_id');
            const searchInput = document.getElementById('gameSearch');
            
            // Game selection
            gameCards.forEach(card => {
                card.addEventListener('click', function() {
                    // Remove selected class from all cards
                    gameCards.forEach(c => c.classList.remove('selected'));
                    
                    // Add selected class to clicked card
                    this.classList.add('selected');
                    
                    // Update form fields
                    const gameId = this.getAttribute('data-game-id');
                    const gameTitle = this.getAttribute('data-title');
                    
                    selectedGameInput.value = gameTitle;
                    gameIdInput.value = gameId;
                });
            });
            
            // Search functionality
            searchInput.addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase();
                
                gameCards.forEach(card => {
                    const title = card.getAttribute('data-title').toLowerCase();
                    const genre = card.getAttribute('data-genre').toLowerCase();
                    const platform = card.getAttribute('data-platform').toLowerCase();
                    
                    if (title.includes(searchTerm) || genre.includes(searchTerm) || platform.includes(searchTerm)) {
                        card.classList.remove('filtered-out');
                    } else {
                        card.classList.add('filtered-out');
                    }
                });
            });
            
            // Form validation
            form.addEventListener('submit', function(e) {
                const userId = document.getElementById('user_id').value.trim();
                const gameId = document.getElementById('game_id').value.trim();
                const comment = document.getElementById('comment').value.trim();
                
                if (!userId || !gameId || !comment) {
                    e.preventDefault();
                    alert('Please fill in all required fields and select a game');
                }
            });
            
            // Optional: Auto-scroll to center when a card is selected
            gameCards.forEach(card => {
                card.addEventListener('click', function() {
                    const container = document.querySelector('.games-slider');
                    const cardRect = this.getBoundingClientRect();
                    const containerRect = container.getBoundingClientRect();
                    
                    // Calculate scroll position to center the selected card
                    const scrollPos = this.offsetLeft - (containerRect.width / 2) + (cardRect.width / 2);
                    container.scrollTo({
                        left: scrollPos,
                        behavior: 'smooth'
                    });
                });
            });
        });
    </script>
</body>
</html>