<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "urbanloom");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Error: User not logged in.");
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$sql = "SELECT username, email, phone_number, created_at FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];

    // Update user details
    $update_sql = "UPDATE users SET username=?, email=?, phone_number=? WHERE user_id=?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssi", $username, $email, $phone_number, $user_id);

    if ($update_stmt->execute()) {
        echo "<p class='success'>Profile updated successfully!</p>";
        header("Refresh:0");
        exit();
    } else {
        echo "<p class='error'>Error updating profile.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings</title>
    <link rel="stylesheet" href="..\style\user_profile.css">
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

<!-- Replace the existing sidebar content with this -->
<div class="sidebar">
    <a href="users_profile.php" class="active">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
            <circle cx="12" cy="7" r="4"></circle>
        </svg>
        Account Settings
    </a>
    <a href="email_manage.php">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M22 17H2a3 3 0 0 0 3-3V9a7 7 0 0 1 14 0v5a3 3 0 0 0 3 3zm-8.27 4a2 2 0 0 1-3.46 0"></path>
        </svg>
        Email Preferences
    </a>
    <a href="game_issue_bot.php">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <path d="M9.09 9a3 3 0 0 1 5.82 0c0 1.5-1 2-2 2s-2 .5-2 2m0 3h.01"></path>
        </svg>
        Support Center
    </a>
    <a href="game_review.php">
    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
    </svg>
    Review Center
</a>
</div>
    
</div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="card">
            <h2>Account Settings</h2>
            <p>Manage your account’s details.</p>

            <form method="post">
                <div class="form-group">
                    <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    <button type="submit">Edit</button>
                </div>

                <div class="form-group">
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    <button type="submit">Edit</button>
                </div>

                <div class="form-group">
                    <input type="text" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>
                    <button type="submit">Edit</button>
                </div>

                <p class="created_at">Account Created On: <strong><?php echo $user['created_at']; ?></strong></p>
            </form>
        </div>
    </div>

    <!-- FontAwesome for icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

</body>
</html>
