<?php
session_start();
include "config.php";

// Check if the connection is established
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submissions for updating item details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the request is for updating item count
    if (isset($_POST['id']) && isset($_POST['count'])) {
        $id = intval($_POST['id']);
        $count = intval($_POST['count']);

        // Update the count of the item in the session
        $itemUpdated = false;
        foreach ($_SESSION['items'] as &$item) {
            if ($item['id'] === $id) {
                $item['count'] = $count;
                $itemUpdated = true;
                break;
            }
        }

        if ($itemUpdated) {
            $_SESSION['items'] = $_SESSION['items']; // Update session
            echo json_encode(['success' => true, 'message' => 'Item count updated']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Item not found']);
        }
        exit;
    }

    // Check if the request is for updating item details including image
    if (isset($_POST['update']) && isset($_FILES['image']) && isset($_POST['id'])) {
        $id = intval($_POST['id']);
        $image = $_FILES['image'];

        // Handle file upload
        if ($image['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $image['tmp_name'];
            $fileName = $image['name'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            // Validate file extension
            $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($fileExtension, $allowedExts)) {
                // Create a unique name for the file
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                $uploadFileDir = './uploads/';
                $destPath = $uploadFileDir . $newFileName;

                // Move the file to the uploads directory
                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    // Update the item's image in the database
                    $stmt = $conn->prepare("UPDATE items SET image = ? WHERE id = ?");
                    $stmt->bind_param("si", $newFileName, $id);

                    if ($stmt->execute()) {
                        $stmt->close();
                        echo json_encode(['success' => true, 'message' => 'Item updated successfully']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Database update failed']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error moving uploaded file']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid file type']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Error uploading file']);
        }
        exit;
    }
}
?>
