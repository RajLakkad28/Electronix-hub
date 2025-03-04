<?php
require __DIR__ . "/vendor/autoload.php";
include 'config.php';
include 'connection.php';
include 'conn.php';
session_start();

if (!isset($_SESSION['id'])) {
    header("location:login.php");
    exit();
}

\Stripe\Stripe::setApiKey("sk_test_51QyogVH5YQ9aAnHHE68VHOgDJyUIl1W458hZB6vBQoAbnvSiBbENpAvdf88LEeOcTqWpLszDH6absFmmjFG5QFzo00bcNSWt24");

$session_id = $_GET['session_id'];
$order_id = $_GET['order_id'];

$session = \Stripe\Checkout\Session::retrieve($session_id);

if ($session->payment_status === 'paid') {
   
    $update_order_sql = "UPDATE orders SET payment_status = 'success' WHERE id = ?";
    $update_order_stmt = $conn->prepare($update_order_sql);
    $update_order_stmt->bind_param("i", $order_id);
    $update_order_stmt->execute();

   

    header("Location: order_success.php");
    exit();
} else {
    echo "Payment failed! Please try again.";
}
?>
