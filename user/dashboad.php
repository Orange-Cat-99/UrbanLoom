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
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Baumans&family=Open+Sans&family=PT+Sans&family=Roboto&display=swap');

        * {
            cursor: url("https://cur.cursors-4u.net/holidays/hol-4/hol336.cur"), auto !important;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #181a1b;
            color: white;
        }

        /* Navbar styling */
.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 2rem;
    background-color: #242526;
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
    align-items: center; /* Ensure vertical alignment */
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
    background-color: #32CD32;
    color: black;
}

/* Username styling */
.navbar .username {
    font-size: 1rem;
    color: #fff;
    font-weight: bold;
    margin-left: 10px; /* Space between username and icon */
}

/* SVG icon styling */
.navbar .menu svg {
    margin-left: 10px; /* Adjust margin between SVG and username */
    fill: #fff; /* Color the icon */
    cursor: pointer;
    transition: transform 0.3s ease;
}

.navbar .menu svg:hover {
    transform: scale(1.1); /* Slightly enlarge the icon on hover */
}


        .hero {
            text-align: center;
            padding: 5rem 1rem;
            position: relative;
            margin: 2rem auto;
            width: 80%;
            color: white;
            overflow: hidden;
        }

        .hero video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            top: 0;
            left: 0;
            z-index: -1;
        }

        .hero h1 {
            font-family: 'PT Sans', sans-serif;
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
        }

        .hero button {
            background-color: #32CD32;
            color: black;
            font-size: 1rem;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .hero button:hover {
            background-color: #4AE54A;
        }

        /* Game Cards */
        .game-section {
            padding: 2rem;
            text-align: center;
        }

        .game-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); /* Responsive grid */
            gap: 20px;
            padding: 20px;
        }

        /* Remove underline from the game card links */
        .game-section .game-grid a {
            text-decoration: none; /* Removes the underline */
        }

        .game-card {
            background-color: #242526;
            border-radius: 10px;
            padding: 10px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
        }

        .game-card:hover {
            transform: scale(1.05);
        }

        .game-card img {
            width: 100%;  /* Make sure image fills the container */
            height: 200px; /* Fixed height for uniformity */
            object-fit: cover; /* Ensures the image fills without stretching */
            border-radius: 10px;
        }

        .game-card h3 {
            margin: 10px 0 5px;
            font-size: 1.2rem;
            color: white;
        }

        .game-card p {
            font-size: 1rem;
            color: #ddd;
        }

        .game-card .price {
            font-size: 1.2rem;
            font-weight: bold;
            color: #32CD32; /* Green color for visibility */
            margin-top: 5px;
        }

        /* Free Games Section */
.free-games {
    padding: 1.5rem;
    text-align: center;
}

.free-games h2 {
    font-size: 2rem;
    color:rgb(255, 255, 255); /* Highlighted heading color */
    margin-bottom: 1rem;
}

.free-games-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); /* Adjusted for smaller cards */
    gap: 15px;
    padding: 10px;
}

.free-game-card {
    background-color: #333; /* Slightly lighter background */
    border-radius: 10px; /* Softer corners */
    padding: 15px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3); /* Subtle shadow */
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    overflow: hidden;
}

.free-game-card:hover {
    transform: scale(1.05); /* Gentle zoom-in effect */
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.4); /* Slightly stronger shadow */
}

.free-game-card img {
    width: 100%; /* Full width for image */
    height: 150px; /* Smaller fixed height */
    object-fit: cover; /* Prevent image stretching */
    border-radius: 5px; /* Slightly rounded image edges */
    margin-bottom: 0.8rem;
}

.free-game-card h3 {
    font-size: 1.4rem; /* Smaller game title */
    color: white;
    margin: 8px 0;
}

.free-game-card p {
    font-size: 1rem; /* Smaller font for descriptions */
    color: #ccc;
    margin: 4px 0;
}

.free-game-card p:last-of-type {
    font-size: 0.9rem;
    color: #32CD32; /* Highlighted release date */
    font-weight: bold;
    margin-top: 8px;
}

/* Remove underlines from links in free game cards */
.free-game-card a {
    text-decoration: none; /* Remove underline */
    color: inherit; /* Maintain color of the parent element */
}

/* Link hover effect for the game cards */
.free-game-card a:hover {
    opacity: 0.8; /* Subtle fade effect on hover */
}


.sections-container {
    display: flex;
    justify-content: space-between;
    gap: 20px;
    padding: 2rem;
    flex-wrap: wrap;
}

/* Replace the previous column-related CSS with this in your <style> tag */

.sections-container {
    display: flex;
    justify-content: center;
    gap: 40px;
    padding: 2rem;
    max-width: 1400px;
    margin: 0 auto;
    position: relative;
}

/* Vertical divider lines between sections */
.column-section:not(:last-child)::after {
    content: '';
    position: absolute;
    right: -20px;
    top: 0;
    height: 100%;
    width: 1px;
    background: linear-gradient(180deg, 
        transparent 0%, 
        #32CD32 50%, 
        transparent 100%);
}

.column-section {
    flex: 1;
    min-width: 280px;
    max-width: 350px;
    padding: 20px;
    position: relative;
}

.column-section h2 {
    color: white;
    text-align: left;
    margin-bottom: 25px;
    font-size: 1.4rem;
    padding-bottom: 10px;
    border-bottom: 2px solid #32CD32;
    opacity: 0;
    animation: fadeInDown 0.5s ease forwards;
}

/* Modified game card layout */
.column-section .game-card {
    display: flex;
    flex-direction: row;
    gap: 15px;
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
    opacity: 0;
    transform: translateY(20px);
    animation: fadeInUp 0.5s ease forwards;
    animation-delay: calc(var(--card-index) * 0.1s);
    text-decoration: none;
}

.column-section .game-card:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.column-section .game-card:hover {
    transform: translateY(-5px) scale(1.02);
}

/* Modified image styles */
.column-section .game-card img {
    width: 100px;
    height: 140px;
    object-fit: cover;
    border-radius: 8px;
    transition: all 0.3s ease;
    flex-shrink: 0;
}

.column-section .game-card:hover img {
    box-shadow: 0 8px 16px rgba(50, 205, 50, 0.2);
}

/* Game info container */
.column-section .game-card .game-info {
    display: flex;
    flex-direction: column;
    justify-content: center;
    flex: 1;
}

.column-section .game-card h3 {
    color: white;
    font-size: 1.1rem;
    margin: 0 0 5px 0;
    font-weight: 600;
    transition: color 0.3s ease;
}

.column-section .game-card:hover h3 {
    color: #32CD32;
}

.column-section .game-card p {
    color: #a0a0a0;
    font-size: 0.9rem;
    margin: 2px 0;
}

.column-section .game-card .price {
    color: #32CD32;
    font-size: 1rem;
    font-weight: bold;
    margin-top: 5px;
    transition: transform 0.3s ease;
}

.column-section .game-card:hover .price {
    transform: scale(1.1);
}

.column-section .game-card .release-date {
    color: #888;
    font-size: 0.8rem;
    font-style: italic;
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive Design */
@media (max-width: 1200px) {
    .sections-container {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .column-section {
        min-width: 300px;
        max-width: 400px;
    }
    
    .column-section::after {
        display: none;
    }
}

@media (max-width: 768px) {
    .sections-container {
        padding: 1rem;
        gap: 20px;
    }
    
    .column-section {
        min-width: 100%;
    }
    
    .column-section .game-card img {
        width: 80px;
        height: 120px;
    }
}

.features-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    padding: 20px;
    background-color: #181a1b;
}

.feature-card {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    transition: transform 0.3s ease;
}

.feature-card:hover {
    transform: translateY(-5px);
}

.feature-card img {
    width: 100%;
    height: 250px;
    object-fit: cover;
    display: block;
}

.feature-content {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 20px;
    background: linear-gradient(to top, rgba(0,0,0,0.9), rgba(0,0,0,0.4), transparent);
    color: white;
}

.feature-content h2 {
    font-size: 24px;
    margin: 0 0 10px 0;
    font-weight: bold;
}

.feature-content p {
    font-size: 14px;
    margin: 0 0 20px 0;
    line-height: 1.4;
    opacity: 0.9;
}

.shop-button {
    display: inline-flex;
    align-items: center;
    padding: 8px 16px;
    background-color: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 6px;
    color: white;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.shop-button:hover {
    background-color: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.3);
}

.shop-button::after {
    content: "↗";
    margin-left: 6px;
    font-size: 16px;
}

@media (max-width: 768px) {
    .features-container {
        grid-template-columns: 1fr;
    }
    
    .feature-card img {
        height: 200px;
    }
    
    .feature-content {
        padding: 15px;
    }
    
    .feature-content h2 {
        font-size: 20px;
    }
    
    .feature-content p {
        font-size: 13px;
        margin-bottom: 15px;
    }
}

    </style>
    <link rel="icon" href="STK-20250209-WA0001.webp" type="image/icon type">
</head>
<body>

    <!-- Navbar -->
<div class="navbar">
    <div class="logo"><img src="STK-20250209-WA0001.webp" alt=""></div>
    <div class="menu">
        <a href="dashboad.php">Home</a>
        <a href="browse.php">Browse</a>
        <a href="wishlist.php">Wishlist</a>
        <a href="library.php">Library</a>
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
            <source src="i2.mp4" type="video/mp4">
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
                        <img src="games_posters/' . $image_url . '" alt="' . $title . '">
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
        <img src="w1.jpg" alt="Fortnite x Adidas">
        <div class="feature-content">
            <h2>Fortnite</h2>
            <p>Kick it in style with new adidas Kicks!</p>
            <a href="https://www.adidas.co.in/superstar-shoes/EG4959.html?pr=taxonomy_rr&slot=1&rec=mt" class="shop-button">See In Shop</a>
        </div>
    </div>

    <div class="feature-card">
        <img src="w2.jpg" alt="Fortnite x Jujutsu Kaisen">
        <div class="feature-content">
            <h2>Fortnite</h2>
            <p>Keikatsu! Curse the competition as the King of Curses Ryomen Sukuna, cursed spirit Mahito, and assassin Toji Fushiguro.</p>
            <a href="https://www.netflix.com/in/title/81278456" class="shop-button">See In Shop</a>
        </div>
    </div>

    <div class="feature-card">
        <img src="w3.jpg" alt="Fortnite x Metallica">
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
                            <img src="games_posters/' . $row["image_url"] . '" alt="' . $row["title"] . '">
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
                        <img src="games_posters/' . $row["image_url"] . '" alt="' . $row["title"] . '">
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
                        <img src="games_posters/' . $row["image_url"] . '" alt="' . $row["title"] . '">
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
                        <img src="games_posters/' . $row["image_url"] . '" alt="' . $row["title"] . '">
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