<php
session_start();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Epic Games - Email Preferences</title>
    <link rel="stylesheet" href="..\style\email_manage.css">
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <div class="logo"><img src="..\elements\STK-20250209-WA0001.webp" alt=""></div>
        <div class="menu">
            <a href="dashboard.php">Home</a>
            <a href="browse.php">Browse</a>
            <a href="wishlist.php">Wishlist</a>
            <a href="library.php">Library</a>
            <a href="chatting.php">Friends</a>
            <a href="users_profile.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 48 48">
                    <path d="M24,4C12.972,4,4,12.972,4,24s8.972,20,20,20s20-8.972,20-20S35.028,4,24,4z M24,13c2.761,0,5,2.239,5,5 c0,2.761-2.239,5-5,5s-5-2.239-5-5C19,15.239,21.239,13,24,13z M33,29.538C33,32.397,29.353,35,24,35s-9-2.603-9-5.462v-0.676 C15,27.834,15.834,27,16.862,27h14.276C32.166,27,33,27.834,33,28.862V29.538z"></path>
                </svg>
            </a>
        </div>
    </div>

    <!-- Sidebar for User Settings -->
    <nav class="sidebar">
        <a href="users_profile.php" class="nav-item">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>
            ACCOUNT SETTINGS
        </a>
        <a href="email_manage.php" class="nav-item active">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 17H2a3 3 0 0 0 3-3V9a7 7 0 0 1 14 0v5a3 3 0 0 0 3 3zm-8.27 4a2 2 0 0 1-3.46 0"></path>
            </svg>
            EMAIL PREFERENCES
        </a>
        <a href="game_issue_bot.php">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <path d="M9.09 9a3 3 0 0 1 5.82 0c0 1.5-1 2-2 2s-2 .5-2 2m0 3h.01"></path>
        </svg>
        Support Center
    </a>
    </a>
    <a href="game_review.php">
    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
    </svg>
    Review Center
</a>
</div>
        <!-- More sidebar links as needed -->
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <h1>Email Preferences</h1>
        <p>
            Manage your Epic Games email subscription preferences for news, surveys, and special offers.
            Transactional emails such as purchase receipts, email verification, password resets, and two-factor
            authentication are not affected by your subscription preferences.
        </p>

        <div class="card">
            <h2>Subscriptions</h2>
            <p>Change your preferences below:</p>

            <div class="subscription-container">
                <div class="toggle-container">
                    <input type="checkbox" id="toggle-emails">
                    <label for="toggle-emails">Receive email updates</label>
                </div>
            </div>

            <!-- Success message (Initially hidden) -->
            <div class="success" id="success-message">Your email preferences have been saved!</div>
        </div>
    </main>

    <script>
        // JavaScript to toggle success message visibility
        const toggleCheckbox = document.getElementById('toggle-emails');
        const successMessage = document.getElementById('success-message');

        toggleCheckbox.addEventListener('change', function() {
            if (this.checked) {
                successMessage.style.display = 'block'; // Show the success message
            } else {
                successMessage.style.display = 'none'; // Hide the success message if unchecked
            }
        });
    </script>
</body>
</html>
