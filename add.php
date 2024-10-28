<?php
session_start();
include 'config.php'; // Database configuration
include 'header.php'; // Header file for navigation and session management

// Check if the connection is established
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submissions for adding products
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        $name = $_POST['name'];
        $price = $_POST['price'];
        $description = $_POST['description'];

        // Handle file upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['image']['tmp_name'];
            $fileName = $_FILES['image']['name'];
            $fileSize = $_FILES['image']['size'];
            $fileType = $_FILES['image']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            // Validate file extension
            $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($fileExtension, $allowedExts)) {
                // Create a unique name for the file
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                $uploadFileDir = './uploads/';
                $dest_path = $uploadFileDir . $newFileName;

                // Move the file to the uploads directory
                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    // File upload successful
                    $stmt = $conn->prepare("INSERT INTO items (name, price, image, description) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("siss", $name, $price, $newFileName, $description);
                    $stmt->execute();
                    $stmt->close();
                    echo "<div class='alert alert-success'>Product added successfully.</div>";
                } else {
                    echo "<div class='alert alert-danger'>There was an error uploading the file, please try again.</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>No file uploaded or there was an upload error.</div>";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Add New Product</h2>

        <div class="card mb-4">
            <div class="card-header">Add Product</div>
            <div class="card-body">
                <form method="POST" action="add.php" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="price">Price:</label>
                        <input type="number" class="form-control" id="price" name="price" required>
                    </div>
                    <div class="form-group">
                        <label for="image">Image File:</label>
                        <input type="file" class="form-control-file" id="image" name="image" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea class="form-control" id="description" name="description" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Product</button>
                </form>
            </div>
        </div>

        <a href="manage.php" class="btn btn-secondary">Manage Products</a>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
