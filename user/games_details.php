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
    <style>

        * {
            cursor: url("https://cur.cursors-4u.net/holidays/hol-4/hol336.cur"), auto !important;
        }

     /* Navbar styling */
.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 2rem;
    background-color: #242526; /* Dark Gray */
}

/* Logo image styling */
.navbar .logo img {
    height: 50px; /* Adjust the height as needed */
    width: auto; /* Maintain aspect ratio */
    max-width: 150px; /* Prevent it from getting too large */
    object-fit: contain; /* Ensures the image fits properly */
}

.navbar .menu {
    display: flex;
    gap: 1rem;
    align-items: center;
}

/* Menu links */
.navbar .menu a {
    text-decoration: none;
    color: #fff;
    font-size: 1rem;
    padding: 0.5rem;
    border-radius: 5px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Hover effect on menu links */
.navbar .menu a:hover {
    background-color: #32CD32; /* Green */
    color: black;
}

/* Username styling */
.navbar .username {
    font-size: 1rem;
    color: #fff;
    font-weight: bold;
    margin-left: 10px;
}

/* SVG icon styling */
.navbar .menu svg {
    margin-left: 10px;
    fill: #fff;
    cursor: pointer;
    transition: transform 0.3s ease;
}

.navbar .menu svg:hover {
    transform: scale(1.1);
}

/* Page Styling */
body {
    margin: 0;
    padding: 20px;
    background-color: #242526; /* Dark Gray */
    color: #f1f1f1; /* Light Gray Text */
    font-family: Arial, sans-serif;
    line-height: 1.6;
}

.game-details-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    background-color: #333333; /* Dark Gray Background */
    border-radius: 8px;
}

.tabs ul {
        display: flex; /* Use flexbox for horizontal alignment */
        justify-content: center; /* Center the tabs horizontally */
        list-style: none; /* Remove default list styling */
        padding: 0; /* Remove padding */
        margin: 0; /* Remove margin */
        background-color: #222; /* Background color of the tabs */
        border-bottom: 2px solid #444; /* Add a bottom border */
    }

    .tabs ul li {
        margin: 0; /* Remove margin */
    }

    .tabs ul li a {
        display: block;
        padding: 10px 20px; /* Add padding for spacing */
        text-decoration: none; /* Remove underline from links */
        color: #f1f1f1; /* Light text color */
        border-bottom: 2px solid transparent; /* Bottom border for active indicator */
        transition: all 0.3s ease; /* Smooth hover effect */
    }

    .tabs ul li a:hover {
        background-color: #444; /* Highlight background on hover */
        color: #32CD32; /* Change text color on hover */
    }

    .tabs ul li a.active {
        border-bottom: 2px solid #32CD32; /* Active tab indicator */
        font-weight: bold; /* Bold the active tab */
    }

.content-wrapper {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 20px;
}

.media-section {
    background-color: #333333; /* Dark Gray */
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.main-video {
    width: 100%;
    height: 400px;
    overflow: hidden;
    border-radius: 8px;
    background-color: #444444; /* Darker Gray */
}

.main-video img {
    width: 100%;
    height: 400px;
    object-fit: cover;
    display: block;
}

.game-info {
    background-color: #333333; /* Dark Gray */
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.game-title {
    font-size: 24px;
    margin: 0 0 15px 0;
    color: #32CD32; /* Green for title */
}

.game-meta {
    display: flex;
    gap: 15px;
    margin-bottom: 15px;
    color: #f1f1f1; /* Light Gray */
    font-size: 14px;
}

.game-description {
    color: #f1f1f1; /* Light Gray */
    margin-bottom: 20px;
}

.stock-status {
    color: #4CAF50; /* Green Text */
    margin-bottom: 10px;
    font-weight: bold;
}

.price {
    font-size: 24px;
    font-weight: bold;
    color: #32CD32; /* Green for price */
    margin-bottom: 15px;
}

.btn-buy,
.btn-cart,
.btn-wishlist {
    width: 100%;
    padding: 10px;
    margin-bottom: 8px;
    border: none;
    border-radius: 4px;
    font-weight: bold;
    cursor: pointer;
}

.btn-buy {
    background-color: #32CD32; /* Green */
    color: white;
}

.btn-cart {
    background-color: #2196F3; /* Blue */
    color: white;
}

.btn-wishlist {
    background-color: #444444; /* Darker Gray */
    color: #f1f1f1;
}

.additional-info {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #ddd;
}

.rewards,
.refund {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    color: #f1f1f1; /* Light Gray */
    font-size: 14px;
}

.system-requirements {
    margin-top: 40px;
    padding: 20px;
    background: #333333; /* Dark Gray */
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.sys-req-title {
    font-size: 22px;
    font-weight: bold;
    margin-bottom: 15px;
    color: #32CD32; /* Green for title */
}

.requirements {
    display: flex;
    gap: 30px;
}

.requirements div {
    flex: 1;
    background: #444444; /* Darker Gray */
    padding: 15px;
    border-radius: 6px;
}

.requirements h3 {
    margin-bottom: 10px;
    font-size: 18px;
    color: #32CD32; /* Green */
}

@media (max-width: 1024px) {
    .content-wrapper {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .game-details-container {
        padding: 10px;
    }

    .main-video {
        height: 300px;
    }

    .main-video img {
        height: 300px;
    }

    .requirements {
        flex-direction: column;
    }
}


.reviews-section {
    margin-top: 40px;
    padding: 20px;
    background: #333333; /* Dark Gray */
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.reviews-section h2 {
    color: #32CD32; /* Green */
    margin-bottom: 15px;
}

.review {
    padding: 10px;
    border-bottom: 1px solid #444;
    margin-bottom: 10px;
}

.review strong {
    color: #f1f1f1;
}

.review span {
    color: #FFD700; /* Gold for stars */
    margin-left: 10px;
}

.review p {
    color: #f1f1f1;
    margin: 5px 0;
}

.review small {
    color: #aaa;
    display: block;
    margin-top: 5px;
}


    </style>

<link rel="icon" href="STK-20250209-WA0001.webp" type="image/icon type">


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
                    <img src="games_posters/<?php echo htmlspecialchars($game['image_url']); ?>" 
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