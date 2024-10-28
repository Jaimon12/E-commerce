<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ECommerceDB";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // Log error details to a file (for production use, instead of showing sensitive data to users)
    error_log("Connection failed: " . $conn->connect_error);
    
    // Display a generic error message to the user
    die("Sorry, we're experiencing some technical issues. Please try again later.");
}

// Set character set to UTF-8 (important for handling special characters)
if (!$conn->set_charset("utf8")) {
    error_log("Error loading character set utf8: " . $conn->error);
}
?>
