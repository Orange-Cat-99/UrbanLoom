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
    <style>
        :root {
            --primary-color: #32CD32;
            --secondary-color: #228B22;
            --dark-bg: #121212;
            --navbar-bg: #1A1A1A;
            --card-bg: #1e1e1e;
            --text-primary: #ffffff;
            --text-secondary: #b0b0b0;
            --hover-color: #2E8B57;
        }


        * {
            cursor: url("https://cur.cursors-4u.net/holidays/hol-4/hol336.cur"), auto !important;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: var(--dark-bg);
            color: var(--text-primary);
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            background-color: var(--navbar-bg);
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            box-sizing: border-box;
        }

        .navbar .logo img {
            height: 50px;
            max-width: 150px;
            object-fit: contain;
        }

        .navbar .menu {
            display: flex;
            gap: 1.2rem;
            align-items: center;
        }

        .navbar .menu a {
            text-decoration: none;
            color: var(--text-primary);
            font-size: 1rem;
            padding: 0.5rem;
            border-radius: 5px;
            transition: background 0.3s ease;
        }

        .navbar .menu a:hover {
            background-color: var(--primary-color);
            color: black;
        }

        .library {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(500px, 1fr));
            gap: 30px;
            padding: 20px;
            max-width: 1400px;
            width: 100%;
            margin-top: 80px;
            box-sizing: border-box;
        }

        .game {
            display: flex;
            align-items: center;
            background: var(--card-bg);
            border-radius: 12px;
            padding: 15px;
            gap: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .game:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .game img {
            width: 180px;
            height: 180px;
            object-fit: cover;
            border-radius: 12px;
        }

        .game-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .game h2 {
            font-size: 1.5rem;
            color: var(--text-primary);
            font-weight: 600;
            margin-bottom: 10px;
        }

        .play-button {
            padding: 12px 16px;
            background-color: var(--secondary-color);
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .play-button:hover {
            background-color: var(--hover-color);
        }

        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                padding: 1rem;
                align-items: center;
                text-align: center;
            }

            .library {
                grid-template-columns: 1fr;
            }

            .game {
                flex-direction: column;
                text-align: center;
                padding: 20px;
            }
        }
    </style>
    <link rel="icon" href="STK-20250209-WA0001.webp" type="image/icon type">
</head>
<body>
    <div class="navbar">
        <div class="logo"><img src="STK-20250209-WA0001.webp" alt=""></div>
        <div class="menu">
            <a href="dashboad.php">Home</a>
            <a href="browse.php">Browse</a>
            <a href="wishlist.php">Wishlist</a>
            <a href="library.php">Library</a>
        </div>
    </div>

    <h1>My Game Library</h1>
    <div class="library">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="game">
                <img src="games_posters/<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
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
