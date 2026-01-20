<?php require_once __DIR__ . '/../config/bootstrap.php'; ?>
<html>
<head>
    <title>Payment Completed</title>
    <link rel="stylesheet" href="css/success.css">
</head>
<body>
    <?php require_once __DIR__ . '/../navigation/navigation.php'; ?>
    <div id="successcontainer">
        <h1>Payment completed!</h1>
        <p>Thank you for your reservation! The transaction was successful!</p>
        <script src="/Makina/registerLogin/sessionTimeout.js"></script>
    </div>
</body>
</html>

<?php
include 'config.php';
include 'db_config.php';

if (isset($_GET['payment_intent'])) {
    $paymentIntentId = $_GET['payment_intent'];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/payment_intents/' . $paymentIntentId);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . STRIPE_SECRET_KEY
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    $intent = json_decode($response, true);

    if (!empty($intent['status']) && $intent['status'] === 'succeeded') {
        $metadata = $intent['metadata'] ?? [];
        $bookingIds = isset($metadata['booking_ids']) ? array_filter(explode(',', $metadata['booking_ids'])) : [];
        $userId = $_SESSION['id'] ?? (int)($metadata['user_id'] ?? 0);
        $providerTransactionId = $intent['id'] ?? $paymentIntentId;

        foreach ($bookingIds as $bookingId) {
            $bookingId = (int)$bookingId;
            $stmt = $conn->prepare("UPDATE bookings SET status = 'confirmed' WHERE id = ? AND status = 'pending'");
            $stmt->bind_param('i', $bookingId);
            $stmt->execute();
            $stmt->close();

            $stmt = $conn->prepare("SELECT total_price FROM bookings WHERE id = ?");
            $stmt->bind_param('i', $bookingId);
            $stmt->execute();
            $stmt->bind_result($totalPrice);
            $stmt->fetch();
            $stmt->close();

            $status = strtoupper($intent['status']);
            $stmt = $conn->prepare("INSERT INTO transactions (booking_id, provider, provider_transaction_id, checkout_session_id, amount, status, created_at) VALUES (?, 'stripe', ?, ?, ?, ?, NOW())");
            $stmt->bind_param('issds', $bookingId, $providerTransactionId, $providerTransactionId, $totalPrice, $status);
            $stmt->execute();
            $stmt->close();
        }

        if ($userId) {
            $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            $stmt->close();
        }
    } else {
        echo "Payment not completed.";
    }
} elseif (isset($_GET['session_id'])) {
    $sessionId = $_GET['session_id'];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/checkout/sessions/' . $sessionId . '?expand[]=payment_intent');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . STRIPE_SECRET_KEY
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    $session = json_decode($response, true);

    if (!empty($session['payment_status']) && $session['payment_status'] === 'paid') {
        $metadata = $session['metadata'] ?? [];
        $bookingIds = isset($metadata['booking_ids']) ? array_filter(explode(',', $metadata['booking_ids'])) : [];
        $userId = $_SESSION['id'] ?? (int)($metadata['user_id'] ?? 0);
        $paymentIntent = $session['payment_intent'] ?? null;
        if (is_array($paymentIntent)) {
            $providerTransactionId = $paymentIntent['id'] ?? $sessionId;
        } else {
            $providerTransactionId = $paymentIntent ?: $sessionId;
        }

        foreach ($bookingIds as $bookingId) {
            $bookingId = (int)$bookingId;
            $stmt = $conn->prepare("UPDATE bookings SET status = 'confirmed' WHERE id = ? AND status = 'pending'");
            $stmt->bind_param('i', $bookingId);
            $stmt->execute();
            $stmt->close();

            $stmt = $conn->prepare("SELECT total_price FROM bookings WHERE id = ?");
            $stmt->bind_param('i', $bookingId);
            $stmt->execute();
            $stmt->bind_result($totalPrice);
            $stmt->fetch();
            $stmt->close();

            $status = strtoupper($session['payment_status']);
            $stmt = $conn->prepare("INSERT INTO transactions (booking_id, provider, provider_transaction_id, checkout_session_id, amount, status, created_at) VALUES (?, 'stripe', ?, ?, ?, ?, NOW())");
            $stmt->bind_param('issds', $bookingId, $providerTransactionId, $sessionId, $totalPrice, $status);
            $stmt->execute();
            $stmt->close();
        }

        if ($userId) {
            $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            $stmt->close();
        }
    } else {
        echo "Payment not completed.";
    }
} else {
    echo "Invalid request. Payment session is missing.";
}
?>
