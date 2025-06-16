<?php
$conn = new mysqli("localhost", "root", "", "db_artikel");

session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// Hapus kategori
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM category WHERE id = $id");
    header("Location: kategori.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kategori</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600">
    <link rel="stylesheet" href="css/fontawesome.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/tooplate.css">
    <style>
        td {
            vertical-align: middle;
        }
    </style>
</head>
<body id="reportsPage" class="bg02">
<div class="" id="home">
    <div class="container">
        <!-- Navbar -->
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
                            <li class="nav-item"><a class="nav-link" href="index.php">Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link" href="artikel.php">Artikel</a></li>
                            <li class="nav-item active"><a class="nav-link" href="kategori.php">Kategori</a></li>
                            <li class="nav-item"><a class="nav-link" href="penulis.php">Penulis</a></li>
                        </ul>
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link d-flex" href="logout.php">
                                    <i class="far fa-user mr-2 tm-logout-icon"></i><span>Logout</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>

        <!-- Tabel Kategori -->
        <div class="row tm-content-row tm-mt-big">
            <div class="col-12 tm-col">
                <div class="bg-white tm-block h-100">
                    <div class="row">
                        <div class="col-md-8 col-sm-12">
                            <h2 class="tm-block-title d-inline-block">Kategori</h2>
                        </div>
                        <div class="col-md-4 col-sm-12 text-right">
                            <a href="add-kategori.php" class="btn btn-small btn-primary">Tambah Kategori</a>
                        </div>
                    </div>

                    <div class="table-responsive mt-3">
                        <table class="table table-hover table-striped tm-table-striped-even">
                            <thead>
                                <tr class="tm-bg-gray">
                                    <th>No.</th>
                                    <th>Nama Kategori</th>
                                    <th>Deskripsi</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $result = $conn->query("SELECT * FROM category ORDER BY id DESC");
                                $no = 1;
                                while ($row = $result->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><?= htmlspecialchars($row['description']) ?></td>
                                    <td class="text-center">
                                        <a href="add-kategori.php?edit=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                        <a href="kategori.php?delete=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus kategori ini?')" class="btn btn-sm btn-danger">Hapus</a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="row tm-mt-small">
            <div class="col-12 font-weight-light">
                <p class="text-muted">
                    <i class="fas fa-user"></i> Login as: <?= $_SESSION['email'] ?>
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
