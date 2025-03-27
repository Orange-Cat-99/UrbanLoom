<?php

session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "urbanloom";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UrbanLoom</title>
    <link rel="stylesheet" href="..\style\dashboad.css">
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


    <!-- Hero Section -->
    <div class="hero">
        <video autoplay loop muted>
            <source src="..\elements\i2.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <h1>Welcome To UrbanLoom</h1>
        <p>BROWSE OUR POPULAR GAMES HERE</p>
        <button onclick="window.location.href='browse.php';">Browse Now</button>

    </div>

    
    <!-- Game Section -->
<div class="game-section">
    <h2>Most Popular Games</h2>
    <div class="game-grid">
        <?php
        $sql = "SELECT game_id, title, genre, price, image_url FROM games LIMIT 12"; // Added game_id for identification
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $game_id = $row["game_id"]; // Store game_id for the link
                $image_url = $row["image_url"];
                $title = $row["title"];
                $genre = $row["genre"];
                $price = number_format($row["price"], 2);

                // Wrap the game card in a link
                echo '<a href="games_details.php?id=' . $game_id . '" class="game-card">
                        <img src="../games_posters/' . $image_url . '" alt="' . $title . '">
                        <h3>' . $title . '</h3>
                        <p>' . $genre . '</p>
                        <p class="price">$' . $price . '</p>
                      </a>';
            }
        } else {
            echo "<p>No games available.</p>";
        }
        ?>
    </div>
</div>


<div class="features-container">
    <div class="feature-card">
        <img src="..\elements\w1.jpg" alt="Fortnite x Adidas">
        <div class="feature-content">
            <h2>Fortnite</h2>
            <p>Kick it in style with new adidas Kicks!</p>
            <a href="https://www.adidas.co.in/superstar-shoes/EG4959.html?pr=taxonomy_rr&slot=1&rec=mt" class="shop-button">See In Shop</a>
        </div>
    </div>

    <div class="feature-card">
        <img src="..\elements\w2.jpg" alt="Fortnite x Jujutsu Kaisen">
        <div class="feature-content">
            <h2>Fortnite</h2>
            <p>Keikatsu! Curse the competition as the King of Curses Ryomen Sukuna, cursed spirit Mahito, and assassin Toji Fushiguro.</p>
            <a href="https://www.netflix.com/in/title/81278456" class="shop-button">See In Shop</a>
        </div>
    </div>

    <div class="feature-card">
        <img src="..\elements\w3.jpg" alt="Fortnite x Metallica">
        <div class="feature-content">
            <h2>Fortnite</h2>
            <p>There's a fire inside that can't be quenched. Grab the Puppet Master Outfits for Lars, James, Kirk, and Robert!</p>
            <a href="https://www.metallica.com/?srsltid=AfmBOoqMx81bsFWpABL23Zgu7tfb63j-Y_sdA8ryGkaqM_i6w3g-wXn4" class="shop-button">See In Shop</a>
        </div>
    </div>
</div>



   <!-- Free Games Section -->
<div class="free-games">
    <h2>Free Games</h2>
    <div class="free-games-container">
        <?php
        $sql = "SELECT game_id, title, description, image_url 
                FROM games 
                WHERE price = 0 
                ORDER BY release_date DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Create a URL with the game_id to redirect to a detailed page
                $game_url = "games_details.php?game_id=" . $row["game_id"];
                echo '<div class="free-game-card">
                        <a href="' . $game_url . '">
                            <img src="../games_posters/' . $row["image_url"] . '" alt="' . $row["title"] . '">
                            <h3>' . $row["title"] . '</h3>
                        </a>
                        <p>' . $row["description"] . '</p>
                        <p class="free-label">Free to Play</p> <!-- Added Free to Play text -->
                      </div>';
            }
        } else {
            echo "<p>No free games available.</p>";
        }
        ?>
    </div>
</div>



<div class="sections-container">
    <!-- Top Sellers Column -->
    <div class="column-section">
        <h2>Top Sellers</h2>
        <?php
        $sql = "SELECT game_id, title, genre, price, image_url FROM games 
                WHERE price > 0 
                ORDER BY price DESC 
                LIMIT 4";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<a href="games_details.php?id=' . $row["game_id"] . '" class="game-card">
                        <img src="../games_posters/' . $row["image_url"] . '" alt="' . $row["title"] . '">
                        <div class="game-info">
                            <h3>' . $row["title"] . '</h3>
                            <p>' . $row["genre"] . '</p>
                            <p class="price">$' . number_format($row["price"], 2) . '</p>
                        </div>
                      </a>';
            }
        }
        ?>
    </div>

    <!-- Most Played Column -->
    <div class="column-section">
        <h2>Most Played</h2>
        <?php
        $sql = "SELECT game_id, title, genre, price, image_url FROM games 
                ORDER BY RAND() 
                LIMIT 4";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $price_display = $row["price"] > 0 ? '$' . number_format($row["price"], 2) : 'Free';
                echo '<a href="games_details.php?id=' . $row["game_id"] . '" class="game-card">
                        <img src="../games_posters/' . $row["image_url"] . '" alt="' . $row["title"] . '">
                        <div class="game-info">
                            <h3>' . $row["title"] . '</h3>
                            <p>' . $row["genre"] . '</p>
                            <p class="price">' . $price_display . '</p>
                        </div>
                      </a>';
            }
        }
        ?>
    </div>

    <!-- Upcoming Wishlisted Column -->
    <div class="column-section">
        <h2>Coming Soon</h2>
        <?php
        $sql = "SELECT game_id, title, genre, price, image_url, release_date FROM games 
                WHERE release_date > CURDATE() 
                ORDER BY release_date ASC 
                LIMIT 4";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $release_date = date('m/d/y', strtotime($row["release_date"]));
                echo '<a href="games_details.php?id=' . $row["game_id"] . '" class="game-card">
                        <img src="../games_posters/' . $row["image_url"] . '" alt="' . $row["title"] . '">
                        <div class="game-info">
                            <h3>' . $row["title"] . '</h3>
                            <p>' . $row["genre"] . '</p>
                            <p class="release-date">Available ' . $release_date . '</p>
                            <p class="price">$' . number_format($row["price"], 2) . '</p>
                        </div>
                      </a>';
            }
        }
        ?>
    </div>
</div>



</body>
</html>

<?php
$conn->close();
?>