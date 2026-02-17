<?php
require_once __DIR__ . '/../config/bootstrap.php';
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Motorcycle Cart</title>
    <link rel="stylesheet" href="css/shoppingCart.css">
</head>
<body>
    <?php require_once __DIR__ . '/../navigation/navigation.php'; ?>

    <div id="cart-container">
        <h1>MOTORCYCLE CART</h1>
        <table>
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Vehicle</th>
                    <th>Price per day</th>
                    <th>Number of days</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include 'config.php';
                include 'db_config.php';
                    $sql = "SELECT * FROM motorcycle_cart WHERE user_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $_SESSION['id']);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $products = $result->fetch_all(MYSQLI_ASSOC);
                    $stmt->close();

                    $total = 0;

                    foreach ($products as $product) {
                        $sql = "SELECT * FROM motorcycles WHERE id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $product['motorcycle_id']);
                        $stmt->execute();
                        $motorcycleResult = $stmt->get_result();
                        $motorcycleFound = $motorcycleResult->fetch_all(MYSQLI_ASSOC);
                        $stmt->close();

                        $sql = "SELECT * FROM motorcycle_images WHERE motorcycle_id = ? LIMIT 1";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $product['motorcycle_id']);
                        $stmt->execute();
                        $imageResult = $stmt->get_result();
                        $image = $imageResult->fetch_all(MYSQLI_ASSOC);
                        $stmt->close();

                        $start_date = $product['start_date'];
                        $end_date = $product['end_date'];
                        $interval = date_diff(date_create($start_date), date_create($end_date));
                        ?>
                        <tr>
                            <td><img src="/Makina/images/motorcycles/<?php echo htmlspecialchars($image[0]['image_path']); ?>" alt="Motorcycle Image"></td>
                            <td><?php echo htmlspecialchars($motorcycleFound[0]['name']); ?></td>
                            <td><?php echo htmlspecialchars($motorcycleFound[0]['price_per_day']); ?></td>
                            <td><?php echo htmlspecialchars($interval->days); ?></td>
                            <td><?php echo htmlspecialchars($product['total_price']); ?></td>
                        </tr>
                        <?php
                            $total += $product['total_price'];
                    }
                ?>
            </tbody>
        </table>
        <h2>Total: <?php echo $total; ?></h2>
    </div>
    <div id="checkout-button-container">
        <form action="/Makina/pagesa/stripe_motorcycle_checkout.php" method="post">
            <button type="submit">
                Proceed to Checkout
            </button>
            <script src="/Makina/registerLogin/sessionTimeout.js"></script>
        </form>
    </div>
</body>
</html>
