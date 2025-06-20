<?php
session_start();
require_once 'config.php';

$is_logged_in = isset($_SESSION['user_id']);
$user_name = $is_logged_in ? $_SESSION['user_name'] : '';
$user_id = $is_logged_in ? $_SESSION['user_id'] : null;
$is_admin = false;
if ($is_logged_in) {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $result = $conn->query("SHOW COLUMNS FROM users LIKE 'is_admin'");
    $is_admin_exists = $result->num_rows > 0;
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
}

$error = "";
$success = "";

// Register user
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    // Validate input
    if (empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        // Check if email already exists
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = "Email already registered";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $hashed_password);
            
            if ($stmt->execute()) {
                $success = "Registration successful! You can now <a href='login.php'>login</a>";
            } else {
                $error = "Error: " . $stmt->error;
            }
        }
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | ProyectoY</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php if ($is_logged_in): ?>
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
                <span class="app-title">Register</span>
            </div>
            <div class="topbar-right">
                <span class="user-info"><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($user_name); ?></span>
            </div>
        </header>
        <section class="dashboard-section">
<?php else: ?>
    <div class="container">
<?php endif; ?>
        <h2>Register</h2>
        
        <?php if(!empty($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if(!empty($success)): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="registerForm">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <button type="submit">Register</button>
        </form>
        
        <p>Already have an account? <a href="login.php">Login here</a></p>
<?php if ($is_logged_in): ?>
        </section>
    </div>
<?php else: ?>
    </div>
<?php endif; ?>
    <script src="validation.js"></script>
</body>
</html> 