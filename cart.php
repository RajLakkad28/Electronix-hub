<?php
include 'config.php';
include 'connection.php';
include 'conn.php';
session_start();

if (!isset($_SESSION['id']) || $_SESSION['email'] == "admin@gmail.com") {
    header("location:login.php");
    exit();
}

$user_id = $_SESSION['id'];

if (isset($_GET['cart_id'])) {
    $cart_id = $_GET['cart_id'];
    $delete_sql = "DELETE FROM cart WHERE id = ? AND user_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("ii", $cart_id, $user_id);
    $delete_stmt->execute();
    header("Location: cart.php");
    exit();
}

$sql = "SELECT cart.id, cart.product_id, products.name, products.filename, products.description, products.price 
        FROM cart 
        JOIN products ON cart.product_id = products.id 
        WHERE cart.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include 'navbar.php' ?>

    <div class="container-fluid mt-4">
        <div class="card shadow-lg p-4">
            <h2 class="text-center text-primary">Your Cart</h2>

            <?php if ($result->num_rows > 0): ?>
                <form action="checkout.php" method="post">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Image</th>
                                <th>Description</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Remove</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $total_price = 0; 
                            while ($row = $result->fetch_assoc()): 
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><img src="<?php echo htmlspecialchars($row['filename']); ?>" width="50"></td>
                                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                                    <td>₹<?php echo number_format($row['price'], 2); ?></td>
                                    <td>
                                        <input type="number" name="quantities[<?php echo $row['product_id']; ?>]" 
                                               value="1" min="1" max="10" class="form-control quantity-input"
                                               data-price="<?php echo $row['price']; ?>" required>
                                    </td>
                                    <td class="total-item-price">₹<?php echo number_format($row['price'], 2); ?></td>
                                    <td>
                                        <a href="cart.php?cart_id=<?php echo $row['id']; ?>" class="btn btn-danger">Remove</a>
                                    </td>
                                </tr>
                                <?php 
                                $total_price += $row['price']; 
                                ?>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    
                    <label>Enter delivery address:</label><br>
                    <textarea name="address" class="form-control" required></textarea>

                    <div class="text-end">
                        <h4>Total: <span id="total-price">₹<?php echo number_format($total_price, 2); ?></span></h4>
                        <input type="hidden" name="total_price" id="hidden-total-price" value="<?php echo $total_price; ?>">
                        <button type="submit" class="btn btn-success">Buy Now</button>
                    </div>
                    
                </form>
            <?php else: ?>
                <p class="text-center text-danger">Your cart is empty.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            function updateTotal() {
                let totalPrice = 0;

                $(".quantity-input").each(function() {
                    let price = parseFloat($(this).data("price"));
                    let quantity = parseInt($(this).val());
                    let itemTotal = price * quantity;

                    $(this).closest("tr").find(".total-item-price").text(`₹${itemTotal.toFixed(2)}`);

                    totalPrice += itemTotal;
                });

                $("#total-price").text(`₹${totalPrice.toFixed(2)}`);
                $("#hidden-total-price").val(totalPrice);
            }

            $(".quantity-input").on("input", function() {
                updateTotal();
            });
        });
    </script>
</body>
</html>
