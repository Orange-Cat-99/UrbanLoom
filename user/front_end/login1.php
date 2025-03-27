<?php
session_start();

// Disable warnings and notices
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', 0);

// Database Connection
$servername = "localhost"; // Change if needed
$username = "root"; // Your DB username
$password = ""; // Your DB password
$database = "urbanloom"; // Your Database Name

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Login
$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    // Modified query to check both username or email
    $stmt = $conn->prepare("SELECT user_id, username, password FROM users WHERE email = ? OR username = ?");
    $stmt->bind_param("ss", $email, $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $username, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            header("Location: dashboad.php");
            exit();
        } else {
            $error = "Invalid email/username or password!";
        }
    } else {
        $error = "User not found!";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script type="module" src="https://unpkg.com/@splinetool/viewer@1.9.61/build/spline-viewer.js"></script>
    <link rel="stylesheet" href="..\style\login.css">
</head>
<body>
    <!-- New Spline model -->
    <spline-viewer url="https://prod.spline.design/JvaBvw-ufbTsUHbd/scene.splinecode"></spline-viewer>

    <div class="login-container">
        <h2>Login</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Enter Username or Email" required>
            <input type="password" name="password" placeholder="Enter Password" required>
            <button type="submit">Login</button>
        </form>
        <div class="login-link">
            Create account here <a href="sigin_up.php">SiginUp here</a>
        </div>
    </div>

    <!-- The Modal -->
    <div id="errorModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3 id="errorMessage"></h3>
            <button id="closeBtn">Close</button>
        </div>
    </div>

    <script>
        // Show custom modal for errors
        <?php if ($error) { ?>
            // Display the modal
            var modal = document.getElementById("errorModal");
            var message = document.getElementById("errorMessage");
            var closeBtn = document.getElementById("closeBtn");

            message.textContent = "<?php echo $error; ?>";
            modal.style.display = "block";

            // Close the modal when the user clicks the "close" button
            closeBtn.onclick = function() {
                modal.style.display = "none";
            }

            // Close the modal when the user clicks the "X" button
            document.querySelector(".close").onclick = function() {
                modal.style.display = "none";
            }

            // Close the modal when clicking outside the modal
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        <?php } ?>
    </script>
</body>
</html>
