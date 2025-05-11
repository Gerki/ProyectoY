<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user information
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_email = $_SESSION['user_email'];

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "proyectoy";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the is_admin column exists
$result = $conn->query("SHOW COLUMNS FROM users LIKE 'is_admin'");
$is_admin_exists = $result->num_rows > 0;

// Default to not admin
$is_admin = false;

// Only check for admin status if the column exists
if ($is_admin_exists) {
    // Check if user is admin using a query that's safe if the column doesn't exist
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    // Only check the is_admin field if it exists in the result
    if (isset($user['is_admin'])) {
        $is_admin = $user['is_admin'] == 1;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | ProyectoY</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .admin-badge {
            background-color: #28a745;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
        
        .admin-btn {
            background-color: #17a2b8;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($user_name); ?>!</h2>
        
        <div class="user-info">
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user_email); ?></p>
            <?php if ($is_admin): ?>
                <p><span class="admin-badge">Admin</span></p>
            <?php endif; ?>
        </div>
        
        <div class="actions">
            <a href="profile.php" class="btn">Edit Profile</a>
            <?php if ($is_admin): ?>
                <a href="admin.php" class="btn admin-btn">Admin Panel</a>
            <?php endif; ?>
            <a href="logout.php" class="btn">Logout</a>
        </div>
    </div>
</body>
</html> 