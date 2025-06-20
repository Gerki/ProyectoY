<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_email = $_SESSION['user_email'];

// Database connection
require_once 'config.php';
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the is_admin column exists
$result = $conn->query("SHOW COLUMNS FROM users LIKE 'is_admin'");
$is_admin_exists = $result->num_rows > 0;
$is_admin = false;
if ($is_admin_exists) {
    $stmt = $conn->prepare("SELECT is_admin FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header">
            <span class="sidebar-title">ProyectoY</span>
        </div>
        <ul class="sidebar-nav">
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
            <li><a href="checkout.php"><i class="fas fa-credit-card"></i> Upgrade</a></li>
            <?php if ($is_admin): ?>
            <li><a href="admin.php"><i class="fas fa-users-cog"></i> Admin</a></li>
            <?php endif; ?>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>
    <div class="main-content">
        <header class="topbar">
            <div class="topbar-left">
                <span class="app-title">Dashboard</span>
            </div>
            <div class="topbar-right">
                <span class="user-info"><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($user_name); ?></span>
            </div>
        </header>
        <section class="dashboard-section">
            <h2>Welcome, <?php echo htmlspecialchars($user_name); ?>!</h2>
            <div class="user-details">
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user_email); ?></p>
                <?php if ($is_admin): ?>
                <p><span class="admin-badge">Admin</span></p>
                <?php endif; ?>
            </div>
        </section>
    </div>
</body>
</html> 