<?php
session_start();
include "config.php"; // Database configuration
include "header.php";

// Check if the connection is established
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize items session if not set or empty
if (!isset($_SESSION['items']) || empty($_SESSION['items'])) {
    $items = [];
    $item_query = "SELECT * FROM items";
    $stmt = $conn->prepare($item_query);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($item_res = $result->fetch_assoc()) {
            $game = array(
                "id" => $item_res['id'],
                "name" => $item_res['name'],
                "price" => $item_res['price'],
                "image" => $item_res['image'],
                "description" => $item_res['description'],
                "count" => 0
            );
            $items[] = $game;
        }
        $_SESSION['items'] = $items;
    } else {
        // Clear session items if no results
        $_SESSION['items'] = [];
    }
} else {
    $items = $_SESSION['items'];
}

// Handle POST requests to update item count
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['action'])) {
    $id = intval($_POST['id']);
    $action = $_POST['action'];

    foreach ($_SESSION['items'] as &$item) {
        if ($item['id'] == $id) {
            if ($action === 'increase') {
                $item['count']++;
            } elseif ($action === 'decrease' && $item['count'] > 0) {
                $item['count']--;
            }
            break;
        }
    }

    // Update session with new values
    $_SESSION['items'] = $_SESSION['items']; 
    echo json_encode(['success' => true, 'count' => $item['count']]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-commerce Items</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .grid-container {
            display: grid;
            gap: 20px;
        }
        @media (min-width: 600px) {
            .grid-container {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        @media (max-width: 599px) {
            .grid-container {
                grid-template-columns: 1fr;
            }
        }
        .grid-item {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
            border-radius: 10px;
        }
        .grid-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
        }
        .grid-item h3 {
            margin: 10px 0;
        }
        .buttons {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }
        .buttons button {
            padding: 5px 10px;
            font-size: 16px;
            cursor: pointer;
        }
        .count {
            font-size: 16px;
        }
    </style>
</head>
<body>
<div class="grid-container">
    <?php if (!empty($items)): ?>
        <?php foreach ($items as $product): ?>
            <div class="grid-item">
                </p>
                <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                <div class="buttons">
                    <button onclick="updateCount(<?php echo $product['id']; ?>, 'decrease')">-</button>
                    <span class="count" id="count-<?php echo $product['id']; ?>"><?php echo $product['count']; ?></span>
                    <button onclick="updateCount(<?php echo $product['id']; ?>, 'increase')">+</button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No items available in the store.</p>
    <?php endif; ?>
</div>
<script>
function updateCount(id, action) {
    fetch('index.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id=${id}&action=${action}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('count-' + id).innerText = data.count;
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>
</body>
</html>
<?php include "footer.php"; // Footer ?>
	