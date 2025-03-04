<?php
include './conn.php';

$message = "";
$toastClass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];


    $checkEmailStmt = $conn->prepare("SELECT email FROM userdata WHERE email = ?");
    $checkEmailStmt->bind_param("s", $email);
    $checkEmailStmt->execute();
    $checkEmailStmt->store_result();

    if ($checkEmailStmt->num_rows > 0) {
        $message = "Email ID already exists";
        $toastClass = "alert-danger";
    } else {

        $stmt = $conn->prepare("INSERT INTO userdata (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password);

        if ($stmt->execute()) {
            $message = "Account created successfully";
            $toastClass = "alert-success";
        } else {
            $message = "Error: " . $stmt->error;
            $toastClass = "alert-danger";
        }

        $stmt->close();
    }

    $checkEmailStmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png"
        href="https://ubrainstudios.com/NewSite/image/about-us/aboutIntro-ubrain-logo.webp">
    <title>Sign up Screen</title>
    <!--     Fonts and icons     -->
    <link rel="stylesheet" type="text/css"
        href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />
    <!-- Nucleo Icons -->
    <link href="assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="assets/css/nucleo-svg.css" rel="stylesheet" />
    
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
        <div class="alert <?= $toastClass ?> alert-dismissible text-white p-2" role="alert"
            style="font-size: 0.875rem; max-width: 300px; margin: 0 auto;">
            <span class="text-sm"><?= $message ?></span>
            <button type="button" class="btn-close text-lg py-2 opacity-10" data-bs-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>


    <main class="main-content main-content-bg mt-0">
        <div class="page-header align-items-start min-height-300 m-3 border-radius-xl bg-gray-200"
            style="background-image: url('https://images.unsplash.com/photo-1545569341-9eb8b30979d9?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80'); background-size: cover; background-position:center;">
            <span class="mask bg-gradient-dark opacity-4"></span>
        </div>
        <div class="container">
            <div class="row mt-lg-n12 mt-md-n12 mt-n11 justify-content-center">
                <div class="col-xl-4 col-lg-5 col-md-7 mx-auto">
                    <div class="card mt-8">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-success shadow-sucess border-radius-lg py-3 pe-1 text-center py-4">
                                <h4 class="font-weight-bolder text-white mt-1">Join us today</h4>
                                <p class="mb-1 text-white text-sm">Enter your email and password to register</p>
                            </div>
                        </div>
                        <div class="card-body pb-3">
                            <form action="" method="POST">
                                <div class="input-group input-group-outline mb-3">
                                    <input type="text" name="username" class="form-control " placeholder="Name"
                                        required>
                                </div>
                                <div class="input-group input-group-outline mb-3">
                                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                                </div>
                                <div class="input-group input-group-outline mb-3">
                                    <input type="password" name="password" class="form-control" placeholder="password"
                                        required>
                                </div>
                                <div class="form-check text-left ps-0">
                                    <input class="form-check-input bg-dark border-dark" type="checkbox" value=""
                                        id="flexCheckDefault" checked>
                                    <label class="form-check-label" for="flexCheckDefault">
                                        I agree the <a href="#" class="text-dark font-weight-bolder">Terms and
                                            Conditions</a>
                                    </label>
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn bg-gradient-success w-100 mt-4 mb-0">Sign
                                        up</button>
                                </div>
                            </form>
                        </div>
                        <div class="card-footer text-center pt-0 px-sm-4 px-1">
                            <p class="mb-4 mx-auto">
                                Already have an account?
                                <a href="login.php" class="text-primary text-gradient font-weight-bold">Sign in</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>