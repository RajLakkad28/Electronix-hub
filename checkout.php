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

$user_id = $_SESSION['id'];
$address = $_POST['address'];
$total_price = $_POST['total_price'];
$quantities = $_POST['quantities']; 

$user_query = "SELECT username, email FROM userdata WHERE id = ?";
$user_stmt = $conn->prepare($user_query);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();
$customer_name = $user['username'];
$customer_email = $user['email'];


$order_id=$_POST['order_id'];

if(!isset($_POST['order_id'])){
$order_sql = "INSERT INTO orders (user_id, address, total_price, payment_status) VALUES (?, ?, ?, 'fail')";
$order_stmt = $conn->prepare($order_sql);
$order_stmt->bind_param("isi", $user_id, $address, $total_price);
$order_stmt->execute();
$order_id = $order_stmt->insert_id;
$cart_sql = "SELECT cart.product_id, products.price FROM cart 
             JOIN products ON cart.product_id = products.id 
             WHERE cart.user_id = ?";
$cart_stmt = $conn->prepare($cart_sql);
$cart_stmt->bind_param("i", $user_id);
$cart_stmt->execute();
$result = $cart_stmt->get_result();


while ($row = $result->fetch_assoc()) {
    $product_id = $row['product_id'];
    $price = $row['price'];
    $quantity = isset($quantities[$product_id]) ? $quantities[$product_id] : 1; 

    $order_item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $order_item_stmt = $conn->prepare($order_item_sql);
    $order_item_stmt->bind_param("iiid", $order_id, $product_id, $quantity, $price);
    $order_item_stmt->execute();
}
$delete_cart_sql = "DELETE FROM cart WHERE user_id = ?";
$delete_cart_stmt = $conn->prepare($delete_cart_sql);
$delete_cart_stmt->bind_param("i", $_SESSION['id']);
$delete_cart_stmt->execute();
}
\Stripe\Stripe::setApiKey("sk_test_51QyogVH5YQ9aAnHHE68VHOgDJyUIl1W458hZB6vBQoAbnvSiBbENpAvdf88LEeOcTqWpLszDH6absFmmjFG5QFzo00bcNSWt24");

$quantities_json = json_encode($quantities);


$checkout_session = \Stripe\Checkout\Session::create([
    "mode" => "payment",
    "success_url" => "https://localhost/practice/product/order.php?session_id={CHECKOUT_SESSION_ID}&order_id=$order_id",
    "cancel_url" => "https://localhost/practice/product/cart.php",
    "customer_email" => $customer_email,
    "billing_address_collection" => "required",
    "metadata" => [
        "customer_name" => $customer_name,
        "customer_address" => $address,
        "user_id" => $user_id,
        "quantities" => $quantities_json,
        "order_id" => $order_id
    ],
    "line_items" => [
        [
            "quantity" => 1,
            "price_data" => [
                "currency" => "INR",
                "unit_amount" => $total_price * 100,
                "product_data" => [
                    "name" => "Order Payment",
                ]
            ]
        ]
    ]
]);

http_response_code(303);
header("location: " . $checkout_session->url);
?>
