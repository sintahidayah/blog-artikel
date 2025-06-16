<?php
// Cek session sesuai dengan login.php asli Anda
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600">
    <link rel="stylesheet" href="css/fontawesome.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/tooplate.css">
</head>

<body id="reportsPage">
    <div class="" id="home">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav class="navbar navbar-expand-xl navbar-light bg-light">
                        <a class="navbar-brand" href="#">
                            <i class="fas fa-3x fa-tachometer-alt tm-site-icon"></i>
                            <h1 class="tm-site-title mb-0">Dashboard</h1>
                        </a>
                        <button class="navbar-toggler ml-auto mr-0" type="button" data-toggle="collapse"
                            data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav mx-auto">
                                <li class="nav-item">
                                    <a class="nav-link active" href="index.php">Dashboard<span
                                            class="sr-only">(current)</span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="artikel.php">Artikel</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="kategori.php">Kategori</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="penulis.php">Penulis</a>
                                </li>
                            </ul>
                            <ul class="navbar-nav">
                                <li class="nav-item">
                                    <a class="nav-link d-flex" href="logout.php">
                                        <i class="far fa-user mr-2 tm-logout-icon"></i>
                                        <span>Logout</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
            <!-- row -->
            <div class="row tm-content-row tm-mt-big justify-content-center">
                <div class="col-md-6">
                    <div class="bg-white tm-block text-center py-5">
                        <h2 class="tm-block-title mb-4">Manajemen Konten</h2>
                        <a href="artikel.php" class="btn btn-primary btn-block mb-3">Kelola Artikel</a>
                        <a href="kategori.php" class="btn btn-info btn-block mb-3">Kelola Kategori</a>
                        <a href="penulis.php" class="btn btn-secondary btn-block">Kelola Penulis</a>
                    </div>
                </div>
            </div>

            <footer class="row tm-mt-small">
                <div class="col-12 font-weight-light">
                    <p class="text-muted">
                        <i class="fas fa-user"></i> 
                        Login as: <?= $_SESSION['email'] ?>
                    </p>
                    <p class="d-inline-block tm-bg-black text-white py-2 px-4">
                        Copyright &copy; 2025. Tata's article
                    </p>
                </div>
            </footer>
        </div>
    </div>

    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>

</html>