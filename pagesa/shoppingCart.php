<?php
require_once __DIR__ . '/../config/bootstrap.php';
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="css/shoppingCart.css">
</head>
<body>
    <?php require_once __DIR__ . '/../navigation/navigation.php'; ?>

    <div id="cart-container">
        <h1>SHOPPING CART</h1>
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
                    $sql = "SELECT * FROM cart WHERE user_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $_SESSION['id']);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $products = $result->fetch_all(MYSQLI_ASSOC);
                    $stmt->close();

                    $total = 0;

                    foreach ($products as $product) {
                        $sql = "SELECT * FROM cars WHERE id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $product['car_id']);
                        $stmt->execute();
                        $carResult = $stmt->get_result();
                        $carFound = $carResult->fetch_all(MYSQLI_ASSOC);
                        $stmt->close();

                        $sql = "SELECT * FROM car_images WHERE car_id = ? LIMIT 1";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $product['car_id']);
                        $stmt->execute();
                        $imageResult = $stmt->get_result();
                        $image = $imageResult->fetch_all(MYSQLI_ASSOC);
                        $stmt->close();

                        $start_date = $product['start_date'];
                        $end_date = $product['end_date'];
                        $interval = date_diff(date_create($start_date), date_create($end_date));
                        ?>
                        <tr>
                            <td><img src="/Makina/images/cars/<?php echo htmlspecialchars($image[0]['image_path']); ?>" alt="Car Image"></td>
                            <td><?php echo htmlspecialchars($carFound[0]['name']); ?></td>
                            <td><?php echo htmlspecialchars($carFound[0]['price_per_day']); ?></td>
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
        <form action="/Makina/pagesa/stripe_checkout.php" method="post">
            <button type="submit">
                Proceed to Checkout
            </button>
            <script src="/Makina/registerLogin/sessionTimeout.js"></script>
        </form>
    </div>
</body>
</html>
