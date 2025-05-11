<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "proyectoy";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$error = "";
$success = "";

// Get current user data
$stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Update profile
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    // Validate input
    if (empty($name)) {
        $error = "Name is required";
    } else {
        // Get current password hash
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user_data = $result->fetch_assoc();
        
        // Check if updating password
        if (!empty($current_password)) {
            // Verify current password
            if (!password_verify($current_password, $user_data['password'])) {
                $error = "Current password is incorrect";
            } elseif (empty($new_password) || strlen($new_password) < 6) {
                $error = "New password must be at least 6 characters long";
            } elseif ($new_password !== $confirm_password) {
                $error = "New passwords do not match";
            } else {
                // Hash the new password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                
                // Update name and password
                $stmt = $conn->prepare("UPDATE users SET name = ?, password = ? WHERE id = ?");
                $stmt->bind_param("ssi", $name, $hashed_password, $user_id);
                
                if ($stmt->execute()) {
                    $_SESSION['user_name'] = $name;
                    $success = "Profile updated successfully";
                    $user['name'] = $name;
                } else {
                    $error = "Error updating profile: " . $stmt->error;
                }
            }
        } else {
            // Update only name
            $stmt = $conn->prepare("UPDATE users SET name = ? WHERE id = ?");
            $stmt->bind_param("si", $name, $user_id);
            
            if ($stmt->execute()) {
                $_SESSION['user_name'] = $name;
                $success = "Profile updated successfully";
                $user['name'] = $name;
            } else {
                $error = "Error updating profile: " . $stmt->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | ProyectoY</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>My Profile</h2>
        
        <?php if(!empty($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if(!empty($success)): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="profileForm">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email (cannot be changed)</label>
                <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
            </div>
            
            <h3>Change Password</h3>
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password">
            </div>
            
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password">
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password">
            </div>
            
            <button type="submit">Update Profile</button>
        </form>
        
        <div class="actions">
            <a href="dashboard.php" class="btn">Back to Dashboard</a>
        </div>
    </div>
    
    <script src="validation.js"></script>
</body>
</html> 