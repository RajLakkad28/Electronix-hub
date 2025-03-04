<?php
include 'config.php';
include 'connection.php';
include 'conn.php';
session_start();

if (!isset($_SESSION['id']) || $_SESSION['email'] == "admin@gmail.com") {
    header("location:login.php");
    exit();
}

$user_id = $_SESSION['id'];
$order_id = $_GET['order_id'];
$sql = "SELECT orders.id, orders.order_date,orders.total_price, products.name, order_items.quantity, orders.address, orders.status, products.price, orders.total_price FROM orders JOIN order_items ON orders.id = order_items.order_id JOIN products ON products.id = order_items.product_id WHERE orders.user_id = ? AND orders.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid mt-5">
        <div class="card p-4 shadow">
            <h2 class="text-center text-primary">Electronix Hub Invoice</h2>
            <p><strong>Order ID:</strong> <?php echo $order['id']; ?></p>
            <p><strong>Order Date:</strong> <?php echo $order['order_date']; ?></p>
            <p><strong>Shipping Address:</strong> <?php echo $order['address']; ?></p>

            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total_price = 0;
                    $result->data_seek(0);
                    while ($row = $result->fetch_assoc()): 
                        $subtotal = $row['quantity'] * $row['price'];

                    ?>
                    <tr>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td>₹<?php echo $row['price']; ?></td>
                        <td>₹<?php echo $subtotal; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <h4 class="text-end">Total Price: ₹<?php echo number_format($order['total_price'], 2); ?></h4>
            
            <button class='btn btn-primary' onClick="window.print()">Print the invoice</button>
        </div>
    </div>
</body>
</html>
