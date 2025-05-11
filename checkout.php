<?php
require_once 'config.php';
require_once 'vendor/autoload.php'; // You'll need to install Stripe PHP library
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Initialize Stripe
\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

try {
    // Create a Checkout Session
    $checkout_session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => 'usd',
                'product_data' => [
                    'name' => 'Premium Plan',
                ],
                'unit_amount' => 2000, // $20.00
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => 'http://localhost/ProyectoY/payment_success.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => 'http://localhost/ProyectoY/payment_cancel.php',
        'customer_email' => $_SESSION['user_email'],
        'metadata' => [
            'user_id' => $_SESSION['user_id']
        ]
    ]);
} catch(Exception $e) {
    error_log($e->getMessage());
    header("Location: error.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
    <div class="container">
        <h2>Complete Your Purchase</h2>
        <div class="product-info">
            <h3>Premium Plan</h3>
            <p>Price: $20.00</p>
        </div>
        <button id="checkout-button" class="btn">Proceed to Payment</button>
    </div>

    <script>
        var stripe = Stripe('<?php echo STRIPE_PUBLISHABLE_KEY; ?>');
        var checkoutButton = document.getElementById('checkout-button');

        checkoutButton.addEventListener('click', function() {
            stripe.redirectToCheckout({
                sessionId: '<?php echo $checkout_session->id; ?>'
            }).then(function (result) {
                if (result.error) {
                    alert(result.error.message);
                }
            });
        });
    </script>
</body>
</html> 