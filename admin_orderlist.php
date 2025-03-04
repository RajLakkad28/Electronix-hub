<?php
include 'config.php';
include 'connection.php';
include 'conn.php';
$index = 0;
session_start();
if (!isset($_SESSION['email']) || $_SESSION['email'] != "admin@gmail.com") {
    header('Location:login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $update_sql = "UPDATE orders SET status = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("si", $status, $order_id);
    $update_stmt->execute();
}

$order_id = $_GET['order_id'];
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'All';


$sql = "SELECT orders.id, orders.status, orders.order_date, 
               userdata.username, products.name, order_items.quantity, orders.address,order_items.price,products.filename,products.description,orders.total_price
        FROM orders 
        JOIN userdata ON orders.user_id = userdata.id 
        JOIN order_items ON orders.id = order_items.order_id 
        JOIN products ON order_items.product_id = products.id where order_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $order_id);


$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<?php include "adminnavbar.php" ?>

<body class="bg-light">
    <div class="container-fluid">
        <div class="card shadow-lg p-4 rounded-4 border-0 bg-white">
            <h2 class="text-center mb-4 text-danger fw-bold">Manage Orders</h2>

            <?php if ($result->num_rows > 0): ?>


                <?php while ($row = $result->fetch_assoc()): ?>

                    <?php $status = $row['status'];
                    $address = $row['address'];
                    $total_price = $row['total_price']; ?>

                    <div class='container'>
                        <div class='row justify-content-center mb-3'>
                            <div class='col-md-12 col-xl-10'>
                                <div class='card shadow border rounded-3'>
                                    <div class='card-body'>
                                        <div class='row'>
                                            <div class='col-md-12 col-lg-3 col-xl-3 mb-4 mb-lg-0'>
                                                <div class='bg-image hover-zoom ripple rounded ripple-surface'>
                                                    <img src="<?php echo htmlspecialchars($row['filename']); ?>"
                                                        class='w-100' />
                                                    <a href='#!'>
                                                        <div class='hover-overlay'>
                                                            <div class='mask'
                                                                style='background-color: rgba(253, 253, 253, 0.15);'>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class='col-md-6 col-lg-6 col-xl-6'>
                                                <h5><?php echo $row['name']; ?></h5>
                                                
                                                <div class='mt-1 mb-0 text-muted small'>
                                                    <span><?php echo $row['description']; ?></span>
                                                </div>
                                                <h6>your Quantity-<?php echo $row['quantity']; ?></h6>

                                            </div>
                                            <div class='col-md-6 col-lg-3 col-xl-3 border-sm-start-none border-start'>
                                                <div class='d-flex flex-row align-items-center mb-1'>
                                                    <h4 class='mb-1 me-1'>₹<?php echo $row['price']; ?>/-</h4>
                                                </div>
                                                <div class='d-flex flex-row align-items-center mb-1'>

                                                    <h4 class='mb-1 me-1'><?php echo $row['status']; ?>/-</h4>

                                                </div>
                                                <h6 class='text-success'>Free shipping</h6>
                                                <div class='d-flex flex-column mt-4'>
                                                    <form action='productdetail.php' method='GET'>
                                                        <input type='hidden' name='id' value='" . $product[' id'] . "'>
                                               
                                                                    </form>
                                   
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                        <?php endwhile; ?>
                                    
                                        <h4 style="text-align:center">Total: <span id="
                                                    total-price">₹<?php echo number_format($total_price, 2); ?>/-</span>
                                                    </h4>
                                                    <label style=" font-size:23px;color:red;text-align:center "> <i
                                                            class="bi bi-geo-alt-fill"></i>
                                                        <?php echo $address ?></label>

                                                    <form method="post" style="text-align:center">
                                                        <input type="hidden" name="order_id"
                                                            value="<?php echo $order_id; ?>">
                                                        <select name="status" class="form-select d-inline w-auto">
                                                            <option value="Pending" <?php if ($status == 'Pending')
                                                                echo 'selected'; ?>>Pending</option>
                                                            <option value="Processing" <?php if ($status == 'Processing')
                                                                echo 'selected'; ?>>Processing</option>
                                                            <option value="Delivered" <?php if ($status == 'Delivered')
                                                                echo 'selected'; ?>>Delivered</option>
                                                            <option value="Cancelled" <?php if ($status == 'Cancelled')
                                                                echo 'selected'; ?>>Cancelled</option>
                                                        </select>
                                                        <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                                    </form>
                                                    

                                                <?php else: ?>
                                                    <p class="text-center text-danger">no item found

                                                    </p>
                                                <?php endif; ?>
                                        </div>
                                    </div>
</body>

</html>