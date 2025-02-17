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
    <style>

        * {
            cursor: url("https://cur.cursors-4u.net/holidays/hol-4/hol336.cur"), auto !important;
        }


        body {
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: #ffffff;
            margin: 0;
            padding: 0;
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

        .container {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }

        h1 {
            font-size: 32px;
            margin-bottom: 20px;
        }

        .genres {
            display: flex;
            gap: 20px;
            margin-bottom: 40px;
            justify-content: space-between;
        }

        .genre-card {
            background-color: #1e1e1e;
            border-radius: 10px;
            flex: 1;
            padding: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .genre-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 10px;
        }

        .genre-title {
            margin-top: 10px;
            font-size: 18px;
            font-weight: bold;
        }

        .games {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 40px;  /* Add space above the games section */
        }

        .game-card {
            background-color: #1e1e1e;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .game-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 10px;
        }

        .game-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .game-price {
            font-size: 14px;
            color: #00ff00;
        }

        .filter-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .filter-bar select,
        .filter-bar input {
            padding: 5px 10px;
            background-color: #1e1e1e;
            color: #ffffff;
            border: 1px solid #333333;
            border-radius: 5px;
        }

        .filter-bar input {
            flex: 1;
            max-width: 300px;
        }

          /* Styling for the genre options section */
    .genre-options {
        margin-top: 30px;
        text-align: center;
    }

    .genre-options h2 {
        font-size: 22px;
        margin-bottom: 15px;
        color: #ffffff;
    }

    .options {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .options button {
        padding: 10px 20px;
        background-color: #1e1e1e;
        color: #ffffff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        font-size: 14px;
    }

    .options button:hover {
        background-color: #00ff00;
        color: #000000;
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
                    <img src="games_posters/<?php echo $image; ?>" alt="<?php echo $genre; ?>" class="genre-image">
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
                    <img src="games_posters/<?php echo $image_url; ?>" alt="<?php echo $title; ?>" class="game-image">
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
                    <img src="games_posters/<?php echo $image_url; ?>" alt="<?php echo $title; ?>" class="game-image">
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
