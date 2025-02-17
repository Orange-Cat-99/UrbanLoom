<?php
session_start();

// Disable warnings and notices
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', 0);

// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "urbanloom";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Registration
$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $phone_number = isset($_POST['phone_number']) ? trim($_POST['phone_number']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $confirm_password = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';

    // Basic validation
    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } elseif (!preg_match("/^[0-9]{10}$/", $phone_number)) {
        $error = "Invalid phone number!";
    } else {
        // Check if the email, username, or phone number already exists
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? OR username = ? OR phone_number = ?");
        $stmt->bind_param("sss", $email, $username, $phone_number);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Email, username, or phone number already exists!";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user into the database with timestamp
            $stmt = $conn->prepare("INSERT INTO users (email, username, phone_number, password, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("ssss", $email, $username, $phone_number, $hashed_password);

            if ($stmt->execute()) {
                $_SESSION['username'] = $username;
                $_SESSION['user_id'] = $stmt->insert_id;
                header("Location: dashboad.php");
                exit();
            } else {
                $error = "Error registering user. Please try again.";
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
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

        .register-container {
            position: relative;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0px 0px 15px rgba(255, 255, 255, 0.2);
            width: 350px;
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
            background:rgb(11, 171, 14);
            border: none;
            color: white;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: 0.3s;
        }

        button:hover {
            background:rgb(20, 126, 12) ;
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

        /* Error Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            overflow: auto;
            padding-top: 60px;
        }

        .modal-content {
            background: rgba(255, 255, 255, 0.8);
            margin: auto;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 400px;
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
        }

        .modal-content button:hover {
            background-color: #c0392b;
        }

        .close {
            position: absolute;
            top: 10px;
            right: 25px;
            color: #000;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover {
            color: #000;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <spline-viewer url="https://prod.spline.design/JvaBvw-ufbTsUHbd/scene.splinecode"></spline-viewer>

    <div class="register-container">
        <h2>Create Account</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Enter Username" required>
            <input type="email" name="email" placeholder="Enter Email" required>
            <input type="text" name="phone_number" placeholder="Enter Phone Number (10 digits)" required>
            <input type="password" name="password" placeholder="Enter Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <button type="submit">Create Account</button>
        </form>
        <div class="login-link">
            Already have an account? <a href="login1.php">Login here</a>
        </div>
    </div>

    <script>
        <?php if ($error) { ?>
            alert("<?php echo $error; ?>");
        <?php } ?>
    </script>
</body>
</html>
