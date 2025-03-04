<?php
include 'config.php';
include 'connection.php';
include 'conn.php';
session_start();

if (!isset($_SESSION['email']) || $_SESSION['email'] != "admin@gmail.com") {

    header("Location: login.php");
    exit();
}
$_SESSION['current_page'] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

$errors = [];


$limit = 5;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1)
    $page = 1;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'latest';

$sortOptions = [
    "low_high" => "Price ASC",
    "high_low" => "Price DESC",
    "latest" => "id DESC"
];
$orderBy = $sortOptions[$sort] ?? "id DESC";


$countQuery = "SELECT COUNT(*) AS total FROM products";
$params = [];
$types = "";

if (!empty($search)) {
    $countQuery .= " WHERE name LIKE ?";
    $params[] = "%$search%";
    $types .= "s";
}

$countStmt = $conn->prepare($countQuery);
if (!empty($params)) {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$countResult = $countStmt->get_result();
$row = $countResult->fetch_assoc();
$total_records = $row['total'];
$total_pages = ceil($total_records / $limit);
$countStmt->close();


function fetchProducts($conn, $search, $orderBy, $limit, $offset)
{
    $productQuery = "
             SELECT products.*,r.review_count,r.rating_avg
            FROM products
            left join(SELECT product_id,COUNT(*) as review_count,round(avg(rating),1) as rating_avg
                      from rating GROUP by product_id)
                      r on r.product_id=products.id";
   
    $types = "";

    if (!empty($search)) {
        $productQuery .= " WHERE products.name LIKE ?";
        $params[] = "%$search%";
        $types .= "s";
    }

    $productQuery .= " ORDER BY $orderBy LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $types .= "ii";

    $statement = $conn->prepare($productQuery);
    $statement->bind_param($types, ...$params);
    $statement->execute();
    $result = $statement->get_result();
    $products = $result->fetch_all(MYSQLI_ASSOC);
    $statement->close();

    return $products;
}
$products = fetchProducts($conn, $search, $orderBy, $limit, $offset);


?>

<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes" />
    <meta charset="UTF-8" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <link rel="shortcut icon" href="https://cdn-icons-png.flaticon.com/512/295/295128.png">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
    integrity="sha384-tViUnnbYAV00FLIhhi3v/dWt3Jxw4gZQcNoSCxCIFNJVCx7/D55/wXsrNIRANwdD" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" type="text/javascript"></script>
    <link href="https://cdn.jsdelivr.net/npm/@coreui/coreui-pro@5.10.0/dist/css/coreui.min.css" rel="stylesheet"
        integrity="sha384-iFYnumxmAfPWEvBBHVgQ1pcH7Bj9XLrhznQ6DpVFtF3dGwlEAqe4cmd4NY4cJALM" crossorigin="anonymous">
    <script defer src="https://cdn.jsdelivr.net/npm/@coreui/coreui-pro@5.10.0/dist/js/coreui.bundle.min.js"></script>


</head>

<body>
<?php include "adminnavbar.php"; ?>

    <div class="d-flex justify-content-center mt-3">
        <form class="d-flex my-2 my-lg-0" method="GET" action="">
            <input class="form-control w-50 p-3" type="search" name="search" placeholder="Search products"
                value="<?= htmlspecialchars($search); ?>">
            <select name="sort" class="btn btn-primary dropdown-toggle ms-1">
                <option value="">Sort by</option>
                <option value="low_high" <?= $sort == "low_high" ? 'selected' : ''; ?>>Price: Low to High</option>
                <option value="high_low" <?= $sort == "high_low" ? 'selected' : ''; ?>>Price: High to Low</option>
                <option value="latest" <?= $sort == "latest" ? 'selected' : ''; ?>>Newest First</option>
            </select>
            <button class="btn btn-light" type="submit" style="font-weight: bolder; color: green; margin-left: 10px;">
                <i class="bi bi-search"></i>
            </button>
        </form>
    </div>

    <div class="container mt-4">
        <?php if (empty($products)): ?>
            <p class="text-center">No products found.</p>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <div class="row justify-content-center mb-3">
                    <div class="col-md-10">
                        <div class="card shadow border rounded-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <img src="<?= $product['filename']; ?>" class="w-100 rounded">
                                    </div>
                                    <div class="col-md-6">
                                        <h5><?= $product['name']; ?></h5>
                                        <p><?= $product['description']; ?></p>

                                        <h6>Quantity: <?= $product['quantity']; ?></h6>
                                        <p data-coreui-precision="0.01" data-coreui-read-only="true" data-coreui-toggle="rating"
                                            data-coreui-value="<?php echo $product['rating_avg'] ?? 0 ?>">
                                            (<?php echo $product['rating_avg'] ?? 0 ?>)</p>
                                        <h6>(<?= $product['review_count'] ?? 0; ?> review)</h6>
                                    </div>
                                    <div class="col-md-3">
                                        <h4 class="mb-1">₹<?= $product['Price']; ?>/-</h4>
                                        
                                <div>
                                <form action="update.php" method="GET" class="d-inline p-2">
                                        <input type="hidden" name="id" value="<?= $product["id"] ?>">
                                        <button type="submit" class="btn btn-primary btn-sm"><i class="bi-pencil-square"></i></button>
                                    </form>
                                   <form action="delete.php" method="GET" class="d-inline p-2">
                                        <input type="hidden" name="id" value="<?= $product["id"] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                                    </form> 
                                </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <div class="pagination justify-content-center text-center mt-4">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1; ?>&search=<?= urlencode($search); ?>&sort=<?= urlencode($sort); ?>"
                    class="btn btn-dark">Prev</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?= $i; ?>&search=<?= urlencode($search); ?>&sort=<?= urlencode($sort); ?>"
                    class="btn btn-outline-dark"><?= $i; ?></a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="?page=<?= $page + 1; ?>&search=<?= urlencode($search); ?>&sort=<?= urlencode($sort); ?>"
                    class="btn btn-dark">Next</a>
            <?php endif; ?>
        </div>
    </div>

                         

</body>

</html>
<?php
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM products WHERE id='$id'";

    if ($conn->query($sql) === TRUE) {
        echo "Record deleted successfully";
    } else {
        echo "Error deleting record: " . $conn->error;
    }

}
?>