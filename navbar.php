<?php


include 'conn.php';
$user_id = $_SESSION['id'];
$sqla = "select count(*) as count from cart where user_id=? ";
$stmta = $conn->prepare($sqla);
$stmta->bind_param("i", $user_id);
$stmta->execute();
$resulta = $stmta->get_result();
while ($rowa = $resulta->fetch_assoc()) {
    $count = $rowa['count'];
}
$order_count="select count(*) as ordercount from orders join order_items ON orders.id=order_items.order_id where user_id=?  ";
$stmtab=$conn->prepare($order_count);
$stmtab->bind_param("i", $user_id);
$stmtab->execute();
$rsultab=$stmtab->get_result();
while($rowab=$rsultab->fetch_assoc()) {
    $ordercount=$rowab['ordercount'];
}
$wishlist_count="select count(*) as wishlistcount from wishlist where user_id=?  ";
$stmtabc=$conn->prepare($wishlist_count);
$stmtabc->bind_param("i", $user_id);
$stmtabc->execute();
$rsultabc=$stmtabc->get_result();
while($rowabc=$rsultabc->fetch_assoc()) {
    $wishlistcount=$rowabc['wishlistcount'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Product Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        integrity="sha384-tViUnnbYAV00FLIhhi3v/dWt3Jxw4gZQcNoSCxCIFNJVCx7/D55/wXsrNIRANwdD" crossorigin="anonymous">
    <link href="https://use.fontawesome.com/releases/v5.0.1/css/all.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    

</head>
<style>
    label{
        font-size: 25px;
        
    }
    a.hover:hover {

  font-size: 18px;
}
</style>
<body>
    <nav class="navbar navbar-expand-sm navbar-light bg-secondary " style="background-color: #e3f2fd;">
        <div class="container-fluid">
            <a class="navbar-brand text-white" href="#" style="font-weight:bold; color:white;">Electronix Hub</a>
            <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse"
                data-bs-target="#collapsibleNavId" aria-controls="collapsibleNavId" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="collapsibleNavId">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active text-white hover" aria-current="page" href="getproduct.php">Home</a>
                    </li>
                    
                </ul>
                <label class="text-white"><i class="bi bi-person"></i><?php echo $_SESSION['username']?></label>
                <a href="./cart.php" role="button" class=" btn btn-light position-relative hover"
                    style="font-weight:bolder;color:green;margin-left:10px">
                    <i class="bi bi-cart"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        <?php echo $count ?>
                    </span>
                     Cart
                </a>
                <a href="./orders.php" role="button" class=" btn btn-light position-relative hover"
                    style="font-weight:bolder;color:green;margin-left:10px">
                    <i class="bi bi-shop"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        <?php echo $ordercount ?>
                    </span>
                     orders
                </a>    
                <a href="./wishlist.php" role="button" class=" btn btn-light position-relative hover"
                    style="font-weight:bolder;color:green;margin-left:10px">
                    <i class="bi bi-heart"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        <?php echo $wishlistcount ?>
                    </span>
                     Wishlist
                </a> 
                <a href="./logout.php" class="btn btn-light my-2 my-sm-0 ms-2 hover" type="submit"
                    style="font-weight:bolder;color:green;margin-left:20px"><i class="bi bi-box-arrow-right"></i> logout</a>
                

            </div>
        </div>
    </nav>
</body>

</html>