<?php
include 'config.php';
include 'connection.php';
include 'conn.php';
session_start();

if (!isset($_SESSION['email']) || $_SESSION['email'] == "admin@gmail.com") {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['id'])) {
    $_SESSION['alert_message'] = "Please log in first.";
    $_SESSION['alert_status'] = "error";
    header("Location: login.php");
    exit();
}

$errors = [];
$message = "";
$status = "";
$wishlistmessage = "";
$wishliststatus = "";
$user_id = $_SESSION['id'];


if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("s", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $name = $row["name"];
        $quantity = $row["quantity"];
        $description = $row["description"];
        $price = $row["Price"];
        $image = $row["filename"];
    } else {
        echo "No results found.";
        exit();
    }
}
if (!$product_id) {
    echo "Invalid product ID.";
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) {
    $checkCart = $conn->prepare("SELECT id FROM cart WHERE user_id = ? AND product_id = ?");
    $checkCart->bind_param("ii", $user_id, $product_id);
    $checkCart->execute();
    $result = $checkCart->get_result();

    if ($result->num_rows > 0) {
        $message = "Product already in cart!";
        $status = "warning";
    } else {
        $insertCart = $conn->prepare("INSERT INTO cart (user_id, product_id) VALUES (?, ?)");
        $insertCart->bind_param("ii", $user_id, $product_id);
        $insertCart->execute();
        $message = "Product added to cart!";
        $status = "success";
    }
}
$check = $conn->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
$check->bind_param("ii", $user_id, $product_id);
$check->execute();
$checkresult = $check->get_result();
$in_wishlist = $checkresult->num_rows > 0;
$color = $in_wishlist ? "red" : "white";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_wishlist'])) {
    if ($in_wishlist) {
        $deletewishlist = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
        $deletewishlist->bind_param("ii", $user_id, $product_id);
        $deletewishlist->execute();
        $wishlistmessage = "Removed from your wishlist";
        $wishliststatus = "success";
        $color = "white";
    } else {
        $insertwishlist = $conn->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
        $insertwishlist->bind_param("ii", $user_id, $product_id);
        $insertwishlist->execute();
        $wishlistmessage = "Added to your wishlist";
        $wishliststatus = "success";
        $color = "red";
    }

}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_review'])) {
    $rating = $_POST['rating'];
    $review = trim($_POST['review']);

    $checkReview = $conn->prepare("SELECT id FROM rating WHERE user_id = ? AND product_id = ?");
    $checkReview->bind_param("ii", $user_id, $product_id);
    $checkReview->execute();
    $result = $checkReview->get_result();
    $updatereview = $checkReview->num_rows > 0;
    if ($result->num_rows > 0) {

        $updateReview = $conn->prepare("UPDATE rating SET rating = ?, review = ? WHERE user_id = ? AND product_id = ?");
        $updateReview->bind_param("isii", $rating, $review, $user_id, $product_id);
        $updateReview->execute();
        $message = "Review updated successfully!";
    } else {

        $insertReview = $conn->prepare("INSERT INTO rating (user_id, product_id, rating, review) VALUES (?, ?, ?, ?)");
        $insertReview->bind_param("iiis", $user_id, $product_id, $rating, $review);
        $insertReview->execute();
        $message = "Review submitted successfully!";
    }

    $status = "success";
}

if (isset($_GET['id'])) {

    $fetchreview = $conn->prepare("SELECT rating.*,userdata.username FROM rating join userdata on userdata.id=rating.user_id WHERE product_id = ? order by rating.id desc");
    $fetchreview->bind_param("s", $product_id);
    $fetchreview->execute();
    $fetchresult = $fetchreview->get_result();
}
if (isset($_GET['id'])) {
    $updatestmt = $conn->prepare("SELECT rating.*,userdata.username FROM rating join userdata on userdata.id = rating.user_id WHERE product_id = ? and user_id=?");
    $updatestmt->bind_param("si", $product_id, $user_id);
    $updatestmt->execute();
    $updateresult = $updatestmt->get_result();

    if ($updateresult->num_rows > 0) {
        $updaterow = $updateresult->fetch_assoc();
        $updatereview = $updaterow['review'];
        $updaterating = $updaterow['rating'];
    }
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
    <link href="https://cdn.jsdelivr.net/npm/@coreui/coreui@5.3.1/dist/css/coreui.min.css" rel="stylesheet"
        integrity="sha384-PDUiPu3vDllMfrUHnurV430Qg8chPZTNhY8RUpq89lq22R3PzypXQifBpcpE1eoB" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@coreui/coreui-pro@5.10.0/dist/css/coreui.min.css" rel="stylesheet"
        integrity="sha384-iFYnumxmAfPWEvBBHVgQ1pcH7Bj9XLrhznQ6DpVFtF3dGwlEAqe4cmd4NY4cJALM" crossorigin="anonymous">
    <script defer src="https://cdn.jsdelivr.net/npm/@coreui/coreui-pro@5.10.0/dist/js/coreui.bundle.min.js"></script>

</head>

<body>
    <?php include 'navbar.php'; ?>
    <section class="py-5">
        <div class="container">
            <div class="card shadow-lg p-4">
                <div class="row gx-5">
                    <aside class="col-lg-5">
                        <div class="border rounded-4 mb-3 d-flex justify-content-center">
                            <a data-fslightbox="mygalley" class="rounded-4" target="_blank" data-type="image"
                                href="<?php echo $image; ?>">
                                <img style="max-width: 100%; max-height: 100vh; margin: auto;" class="rounded-4 fit"
                                    src="<?php echo $image; ?>" />
                            </a>
                        </div>
                    </aside>
                    <main class="col-lg-6">
                        <div class="ps-lg-3">
                            <h4 class="title text-dark"><?php echo $name; ?></h4>
                            <div class="d-flex flex-row my-3">
                                <span class="text-success ms-2">In stock</span>
                            </div>
                            <div class="mb-3">
                                <span class="h5"><?php echo $price; ?></span>
                                <span class="text-muted">/-</span>
                            </div>
                            <p><?php echo $description; ?></p>
                            <hr />
                            <a href="cart.php" class="btn btn-warning shadow-0"> Buy now </a>
                            <form method="post" class='d-inline p-2'>

                                <button type="submit" name="add_to_cart" class="btn btn-primary shadow-0">
                                    <i class="me-1 fa fa-shopping-basket"></i> Add to cart
                                </button>
                                <button type="submit" name="add_to_wishlist" class="btn btn-primary shadow-0">
                                    <i class="bi bi-heart-fill" style="color: <?php echo $color; ?>;"></i> Wishlist
                                </button>
                            </form>

                        </div>
                </div>
                <form method="post" class="mt-4">
                    <h5>Rate this product:</h5>
                    <div>
                        <div class="rateyo" data-rateyo-rating="<?php echo $updaterating ?? 1 ?>"
                            data-rateyo-num-stars="5"></div>
                        <span class="result">Rating: 1</span>
                        <input type="hidden" name="rating" id="ratingValue" value="1">
                    </div>
                    <label for="review" class="mt-2">Write a review:</label>
                    <textarea name="review" id="review" class="form-control" rows="3"
                        required><?php echo $updatereview ?? "" ?></textarea>

                    <button type="submit" name="submit_review" class="btn btn-primary mt-2">Submit Review</button>
                </form>
                </main>
            </div>
            <?php if ($fetchresult->num_rows > 0): ?>
                <?php while ($fetchrow = $fetchresult->fetch_assoc()): ?>
                    <div class="card shadow-lg p-4 ">
                        <h5><img src="https://img.icons8.com/?size=100&id=x0qTmzjcFRhW&format=png&color=000000" alt="person"
                                width="35" height="35"> <?= $fetchrow['username']; ?></h5>
                        <div data-coreui-read-only="true" data-coreui-toggle="rating"
                            data-coreui-value="<?php echo $fetchrow['rating']; ?>">&nbsp; &nbsp; &nbsp;&nbsp;&nbsp;</div>
                        <p>&nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;<?= $fetchrow['review'] ?></p>

                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="card shadow-lg p-4">
                    <h4 class="text-center text-danger">no review found

                    </h4>
                </div>

            <?php endif; ?>
        </div>
    </section>

    <?php if ($message): ?>
        <script>
            Swal.fire({
                toast: true,
                position: 'top',
                icon: '<?php echo $status; ?>',
                title: '<?php echo $message; ?>',
                showConfirmButton: false,
                timer: 2000
            });
        </script>
    <?php endif; ?>
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
    <script>
        $(function () {
            $(".rateyo").rateYo({
                starWidth: "40px",
                fullStar: true
            }).on("rateyo.change", function (e, data) {
                var rating = Math.round(data.rating);
                $(this).parent().find('.result').text('Rating: ' + rating);
                $("#ratingValue").val(rating);
            });
        });

    </script>
</body>

</html>