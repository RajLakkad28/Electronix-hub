<?php
include 'config.php';
include 'connection.php';
include 'conn.php';
session_start();


if ($_SESSION['email'] == "admin@gmail.com" || !isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$_SESSION['current_page'] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

$wishlistmessage = "";
$wishliststatus = "";

$user_id = $_SESSION['id'];
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


function fetchProducts($conn, $user_id, $search, $orderBy, $limit, $offset)
{
    $productQuery = "
         SELECT products.*, IF(wishlist.id IS NOT NULL, TRUE, FALSE) AS wishlist_id,r.review_count,r.rating_avg
        FROM products
        LEFT JOIN wishlist ON products.id = wishlist.product_id AND wishlist.user_id = ?
        left join(SELECT product_id,COUNT(*) as review_count,round(avg(rating),1) as rating_avg
                  from rating GROUP by product_id)
                  r on r.product_id=products.id";
    $params = [$user_id];
    $types = "i";

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

// function fetchreview($conn)
// {
//     $reviewQuery = "
//         SELECT rating.product_id, COUNT(rating.product_id) AS review_count
//         FROM rating
//         GROUP BY rating.product_id";

//     $countreview = $conn->prepare($reviewQuery);
//     $countreview->execute();
//     $result = $countreview->get_result();
//     $reviewMap = [];
//     while ($row = $result->fetch_assoc()) {
//         $reviewMap[$row['product_id']] = $row['review_count'];
//     }

//     $countreview->close();
//     return $reviewMap;
// }

// function fetchrating($conn)
// {
//     $ratingQuery = "
//             SELECT rating.product_id, AVG(rating.rating) AS rating_count
//             FROM rating
//             GROUP BY rating.product_id";

//     $countrating = $conn->prepare($ratingQuery);
//     $countrating->execute();
//     $results = $countrating->get_result();

//     $ratingMap = [];
//     while ($rows = $results->fetch_assoc()) {
//         $ratingMap[$rows['product_id']] = round($rows['rating_count'], 2);
//     }

//     $countrating->close();
//     return $ratingMap;

// }


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_wishlist'])) {
    $product_id = $_POST['product_id'];

    $wishlistCheck = $conn->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
    $wishlistCheck->bind_param("ii", $user_id, $product_id);
    $wishlistCheck->execute();
    $wishlistCheck->store_result();
    $isInWishlist = $wishlistCheck->num_rows > 0;
    $wishlistCheck->close();

    if ($isInWishlist) {
        $deleteWishlist = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
        $deleteWishlist->bind_param("ii", $user_id, $product_id);
        $deleteWishlist->execute();
        $wishlistmessage = "Removed from your wishlist";
    } else {
        $insertWishlist = $conn->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
        $insertWishlist->bind_param("ii", $user_id, $product_id);
        $insertWishlist->execute();
        $wishlistmessage = "Added to your wishlist";
    }

    $wishliststatus = "success";
}


$products = fetchProducts($conn, $user_id, $search, $orderBy, $limit, $offset);
// $ratings = fetchrating($conn);
// $reviews = fetchreview($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <link rel="shortcut icon" href="https://cdn-icons-png.flaticon.com/512/295/295128.png">
    <link href="https://cdn.jsdelivr.net/npm/@coreui/coreui-pro@5.10.0/dist/css/coreui.min.css" rel="stylesheet"
        integrity="sha384-iFYnumxmAfPWEvBBHVgQ1pcH7Bj9XLrhznQ6DpVFtF3dGwlEAqe4cmd4NY4cJALM" crossorigin="anonymous">
    <script defer src="https://cdn.jsdelivr.net/npm/@coreui/coreui-pro@5.10.0/dist/js/coreui.bundle.min.js"></script>

</head>

<body>
    <?php include "navbar.php"; ?>

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
                                        <h6 class="text-success">Free shipping</h6>
                                        <form action="productdetail.php" method="GET" class='d-inline p-2'>
                                            <input type="hidden" name="id" value="<?= $product['id']; ?>">
                                            <button type="submit" class="btn btn-primary shadow-0"><i
                                                    class="bi bi-info-circle"></i> Details</button>
                                        </form>
                                        <form method="post" class='d-inline p-2'>
                                            <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                                            <button type="submit" name="add_to_wishlist" class="btn btn-primary shadow-0">
                                                <i class="bi bi-heart-fill"
                                                    style="color: <?= ($product['wishlist_id']) ? 'red' : 'white'; ?>;"></i>
                                                Wishlist
                                            </button>
                                        </form>
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

    <?php include 'footer.php'; ?>
    <?php if ($wishlistmessage): ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Swal.fire({
                toast: true,
                position: 'top',
                icon: '<?php echo $wishliststatus; ?>',
                title: '<?php echo $wishlistmessage; ?>',
                showConfirmButton: false,
                timer: 2000
            });
        </script>
    <?php endif; ?>
</body>

</html>