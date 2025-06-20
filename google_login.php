<?php
require_once 'config.php';

// Generate random state parameter for security
$state = bin2hex(random_bytes(16));
$_SESSION['oauth_state'] = $state;

// Build Google OAuth URL
$params = [
    'client_id' => GOOGLE_CLIENT_ID,
    'redirect_uri' => GOOGLE_REDIRECT_URI,
    'response_type' => 'code',
    'scope' => 'email profile',
    'state' => $state,
    'access_type' => 'online',
    'prompt' => 'select_account'
];

$auth_url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login with Google</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Login with Google</h2>
        <a href="<?php echo htmlspecialchars($auth_url); ?>" class="btn">
            <img src="https://www.google.com/favicon.ico" alt="Google" style="width: 20px; margin-right: 10px;">
            Continue with Google
        </a>
    </div>
</body>
</html> 