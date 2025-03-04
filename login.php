<?php
include './conn.php';

$message = "";
$alertClass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];


    $stmt = $conn->prepare("SELECT password, username,id,control FROM userdata WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($db_password, $username,$id,$control);
        $stmt->fetch();

        if ($password === $db_password && $control=="unblock") { 
            $message = "Login successful!";
            $alertClass = "alert-success"; // Success message
            if ($email == "admin@gmail.com") {
                session_start();
                $_SESSION['email'] = $email;
                $_SESSION['username'] = $username;
                
                header("Location: dashboard.php");

                exit();
            } else {    
                session_start();
                $_SESSION['email'] = $email;
                $_SESSION['id'] = $id;
                $_SESSION['username'] = $username;
                header("Location: getProduct.php");
                exit();
            }
        } else if($control=="Block") {
            $message = " user is blocked";
            $alertClass = "alert-danger"; 
        }
        else{
          $message = "Incorrect password!";
            $alertClass = "alert-danger";  // Error message
        }
    } else {
        $message = "Email not found!";
        $alertClass = "alert-warning"; 
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="icon" type="image/png" href="https://ubrainstudios.com/NewSite/image/about-us/aboutIntro-ubrain-logo.webp">
  <title>Login Screen</title>
  <!--     Fonts and icons     -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />
  <!-- Nucleo Icons -->
  <link href="assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <!-- Material Icons -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
  <!-- Sweet Alert -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.5/dist/sweetalert2.min.css">
  <!-- CSS Files -->
  <link id="pagestyle" href="assets/css/material-dashboard.css" rel="stylesheet" />
</head>

<body class="bg-light">


    <?php if ($message != ""): ?>
    <div class="alert <?= $alertClass ?> alert-dismissible text-white" role="alert" style="font-size: 0.875rem; max-width: 300px; margin: 0 auto;">
        <span class="text-sm"><?= $message ?></span>
        <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php endif; ?>

    <main class="main-content mt-0">
    <div class="page-header align-items-start min-height-300 m-3 border-radius-xl" style="background-image: url('https://images.unsplash.com/photo-1491466424936-e304919aada7?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1949&q=80');">
      <span class="mask bg-gradient-dark opacity-6"></span>
    </div>
    <div class="container mb-4">
      <div class="row mt-lg-n12 mt-md-n12 mt-n12 justify-content-center">
        <div class="col-xl-4 col-lg-5 col-md-7 mx-auto">
          <div class="card mt-8">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-success shadow-success border-radius-lg py-3 pe-1 text-center py-4">
                <h4 class="font-weight-bolder text-white mt-1">Sign In</h4>
                <p class="mb-1 text-sm text-white">Enter your email and password to Sign In</p>
              </div>
            </div>
            <div class="card-body">
              <form role="form" class="text-start" action="" method="post">
                <div class="input-group input-group-static mb-4">
                  <label>Email</label>
                  <input type="email" name='email' class="form-control" placeholder="john@email.com" required>
                </div>
                <div class="input-group input-group-static mb-4">
                  <label>Password</label>
                  <input type="password" name='password' class="form-control" placeholder="•••••••••••••" required>
                </div>
                <div class="form-check form-switch d-flex align-items-center mb-3">
                  <input class="form-check-input" type="checkbox" id="rememberMe" checked="">
                  <label class="form-check-label mb-0 ms-3" for="rememberMe">Remember me</label>
                </div>
                <div class="text-center">
                  <button type="submit" class="btn bg-gradient-success w-100 mt-3 mb-0">Sign in</button>
                </div>
              </form>
            </div>
            <div class="card-footer text-center pt-0 px-lg-2 px-1">
              <p class="card-footer text-center pt-0 px-sm-4 px-1">
                Don't have an account?
                <a href="./register.php" class="text-primary text-gradient font-weight-bold ">Sign up</a>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="assets/js/core/popper.min.js"></script>
    <script src="assets/js/core/bootstrap.min.js"></script>
    <script src="assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="assets/js/plugins/smooth-scrollbar.min.js"></script>
    <script src="assets/js/material-dashboard.min.js"></script>
</body>

</html>
