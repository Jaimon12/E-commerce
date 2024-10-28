<?php
session_start(); // Start the session
include "config.php"; // Include your database configuration file
include "header.php";

// Check if there are items in the session with a count greater than zero
$items = isset($_SESSION['items']) ? $_SESSION['items'] : array();
$hasItems = false;
$totalPayable = 0;

foreach ($items as $item) {
    if ($item['count'] > 0) {
        $hasItems = true;
        $totalPayable += $item['count'] * $item['price'];
    }
}

if (!$hasItems) {
    header('Location: index.php');
    exit;
}

// Process the order if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mobile = trim($_POST['mobile']);
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $date = date('Y-m-d');
    $time = date('H:i:s');

    // Save user details in the session
    $_SESSION['user_details'] = [
        'mobile' => $mobile,
        'name' => $name,
        'address' => $address
    ];

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Insert order details into the orderDetails table
        $orderQuery = "INSERT INTO orderDetails (mobileno, address, name, price, date, time) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($orderQuery);
        $stmt->bind_param("ssssss", $mobile, $address, $name, $totalPayable, $date, $time);

        if (!$stmt->execute()) {
            throw new Exception("Error inserting order details: " . $stmt->error);
        }

        $orderId = $stmt->insert_id; // Get the last inserted order id

        // Insert each item with count greater than zero into the orderItemDetails table
        $orderItemQuery = "INSERT INTO orderItemDetails (orderid, itemid, count) VALUES (?, ?, ?)";
        $itemStmt = $conn->prepare($orderItemQuery);

        foreach ($items as $item) {
            if ($item['count'] > 0) {
                $itemStmt->bind_param("iii", $orderId, $item['id'], $item['count']);
                if (!$itemStmt->execute()) {
                    throw new Exception("Error inserting order item details: " . $itemStmt->error);
                }
            }
        }

        // Commit the transaction
        $conn->commit();

        // Reset the item counts to zero
        foreach ($_SESSION['items'] as &$item) {
            $item['count'] = 0;
        }

        // Update the session items
        $_SESSION['items'] = $items;

        // Close statements
        $itemStmt->close();
        $stmt->close();
        $conn->close();

        // Redirect to the order summary page
        header('Location: order_summary.php');
        exit;
    } catch (Exception $e) {
        // Rollback the transaction if any error occurs
        $conn->rollback();

        // Close statements
        if (isset($itemStmt)) $itemStmt->close();
        if (isset($stmt)) $stmt->close();

        $conn->close();

        // Display error message
        echo "Error processing order: " . $e->getMessage();
    }
} else {
    header('Location: index.php');
    exit;
}
include "footer.php";
?>
