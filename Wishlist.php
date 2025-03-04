<?php
include 'config.php';
include 'connection.php';
include 'conn.php';
session_start();

if (!isset($_SESSION['id'])||$_SESSION['email']=="admin@gmail.com") {
    header("location:login.php");
    exit();
}

$user_id = $_SESSION['id'];

$sql = " SELECT products.*, IF(wishlist.id IS NOT NULL, TRUE, FALSE) AS wishlist_id,r.review_count,r.rating_avg
        FROM products
         JOIN wishlist ON products.id = wishlist.product_id AND wishlist.user_id = ?
        left join(SELECT product_id,COUNT(*) as review_count,round(avg(rating),1) as rating_avg
                  from rating GROUP by product_id)
                  r on r.product_id=products.id";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['id'])){
    $product_id=$_POST['id'];
    $deletewishlist=$conn->prepare("DELETE FROM wishlist where product_id=?");
    $deletewishlist->bind_param('i', $product_id);
    $deletewishlist->execute();
    header("location: wishlist.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@coreui/coreui-pro@5.10.0/dist/css/coreui.min.css" rel="stylesheet"
        integrity="sha384-iFYnumxmAfPWEvBBHVgQ1pcH7Bj9XLrhznQ6DpVFtF3dGwlEAqe4cmd4NY4cJALM" crossorigin="anonymous">
    <script defer src="https://cdn.jsdelivr.net/npm/@coreui/coreui-pro@5.10.0/dist/js/coreui.bundle.min.js"></script>

<body>
<?php include "navbar.php"?>
    <div class="container-fluid">
        <div class="card shadow-lg p-4 rounded-4 border-0 bg-white">
            <h2 class="text-center mb-4 text-primary fw-bold"> <?php echo strtoupper($_SESSION['username'])?>,This is your Wishlist❤️</h2>
            
                    <?php while ($row = $result->fetch_assoc()) : ?>
                       <?php $product_id=$row['id'];
                      ?>

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
                                                <h6>Quantity-<?php echo $row['quantity']; ?></h6>
                                                <p data-coreui-precision="0.01" data-coreui-read-only="true" data-coreui-toggle="rating"
                                            data-coreui-value="<?php echo $row['rating_avg'] ?? 0 ?>">
                                            (<?php echo $row['rating_avg'] ?? 0 ?>)</p>
                                        <h6>( <?= $row['review_count'] ?? 0; ?> review)</h6>
                                            </div>
                                            <div class='col-md-6 col-lg-3 col-xl-3 border-sm-start-none border-start'>
                                                <div class='d-flex flex-row align-items-center mb-1'>
                                                    <h4 class='mb-1 me-1'>₹<?php echo $row['Price']; ?>/-</h4>
                                                </div>
                                                
                                                <h6 class='text-success'>Free shipping</h6> 
                                                <br>
                                                <div class='d-inline p-2 m-2'>
                                                    
                                    <form action='productdetail.php' method='GET' class="d-inline p-2">
                                        <input type='hidden' name='id' value=<?php echo $product_id?>>
                                        <button type='submit' class='btn btn-primary btn-sm d-inline p-2'><i class="bi bi-info-circle"></i> Detail</button>
                                    </form>                                            
                                    <form action="" method='post' class="d-inline p-2">
                                        <input type='hidden' name='id' value=<?php echo $product_id?>>
                                        <button type='submit' class='btn btn-danger btn-sm d-inline p-2'><i class="bi bi-trash"></i> Remove</button>
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
                
                
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>