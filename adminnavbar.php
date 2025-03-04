<?php

include 'conn.php';
// $user_id=$_SESSION['id_user'];
$order_count="select count(*) as ordercount from order_items";
$order_counts=$conn->prepare($order_count);
$order_counts->execute();
$count_result=$order_counts->get_Result();
while($count=$count_result->fetch_assoc()){
    $ordercount=$count['ordercount'];
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" integrity="sha384-tViUnnbYAV00FLIhhi3v/dWt3Jxw4gZQcNoSCxCIFNJVCx7/D55/wXsrNIRANwdD" crossorigin="anonymous">

    <title>Dashboard</title>
</head>
<style>
    nav{
        color: white;
    }
    a.hover:hover {

font-size: 18px;
}
</style>
<body>
    <nav class="navbar navbar-expand-sm navbar-light bg-secondary text-white" >
        <div class="container-fluid">   
            <a class="navbar-brand text-white" href="dashboard.php">Electronix Hub</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active text-white hover" aria-current="page" href="dashboard.php">Home</a>
                    </li>
                    
                </ul>
                <form class="d-flex my-2 my-lg-0">
                    <a href="./addProduct.php" class="btn btn-light my-2 my-sm-0 hover" type="submit"
                        style="font-weight:bolder;color:green;margin-left:20px">
                        <i class="bi-plus-circle-fill"></i> ADD </a>
                </form>
                <form class="d-flex my-2 my-lg-0">
                    <a href="./updateproduct.php" class="btn btn-light my-2 my-sm-0 hover" type="submit"
                        style="font-weight:bolder;color:green;margin-left:20px">
                        <i class="bi-pencil-square"></i>  Products</a>
                </form>
                <a href="./admin_orders.php" role="button" class=" btn btn-light position-relative hover"
                    style="font-weight:bolder;color:green;margin-left:20px">
                    <i class="bi bi-shop"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        <?php echo $ordercount ?>
                    </span>
                     orders
                </a>
                <form class="d-flex my-2 my-lg-0">
                    <a href="logout.php" class="btn btn-light my-2 my-sm-0 hover" type="submit"
                        style="font-weight:bolder;color:green;margin-left:20px">
                        <i class="bi bi-box-arrow-right"></i> logout</a>
                </form>
            </div>
        </div>
        
    </nav>
</body>
</html>