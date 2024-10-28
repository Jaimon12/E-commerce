<?php
session_start();
include __DIR__ . "/config.php";

// Check if the connection is established
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Remove the item from the cart
    foreach ($_SESSION['items'] as $key => $item) {
        if ($item['id'] === $id) {
            unset($_SESSION['items'][$key]); // Remove item from session
            $_SESSION['items'] = array_values($_SESSION['items']); // Re-index array
            echo json_encode(['success' => true, 'message' => 'Item removed']);
            exit;
        }
    }
    echo json_encode(['success' => false, 'message' => 'Item not found']);
    exit;
}
?>
