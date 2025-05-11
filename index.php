<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "proyectoy";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']);
$is_admin = false;
$user_name = "";

if ($is_logged_in) {
    // Get user information
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT name, is_admin FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if ($user) {
        $user_name = $user['name'];
        $is_admin = $user['is_admin'] == 1;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <title>Visualgv.com</title>
</head>
<body>
    <header>
        <h1>Visualgv.com</h1>
        <div class="search-bar">
            <input type="text" placeholder="Search...">
        </div>
        <div class="user-profile">
            <?php if ($is_logged_in): ?>
                <span class="welcome-message">Welcome, <?php echo htmlspecialchars($user_name); ?></span>
                <?php if ($is_admin): ?>
                    <span class="admin-badge">Admin</span>
                <?php endif; ?>
                <a href="logout.php" class="logout-btn">Logout</a>
            <?php else: ?>
                <a href="login.php" class="login-btn">Login</a>
                <a href="register.php" class="register-btn">Register</a>
            <?php endif; ?>
            <i class="fas fa-bell"></i>
        </div>
        <div class="menu-toggle" onclick="toggleMenu()">☰ Menu</div>
    </header>
    <nav>
        <ul>
            <?php if ($is_logged_in): ?>
                <li><a href="#"><i class="fas fa-building"></i> Organizaciones</a></li>
                <li><a href="#"><i class="fas fa-folder"></i> Files</a></li>
                <li><a href="#"><i class="fas fa-cogs"></i> Processes</a></li>
                <li><a href="#"><i class="fas fa-box"></i> Asset Inventory</a></li>
                <li><a href="#"><i class="fas fa-handshake"></i> Matches</a></li>
                <?php if ($is_admin): ?>
                    <li><a href="admin.php"><i class="fas fa-users-cog"></i> User Management</a></li>
                <?php endif; ?>
                <li><a href="#"><i class="fas fa-vr-cardboard"></i> Augmented Reality</a></li>
                <li><a href="#"><i class="fas fa-camera"></i> Evidences</a></li>
                <li><a href="#"><i class="fas fa-chart-bar"></i> Reports</a></li>
            <?php else: ?>
                <li class="login-required">Please login to access the features</li>
            <?php endif; ?>
        </ul>
    </nav>
    <main>
        <?php if ($is_logged_in): ?>
            <!-- Rest of your existing content -->
            <section class="module">
                <h3>Organizations</h3>
                <p>Manage your organizations here.</p>
                <div>
                    <!-- Your existing organization forms -->
                    <form role="organizacion" class="organizacion" action="process_organization.php" method="post">
                        <!-- ... existing organization form content ... -->
                    </form>
                    <!-- ... rest of your existing content ... -->
                </div>
            </section>
            <!-- ... other sections ... -->
        <?php else: ?>
            <div class="login-required-message">
                <h2>Welcome to Visualgv.com</h2>
                <p>Please <a href="login.php">login</a> or <a href="register.php">register</a> to access the features.</p>
            </div>
        <?php endif; ?>
    </main>
    <script>
        function toggleMenu() {
            const nav = document.querySelector('nav');
            nav.classList.toggle('active');
        }
    </script>
</body>
</html> 