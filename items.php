<?php
session_start();
include "config.php"; // Database configuration
include "header.php"; // Start the session if not already started


// Include the config file
include __DIR__ . "/config.php";

// Check if the connection is established
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Load items from session or database
if (isset($_SESSION['items'])) {
    $items = $_SESSION['items'];
} else {
    $items = array();
    $item_query = "SELECT * FROM ECommerce"; // Ensure this matches the actual table name
    $stmt = $conn->prepare($item_query);
    
    if ($stmt === false) {
        die("Query failed: " . $conn->error);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    while ($item_res = $result->fetch_assoc()) {
        $game = array(
            "id" => $item_res['id'],
            "name" => $item_res['name'],
            "price" => $item_res['price'],
            "image" => $item_res['image'],
            "description" => $item_res['description'],
            "count" => 0
        );
        array_push($items, $game);
    }
    
    $_SESSION['items'] = $items; // Save items to session
}

// Update item count if an AJAX request is received
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['action'])) {
    $id = intval($_POST['id']);
    $action = $_POST['action'];

    // Sanitize and validate input
    $valid_actions = ['increase', 'decrease'];
    if (!in_array($action, $valid_actions)) {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit;
    }

    foreach ($_SESSION['items'] as &$item) {
        if ($item['id'] == $id) {
            if ($action === 'increase') {
                $item['count']++;
            } elseif ($action === 'decrease' && $item['count'] > 0) {
                $item['count']--;
            }
            $_SESSION['items'] = $_SESSION['items']; // Update session with the modified item
            echo json_encode(['success' => true, 'count' => $item['count']]);
            exit;
        }
    }

    // Item not found in session
    echo json_encode(['success' => false, 'message' => 'Item not found']);
    exit;
}
include "footer.php"; 
?>
