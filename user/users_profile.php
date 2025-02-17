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
    <style>
/* General Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    cursor: url("https://cur.cursors-4u.net/holidays/hol-4/hol336.cur"), auto !important;
}

body {
    display: flex;
    height: 100vh;
    background: #242526;
    color: #fff;  /* Changed to white for better contrast */
    line-height: 1.6;
}

/* Navbar styling */
.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 2rem;
    background-color: #242526;
    width: 100%;
    position: fixed;
    top: 0;
    z-index: 1000;
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

.navbar .username {
    font-size: 1rem;
    color: #fff;
    font-weight: bold;
    margin-left: 10px;
}

.navbar .menu svg {
    margin-left: 10px;
    fill: #fff;
    cursor: pointer;
    transition: transform 0.3s ease;
}

.navbar .menu svg:hover {
    transform: scale(1.1);
}

/* Sidebar */
.sidebar {
    width: 250px;
    background: #242526;
    padding: 80px 0 20px 0;
    border-right: 1px solid #32CD32;  /* Changed to match hover color */
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-top: 60px;
}

.sidebar a {
    text-decoration: none;
    color: #fff;  /* Changed to white */
    font-size: 15px;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 24px;
    transition: all 0.3s ease;
}

.sidebar a:hover, .sidebar .active {
    background: #32CD32;  /* Changed to match navbar hover */
    color: #fff;
    border-left: 3px solid #fff;
}

.nav-icon {
    width: 20px;
    height: 20px;
    opacity: 0.7;
}

.sidebar a:hover .nav-icon, .sidebar .active .nav-icon {
    opacity: 1;
    stroke: #fff;
}

.main-content {
    flex: 1;
    margin-left: 250px;
    padding: 100px 40px 40px 40px;
    max-width: 800px;
}

.card {
    background: #242526;
    padding: 32px;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    width: 100%;
    border: 1px solid #32CD32;
}

h2 {
    font-size: 28px;
    margin-bottom: 12px;
    color: #fff;
    font-weight: 600;
}

p {
    color: #fff;
    font-size: 15px;
    margin-bottom: 24px;
}

.form-group {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #242526;
    padding: 12px;
    border-radius: 8px;
    margin-top: 16px;
    border: 1px solid #32CD32;
    transition: all 0.3s ease;
}

.form-group:focus-within {
    border-color: #32CD32;
    box-shadow: 0 0 0 3px rgba(50, 205, 50, 0.1);
}

.form-group input {
    border: none;
    background: transparent;
    flex: 1;
    font-size: 15px;
    padding: 8px;
    color: #fff;
}

.form-group input:focus {
    outline: none;
}

.form-group button {
    background: #32CD32;
    border: none;
    color: #fff;
    padding: 8px 16px;
    cursor: pointer;
    border-radius: 6px;
    font-weight: 500;
    transition: background 0.3s ease;
}

.form-group button:hover {
    background: #28a428;
}

.created_at {
    font-size: 14px;
    color: #fff;
    margin-top: 24px;
    padding-top: 24px;
    border-top: 1px solid #32CD32;
}

.success {
    color: #32CD32;
    background: #242526;
    padding: 12px 16px;
    border-radius: 8px;
    font-size: 14px;
    margin-top: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
    border: 1px solid #32CD32;
}

.error {
    color: #ff4444;
    background: #242526;
    padding: 12px 16px;
    border-radius: 8px;
    font-size: 14px;
    margin-top: 16px;
    border: 1px solid #ff4444;
}

@media (max-width: 768px) {
    body {
        flex-direction: column;
    }

    .navbar {
        flex-direction: column;
        padding: 1rem;
    }

    .sidebar {
        width: 100%;
        height: auto;
        position: static;
        padding: 10px;
        border-right: none;
        border-bottom: 1px solid #32CD32;
        flex-direction: row;
        overflow-x: auto;
        margin-top: 60px;
    }

    .sidebar a {
        padding: 8px 16px;
        font-size: 14px;
        white-space: nowrap;
    }

    .main-content {
        margin-left: 0;
        padding: 20px;
    }

    .card {
        padding: 20px;
    }

    .form-group {
        flex-direction: column;
        gap: 12px;
    }

    .form-group button {
        width: 100%;
    }
}
    </style>
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
