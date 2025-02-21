<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "urbanloom";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Determine what to show
$show_games_all = isset($_GET['show_games_all']) ? true : false;
$show_users_all = isset($_GET['show_users_all']) ? true : false;

// Handle Add/Update form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Game form submission
    if (isset($_POST['title'])) {
        $title = $_POST['title'];
        $genre = $_POST['genre'];
        $platform = $_POST['platform'];
        $price = $_POST['price'];
        $stock = $_POST['stock'];
        $release_date = $_POST['release_date'];
        $description = $_POST['description'];
        $image_url = $_POST['image_url'];

        if (isset($_POST['update_id'])) {
            // Update existing game
            $id = $_POST['update_id'];
            $sql = "UPDATE games SET title=?, genre=?, platform=?, price=?, stock=?, release_date=?, description=?, image_url=? WHERE game_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssdisssi", $title, $genre, $platform, $price, $stock, $release_date, $description, $image_url, $id);
        } else {
            // Add new game
            $sql = "INSERT INTO games (title, genre, platform, price, stock, release_date, description, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssdisss", $title, $genre, $platform, $price, $stock, $release_date, $description, $image_url);
        }
        
        if ($stmt->execute()) {
            echo "<script>alert('Operation successful');</script>";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    }
    
    // To-do form submission
    if (isset($_POST['todo_task'])) {
        $task = $_POST['todo_task'];
        $priority = $_POST['todo_priority'];
        $due_date = $_POST['todo_due_date'];
        
        $sql = "INSERT INTO todos (task, priority, due_date, status) VALUES (?, ?, ?, 'pending')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $task, $priority, $due_date);
        
        if ($stmt->execute()) {
            echo "<script>alert('To-do added successfully');</script>";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    }
    
    // To-do status update
    if (isset($_POST['todo_complete'])) {
        $todo_id = $_POST['todo_complete'];
        $sql = "UPDATE todos SET status = 'completed', completed_at = NOW() WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $todo_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM games WHERE game_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "<script>alert('Game deleted successfully');</script>";
        // Maintain view state after delete
        $redirect = $_SERVER['PHP_SELF'];
        $params = [];
        if ($show_games_all) $params[] = "show_games_all=1";
        if ($show_users_all) $params[] = "show_users_all=1";
        if (!empty($params)) $redirect .= '?' . implode('&', $params);
        echo "<script>window.location.href='$redirect';</script>";
    }
    $stmt->close();
}

// Handle To-do delete
if (isset($_GET['delete_todo'])) {
    $todo_id = $_GET['delete_todo'];
    $sql = "DELETE FROM todos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $todo_id);
    if ($stmt->execute()) {
        echo "<script>alert('To-do deleted successfully');</script>";
        echo "<script>window.location.href='" . $_SERVER['PHP_SELF'] . buildQueryString() . "';</script>";
    }
    $stmt->close();
}

// Get game data for editing
$edit_game = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $sql = "SELECT * FROM games WHERE game_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_game = $result->fetch_assoc();
    $stmt->close();
}

// Get limited games data for preview
$limit_games = $show_games_all ? 100 : 3; // Show only 3 games in preview mode
$sql_games = "SELECT * FROM games ORDER BY game_id DESC LIMIT $limit_games";
$games_result = $conn->query($sql_games);

// Get limited users data for preview
$limit_users = $show_users_all ? 100 : 3; // Show only 3 users in preview mode
$sql_users = "SELECT * FROM users ORDER BY created_at DESC LIMIT $limit_users";
$users_result = $conn->query($sql_users);

// Count total records for stats
$total_games = $conn->query("SELECT COUNT(*) as count FROM games")->fetch_assoc()['count'];
$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];

// Get to-do list items
$sql_todos = "SELECT * FROM todos ORDER BY 
              CASE WHEN status = 'pending' THEN 0 ELSE 1 END, 
              CASE priority
                  WHEN 'high' THEN 1
                  WHEN 'medium' THEN 2
                  WHEN 'low' THEN 3
              END, 
              due_date ASC";
$todos_result = $conn->query($sql_todos);

// Get active users data for graph (last 7 days)
$active_users_data = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $next_date = date('Y-m-d', strtotime("-" . ($i - 1) . " days"));
    
    // Query for active users on this day (simulate with login_history table)
    // In a real application, you would query your actual login or activity table
    $sql = "SELECT COUNT(DISTINCT user_id) as count FROM user_logins 
            WHERE login_time >= '$date 00:00:00' AND login_time < '$next_date 00:00:00'";
    
    // For demonstration, we'll generate random data
    // In production, use the actual query above
    $active_count = rand(15, 50);
    
    $active_users_data[] = [
        'date' => date('M d', strtotime($date)),
        'count' => $active_count
    ];
}

// Helper function to preserve query parameters
function buildQueryString($params = []) {
    global $show_games_all, $show_users_all;
    
    $query_params = [];
    if (isset($params['show_games_all'])) {
        if ($params['show_games_all']) $query_params[] = "show_games_all=1";
    } else if ($show_games_all) {
        $query_params[] = "show_games_all=1";
    }
    
    if (isset($params['show_users_all'])) {
        if ($params['show_users_all']) $query_params[] = "show_users_all=1";
    } else if ($show_users_all) {
        $query_params[] = "show_users_all=1";
    }
    
    if (!empty($query_params)) {
        return '?' . implode('&', $query_params);
    }
    return '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>

        /* Scrollbar for WebKit browsers (Chrome, Edge, Safari) */
::-webkit-scrollbar {
  width: 10px; /* Width of the vertical scrollbar */
  height: 10px; /* Height of the horizontal scrollbar */
}

::-webkit-scrollbar-track {
  background:rgb(255, 255, 255); /* Track background */
  border-radius: 10px;
}

::-webkit-scrollbar-thumb {
  background: #888; /* Scrollbar color */
  border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
  background: #555; /* Color on hover */
}

/* Firefox scrollbar styling */
* {
  scrollbar-width: thin; /* "auto" or "thin" */
  scrollbar-color: #888 #f1f1f1; /* thumb color, track color */
}

        /* Global Styles */
        body {
            background-color: #f8f9fa;
            color: #333;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            line-height: 1.6;
        }

        .container {
            max-width: 1400px;
            padding: 2rem;
        }

        /* Headers */
        h2 {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.5rem;
        }

        h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 4px;
            background: linear-gradient(to right, #3498db, #2ecc71);
            border-radius: 2px;
        }

        /* Form Container */
        .form-container {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }

        /* Form Controls */
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 0.7rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
            outline: none;
        }

        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }

        /* Form Layout */
        .row.mb-3 {
            margin-bottom: 1.5rem !important;
        }

        .col {
            padding: 0 1rem;
        }

        /* Select Dropdown */
        select.form-control {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8' viewBox='0 0 8 8'%3E%3Cpath fill='%23333' d='M0 2l4 4 4-4z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 8px 8px;
            padding-right: 2.5rem;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }

        /* Dashboard Layout */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-top: 2rem;
        }

        .dashboard-full-width {
            grid-column: 1 / 3;
        }

        /* Card Containers */
        .card-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem 1.5rem;
            background-color: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }

        .card-header h3 {
            margin: 0;
            font-size: 1.25rem;
            color: #2c3e50;
            font-weight: 600;
        }

        .card-body {
            padding: 1.5rem;
            flex-grow: 1;
            overflow: auto;
        }

        .card-footer {
            padding: 1rem 1.5rem;
            background-color: #f8f9fa;
            border-top: 1px solid #e9ecef;
            text-align: center;
        }

        /* Table Styles */
        .table {
            margin: 0;
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
            color: #2c3e50;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            padding: 1rem;
            text-align: left;
        }

        .table tbody tr {
            transition: all 0.3s ease;
            border-bottom: 1px solid #e9ecef;
        }

        .table tbody tr:last-child {
            border-bottom: none;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .table td {
            padding: 1rem;
            vertical-align: middle;
        }

        /* Action buttons column */
        .action-column {
            white-space: nowrap;
            text-align: right;
            min-width: 160px;
        }

        /* Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
            padding: 1.5rem;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.12);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1.25rem;
            font-size: 1.75rem;
        }

        .games-icon {
            background: linear-gradient(135deg, #4e54c8, #8f94fb);
            color: white;
        }

        .users-icon {
            background: linear-gradient(135deg, #11998e, #38ef7d);
            color: white;
        }

        .active-users-icon {
            background: linear-gradient(135deg, #FF416C, #FF4B2B);
            color: white;
        }

        .todo-icon {
            background: linear-gradient(135deg, #f7b733, #fc4a1a);
            color: white;
        }

        .stat-info {
            flex-grow: 1;
        }

        .stat-number {
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0;
            color: #2c3e50;
        }

        .stat-label {
            font-size: 0.9rem;
            color: #6c757d;
            margin: 0;
        }

        /* Button Styles */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.6rem 1.25rem;
            font-weight: 500;
            border-radius: 6px;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-primary {
            background-color: #0d6efd;
            color: white;
            box-shadow: 0 2px 4px rgba(13, 110, 253, 0.2);
        }

        .btn-primary:hover {
            background-color: #0b5ed7;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(13, 110, 253, 0.2);
        }

        .btn-warning {
            background-color: #ffc107;
            color: #000;
        }

        .btn-warning:hover {
            background-color: #e0a800;
        }

        .btn-danger {
            background-color: #dc3545;
            color: #fff;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .action-btn {
            margin-left: 0.5rem;
            padding: 0.25rem 0.75rem;
            min-width: 50px;
            text-align: center;
        }
        
        .action-btn:first-child {
            margin-left: 0;
        }

        .view-all-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #3498db;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .view-all-btn i {
            margin-left: 0.5rem;
            transition: all 0.2s ease;
        }

        .view-all-btn:hover {
            color: #0d6efd;
            text-decoration: none;
        }

        .view-all-btn:hover i {
            transform: translateX(3px);
        }

        /* Todo List Styles */
        .todo-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .todo-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
            transition: all 0.2s ease;
        }

        .todo-item:last-child {
            border-bottom: none;
        }

        .todo-item:hover {
            background-color: #f8f9fa;
        }

        .todo-checkbox {
            margin-right: 1rem;
            width: 20px;
            height: 20px;
        }

        .todo-content {
            flex-grow: 1;
        }

        .todo-title {
            font-weight: 500;
            margin-bottom: 0.25rem;
            color: #2c3e50;
        }

        .todo-completed .todo-title {
            text-decoration: line-through;
            color: #6c757d;
        }

        .todo-meta {
            display: flex;
            font-size: 0.8rem;
            color: #6c757d;
        }

        .todo-due {
            margin-right: 1rem;
        }

        .todo-priority {
            padding: 0.1rem 0.5rem;
            border-radius: 10px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .priority-high {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        .priority-medium {
            background-color: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }

        .priority-low {
            background-color: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
        }

        .todo-actions {
            display: flex;
            align-items: center;
        }

        .todo-action-btn {
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            padding: 0.25rem;
            margin-left: 0.5rem;
            transition: all 0.2s ease;
        }

        .todo-action-btn:hover {
            color: #dc3545;
            transform: scale(1.1);
        }

        .todo-add-form {
            display: flex;
            margin-bottom: 1rem;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .todo-add-form input[type="text"] {
            flex-grow: 1;
            min-width: 200px;
        }

        .todo-form-controls {
            display: flex;
            gap: 0.5rem;
        }

        /* Chart Styles */
        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: 1rem;
        }

        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .dashboard-full-width {
                grid-column: 1;
            }

            .container {
                padding: 1rem;
            }
        }

        @media (max-width: 768px) {
            .stats-container {
                grid-template-columns: 1fr 1fr;
                gap: 1rem;
            }

            .todo-add-form {
                flex-direction: column;
            }

            .todo-form-controls {
                width: 100%;
            }
        }

        @media (max-width: 576px) {
            .stats-container {
                grid-template-columns: 1fr;
            }

            .table thead {
                display: none;
            }

            .table, .table tbody, .table tr, .table td {
                display: block;
                width: 100%;
            }

            .table tr {
                margin-bottom: 1rem;
                border: 1px solid #e9ecef;
                border-radius: 8px;
                padding: 0.5rem;
            }

            .table td {
                text-align: right;
                padding: 0.5rem;
                position: relative;
                border: none;
            }

            .table td::before {
                content: attr(data-label);
                position: absolute;
                left: 0.5rem;
                font-weight: 600;
            }
            
            .action-column {
                display: flex;
                justify-content: flex-end;
                padding-top: 0.75rem;
                border-top: 1px dashed #e9ecef;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2><?php echo $edit_game ? 'Edit Game' : 'Add New Game'; ?></h2>
        
        <div class="form-container">
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . buildQueryString(); ?>">
                <?php if ($edit_game) { ?>
                    <input type="hidden" name="update_id" value="<?php echo $edit_game['game_id']; ?>">
                <?php } ?>
                
                <div class="row mb-3">
                    <div class="col">
                        <input type="text" name="title" class="form-control" placeholder="Title" 
                               value="<?php echo $edit_game ? $edit_game['title'] : ''; ?>" required>
                    </div>
                    <div class="col">
                        <select name="genre" class="form-control" required>
                            <option value="">Select Genre</option>
                            <?php
                            $genres = ['Action Games', 'Action-Adventure Games', 'Adventure Games', 'Casual Games'];
                            foreach ($genres as $genre) {
                                $selected = ($edit_game && $edit_game['genre'] == $genre) ? 'selected' : '';
                                echo "<option value='$genre' $selected>$genre</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col">
                        <input type="text" name="platform" class="form-control" placeholder="Platform" 
                               value="<?php echo $edit_game ? $edit_game['platform'] : ''; ?>" required>
                    </div>
                    <div class="col">
                        <input type="number" name="price" class="form-control" placeholder="Price" step="0.01" 
                               value="<?php echo $edit_game ? $edit_game['price'] : ''; ?>" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col">
                        <input type="number" name="stock" class="form-control" placeholder="Stock" 
                               value="<?php echo $edit_game ? $edit_game['stock'] : ''; ?>" required>
                    </div>
                    <div class="col">
                        <input type="date" name="release_date" class="form-control" 
                               value="<?php echo $edit_game ? $edit_game['release_date'] : ''; ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <textarea name="description" class="form-control" placeholder="Description" rows="3"><?php echo $edit_game ? $edit_game['description'] : ''; ?></textarea>
                </div>

                <div class="mb-3">
                    <input type="url" name="image_url" class="form-control" placeholder="Image URL" 
                           value="<?php echo $edit_game ? $edit_game['image_url'] : ''; ?>">
                </div>

                <button type="submit" class="btn btn-primary"><?php echo $edit_game ? 'Update Game' : 'Add Game'; ?></button>
                <?php if ($edit_game) { ?>
                    <a href="<?php echo $_SERVER['PHP_SELF'] . buildQueryString(); ?>" class="btn btn-secondary">Cancel</a>
                <?php } ?>
            </form>
        </div>

        <!-- Stats Section -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon games-icon">
                    <i class="fas fa-gamepad"></i>
                </div>
                <div class="stat-info">
                    <h2 class="stat-number"><?php echo $total_games; ?></h2>
                    <p class="stat-label">Total Games</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon users-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h2 class="stat-number"><?php echo $total_users; ?></h2>
                    <p class="stat-label">Registered Users</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon active-users-icon">
                    <i class="fas fa-user-clock"></i>
                </div>
                <div class="stat-info">
                    <h2 class="stat-number"><?php echo end($active_users_data)['count']; ?></h2>
                    <p class="stat-label">Active Users Today</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon todo-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="stat-info">
                    <h2 class="stat-number"><?php 
                        $pending_count = $conn->query("SELECT COUNT(*) as count FROM todos WHERE status='pending'")->fetch_assoc()['count'] ?? 0;
                        echo $pending_count;
                    ?></h2>
                    <p class="stat-label">Pending Tasks</p>
                </div>
            </div>
        </div>

        <!-- Active Users Graph -->
        <div class="dashboard-grid">
            <div class="card-container dashboard-full-width">
                <div class="card-header">
                    <h3>Active Users - Last 7 Days</h3>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="activeUsersChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Grid -->
        <div class="dashboard-grid">
            <!-- Games Section -->
            <div class="card-container">
                <div class="card-header">
                    <h3>Games List</h3>
                    <?php if (!$show_games_all) { ?>
                        <a href="<?php echo $_SERVER['PHP_SELF'] . buildQueryString(['show_games_all' => true]); ?>" class="view-all-btn">
                            View All <i class="fas fa-arrow-right"></i>
                        </a>
                    <?php } else { ?>
                        <a href="<?php echo $_SERVER['PHP_SELF'] . buildQueryString(['show_games_all' => false]); ?>" class="view-all-btn">
                            Show Less <i class="fas fa-arrow-up"></i>
                        </a>
                    <?php } ?>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Platform</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($games_result->num_rows > 0) {
                                    while ($row = $games_result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td data-label='ID'>" . $row['game_id'] . "</td>";
                                        echo "<td data-label='Title'>" . $row['title'] . "</td>";
                                        echo "<td data-label='Platform'>" . $row['platform'] . "</td>";
                                        echo "<td data-label='Price'>$" . number_format($row['price'], 2) . "</td>";
                                        echo "<td data-label='Stock'>" . $row['stock'] . "</td>";
                                        echo "<td class='action-column'>";
                                        echo "<a href='" . $_SERVER['PHP_SELF'] . "?edit=" . $row['game_id'] . buildQueryString() . "' class='btn btn-warning btn-sm action-btn'>Edit</a>";
                                        echo "<a href='" . $_SERVER['PHP_SELF'] . "?delete=" . $row['game_id'] . buildQueryString() . "' class='btn btn-danger btn-sm action-btn' onclick='return confirm(\"Are you sure?\")'>Delete</a>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6'>No games found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Users Section -->
            <div class="card-container">
                <div class="card-header">
                    <h3>Recent Users</h3>
                    <?php if (!$show_users_all) { ?>
                        <a href="<?php echo $_SERVER['PHP_SELF'] . buildQueryString(['show_users_all' => true]); ?>" class="view-all-btn">
                            View All <i class="fas fa-arrow-right"></i>
                        </a>
                    <?php } else { ?>
                        <a href="<?php echo $_SERVER['PHP_SELF'] . buildQueryString(['show_users_all' => false]); ?>" class="view-all-btn">
                            Show Less <i class="fas fa-arrow-up"></i>
                        </a>
                    <?php } ?>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Registered</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($users_result->num_rows > 0) {
                                    while ($row = $users_result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td data-label='ID'>" . $row['user_id'] . "</td>";
                                        echo "<td data-label='Username'>" . $row['username'] . "</td>";
                                        echo "<td data-label='Email'>" . $row['email'] . "</td>";
                                        echo "<td data-label='Registered'>" . date('M d, Y', strtotime($row['created_at'])) . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4'>No users found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- To-Do List Section -->
            <div class="card-container">
                <div class="card-header">
                    <h3>Game Store To-Do List</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . buildQueryString(); ?>" class="todo-add-form">
                        <input type="text" name="todo_task" class="form-control" placeholder="Add new task..." required>
                        <div class="todo-form-controls">
                            <select name="todo_priority" class="form-control" required>
                                <option value="high">High Priority</option>
                                <option value="medium" selected>Medium Priority</option>
                                <option value="low">Low Priority</option>
                            </select>
                            <input type="date" name="todo_due_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                            <button type="submit" class="btn btn-primary">Add</button>
                        </div>
                    </form>

                    <ul class="todo-list">
                        <?php
                        if ($todos_result->num_rows > 0) {
                            while ($todo = $todos_result->fetch_assoc()) {
                                $completed = $todo['status'] === 'completed';
                                $priorityClass = 'priority-' . $todo['priority'];
                                $itemClass = $completed ? 'todo-item todo-completed' : 'todo-item';
                                ?>
                                <li class="<?php echo $itemClass; ?>">
                                    <?php if (!$completed) { ?>
                                        <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . buildQueryString(); ?>" style="display:flex; align-items:center;">
                                            <input type="hidden" name="todo_complete" value="<?php echo $todo['id']; ?>">
                                            <button type="submit" class="todo-checkbox" style="background:none; border:none; cursor:pointer;">
                                                <i class="far fa-square"></i>
                                            </button>
                                        </form>
                                    <?php } else { ?>
                                        <span class="todo-checkbox">
                                            <i class="fas fa-check-square text-success"></i>
                                        </span>
                                    <?php } ?>
                                    
                                    <div class="todo-content">
                                        <div class="todo-title"><?php echo htmlspecialchars($todo['task']); ?></div>
                                        <div class="todo-meta">
                                            <span class="todo-due">
                                                <i class="far fa-calendar-alt"></i> 
                                                <?php echo date('M d, Y', strtotime($todo['due_date'])); ?>
                                            </span>
                                            <span class="todo-priority <?php echo $priorityClass; ?>">
                                                <?php echo $todo['priority']; ?>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="todo-actions">
                                        <a href="<?php echo $_SERVER['PHP_SELF'] . '?delete_todo=' . $todo['id'] . buildQueryString(); ?>" 
                                           class="todo-action-btn" onclick="return confirm('Delete this task?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </li>
                                <?php
                            }
                        } else {
                            echo '<li class="todo-item"><div class="todo-content"><div class="todo-title">No tasks found</div></div></li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Active Users Chart
        var ctx = document.getElementById('activeUsersChart').getContext('2d');
        var activeUsersData = <?php echo json_encode($active_users_data); ?>;
        
        var labels = activeUsersData.map(function(item) {
            return item.date;
        });
        
        var data = activeUsersData.map(function(item) {
            return item.count;
        });
        
        var activeUsersChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Active Users',
                    data: data,
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 3,
                    pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointBorderColor: '#fff',
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: "rgba(0, 0, 0, 0.05)",
                        },
                        ticks: {
                            callback: function(value) {
                                return value;
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: "rgb(255, 255, 255)",
                        bodyColor: "#858796",
                        titleMarginBottom: 10,
                        titleColor: '#6e707e',
                        titleFontSize: 14,
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        padding: 15,
                        displayColors: false,
                        intersect: false,
                        mode: 'index',
                        caretPadding: 10,
                        callbacks: {
                            label: function(context) {
                                var label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += context.parsed.y + ' users';
                                return label;
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>