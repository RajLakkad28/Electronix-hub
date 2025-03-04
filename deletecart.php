<?php
include 'config.php';
include 'connection.php';
include 'conn.php';

session_start();
if (!isset($_SESSION['id'])) {
    header("location:login.php");
    exit();
}

$user_id = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_id'])) {
    $cart_id = $_POST['cart_id'];
    $delete_sql = "DELETE FROM cart WHERE id = ? AND user_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("ii", $cart_id, $user_id);
    $delete_stmt->execute();
    header("Location: cart.php");
    exit();
}
?>