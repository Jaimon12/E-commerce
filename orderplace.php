<?php
session_start(); // Start the session
include "header.php";

// Retrieve items from the session or initialize as an empty array
$items = isset($_SESSION['items']) ? $_SESSION['items'] : array();
$cartIsEmpty = true;
$totalPayable = 0;

// Calculate the total payable amount and check if the cart is empty
foreach ($items as $item) {
    if ($item['count'] > 0) {
        $cartIsEmpty = false;
        $totalPayable += $item['count'] * $item['price'];
    }
}

// Redirect to homepage if the cart is empty
if ($cartIsEmpty) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place Order</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f8f8;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .payment-method {
            margin-top: 20px;
        }
        .payment-method label {
            font-size: 18px;
        }
        .payment-method .cod-option {
            display: flex;
            align-items: center;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            width: 100%;
        }
        .payment-method .cod-option input {
            margin-right: 10px;
        }
        .cart-container {
            margin-top: 20px;
        }
        .cart-item {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .cart-item img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }
        .cart-item-details {
            flex: 1;
            margin-left: 10px;
        }
        .cart-item-details h3 {
            margin: 0 0 5px;
            font-size: 16px;
        }
        .cart-item-details p {
            margin: 0;
            font-size: 14px;
        }
        .total {
            text-align: right;
            font-size: 18px;
            margin-top: 20px;
        }
        .place-order {
            text-align: center;
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
        <h2>Place Your Order</h2>
        <form action="process_order.php" method="POST">
            <div class="form-group">
                <label for="mobile">Mobile Number</label>
                <input type="text" id="mobile" name="mobile" pattern="\d{10}" title="Please enter a valid 10-digit mobile number" required>
            </div>
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <textarea id="address" name="address" rows="4" required></textarea>
            </div>

            <div class="form-group payment-method">
                <label for="payment-method">Mode of Payment</label>
                <div class="cod-option">
                    <input type="radio" id="cod" name="payment_method" value="COD" checked>
                    <label for="cod">Cash on Delivery</label>
                </div>
            </div>

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
                <button type="submit">Place Order</button>
            </div>
        </form>
    </div>
</body>
</html>
