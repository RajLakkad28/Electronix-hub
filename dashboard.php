<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['email'] != "admin@gmail.com") {
    header("location:login.php");
    exit();
}

include './conn.php'; 
$_SESSION['current_page'] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if (!empty($search)) {
    $statement = $conn->prepare("SELECT * FROM userdata WHERE username LIKE ? OR email LIKE ?");
    $searchTerm = "%$search%";
    $statement->bind_param("ss", $searchTerm, $searchTerm);
} else {
    $statement = $conn->prepare("SELECT * FROM userdata");
}

$statement->execute();
$result = $statement->get_result();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['control'])) {
    $user_id = $_POST['user_id'];
    $control = $_POST['control'];

    $update_sql = "UPDATE userdata SET control = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("si", $control, $user_id);
    
    if ($update_stmt->execute()) {
        header("Location: " . $_SESSION['current_page']);
            exit(); 
    } else {
        echo "<script>alert('Error updating user control');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <link rel="shortcut icon" href="https://cdn-icons-png.flaticon.com/512/295/295128.png">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        integrity="sha384-tViUnnbYAV00FLIhhi3v/dWt3Jxw4gZQcNoSCxCIFNJVCx7/D55/wXsrNIRANwdD" crossorigin="anonymous">
    <title>Dashboard</title>
    <style>
        th {
            height: 50px;
            background-color: #04AA6D;
            color: white;
        }

        table {
            border-collapse: collapse;
            width: 50%;
        }

        td,
        th {
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: coral;
        }
    </style>
</head>

<body>
    <?php include './adminnavbar.php'; ?>
    <div class="d-flex justify-content-left mt-3">
        <form class="d-flex my-2 my-lg-0" method="GET" action="">
            <input class="form-control me-2" type="search" name="search" placeholder="Search users" aria-label="Search"
                value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button class="btn btn-light" type="submit" style="font-weight:bolder;color:green;"><i
                    class="bi bi-search"></i></button>
        </form>
    </div>
    <br>
    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Username</th>
                <th>email Id</th>
                <th>Detail</th>
                <th>orders</th>
                <th>Access Control</th>
            </tr>

            <?php while ($row = $result->fetch_assoc()): ?>
    <?php $control = $row['control']; ?>
    <tr>
        <td><?= htmlspecialchars($row["username"]) ?></td>
        <td><?= htmlspecialchars($row["email"]) ?></td>
        <td>
            <form action="admin_user_detail.php" method="GET" class='d-inline p-2'>
                <input type='hidden' name='id' value="<?= $row['id'] ?>">
                <button type='submit' class='btn btn-warning shadow-0'>
                    <i class='bi bi-star'></i> Ratings
                </button>
            </form>
        </td>
        <td>
            <form action="admin_user_orderlist.php" method="GET" class='d-inline p-2'>
                <input type='hidden' name='id' value="<?= $row['id'] ?>">
                <button type='submit' class='btn btn-primary shadow-0'>
                    <i class='bi bi-shop'></i> Orders
                </button>
            </form>
        </td>
        <td>
            <form method="post" style="text-align:center">
                <input type="hidden" name="user_id" value="<?= $row['id']; ?>">
                <select name="control" class="form-select d-inline w-auto">
                    <option value="Block" <?= $control === 'Block' ? 'selected' : ''; ?>>Block</option>
                    <option value="unblock" <?= $control === 'unblock' ? 'selected' : ''; ?>>Unblock</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm">Update</button>
            </form>
        </td>
    </tr>
<?php endwhile; ?>

        </table>
    <?php else: ?>
        <p>No results found.</p>
    <?php endif; ?>



</body>

</html>