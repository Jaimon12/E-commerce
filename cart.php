<?php
session_start();
include 'config.php'; // Database configuration
include 'header.php';
 // Start the session
$items = isset($_SESSION['items']) ? $_SESSION['items'] : array();
$cartIsEmpty = true;
$totalPayable = 0;

foreach ($items as $item) {
    if ($item['count'] > 0) {
        $cartIsEmpty = false;
        $totalPayable += $item['count'] * $item['price'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f8f8;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .empty-cart {
            text-align: center;
            padding: 50px 0;
            color: #555;
        }
        .cart-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .cart-item {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            border-radius: 10px;
            background-color: #fff;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .cart-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 10px;
        }
        .cart-item-details {
            flex: 1;
        }
        .cart-item-details h3 {
            margin: 0 0 10px;
        }
        .cart-item-details p {
            margin: 0;
        }
        .total {
            text-align: right;
            font-size: 18px;
            margin-top: 20px;
        }
        .place-order {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .place-order button {
            padding: 10px 20px;
            font-size: 18px;
            cursor: pointer;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($cartIsEmpty): ?>
            <div class="empty-cart">
                <h2>Add Items to the Cart</h2>
            </div>
        <?php else: ?>
            <div class="cart-container">
                <?php foreach ($items as $item): ?>
                    <?php if ($item['count'] > 0): ?>
                        <div class="cart-item">
                            <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                            <div class="cart-item-details">
                                <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                <p>Price: ₹<?php echo number_format($item['price'], 2); ?></p>
                                <p>Quantity: <?php echo $item['count']; ?></p>
                                <p>Total: ₹<?php echo number_format($item['count'] * $item['price'], 2); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <div class="total">
                <h3>Total Payable: ₹<?php echo number_format($totalPayable, 2); ?></h3>
            </div>
            <div class="place-order">
                <button onclick="placeOrder()">Place Order</button>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function placeOrder() {
                        window.location.href = 'orderplace.php';

        }
    </script>
</body>
</html>
<?php include "footer.php"; // Footer ?>