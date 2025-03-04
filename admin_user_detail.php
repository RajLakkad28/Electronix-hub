<?php
include 'conn.php';
include 'connection.php';
include 'config.php';
session_start();

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $sql = "select products.*,rating.product_id,rating.review as review_count,rating
            from products
             join rating on products.id=rating.product_id where user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
}
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
    <?php include "adminnavbar.php"; ?>

    

    <div class="container mt-4">
        
        <?php if ($result->num_rows > 0): ?>

            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="row justify-content-center mb-3">
                    <div class="col-md-10">
                        <div class="card shadow border rounded-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <img src=" <?= $row['filename']; ?>" class="w-100 rounded">
                                    </div>
                                    <div class="col-md-6">
                                        <h5> <?= $row['name']; ?></h5>
                                        <p><?= $row['description']; ?></p>

                                        <h6>Quantity: <?= $row['quantity']; ?></h6>
                                        <p data-coreui-precision="0.01" data-coreui-read-only="true" data-coreui-toggle="rating"
                                            data-coreui-value="<?php echo $row['rating'] ?? 0 ?>">
                                            (<?php echo $row['rating'] ?? 0 ?>)</p>
                                        <h6>(review:-<?= $row['review_count'] ?? 0; ?> )</h6>
                                    </div>
                                    <div class="col-md-3">
                                        <h4 class="mb-1">₹ <?= $row['Price']; ?>/-</h4>
                                        <h6 class="text-success">Free shipping</h6>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>

        <?php else: ?>
            <h4 class="text-center">No products found please add rating and review .</h4>
        <?php endif; ?>

</body>

</html>