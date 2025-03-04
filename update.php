    <?php
session_start();

if (!isset($_SESSION['email']) || $_SESSION['email'] != "admin@gmail.com") {
    header("Location: login.php");
    exit();
}

?>
<?php
include './conn.php';
include './config.php';
include './connection.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $name = $row["name"];
        $quantity = $row["quantity"];
        $description = $row["description"];
        $price = $row["Price"];
        $old_image = $row["filename"]; 
    } else {
        echo "No results found.";
    }
}   

if (isset($_POST['update'])) {
    $name = $_POST["name"];
    $quantity = $_POST["quantity"];
    $description = $_POST["description"];
    $price = $_POST["Price"];

    if (!empty($_FILES['file']['name'][0])) {

        $allowed_types = ['image/jpeg', 'image/png', 'image/gif']; 
        
        
        $file_type = $_FILES["file"]["type"][0];
        $path = "uploads/"; 
        $new_image = $path . basename($_FILES["file"]["name"][0]);

       
        if (!in_array($file_type, $allowed_types)) {
            echo "Only JPEG, PNG, and GIF files are allowed.";
            exit;
        }

        
        if (file_exists($old_image)) {
            unlink($old_image);
        }

        if (move_uploaded_file($_FILES["file"]["tmp_name"][0], $new_image)) {

            $sql = "UPDATE products SET name=?, quantity=?, description=?, price=?, filename=? WHERE id=?";
            $statement = $connection->prepare($sql);
            $statement->bind_param('sisssi', $name, $quantity, $description, $price, $new_image, $id);
        } else {
            echo "Error uploading the new image.";
            exit;
        }
    } else {
        
        $sql = "UPDATE products SET name=?, quantity=?, description=?, price=? WHERE id=?";
        $statement = $connection->prepare($sql);
        $statement->bind_param('sissi', $name, $quantity, $description, $price, $id);
    }

    if ($statement->execute()) {
            header("Location: " . $_SESSION['current_page']);
            exit(); 
        
    } else {
        echo "Update failed";
    }

    $statement->close();
}
?>
 <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Product</title>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 500px; margin-top: 50px; }
        .preview-img { display: none; width: 100px; height: 100px; object-fit: cover; margin-top: 10px; }
    </style>
</head>
<body>
    <?php include 'adminnavbar.php'?>
<div class="container">
    <div class="card shadow p-4">
        <h3 class="text-center mb-3">Update Product</h3>
        
        <form  method="post" enctype="multipart/form-data" >
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" value="<?php echo $name  ?>"required>
            </div>
            <div class="mb-3">
                <label class="form-label">Quantity</label>
                <input type="number" name="quantity" class="form-control" min="1" value="<?php echo $quantity  ?>"required>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control"  required ><?php echo $description  ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Price</label>
                <input type="number" name="Price" class="form-control" value="<?php echo $price  ?>"required>
            </div>
           
                <input type="file" id="file" name="file[]"    onchange="readURL(this);" multiple><div>
            <div>
                <?php if (!empty($old_image) && file_exists($old_image)): ?>
                    <img id="blah" src="<?php echo $old_image; ?>" style="width: 200px; height: 200px;" />
                <?php else: ?>
                    <img style="width: 200xp;height: 200px;"id="blah" src="#" alt="image not found" />
                <?php endif; ?>
            </div>
            <button type="submit" name="update" class="btn btn-primary w-100">Update</button>
        </form>
    </div>
</div>
<script type="text/javascript">
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#blah').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>   
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>