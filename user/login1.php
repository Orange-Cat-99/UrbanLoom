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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            cursor: url("https://cur.cursors-4u.net/holidays/hol-4/hol336.cur"), auto !important;
        }
        
        body {
            font-family: Arial, sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
        }

        spline-viewer {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: -1;
        }

        .login-container {
            position: relative;
            background: rgba(255, 255, 255, 0.1); /* Semi-transparent */
            backdrop-filter: blur(5px); /* Frosted Glass Effect */
            -webkit-backdrop-filter: blur(5px);
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0px 0px 15px rgba(255, 255, 255, 0.2);
            width: 320px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        h2 {
            color: #fff;
        }

        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.3);
            color: #fff;
            font-size: 16px;
            outline: none;
        }

        input::placeholder {
            color: rgb(55, 55, 55);
        }

        button {
            width: 100%;
            padding: 12px;
            background: rgba(0, 255, 0, 0.7);
            border: none;
            color: white;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: 0.3s;
        }

        button:hover {
            background: rgba(0, 255, 0, 0.9);
        }

        /* Custom Modal Style with Glass Effect */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4); /* Black background with opacity */
            overflow: auto;
            padding-top: 60px;
        }

        .modal-content {
            background: rgba(255, 255, 255, 0.8); /* Semi-transparent background */
            margin: auto;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            width: 80%;
            max-width: 400px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .modal-content h3 {
            color: #e74c3c;
        }

        .modal-content button {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }

        .modal-content button:hover {
            background-color: #c0392b;
        }

        /* Close button */
        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            position: absolute;
            top: 10px;
            right: 25px;
            color: #000;
        }

        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }

        /* Performance optimization for transitions */
        .modal-content,
        .login-container {
            will-change: transform, opacity;
        }


        .login-link {
            margin-top: 15px;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.8);
        }

        .login-link a {
            color:green;
            text-decoration: none;
            font-weight: bold;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

    </style>
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
