<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "urbanloom";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

$user_id = $_SESSION['user_id'];
$query = "SELECT games.title, games.image_url FROM user_library JOIN games ON user_library.game_id = games.game_id WHERE user_library.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Game Library</title>
    <link rel="stylesheet" href="..\style\library.css">
    <link rel="icon" href="..\elements\STK-20250209-WA0001.webp" type="image/icon type">
</head>
<body>
    <div class="navbar">
        <div class="logo"><img src="STK-20250209-WA0001.webp" alt=""></div>
        <div class="menu">
            <a href="dashboad.php">Home</a>
            <a href="browse.php">Browse</a>
            <a href="wishlist.php">Wishlist</a>
            <a href="library.php">Library</a>
            <a href="chatting.php">Friends</a>
        </div>
    </div>

    <h1>My Game Library</h1>
    <div class="library">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="game">
                <img src="../games_posters/<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                <div class="game-info">
                    <h2><?php echo htmlspecialchars($row['title']); ?></h2>
                    <button onclick="window.location.href='play.php?title=<?php echo urlencode($row['title']); ?>'" class="play-button">Play</button>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
<?php $conn->close(); ?>
