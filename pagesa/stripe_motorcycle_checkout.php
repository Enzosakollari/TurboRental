<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db_config.php';

if (!isset($_SESSION['id'])) {
    header('Location: /Makina/registerLogin/login.html');
    exit;
}

$userId = $_SESSION['id'];

$stmt = $conn->prepare("SELECT * FROM motorcycle_cart WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if (empty($products)) {
    header('Location: /Makina/pagesa/motorcycleCart.php');
    exit;
}

$totalAmount = 0.0;
foreach ($products as $product) {
    $totalAmount += (float)$product['total_price'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Motorcycle Checkout</title>
    <link rel="stylesheet" href="css/checkout.css">
</head>
<body>
    <?php require_once __DIR__ . '/../navigation/navigation.php'; ?>

    <div class="checkout-container">
        <h1>Motorcycle Checkout</h1>
        <p class="total">Total: <?php echo htmlspecialchars(number_format($totalAmount, 2)); ?> <?php echo htmlspecialchars(strtoupper(STRIPE_CURRENCY)); ?></p>

        <form id="payment-form">
            <div class="field">
                <label for="full-name">Full name</label>
                <input id="full-name" type="text" name="full_name" autocomplete="name" required>
            </div>
            <div class="field">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" autocomplete="email" required>
            </div>
            <div class="field">
                <label for="phone">Phone</label>
                <input id="phone" type="tel" name="phone" autocomplete="tel" required>
            </div>
            <div class="field">
                <label>Card details</label>
                <div id="card-element"></div>
            </div>
            <div id="card-errors" class="error" role="alert"></div>
            <button id="submit-button" type="submit">Pay</button>
        </form>
    </div>

    <script src="https://js.stripe.com/v3/"></script>
    <script>
        const stripe = Stripe(<?php echo json_encode(STRIPE_PUBLISHABLE_KEY); ?>);
        const elements = stripe.elements();
        const card = elements.create('card', {
            style: {
                base: {
                    color: '#1a1a1a',
                    fontFamily: '"Segoe UI", Arial, sans-serif',
                    fontSize: '16px',
                    '::placeholder': { color: '#8c8c8c' }
                }
            }
        });
        card.mount('#card-element');

        const form = document.getElementById('payment-form');
        const submitButton = document.getElementById('submit-button');
        const errorElement = document.getElementById('card-errors');

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            errorElement.textContent = '';
            submitButton.disabled = true;
            submitButton.textContent = 'Processing...';

            let intentResponse;
            try {
                intentResponse = await fetch('/Makina/pagesa/create_payment_intent.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'type=motorcycle'
                });
            } catch (error) {
                errorElement.textContent = 'Unable to connect to payment service.';
                submitButton.disabled = false;
                submitButton.textContent = 'Pay';
                return;
            }

            let intentData = {};
            try {
                intentData = await intentResponse.json();
            } catch (error) {
                intentData = {};
            }

            if (!intentResponse.ok || !intentData.client_secret) {
                errorElement.textContent = intentData.error || 'Unable to start payment.';
                submitButton.disabled = false;
                submitButton.textContent = 'Pay';
                return;
            }

            const billingDetails = {
                name: document.getElementById('full-name').value.trim(),
                email: document.getElementById('email').value.trim(),
                phone: document.getElementById('phone').value.trim()
            };

            const result = await stripe.confirmCardPayment(intentData.client_secret, {
                payment_method: {
                    card: card,
                    billing_details: billingDetails
                }
            });

            if (result.error) {
                errorElement.textContent = result.error.message || 'Payment failed.';
                submitButton.disabled = false;
                submitButton.textContent = 'Pay';
                return;
            }

            if (result.paymentIntent && result.paymentIntent.status === 'succeeded') {
                window.location.href = '/Makina/pagesa/motorcycleSuccess.php?payment_intent=' + encodeURIComponent(result.paymentIntent.id);
                return;
            }

            errorElement.textContent = 'Payment processing did not complete.';
            submitButton.disabled = false;
            submitButton.textContent = 'Pay';
        });
    </script>
    <script src="/Makina/registerLogin/sessionTimeout.js"></script>
</body>
</html>
