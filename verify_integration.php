<?php
require_once 'config.php';
session_start();

echo "<h2>Integration Verification</h2>";

// Check database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
echo "✓ Database connection successful<br>";

// Check required tables and columns
$tables = ['users', 'payments'];
$required_columns = [
    'users' => ['google_id', 'is_premium', 'stripe_customer_id'],
    'payments' => ['user_id', 'stripe_session_id', 'amount', 'status']
];

foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows > 0) {
        echo "✓ Table '$table' exists<br>";
        
        // Check columns
        foreach ($required_columns[$table] as $column) {
            $result = $conn->query("SHOW COLUMNS FROM $table LIKE '$column'");
            if ($result->num_rows > 0) {
                echo "✓ Column '$column' exists in '$table'<br>";
            } else {
                echo "✗ Column '$column' missing in '$table'<br>";
            }
        }
    } else {
        echo "✗ Table '$table' missing<br>";
    }
}

// Check configuration
$required_configs = [
    'GOOGLE_CLIENT_ID',
    'GOOGLE_CLIENT_SECRET',
    'STRIPE_SECRET_KEY',
    'STRIPE_PUBLISHABLE_KEY'
];

foreach ($required_configs as $config) {
    if (defined($config) && constant($config) !== 'YOUR_' . $config) {
        echo "✓ $config is configured<br>";
    } else {
        echo "✗ $config is not configured<br>";
    }
}

// Check Stripe PHP library
if (file_exists('vendor/autoload.php')) {
    echo "✓ Stripe PHP library is installed<br>";
} else {
    echo "✗ Stripe PHP library is not installed<br>";
}

$conn->close();
?> 