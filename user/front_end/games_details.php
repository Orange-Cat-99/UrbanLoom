<?php


session_start();


// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "urbanloom";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get game ID from URL
$game_id = isset($_GET['id']) ? $_GET['id'] : 1;

// Fetch game details
$sql = "SELECT game_id, title, genre, platform, price, stock, release_date, description, image_url 
        FROM games 
        WHERE game_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $game_id);
$stmt->execute();
$result = $stmt->get_result();
$game = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($game['title']); ?> - Game Details</title>
    <link rel="stylesheet" href="..\style\game_details.css">

<link rel="icon" href="..\elements\STK-20250209-WA0001.webp" type="image/icon type">


</head>
<body>
    <div class="game-details-container">
        
    <!-- Navbar -->
<div class="navbar">
<div class="logo"><img src="STK-20250209-WA0001.webp" alt=""></div>
    <div class="menu">
        <a href="dashboard.php">Home</a>
        <a href="browse.php">Browse</a>
        <<a href="wishlist.php">Wishlist</a>
        <a href="library.php">Library</a>
        <a href="chatting.php">Friends</a>
        <a href="#">
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

<!-- Tab Navigation -->
<nav class="tabs">
    <ul>
        <li><a href="#overview" class="active">Overview</a></li>
        <li><a href="#addons">Add-Ons</a></li>
        <li><a href="#achievements">Achievements</a></li>
    </ul>
</nav>


        <div class="content-wrapper">
            <div class="media-section">
                <div class="main-video">
                    <img src="../games_posters/<?php echo htmlspecialchars($game['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($game['title']); ?>">
                </div>
            </div>

            <div class="game-info">
                <h1 class="game-title"><?php echo htmlspecialchars($game['title']); ?></h1>
                
                <div class="game-meta">
                    <span><?php echo htmlspecialchars($game['genre']); ?></span>
                    <span><?php echo htmlspecialchars($game['platform']); ?></span>
                    <span>Release: <?php echo date('M d, Y', strtotime($game['release_date'])); ?></span>
                </div>
                
                <div class="game-description">
                    <?php echo nl2br(htmlspecialchars($game['description'])); ?>
                </div>

                <div class="stock-status">
                    <?php echo $game['stock'] > 0 ? 'In Stock' : 'Out of Stock'; ?>
                </div>
                
                <div class="price">$<?php echo number_format($game['price'], 2); ?></div>
                
                <!-- <button class="btn-buy" <?php echo $game['stock'] <= 0 ? 'disabled' : ''; ?>>
                    Buy Now
                </button> -->
                <button class="btn-buy" 
        <?php echo $game['stock'] <= 0 ? 'disabled' : ''; ?>
        onclick="window.location.href='buy_game.php?id=<?php echo $game['game_id']; ?>'">
    Buy Now
</button>
                <!-- <button class="btn-cart" <?php echo $game['stock'] <= 0 ? 'disabled' : ''; ?>>
                    Add To Cart
                </button> -->
                <button class="btn-wishlist" data-game-id="<?php echo $game['game_id']; ?>" onclick="addToWishlist(<?php echo $game['game_id']; ?>)">Add to Wishlist</button>



                <script>
function addToWishlist(gameId) {
    fetch('wishlist.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            action: 'add_to_wishlist',
            game_id: gameId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Added to wishlist!');
            let button = document.querySelector(`button[data-game-id="${gameId}"]`);
            button.disabled = true;
            button.textContent = 'Added to Wishlist';
        } else if (data.status === 'exists') {
            alert('Already in wishlist');
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to add to wishlist');
    });
}


</script>


                <div class="additional-info">
                    <div class="rewards">
                        <span>Epic Rewards</span>
                        <span>Earn 5% Back</span>
                    </div>
                    <div class="refund">
                        <span>Refund Type</span>
                        <span>Self-Refundable</span>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="game-details-container">
    <div class="reviews-section">
        <h2>Player Reviews</h2>
        
        <?php
        // Fetch reviews for the current game from review_1 table
        $review_sql = "SELECT u.username, r.rating, r.comment, r.review_date 
                       FROM reviews_1 r
                       JOIN users u ON r.user_id = u.user_id
                       WHERE r.game_id = ?
                       ORDER BY r.review_date DESC";
        $stmt = $conn->prepare($review_sql);
        $stmt->bind_param("i", $game_id);
        $stmt->execute();
        $review_result = $stmt->get_result();

        if ($review_result->num_rows > 0) {
            while ($review = $review_result->fetch_assoc()) {
                echo '<div class="review">';
                echo '<strong>' . htmlspecialchars($review['username']) . '</strong> ';
                echo '<span>Rated: ' . str_repeat("⭐", $review['rating']) . '</span>';
                echo '<p>' . nl2br(htmlspecialchars($review['comment'])) . '</p>';
                echo '<small>Posted on ' . date('M d, Y', strtotime($review['review_date'])) . '</small>';
                echo '</div>';
            }
        } else {
            echo "<p>No reviews yet. Be the first to review!</p>";
        }
        ?>
    </div>
</div>



    <div class="game-details-container">
        <div class="system-requirements">
            <div class="sys-req-title">  <?php echo htmlspecialchars($game['title']); ?> System Requirements</div>
            <div class="requirements">
                <div>
                    <h3>Minimum</h3>
                    <p><strong>OS:</strong> Windows 10/11 64-bit (version 1909 or higher)</p>
                    <p><strong>Processor:</strong> Intel Core i3-8100 / AMD Ryzen 3 3100</p>
                    <p><strong>Memory:</strong> 16GB</p>
                    <p><strong>Storage:</strong> 140GB</p>
                    <p><strong>Graphics:</strong> NVIDIA GeForce GTX 1650 / AMD Radeon RX 5500 XT</p>
                    <p><strong>Other:</strong> SSD Required</p>
                </div>
                <div>
                    <h3>Recommended</h3>
                    <p><strong>OS:</strong> Windows 10/11 64-bit (version 1909 or higher)</p>
                    <p><strong>Processor:</strong> Intel Core i5-8400 / AMD Ryzen 5 3600</p>
                    <p><strong>Memory:</strong> 16GB</p>
                    <p><strong>Storage:</strong> 140GB</p>
                    <p><strong>Graphics:</strong> NVIDIA GeForce RTX 3060 / AMD Radeon RX 5700</p>
                    <p><strong>Other:</strong> SSD Required</p>
                </div>
            </div>
        </div>
    </div>

</body>
</html>

<?php
$conn->close();
?>