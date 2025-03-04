<?php
include 'config.php';
include 'connection.php';
include 'conn.php';
$index=0;
session_start();
if(!isset($_SESSION['email'])|| $_SESSION['email']!="admin@gmail.com")
{
    header('Location:login.php');
}




$status_filter = isset($_GET['status']) ? $_GET['status'] : 'All';

$limit = 10; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$total_sql = "SELECT COUNT(*) FROM orders JOIN userdata ON orders.user_id = userdata.id";
if ($status_filter !== 'All') {
    $total_sql .= " WHERE orders.status = ?";
}

$total_stmt = $conn->prepare($total_sql);
if ($status_filter !== 'All') {
    $total_stmt->bind_param("s", $status_filter);
}
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_rows = $total_result->fetch_row()[0];
$total_pages = ceil($total_rows / $limit);

$sql = "SELECT orders.id, orders.status, orders.order_date, 
               userdata.username,orders.address,orders.total_price
        FROM orders 
        JOIN userdata ON orders.user_id = userdata.id ";

if ($status_filter !== 'All') {
    $sql .= " WHERE orders.status = ?";
}

$sql .= " ORDER BY orders.order_date DESC LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);

if ($status_filter !== 'All') {
    $stmt->bind_param("sii", $status_filter, $limit, $offset);
} else {
    $stmt->bind_param("ii", $limit, $offset);
}

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
<?php include "adminnavbar.php"?>
<body class="bg-light">
    <div class="container-fluid">
        <div class="card shadow-lg p-4 rounded-4 border-0 bg-white">
            <h2 class="text-center mb-4 text-danger fw-bold">Manage Orders</h2>
            <div class="mb-3">
            <form method="GET" action="admin_orders.php">
                <label for="statusFilter" class="form-label fw-bold">Filter by Status:</label>
                <select name="status" id="statusFilter" class="form-select w-auto d-inline">
                    <option value="All" <?php if ($status_filter == 'All') echo 'selected'; ?>>All</option>
                    <option value="Pending" <?php if ($status_filter == 'Pending') echo 'selected'; ?>>Pending</option>
                    <option value="Processing" <?php if ($status_filter == 'Processing') echo 'selected'; ?>>Processing</option>
                    <option value="Delivered" <?php if ($status_filter == 'Delivered') echo 'selected'; ?>>Delivered</option>
                    <option value="Cancelled" <?php if ($status_filter == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                </select>
                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
            </form>
        </div>
        <?php if ($result->num_rows > 0): ?>
            <table class="table table-bordered table-hover text-center">
                <thead class="table-danger">
                    <tr>
                        <th>Index</th>
                        <th>Order ID</th>
                        <th>Username</th>
                        <th>Total Price</th>
                        <th>Status</th>
                        <th>address</th>
                        <th>Order Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <tr>
                            <td><?php echo $index=$index+1; ?></td>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['username']; ?></td>
                            
                            <td>₹<?php echo $row['total_price'] ?>/-</td>
                            <td class="fw-bold"><?php echo $row['status']; ?></td>
                            <td><?php echo $row['address']; ?></td>
                            <td><?php echo $row['order_date']; ?></td>
                            <td>
                                <form action="admin_orderlist.php" method="GET">
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
                        <li class="page-item"><a class="page-link" href="?status=<?php echo $status_filter; ?>&page=<?php echo $page - 1; ?>">Previous</a></li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?status=<?php echo $status_filter; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <li class="page-item"><a class="page-link" href="?status=<?php echo $status_filter; ?>&page=<?php echo $page + 1; ?>">Next</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php else: ?>
                <p class="text-center text-danger">no item is <?Php echo $status_filter?></p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>