<?php


session_start();


// Database connection details
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'urbanloom';

// Create a connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch genres from the database
$sql_genres = "SELECT DISTINCT genre FROM games LIMIT 4";
$result_genres = $conn->query($sql_genres);

// Fetch games for the listing section
$sql_games = "SELECT title, genre, price, image_url FROM games LIMIT 8";
$result_games = $conn->query($sql_games);

// Static images for genres
$staticImages = [
    'Action Games' => 'action1.jpg',
    'Action-Adventure Games' => 'adventure1.jpg',
    'Adventure Games' => 'genre1.jpg',
    'Casual Games' => 'casual1.jpg',
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Popular Genres</title>
    <link rel="stylesheet" href="../style/browse.css">
    <link rel="icon" href="..\elements\STK-20250209-WA0001.webp" type="image/icon type">
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


<div class="container">
    <h1>Popular Genres</h1>
    <div class="genres">
        <?php
        // Fetch genres and their associated images
        $sql_genres = "SELECT DISTINCT genre, image_url FROM games LIMIT 4";
        $result_genres = $conn->query($sql_genres);

        if ($result_genres->num_rows > 0) {
            while ($row = $result_genres->fetch_assoc()) {
                $genre = $row['genre'];
                $image = $row['image_url'] ?? 'default.jpg'; // Fallback to a default image if no URL is available
                ?>
                <div class="genre-card">
                    <img src="../games_posters/<?php echo $image; ?>" alt="<?php echo $genre; ?>" class="genre-image">
                    <div class="genre-title"> <?php echo $genre; ?> </div>
                </div>
                <?php
            }
        } else {
            echo "<p>No genres found.</p>";
        }
        ?>
    </div>
</div>


    <div class="filter-bar">
        <div>
            Show: 
            <select>
                <option>New Release</option>
                <option>Popular</option>
            </select>
        </div>
        <input type="text" placeholder="Search games..." />
    </div>

    <div class="games">
    <?php
    // Fetch games for the listing section again
    $sql_games = "SELECT game_id, title, genre, price, image_url FROM games LIMIT 8";
    $result_games = $conn->query($sql_games);

    if ($result_games->num_rows > 0) {
        while ($row = $result_games->fetch_assoc()) {
            $game_id = $row['game_id'];
            $title = $row['title'];
            $price = $row['price'];
            $image_url = $row['image_url'] ?? 'default.jpg';
            ?>
            <div class="game-card">
                <!-- Make the image clickable and link to the game details page -->
                <a href="games_details.php?game_id=<?php echo $game_id; ?>">
                    <img src="../games_posters/<?php echo $image_url; ?>" alt="<?php echo $title; ?>" class="game-image">
                </a>
                <div class="game-title"><?php echo $title; ?></div>
                <div class="game-price">$<?php echo number_format($price, 2); ?></div>
            </div>
            <?php
        }
    } else {
        echo "<p>No games found.</p>";
    }
    ?>
</div>


 <!-- Options Section -->
 <div class="genre-options">
        <h2>Explore More Genres</h2>
        <div class="options">
            <button onclick="filterByGenre('Action')">Action</button>
            <button onclick="filterByGenre('Adventure')">Adventure</button>
            <button onclick="filterByGenre('Action-Adventure')">Action-Adventure</button>
            <button onclick="filterByGenre('RPG')">RPG</button>
            <button onclick="filterByGenre('Fighting')">Fighting</button>
            <button onclick="filterByGenre('Horror')">Horror</button>
            <button onclick="filterByGenre('Indie')">Indie</button>
        </div>
    </div>
</div>

<script>
    // JavaScript to handle genre filtering (replace this with actual functionality later)
    function filterByGenre(genre) {
        alert('You selected: ' + genre);
        // Add your filtering functionality here
    }
</script>



<!-- Display Games Again -->
<div class="games">
    <?php
    // Fetch games for the listing section again
    $sql_games = "SELECT game_id, title, genre, price, image_url FROM games LIMIT 8";
    $result_games = $conn->query($sql_games);

    if ($result_games->num_rows > 0) {
        while ($row = $result_games->fetch_assoc()) {
            $game_id = $row['game_id'];
            $title = $row['title'];
            $price = $row['price'];
            $image_url = $row['image_url'] ?? 'default.jpg';
            ?>
            <div class="game-card">
                <!-- Make the image clickable and link to the game details page -->
                <a href="games_details.php?game_id=<?php echo $game_id; ?>">
                    <img src="../games_posters/<?php echo $image_url; ?>" alt="<?php echo $title; ?>" class="game-image">
                </a>
                <div class="game-title"><?php echo $title; ?></div>
                <div class="game-price">$<?php echo number_format($price, 2); ?></div>
            </div>
            <?php
        }
    } else {
        echo "<p>No games found.</p>";
    }
    ?>
</div>



</body>
</html>

<?php
$conn->close();
?>
