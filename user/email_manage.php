<php
session_start();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Epic Games - Email Preferences</title>
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
    background: #121212; /* Dark background to match navbar */
    color: #e0e0e0; /* Light text color */
    line-height: 1.6;
    flex-direction: row;
}

/* Navbar Styling (Horizontal) */
.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 2rem;
    background-color: #242526; /* Dark gray */
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 10;
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
    flex-direction: row;
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
    background-color: #32CD32; /* Light green accent */
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

/* Sidebar Styles (Vertical on Left) */
.sidebar {
    width: 250px;
    background: #181818; /* Dark background for sidebar */
    padding: 20px 0;
    border-right: 1px solid #333;
    height: 100vh;
    position: fixed;
    top: 60px;
    left: 0;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.sidebar a {
    text-decoration: none;
    color: #e0e0e0; /* Light text */
    font-size: 15px;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 24px;
    transition: all 0.3s ease;
}

.sidebar a:hover, .sidebar .active {
    background: #32CD32; /* Light green accent */
    color: #121212; /* Dark text */
    border-left: 3px solid #32CD32; /* Matching left border on hover */
}

.nav-icon {
    width: 20px;
    height: 20px;
    opacity: 0.7;
}

.sidebar a:hover .nav-icon,
.sidebar .active .nav-icon {
    opacity: 1;
    stroke: #32CD32;
}

/* Main Content (Adjusted for Sidebar and Navbar) */
.main-content {
    flex: 1;
    margin-left: 250px;
    padding: 40px;
    margin-top: 80px;
    max-width: 800px;
    background: #1e1e1e; /* Dark background for main content */
    border-radius: 12px;
}

.card {
    background: #2c2f33; /* Card background */
    padding: 32px;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    width: 100%;
    color: #e0e0e0; /* Light text inside the card */
}

h1, h2 {
    color: #e0e0e0; /* Light text for headings */
}

h2 {
    margin-bottom: 24px;
    font-weight: 600;
}

p {
    color: #bbb; /* Lighter color for paragraphs */
    font-size: 15px;
    margin-bottom: 32px;
}

/* Subscription Container */
.subscription-container {
    margin-top: 32px;
}

.toggle-container {
    display: flex;
    align-items: center;
    gap: 12px;
}

.switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #bbb;
    transition: .4s;
    border-radius: 24px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 2px;
    bottom: 2px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .slider {
    background-color: #32CD32; /* Green accent for switch */
}

input:checked + .slider:before {
    transform: translateX(26px);
}

label {
    font-size: 15px;
    cursor: pointer;
    color: #e0e0e0;
}

/* Status Messages */
.success {
    color: #2e7d32;
    background: #e8f5e9;
    padding: 12px 16px;
    border-radius: 8px;
    font-size: 14px;
    margin-top: 16px;
    display: none; /* Initially hidden */
}

.error {
    color: #d32f2f;
    background: #fde8e8;
    padding: 12px 16px;
    border-radius: 8px;
    font-size: 14px;
    margin-top: 16px;
}

/* Responsive Design */
@media (max-width: 768px) {
    body {
        flex-direction: column;
    }

    .sidebar {
        width: 100%;
        height: auto;
        position: static;
        padding: 10px;
        border-right: none;
        border-bottom: 1px solid #e5e5e5;
        flex-direction: row;
        overflow-x: auto;
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

    .toggle-container {
        flex-direction: row;
        align-items: center;
    }

    /* Adjust navbar for mobile view */
    .navbar {
        flex-direction: column;
        align-items: flex-start;
        padding: 1rem;
    }

    .navbar .menu {
        flex-direction: column;
        width: 100%;
    }

    .navbar .menu a {
        width: 100%;
        text-align: left;
        padding: 10px 0;
    }
}

    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <div class="logo"><img src="STK-20250209-WA0001.webp" alt=""></div>
        <div class="menu">
            <a href="dashboard.php">Home</a>
            <a href="browse.php">Browse</a>
            <a href="wishlist.php">Wishlist</a>
            <a href="library.php">Library</a>
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
