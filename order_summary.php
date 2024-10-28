<?php
session_start();
include "config.php"; // Database configuration
include "header.php";  // Start the session


// Check if items are in the session or if none of the item counts are greater than zero
$items = isset($_SESSION['items']) ? $_SESSION['items'] : array();
$userDetails = isset($_SESSION['user_details']) ? $_SESSION['user_details'] : null;

$hasItems = false;
$totalPayable = 0;

// Calculate the total payable amount and check if the cart has any items
foreach ($items as $item) {
    if ($item['count'] > 0) {
        $hasItems = true;
        $totalPayable += $item['count'] * $item['price'];
    }
}

// If no items or no user details, redirect to the homepage
if (!$hasItems || !$userDetails) {
    header('Location: index.php');
    exit;
}

// Clear the items count in the session after the order is placed
foreach ($_SESSION['items'] as &$item) {
    $item['count'] = 0;
}

// Update the session with the cleared items
$_SESSION['items'] = $_SESSION['items'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Summary</title>
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
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .order-details {
            margin-bottom: 20px;
        }
        .order-details h3 {
            margin-bottom: 10px;
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
        .go-home {
            text-align: center;
            margin-top: 20px;
        }
        .go-home button {
            padding: 10px 20px;
            font-size: 18px;
            cursor: pointer;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Order Summary</h2>
        </div>
        <div class="order-details">
            <h3>Customer Details</h3>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($userDetails['name']); ?></p>
            <p><strong>Mobile Number:</strong> <?php echo htmlspecialchars($userDetails['mobile']); ?></p>
            <p><strong>Address:</strong> <?php echo nl2br(htmlspecialchars($userDetails['address'])); ?></p>
            <p><strong>Payment Method:</strong> Cash on Delivery</p>
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

        <div class="go-home">
            <button onclick="window.location.href='index.php'">Go to Home</button>
        </div>
    </div>
</body>
</html>
