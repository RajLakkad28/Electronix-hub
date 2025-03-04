<?php
include 'config.php';
include 'connection.php';
include 'conn.php';
$index = 0;
session_start();

if (!isset($_SESSION['id']) || $_SESSION['email'] == "admin@gmail.com") {
    header("location:login.php");
    exit();
}

$user_id = $_SESSION['id'];
$order_id = $_GET['order_id'];
$sql = "SELECT orders.id, products.name, order_items.quantity, orders.address, orders.payment_status,orders.status, orders.order_date, products.filename, order_items.price, order_items.product_id, orders.total_price, products.description 
FROM orders JOIN order_items ON orders.id = order_items.order_id 
JOIN products ON products.id = order_items.product_id WHERE orders.user_id = ? AND orders.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $order_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include "navbar.php" ?>
    <div class="container-fluid">
        <div class="card shadow-lg p-4 rounded-4 border-0 bg-white">
            <h2 class="text-center mb-4 text-primary fw-bold"> <?php echo strtoupper($_SESSION['username']) ?>, This is your Orders</h2>

            <?php while ($row = $result->fetch_assoc()): ?>
                <?php $product_id = $row['product_id'];
                $address = $row['address'];
                $total_price = $row['total_price'];
                $payment_status=$row['payment_status'] ?>

                <div class='container'>
                    <div class='row justify-content-center mb-3'>
                        <div class='col-md-12 col-xl-10'>
                            <div class='card shadow border rounded-3'>
                                <div class='card-body'>
                                    <div class='row'>
                                        <div class='col-md-12 col-lg-3 col-xl-3 mb-4 mb-lg-0'>
                                            <div class='bg-image hover-zoom ripple rounded ripple-surface'>
                                                <img src="<?php echo htmlspecialchars($row['filename']); ?>" class='w-100' />
                                            </div>
                                        </div>
                                        <div class='col-md-6 col-lg-6 col-xl-6'>
                                            <h5><?php echo $row['name']; ?></h5>
                                            <div class='mt-1 mb-0 text-muted small'>
                                                <span><?php echo $row['description']; ?></span>
                                            </div>
                                            <h6>Your Quantity: <?php echo $row['quantity']; ?></h6>
                                        </div>
                                        <div class='col-md-6 col-lg-3 col-xl-3 border-sm-start-none border-start'>
                                            <div class='d-flex flex-row align-items-center mb-1'>
                                                <h4 class='mb-1 me-1'>₹<?php echo $row['price']; ?>/-</h4>
                                            </div>
                                            <div class='d-flex flex-row align-items-center mb-1'>
                                                <h4 class='mb-1 me-1'><?php echo $row['status']; ?></h4>
                                            </div>
                                            <h6 class='text-success'>Free shipping</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
            <h4 class="text-center">Total: <span id="total-price">₹<?php echo number_format($total_price, 2); ?>/-</span></h4>
            <label class="d-block text-center" style="font-size:23px;color:red;"> <i class="bi bi-geo-alt-fill"></i> <?php echo $address ?></label>
            <h4 class="text-center"> Payment-status:- <?= $payment_status; ?></span></h4>
            
            <?php if ($payment_status=='fail'):?>
                <form method="post" action="checkout.php" class="d-flex justify-content-center">
                 <input type="hidden" name="total_price" id="hidden-total-price" value="<?php echo $total_price; ?>">
                 
                    <button type="submt" class="btn btn-primary d-flex justify-content-center" name="order_id" value="<?= $order_id ?>">Payment</button>
                </form>
                <?php else :?>

           
            <div class="text-center mt-3">
                <a href="invoice.php?order_id=<?php echo $order_id; ?>" class="btn btn-primary">Generate Invoice</a>
            </div>
            <?php endif;?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
