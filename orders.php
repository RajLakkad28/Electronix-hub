<?php
include 'config.php';
include 'connection.php';
include 'conn.php';
$index=0;
session_start();


if (!isset($_SESSION['id'])|| $_SESSION['email']=="admin@gmail.com") {
    header("location:login.php");
    exit();
}
$user_id = $_SESSION['id'];

$limit = 5; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$total_sql = "SELECT COUNT(*) FROM orders where orders.user_id = ?";
$total_stmt=$conn->prepare($total_sql);
$total_stmt->bind_param("i",$user_id);
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_rows = $total_result->fetch_row()[0];
$total_pages = ceil($total_rows / $limit);
$total_stmt = $conn->prepare($total_sql);



$sql = "SELECT orders.id, orders.address,orders.status, orders.order_date,orders.total_price
        FROM orders
        WHERE orders.user_id = ? order by id Desc LIMIT ? OFFSET ?" ;
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $user_id,$limit,$offset);
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
<body >
    <?php include "navbar.php"?>
    <div class="container-fluid">
        <div class="card shadow-lg p-4 rounded-4 border-0 bg-white">
            <h2 class="text-center mb-4 text-primary fw-bold"> <?php echo strtoupper($_SESSION['username'])?>,This is your Orders</h2>
            <table class="table table-bordered table-hover text-center">
                <thead class="table-primary">
                    <tr>
                        <th>Index</th>
                        <th>Order ID</th>
                        <th>total price</th>
                        <th>Status</th>
                        <th>address</th>
                        <th>Order Date</th>
                        <th>Actions</th>
                        
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <tr>
                        <td><?php echo $index=$index+1 ?></td>
                            <td><?php echo $row['id']; ?></td>
                            <td>₹<?php echo number_format($row['total_price'],2) ?>/- </td>
                            <td class="fw-bold"><?php echo $row['status']; ?></td>
                            <td><?php echo $row['address']?></td>
                            <td><?php echo $row['order_date']; ?></td>
                            <td>
                                <form action="orders_list.php" method="GET">
                                    <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" class="btn btn-primary btn-sm">View</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <nav>
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a></li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
