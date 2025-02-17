<?php
session_start();

// Database Configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "urbanloom";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed"]));
}

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle Wishlist Add Request (AJAX)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    // Check for Add to Wishlist action
    if (isset($data['action']) && $data['action'] === 'add_to_wishlist' && isset($data['game_id'])) {
        $game_id = intval($data['game_id']);

        // Check if the game is already in the wishlist
        $check_sql = "SELECT * FROM wishlist WHERE user_id = ? AND game_id = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("ii", $user_id, $game_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo json_encode(["status" => "exists", "message" => "Game already in wishlist"]);
            exit;
        }

        // Insert into wishlist with timestamp
        $insert_sql = "INSERT INTO wishlist (user_id, game_id, added_at) VALUES (?, ?, NOW())";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("ii", $user_id, $game_id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Game added to wishlist"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to add game"]);
        }

        $stmt->close();
        exit;
    }

    // Check for Remove from Wishlist action
    if (isset($data['action']) && $data['action'] === 'remove_from_wishlist' && isset($data['game_id'])) {
        $game_id = intval($data['game_id']);

        // Remove from wishlist
        $remove_sql = "DELETE FROM wishlist WHERE user_id = ? AND game_id = ?";
        $stmt = $conn->prepare($remove_sql);
        $stmt->bind_param("ii", $user_id, $game_id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Game removed from wishlist"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to remove game"]);
        }

        $stmt->close();
        exit;
    }
}

// Fetch wishlist items for display
$wishlist_sql = "SELECT g.* FROM wishlist w 
                 JOIN games g ON w.game_id = g.game_id 
                 WHERE w.user_id = ?";
$stmt = $conn->prepare($wishlist_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$wishlist_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Wishlist</title>
    <link rel="icon" href="STK-20250209-WA0001.webp" type="image/icon type">
    <style>
        * {
            cursor: url("https://cur.cursors-4u.net/holidays/hol-4/hol336.cur"), auto !important;
        }

        body {
            background-color: #18191a;
            color: #e4e6eb;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            background-color: #242526;
        }
        .navbar .logo img {
            height: 50px;
            width: auto;
            max-width: 150px;
            object-fit: contain;
        }
        .navbar .menu {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
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
        .navbar .menu a:hover {
            background-color: #32CD32;
            color: black;
        }
        .wishlist-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }
        .wishlist-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .wishlist-header h1 {
            margin: 0;
            color: #e4e6eb;
            font-size: 24px;
        }
        .wishlist-search {
            background-color: #242526;
            border: 1px solid #3a3b3c;
            color: #e4e6eb;
            padding: 8px 15px;
            border-radius: 5px;
            width: 300px;
        }
        .wishlist-item {
            background-color: #242526;
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            border-radius: 5px;
            overflow: hidden;
            border: 1px solid #3a3b3c;
        }
        .wishlist-item-image {
            width: 292px;
            height: 136px;
            object-fit: cover;
        }
        .wishlist-item-details {
            flex-grow: 1;
            padding: 15px;
        }
        .wishlist-item-title {
            color: #e4e6eb;
            margin: 0 0 10px 0;
            font-size: 20px;
        }
        .wishlist-item-tags {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }
        .game-tag {
            background-color: #3a3b3c;
            color: #e4e6eb;
            padding: 3px 8px;
            border-radius: 5px;
            font-size: 12px;
        }
        .wishlist-item-meta {
            color: #b0b3b8;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .add-to-library-btn, .remove-from-wishlist-btn {
            background-color: #32CD32;
            color: #000;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .add-to-library-btn:hover, .remove-from-wishlist-btn:hover {
            background-color: #2ecc71;
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="logo"><img src="STK-20250209-WA0001.webp" alt=""></div>
    <div class="menu">
        <a href="dashboard.php">Home</a>
        <a href="browse.php">Browse</a>
        <a href="wishlist.php">Wishlist</a>
        <a href="library.php">Library</a>
    </div>
</div>

<div class="wishlist-container">
    <div class="wishlist-header">
        <h1>My Wishlist</h1>
        <input type="text" class="wishlist-search" placeholder="Search by name or tag">
    </div>

    <?php while($wishlist_item = $wishlist_result->fetch_assoc()): ?>
        <div class="wishlist-item">
            <img src="games_posters/<?php echo htmlspecialchars($wishlist_item['image_url']); ?>" 
                 alt="<?php echo htmlspecialchars($wishlist_item['title']); ?>" 
                 class="wishlist-item-image">
            <div class="wishlist-item-details">
                <h2 class="wishlist-item-title"><?php echo htmlspecialchars($wishlist_item['title']); ?></h2>
                <div class="wishlist-item-tags">
                    <span class="game-tag"><?php echo htmlspecialchars($wishlist_item['genre']); ?></span>
                    <span class="game-tag"><?php echo htmlspecialchars($wishlist_item['platform']); ?></span>
                </div>
                <div class="wishlist-item-meta">
                    Release Date: <?php echo htmlspecialchars($wishlist_item['release_date']); ?> | 
                    Price: $<?php echo number_format($wishlist_item['price'], 2); ?>
                </div>
                <button class="add-to-library-btn" onclick="window.location.href='games_details.php';">Buy Game</button>

                <button class="remove-from-wishlist-btn" onclick="removeFromWishlist(<?php echo $wishlist_item['game_id']; ?>)">Remove</button>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<script>
function removeFromWishlist(gameId) {
    fetch('wishlist.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'remove_from_wishlist', game_id: gameId })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.status === 'success') {
            location.reload(); // Reload page to update the wishlist
        }
    })
    .catch(error => alert('Error removing game.'));
}
</script>

</body>
</html>

<?php $conn->close(); ?>
