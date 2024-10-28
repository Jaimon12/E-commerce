<?php
session_start();
include "config.php"; // Include your database configuration file

$orders = [];
$mobile = '';
$searchPerformed = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mobile = trim($_POST['mobile']);
    $searchPerformed = true;

    // Validate mobile number
    if (preg_match('/^\d{10}$/', $mobile)) {
        // Fetch orders from the database
        $orderQuery = "SELECT * FROM orderDetails WHERE mobileno = ?";
        $stmt = $conn->prepare($orderQuery);
        $stmt->bind_param("s", $mobile);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }

        $stmt->close();
    } else {
        $orders = [];
        $mobile = ''; // Clear mobile if invalid
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Search</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
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
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .form-group button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .orders-container {
            margin-top: 20px;
        }
        .order-item {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 15px;
            background-color: #f9f9f9;
        }
        .order-item h3 {
            margin: 0 0 10px;
            font-size: 18px;
        }
        .order-item p {
            margin: 5px 0;
            font-size: 14px;
        }
        .no-orders {
            text-align: center;
            font-size: 16px;
            color: #666;
        }
    </style>
    <?php include 'header.php'; ?>
</head>
<body>
    <div class="container">
        <h2>Search Orders</h2>
        <form action="orders.php" method="POST">
            <div class="form-group">
                <label for="mobile">Mobile Number</label>
                <input type="text" id="mobile" name="mobile" pattern="\d{10}" value="<?php echo htmlspecialchars($mobile); ?>" required>
            </div>
            <div class="form-group">
                <button type="submit">Search</button>
            </div>
        </form>

        <div class="orders-container">
            <?php if ($searchPerformed): ?>
                <?php if (count($orders) > 0): ?>
                    <?php foreach ($orders as $order): ?>
                        <div class="order-item">
                            <h3>Order #<?php echo htmlspecialchars($order['orderid']); ?></h3>
                            <p><strong>Mobile Number:</strong> <?php echo htmlspecialchars($order['mobileno']); ?></p>
                            <p><strong>Address:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
                            <p><strong>Date:</strong> <?php echo htmlspecialchars($order['date']); ?></p>
                            <p><strong>Time:</strong> <?php echo htmlspecialchars($order['time']); ?></p>
                            <p><strong>Order Value:</strong> ₹<?php echo number_format($order['price'], 2); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-orders">There are no orders for the mobile number <?php echo htmlspecialchars($mobile); ?>.</p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php include "footer.php"; // Footer ?>