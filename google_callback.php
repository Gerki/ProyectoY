<?php
require_once 'config.php';
session_start();

// Verify state parameter
if (!isset($_GET['state']) || $_GET['state'] !== $_SESSION['oauth_state']) {
    die('Invalid state parameter');
}

if (isset($_GET['code'])) {
    // Exchange code for access token
    $token_url = 'https://oauth2.googleapis.com/token';
    $token_data = [
        'code' => $_GET['code'],
        'client_id' => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'redirect_uri' => GOOGLE_REDIRECT_URI,
        'grant_type' => 'authorization_code'
    ];

    $ch = curl_init($token_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($token_data));
    curl_setopt($ch, CURLOPT_POST, true);
    $token_response = curl_exec($ch);
    curl_close($ch);

    $token_info = json_decode($token_response, true);

    if (isset($token_info['access_token'])) {
        // Get user info
        $user_info_url = 'https://www.googleapis.com/oauth2/v2/userinfo';
        $ch = curl_init($user_info_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token_info['access_token']
        ]);
        $user_info_response = curl_exec($ch);
        curl_close($ch);

        $user_info = json_decode($user_info_response, true);

        if (isset($user_info['email'])) {
            // Connect to database
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Check if user exists
            $stmt = $conn->prepare("SELECT id, name, email FROM users WHERE email = ?");
            $stmt->bind_param("s", $user_info['email']);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if (!$user) {
                // Create new user
                $stmt = $conn->prepare("INSERT INTO users (name, email, google_id) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $user_info['name'], $user_info['email'], $user_info['id']);
                $stmt->execute();
                $user_id = $conn->insert_id;
            } else {
                $user_id = $user['id'];
            }

            // Create session
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $user_info['name'];
            $_SESSION['user_email'] = $user_info['email'];

            // Redirect to dashboard
            header("Location: dashboard.php");
            exit();
        }
    }
}

// If we get here, something went wrong
header("Location: login.php?error=google_auth_failed");
exit();
?> 