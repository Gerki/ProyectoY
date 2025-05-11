<?php
require_once 'config.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add new columns to users table
$sql = "ALTER TABLE users 
        ADD COLUMN IF NOT EXISTS google_id VARCHAR(255),
        ADD COLUMN IF NOT EXISTS is_premium TINYINT(1) DEFAULT 0,
        ADD COLUMN IF NOT EXISTS stripe_customer_id VARCHAR(255)";

if ($conn->query($sql) === TRUE) {
    echo "Users table updated successfully<br>";
} else {
    echo "Error updating users table: " . $conn->error . "<br>";
}

// Create payments table
$sql = "CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    stripe_session_id VARCHAR(255),
    amount DECIMAL(10,2),
    status VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Payments table created successfully<br>";
} else {
    echo "Error creating payments table: " . $conn->error . "<br>";
}

$conn->close();
?> 