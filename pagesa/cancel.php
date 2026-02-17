<?php require_once __DIR__ . '/../config/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Not Completed</title>
    <link rel="stylesheet" href="/Makina/theme.css">
    <style>
        body, h1, p {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Manrope", sans-serif;
        }

        body {
            background: radial-gradient(1200px 800px at 10% 0%, 
            color: var(--text);
            line-height: 1.6;
        }

        .container {
            margin: 0 auto;
            background-color: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 24px 30px;
            box-shadow: var(--shadow-md);
            text-align: center;
            margin-top: 100px;
            max-width: 500px;
            width: 100%;
        }

        h1 {
            color: var(--accent-2);
            font-size: 2rem;
            margin-bottom: 20px;
        }

        p {
            color: var(--muted);
            font-size: 1rem;
            margin-bottom: 20px;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            color: 
            text-decoration: none;
            border-radius: 999px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            font-size: 1rem;
            font-weight: 600;
            box-shadow: var(--shadow-sm);
        }

        .button:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
    </style>
</head>
<body class="cancelPage">

    <?php require_once __DIR__ . '/../navigation/navigation.php'; ?>
    <div class="container">
        <h1>Payment Not Completed</h1>
        <p>Please try again. If the problem persists, contact technical support.</p>
        <a href="shoppingCart.php" class="button">Back to Cart</a>
    </div>
    <script src="/Makina/registerLogin/sessionTimeout.js"></script>

</body>
</html>
