<?php
require_once 'config.php';
require_once 'vendor/autoload.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Verify session ID
if (!isset($_GET['session_id'])) {
    header("Location: error.php");
    exit();
}

try {
    // Initialize Stripe
    \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

    // Retrieve the checkout session
    $session = \Stripe\Checkout\Session::retrieve($_GET['session_id']);

    // Verify the session belongs to this user
    if ($session->metadata->user_id != $_SESSION['user_id']) {
        throw new Exception('Invalid session');
    }

    // Connect to database
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Update user's subscription status
    $stmt = $conn->prepare("UPDATE users SET is_premium = 1, stripe_customer_id = ? WHERE id = ?");
    $stmt->bind_param("si", $session->customer, $_SESSION['user_id']);
    $stmt->execute();

    // Store payment information
    $stmt = $conn->prepare("INSERT INTO payments (user_id, stripe_session_id, amount, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isds", $_SESSION['user_id'], $session->id, $session->amount_total/100, $session->payment_status);
    $stmt->execute();

    // Redirect to success page
    header("Location: dashboard.php?payment=success");
    exit();

} catch(Exception $e) {
    error_log($e->getMessage());
    header("Location: error.php");
    exit();
}
?> 