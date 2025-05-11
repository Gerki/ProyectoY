<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "proyectoy";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}

// Select the database
$conn->select_db($dbname);

// Create users table
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_admin TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Users table created successfully<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Check if is_admin column exists, add it if not
$result = $conn->query("SHOW COLUMNS FROM users LIKE 'is_admin'");
if ($result->num_rows == 0) {
  $conn->query("ALTER TABLE users ADD COLUMN is_admin TINYINT(1) DEFAULT 0");
    echo "Added is_admin column to users table<br>";
}

// Create admin user if none exists
$stmt = $conn->prepare("SELECT id FROM users WHERE is_admin = 1 LIMIT 1");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // Create admin user
    $admin_name = "Admin";
    $admin_email = "admin@proyectoy.com";
    $admin_password = password_hash("admin123", PASSWORD_DEFAULT);
    $is_admin = 1;
    
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, is_admin) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $admin_name, $admin_email, $admin_password, $is_admin);
    
    if ($stmt->execute()) {
        echo "Admin user created successfully<br>";
        echo "Admin credentials: Email: admin@proyectoy.com, Password: admin123<br>";
        echo "<strong>Please change the admin password after first login!</strong>";
    } else {
        echo "Error creating admin user: " . $stmt->error;
    }
}

$conn->close();
?> 