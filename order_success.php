<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #a8edea, #fed6e3);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Arial', sans-serif;
        }
        .card {
            max-width: 450px;
            text-align: center;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            background: #ffffff;
            animation: fadeIn 0.8s ease-in-out;
        }
        .checkmark {
            font-size: 60px;
            color: #28a745;
            animation: popIn 0.5s ease-in-out;
        }
        @keyframes popIn {
            0% { transform: scale(0.5); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(-20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .btn {
            border-radius: 25px;
            padding: 12px 25px;
            font-weight: bold;
            transition: 0.3s ease-in-out;
        }
        .btn:hover {
            background: #218838;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="checkmark">✔</div>
        <h2 class="mt-3">Payment Successful!</h2>
        <p>Your order has been placed successfully. We appreciate your purchase!</p>
        <a href="getproduct.php" class="btn btn-success mt-3">Continue Shopping</a>
    </div>
</body>
</html>
