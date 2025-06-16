<?php
session_start();
$conn = new mysqli("localhost", "root", "", "db_artikel");

$error = "";

// Jika form dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

        // Cek ke database author
    $stmt = $conn->prepare("SELECT * FROM author WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $password); // untuk keamanan lebih, gunakan password_hash & password_verify
    $stmt->execute();
    $result = $stmt->get_result();

    // Login sukses
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $_SESSION['login'] = true;
        $_SESSION['author_id'] = $user['id'];
        $_SESSION['nickname'] = $user['nickname'];
        $_SESSION['email'] = $user['email'];

        header("Location: index.php");
        exit;
    } else {
        $error = "Email atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSS -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600">
    <link rel="stylesheet" href="css/fontawesome.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/tooplate.css">
</head>
<body class="bg03">
<div class="container">
    <div class="row tm-mt-big">
        <div class="col-12 mx-auto tm-login-col">
            <div class="bg-white tm-block">
                <div class="row">
                    <div class="col-12 text-center">
                        <i class="fas fa-3x fa-tachometer-alt tm-site-icon text-center"></i>
                        <h2 class="tm-block-title mt-3">Login</h2>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        <form action="" method="post" class="tm-login-form">
                            <div class="input-group">
                                <label for="email" class="col-xl-4 col-lg-4 col-md-4 col-sm-5 col-form-label">Email</label>
                                <input name="email" type="email" class="form-control validate col-xl-9 col-lg-8 col-md-8 col-sm-7" id="email" required>
                            </div>
                            <div class="input-group mt-3">
                                <label for="password" class="col-xl-4 col-lg-4 col-md-4 col-sm-5 col-form-label">Password</label>
                                <input name="password" type="password" class="form-control validate" id="password" required>
                            </div>
                            <div class="input-group mt-4 justify-content-center">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                            <div class="input-group mt-3 text-center">
                                <small>Gunakan email dan password dari tabel <strong>author</strong></small>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="row tm-mt-small">
                <div class="col-12 font-weight-light">
                    <p class="d-inline-block tm-bg-black text-white py-2 px-4">
                        Copyright &copy; 2025. Tata's article
                    </p>
                </div>
            </footer>
</div>
</body>
</html>