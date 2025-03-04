<?php
session_start();

if (!isset($_SESSION['email']) || $_SESSION['email'] != "admin@gmail.com") {
    header("Location: login.php");
    exit();
}
include 'config.php';
include 'connection.php';

$productSaved = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productName = trim($_POST['name'] ?? '');
    $productQuantity = (int) ($_POST['quantity'] ?? 0);
    $productDescription = trim($_POST['description'] ?? '');
    $productPrice = trim($_POST['price'] ?? '');
    $filenamesToSave = '';

    if (!$productName) $errors[] = 'Please provide a product name.';
    if ($productQuantity <= 0) $errors[] = 'Please provide a valid quantity.';
    if (!$productDescription) $errors[] = 'Please provide a description.';
    if (!$productPrice || !is_numeric($productPrice)) $errors[] = 'Please provide a valid price.';

    if (empty($errors) && !empty($_FILES['file']['name'][0])) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        
        $fileTmpPath = $_FILES['file']['tmp_name'][0];
        $fileName = time() . '_' . $_FILES['file']['name'][0];
        $destPath = $uploadDir . $fileName;
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($fileExtension, $allowedExtensions)) {
            $errors[] = 'Invalid file type. Only JPG, JPEG, PNG, or GIF allowed.';
        } elseif (!move_uploaded_file($fileTmpPath, $destPath)) {
            $errors[] = 'Error uploading file.';
        } else {
            $filenamesToSave = $destPath;
        }
    }

    if (empty($errors)) {
        $sql = 'INSERT INTO products (name, quantity, description, price, filename) VALUES (?, ?, ?, ?, ?)';
        $stmt = $connection->prepare($sql);
        $stmt->bind_param('sisss', $productName, $productQuantity, $productDescription, $productPrice, $filenamesToSave);
        $stmt->execute();
        $stmt->close();
        $connection->close();
        $productSaved = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Product</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 500px; margin-top: 50px; }
        .preview-img { display: none; width: 100px; height: 100px; object-fit: cover; margin-top: 10px; }
    </style>
</head>
<body>
    
    <?php include 'adminnavbar.php';?>
<div class="container">
    <div class="card shadow p-4">
        <h3 class="text-center mb-3">Add Product</h3>
        <?php if ($productSaved): ?>
            <div class="alert alert-success">Product added successfully.</div>
        <?php elseif (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul><?php foreach ($errors as $error) echo "<li>$error</li>"; ?></ul>
            </div>
        <?php endif; ?>

        <form action="" method="post" enctype="multipart/form-data" id="productForm">
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Quantity</label>
                <input type="number" name="quantity" class="form-control" min="1" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Price</label>
                <input type="text" name="price" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Image</label>
                <input type="file" name="file[]" class="form-control" accept="image/*" onchange="previewImage(event)" required>
                <img id="preview" class="preview-img" />
            </div>
            <button type="submit" class="btn btn-primary w-100">Submit</button>
        </form>
    </div>
</div>
<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function () {
            const img = document.getElementById('preview');
            img.src = reader.result;
            img.style.display = 'block';
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>





