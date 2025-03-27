<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "urbanloom");
if (!$conn) die("Connection failed: " . mysqli_connect_error());

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$game_id = mysqli_real_escape_string($conn, $_GET['id']);
$query = "SELECT * FROM games WHERE game_id = '$game_id'";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    header("Location: index.php");
    exit();
}

$game = mysqli_fetch_assoc($result);
$price = $game['price'];
$vat = $price * 0.18;
$total = $price + $vat;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json'); // Ensure this header is sent before any output

    if (isset($_POST['game_id'])) {
        $user_id = $_SESSION['user_id'] ?? null;
        if (!$user_id) {
            echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
            exit;
        }

        $check_query = "SELECT * FROM user_library WHERE user_id = ? AND game_id = ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("ii", $user_id, $_POST['game_id']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Game already in library']);
            exit;
        }

        $insert_query = "INSERT INTO user_library (user_id, game_id, purchase_date) VALUES (?, ?, NOW())";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("ii", $user_id, $_POST['game_id']);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to add game']);
        }
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buy <?= htmlspecialchars($game['title']) ?></title>
    <link rel="stylesheet" href="..\style\buy_games.css">
<link rel="icon" href="..\elements\STK-20250209-WA0001.webp" type="image/icon type">
</head>
<body>
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
            $user_id = $_SESSION['user_id'] ?? null;
            if ($user_id) {
                $sql = "SELECT username FROM users WHERE user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    echo '<span class="username">' . htmlspecialchars($row['username']) . '</span>';
                } else {
                    echo '<span class="username">Guest</span>';
                }
            } else {
                echo '<span class="username">Guest</span>';
            }
            ?>
        </div>
    </div>

    <div class="container">
        <div class="game-details">
            <h2 class="section-title">CHECKOUT</h2>
            <div class="payment-section">
                <h3 class="section-title">PAYMENT METHODS</h3>
                <div class="payment-option">
                    <input type="radio" name="payment" value="credit_card" checked>
                    Credit Card
                </div>
                <div class="payment-option">
                    <input type="radio" name="payment" value="paypal">
                    PayPal
                </div>
            </div>
        </div>

        <div class="order-summary">
            <h2 class="section-title">ORDER SUMMARY</h2>
            <div class="game-info-header">
                <img src="../games_posters/<?= htmlspecialchars($game['image_url']) ?>" 
                     alt="<?= htmlspecialchars($game['title']) ?>" 
                     class="game-thumbnail">
                <div class="game-basic-info">
                    <h3 class="game-title"><?= htmlspecialchars($game['title']) ?></h3>
                    <p class="publisher"><?= htmlspecialchars($game['platform']) ?></p>
                    <div>$<?= number_format($price, 2) ?></div>
                </div>
            </div>

            <div class="price-breakdown">
                <div class="price-line">
                    <span>Price</span>
                    <span>$<?= number_format($price, 2) ?></span>
                </div>
                <div class="price-line">
                    <span>VAT (18%)</span>
                    <span>$<?= number_format($vat, 2) ?></span>
                </div>
                <div class="price-line total-line">
                    <span>Total</span>
                    <span>$<?= number_format($total, 2) ?></span>
                </div>
            </div>

            <input type="text" class="creator-code" placeholder="Enter creator code">
            
            <div class="terms">
                <label>
                    <input type="checkbox" required>
                    I agree to share my email with the game publisher
                </label>
            </div>

            <button class="place-order-btn">PLACE ORDER</button>

            <div class="support-info">
                Need Help? <a href="#">Contact Us</a>
            </div>
        </div>
    </div>

    <div class="popup-overlay" id="qrPopup">
        <div class="popup-content">
            <img src="..\elements\qr_code.png" alt="Scan QR Code" class="qr-image">
            <h2 class="popup-title">Scan to Confirm</h2>
            <p class="popup-message">Scan the QR code to complete your purchase.</p>
        </div>
    </div>

    <div class="popup-overlay" id="successPopup">
        <div class="popup-content">
            <img src="..\elements\succes_img.png" alt="Success" class="success-image">
            <h2 class="popup-title">Order Successful!</h2>
            <p class="popup-message">Your game has been successfully purchased.</p>
            <button class="popup-button" onclick="window.location.href='dashboad.php'">Continue Shopping</button>
        </div>
    </div>

    <script>
    const placeOrderBtn = document.querySelector('.place-order-btn');
    const qrPopup = document.getElementById('qrPopup');
    const successPopup = document.getElementById('successPopup');
    const game_id = <?= json_encode($game_id) ?>;

    async function addGameToLibrary() {
    const formData = new FormData();
    formData.append('game_id', game_id);
    
    try {
        const response = await fetch(window.location.href, {
            method: 'POST',
            body: formData
        });

        const textResponse = await response.text(); // Read response as text
        console.log(textResponse); // Log raw response to debug

        let data;
        try {
            data = JSON.parse(textResponse); // Try parsing the JSON
        } catch (e) {
            throw new Error('Invalid JSON response: ' + textResponse);
        }

        qrPopup.style.display = 'none';

        if (data.status === 'success') {
            successPopup.style.display = 'flex';
        } else {
            throw new Error(data.message || 'Failed to process order');
        }
    } catch (error) {
        console.error('Error:', error);
        const errorMessage = document.createElement('div');
        errorMessage.className = 'error-message';
        errorMessage.textContent = error.message || 'Unable to process order';
        successPopup.querySelector('.popup-content').prepend(errorMessage);
        successPopup.style.display = 'flex';
    }
}


    placeOrderBtn.addEventListener('click', () => {
        if (!document.querySelector('.terms input').checked) {
            alert('Please agree to the terms');
            return;
        }
        qrPopup.style.display = 'flex';
        setTimeout(() => {
            qrPopup.style.display = 'none';
            addGameToLibrary();
        }, 3000);
    });

    [qrPopup, successPopup].forEach(popup => {
        popup.addEventListener('click', (e) => {
            if (e.target === popup) popup.style.display = 'none';
        });
    });
    </script>
</body>
</html>