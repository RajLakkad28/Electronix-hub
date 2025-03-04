<?php
include 'config.php';
include 'connection.php';
include 'conn.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email']) || $_SESSION['email'] == "admin@gmail.com") {
    header("Location: login.php");
    exit();
}

// Get the user_id from session
$user_id = $_SESSION['user_id']; // Assuming user_id is stored in session
$product_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Validate product ID
if ($product_id <= 0) {
    echo "Invalid product.";
    exit();
}

// Remove the product from the cart
$sqlDelete = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
$stmtDelete = $connection->prepare($sqlDelete);
$stmtDelete->bind_param('ii', $user_id, $product_id);
$stmtDelete->execute();

header("Location: cart.php"); // Redirect back to the cart page
exit();
?>
