<?php
// Cek session sesuai dengan login.php asli Anda
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "db_artikel");

$name = "";
$description = "";
$edit_mode = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $id = intval($_POST['id']);
        $conn->query("UPDATE category SET name = '$name', description = '$description' WHERE id = $id");
    } else {
        $conn->query("INSERT INTO category (name, description) VALUES ('$name', '$description')");
    }

    header("Location: kategori.php");
    exit;
}

if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $result = $conn->query("SELECT * FROM category WHERE id = $id");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $name = $row['name'];
        $description = $row['description'];
        $edit_mode = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $edit_mode ? "Edit" : "Tambah" ?> Kategori</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/fontawesome.min.css">
    <link rel="stylesheet" href="css/tooplate.css">
</head>
<body class="bg02">
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
<div class="container mt-5">
    <div class="bg-white tm-block p-4">
        <h2 class="tm-block-title mb-4"><?= $edit_mode ? "Edit" : "Tambah" ?> Kategori</h2>
        <form method="POST" action="">
            <?php if ($edit_mode): ?>
                <input type="hidden" name="id" value="<?= $_GET['edit'] ?>">
            <?php endif; ?>
            <div class="form-group mb-3">
                <label for="name">Nama Kategori</label>
                <input type="text" name="name" id="name" class="form-control" required value="<?= htmlspecialchars($name) ?>">
            </div>
            <div class="form-group mb-3">
                <label for="description">Deskripsi</label>
                <textarea name="description" id="description" class="form-control" rows="4" required><?= htmlspecialchars($description) ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary"><?= $edit_mode ? "Update" : "Simpan" ?></button>
            <a href="kategori.php" class="btn btn-secondary ml-2">Batal</a>
        </form>
    </div>

    <!-- footer -->
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
<script src="js/bootstrap.min.js"></script>
</body>
</html>
