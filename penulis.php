<?php
$conn = new mysqli("localhost", "root", "", "db_artikel");

session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$error = "";
$isEdit = isset($_GET['edit']);
$penulis = ['nickname' => '', 'email' => '', 'password' => ''];

if ($isEdit) {
    $id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM author WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $penulis = $result->fetch_assoc();
    } else {
        die("Penulis tidak ditemukan.");
    }
}

// Proses Tambah/Edit
if (isset($_POST['submit'])) {
    $nickname = trim($_POST['nickname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid!";
    } elseif (strlen($password) < 8 && empty($_POST['id'])) {
        // Hanya wajib isi password minimal 8 karakter saat tambah
        $error = "Password harus minimal 8 karakter!";
    } else {
        if (isset($_POST['id']) && $_POST['id']) {
            $id = intval($_POST['id']);
            if (!empty($password)) {
                if (strlen($password) < 8) {
                    $error = "Password harus minimal 8 karakter!";
                } else {
                    $hashed = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE author SET nickname = ?, email = ?, password = ? WHERE id = ?");
                    $stmt->bind_param("sssi", $nickname, $email, $hashed, $id);
                }
            } else {
                $stmt = $conn->prepare("UPDATE author SET nickname = ?, email = ? WHERE id = ?");
                $stmt->bind_param("ssi", $nickname, $email, $id);
            }
        } else {
            if (strlen($password) < 8) {
                $error = "Password harus minimal 8 karakter!";
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO author (nickname, email, password) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $nickname, $email, $hashed);
            }
        }

        if (empty($error)) {
            $stmt->execute();
            header("Location: penulis.php");
            exit;
        }
    }
}

// Hapus
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM author WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: penulis.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Penulis</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600">
    <link rel="stylesheet" href="css/fontawesome.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/tooplate.css">
</head>
<body id="reportsPage" class="bg02">
<div id="home">
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
                            <li class="nav-item"><a class="nav-link" href="kategori.php">Kategori</a></li>
                            <li class="nav-item active"><a class="nav-link" href="penulis.php">Penulis</a></li>
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

        <!-- Tabel & Form -->
        <div class="row tm-content-row tm-mt-big">
            <div class="col-xl-8 col-lg-12 tm-md-12 tm-sm-12 tm-col">
                <div class="bg-white tm-block h-100">
                    <h2 class="tm-block-title">Daftar Penulis</h2>
                    <div class="table-responsive mt-3">
                        <table class="table table-hover table-striped tm-table-striped-even">
                            <thead>
                                <tr class="tm-bg-gray">
                                    <th>No.</th>
                                    <th>Nama Penulis</th>
                                    <th>Email</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $result = $conn->query("SELECT * FROM author ORDER BY id DESC");
                                $no = 1;
                                while ($row = $result->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['nickname']) ?></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td class="text-center">
                                        <a href="penulis.php?edit=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                        <a href="penulis.php?delete=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus penulis ini?')" class="btn btn-sm btn-danger">Hapus</a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="col-xl-4 col-lg-12 tm-md-12 tm-sm-12 tm-col">
                <div class="bg-white tm-block h-100">
                    <h2 class="tm-block-title"><?= $isEdit ? 'Edit Penulis' : 'Tambah Penulis' ?></h2>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="post">
                        <?php if ($isEdit): ?>
                            <input type="hidden" name="id" value="<?= $penulis['id'] ?>">
                        <?php endif; ?>
                        <div class="form-group">
                            <label>Nama Penulis</label>
                            <input type="text" name="nickname" class="form-control" required value="<?= htmlspecialchars($penulis['nickname']) ?>">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($penulis['email']) ?>">
                        </div>
                        <div class="form-group">
                            <label>Password <?= $isEdit ? '(Kosongkan jika tidak ingin mengubah)' : '' ?></label>
                            <input type="text" name="password" class="form-control" <?= $isEdit ? '' : 'required' ?>>
                        </div>
                        <button type="submit" name="submit" class="btn btn-primary"><?= $isEdit ? 'Update' : 'Simpan' ?></button>
                        <?php if ($isEdit): ?>
                            <a href="penulis.php" class="btn btn-secondary ml-2">Batal</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="row tm-mt-small">
            <div class="col-12 font-weight-light">
                <p class="text-muted"><i class="fas fa-user"></i> Login as: <?= $_SESSION['email'] ?></p>
                <p class="d-inline-block tm-bg-black text-white py-2 px-4">
                    &copy; 2025 Tata's article
                </p>
            </div>
        </footer>
    </div>
</div>

<script src="js/jquery-3.3.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
