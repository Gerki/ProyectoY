<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config.php';
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_email = $_SESSION['user_email'];

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
if (!$is_admin) {
    header("Location: dashboard.php");
    exit();
}

$message = "";
$error = "";

// Handle user deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    if ($delete_id != $user_id) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND is_admin = 0");
        $stmt->bind_param("i", $delete_id);
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $message = "User deleted successfully";
        } else {
            $error = "Error deleting user. Admins cannot be deleted.";
        }
    } else {
        $error = "You cannot delete your own account";
    }
}

// Handle admin role toggle
if (isset($_GET['toggle_admin']) && is_numeric($_GET['toggle_admin'])) {
    $toggle_id = $_GET['toggle_admin'];
    if ($toggle_id != $user_id) {
        $stmt = $conn->prepare("SELECT is_admin FROM users WHERE id = ?");
        $stmt->bind_param("i", $toggle_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $toggle_user = $result->fetch_assoc();
        if ($toggle_user) {
            $new_status = $toggle_user['is_admin'] ? 0 : 1;
            $stmt = $conn->prepare("UPDATE users SET is_admin = ? WHERE id = ?");
            $stmt->bind_param("ii", $new_status, $toggle_id);
            if ($stmt->execute()) {
                $status_text = $new_status ? "Admin privileges granted" : "Admin privileges revoked";
                $message = "User status updated: " . $status_text;
            } else {
                $error = "Error updating user status";
            }
        }
    } else {
        $error = "You cannot change your own admin status";
    }
}

// Get all users
$stmt = $conn->prepare("SELECT id, name, email, is_admin, created_at FROM users ORDER BY id");
$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | ProyectoY</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .admin-container {
            max-width: 900px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        
        tr:hover {
            background-color: #f5f5f5;
        }
        
        .admin-badge {
            background-color: #28a745;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
        
        .action-btn {
            padding: 6px 12px;
            margin-right: 5px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            display: inline-block;
        }
        
        .delete-btn {
            background-color: #dc3545;
            color: white;
        }
        
        .admin-btn {
            background-color: #17a2b8;
            color: white;
        }
        
        .admin-actions {
            margin-bottom: 20px;
        }
    </style>
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
            <li><a href="admin.php"><i class="fas fa-users-cog"></i> Admin</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>
    <div class="main-content">
        <header class="topbar">
            <div class="topbar-left">
                <span class="app-title">Admin Panel</span>
            </div>
            <div class="topbar-right">
                <span class="user-info"><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($user_name); ?></span>
            </div>
        </header>
        <section class="dashboard-section">
            <div class="container admin-container">
                <h2>Admin Panel</h2>
                
                <?php if(!empty($message)): ?>
                    <div class="success"><?php echo $message; ?></div>
                <?php endif; ?>
                
                <?php if(!empty($error)): ?>
                    <div class="error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <div class="admin-actions">
                    <a href="dashboard.php" class="btn">Back to Dashboard</a>
                </div>
                
                <h3>User Management</h3>
                
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <?php if (isset($user['is_admin']) && $user['is_admin']): ?>
                                    <span class="admin-badge">Admin</span>
                                <?php else: ?>
                                    User
                                <?php endif; ?>
                            </td>
                            <td><?php echo $user['created_at']; ?></td>
                            <td>
                                <?php if ($user['id'] != $user_id): ?>
                                    <a href="?delete=<?php echo $user['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                                    <a href="?toggle_admin=<?php echo $user['id']; ?>" class="action-btn admin-btn">
                                        <?php echo (isset($user['is_admin']) && $user['is_admin']) ? 'Remove Admin' : 'Make Admin'; ?>
                                    </a>
                                <?php else: ?>
                                    (You)
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</body>
</html> 