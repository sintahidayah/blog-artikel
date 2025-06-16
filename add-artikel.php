<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "db_artikel");

// Cek mode edit
$isEdit = isset($_GET['edit']);
$article = [];
$current_category_id = null;
$current_author_id = null;

if ($isEdit) {
    $id = intval($_GET['edit']);
    $article = $conn->query("SELECT * FROM article WHERE id = $id")->fetch_assoc();
    if (!$article) die("Artikel tidak ditemukan.");
    $cat = $conn->query("SELECT category_id FROM article_category WHERE article_id = $id")->fetch_assoc();
    $author = $conn->query("SELECT author_id FROM article_author WHERE article_id = $id")->fetch_assoc();
    $current_category_id = $cat ? $cat['category_id'] : null;
    $current_author_id = $author ? $author['author_id'] : null;
}

// Fetch dropdown data
$categories = $conn->query("SELECT * FROM category");
$authors = $conn->query("SELECT * FROM author");

// Simpan
if (isset($_POST['submit'])) {
    $title = $_POST['title'];
    $date = $_POST['date'];
    $content = $_POST['content'];
    $category_id = intval($_POST['category']);
    $author_id = intval($_POST['author']);
    $picture = $isEdit ? $article['picture'] : null;

    if ($_FILES['picture']['name']) {
        $picture = basename($_FILES["picture"]["name"]);
        move_uploaded_file($_FILES["picture"]["tmp_name"], "img/" . $picture);
    }

    if ($isEdit) {
        $conn->query("UPDATE article SET title='$title', date='$date', content='$content', picture='$picture' WHERE id=$id");
        $conn->query("UPDATE article_category SET category_id=$category_id WHERE article_id=$id");
        $conn->query("UPDATE article_author SET author_id=$author_id WHERE article_id=$id");
    } else {
        $conn->query("INSERT INTO article (title, date, content, picture) VALUES ('$title', '$date', '$content', '$picture')");
        $newId = $conn->insert_id;
        $conn->query("INSERT INTO article_category (article_id, category_id) VALUES ($newId, $category_id)");
        $conn->query("INSERT INTO article_author (article_id, author_id) VALUES ($newId, $author_id)");
    }

    header("Location: artikel.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $isEdit ? 'Edit' : 'Tambah' ?> Artikel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/fontawesome.min.css">
    <link rel="stylesheet" href="css/tooplate.css">

    <style>
        select.form-control, input[type="file"].form-control-file {
            height: auto;
            padding: 8px 12px;
            font-size: 16px;
        }
        select option:first-child {
            color: #888;
        }
        .form-group label {
            font-weight: 500;
        }
    </style>
</head>

<body class="bg02">
<!-- Navbar -->
<div class="row">
    <div class="col-12">
        <nav class="navbar navbar-expand-xl navbar-light bg-light">
            <a class="navbar-brand" href="#"><i class="fas fa-3x fa-tachometer-alt tm-site-icon"></i>
                <h1 class="tm-site-title mb-0">Dashboard</h1>
            </a>
            <button class="navbar-toggler ml-auto mr-0" type="button" data-toggle="collapse" data-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Dashboard</a></li>
                    <li class="nav-item active"><a class="nav-link" href="artikel.php">Artikel</a></li>
                    <li class="nav-item"><a class="nav-link" href="kategori.php">Kategori</a></li>
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

<!-- Form Artikel -->
<div class="container mt-5">
    <div class="bg-white tm-block p-4">
        <h2 class="tm-block-title mb-4"><?= $isEdit ? 'Edit' : 'Tambah' ?> Artikel</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group mb-3">
                <label>Judul</label>
                <input type="text" name="title" class="form-control" required value="<?= $isEdit ? htmlspecialchars($article['title']) : '' ?>">
            </div>
            <div class="form-group mb-3">
                <label>Tanggal dan Waktu</label>
                <input type="datetime-local" id="dateInput" name="date"
                    class="form-control" required
                    value="<?= $isEdit && isset($article['date']) ? date('Y-m-d\TH:i', strtotime($article['date'])) : '' ?>">
            </div>
            <div class="form-group mb-3">
                <label for="category">Kategori</label>
                <select name="category" id="category" class="form-control" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $c['id'] == $current_category_id ? 'selected' : '' ?>>
                            <?= $c['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group mb-3">
                <label for="author">Penulis</label>
                <select name="author" id="author" class="form-control" required>
                    <option value="">-- Pilih Penulis --</option>
                    <?php foreach ($authors as $a): ?>
                        <option value="<?= $a['id'] ?>" <?= $a['id'] == $current_author_id ? 'selected' : '' ?>>
                            <?= $a['nickname'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group mb-3">
                <label>Konten</label>
                <textarea id="editor" name="content" class="form-control" rows="6" required><?= $isEdit ? htmlspecialchars($article['content']) : '' ?></textarea>
            </div>
            <div class="form-group mb-3">
                <label>Gambar <?= $isEdit ? 'Saat Ini' : '' ?></label><br>
                <?php if ($isEdit && $article['picture']): ?>
                    <img src="img/<?= $article['picture'] ?>" width="150" class="mb-2"><br>
                <?php endif; ?>
                <input type="file" name="picture" class="form-control-file" accept=".jpg,.jpeg,.png,.gif">
            </div>
            <button type="submit" name="submit" class="btn btn-primary">
                <?= $isEdit ? 'Simpan Perubahan' : 'Simpan Artikel' ?>
            </button>
            <a href="artikel.php" class="btn btn-secondary ml-2">Batal</a>
        </form>
    </div>

    <footer class="row tm-mt-small mt-5">
        <div class="col-12 font-weight-light">
            <p class="text-muted"><i class="fas fa-user"></i> Login as: <?= $_SESSION['email'] ?></p>
            <p class="d-inline-block tm-bg-black text-white py-2 px-4">
                Copyright &copy; 2025. Tata's article
            </p>
        </div>
    </footer>
</div>

<!-- JS: jQuery, Bootstrap, CKEditor -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.ckeditor.com/4.22.1/full-all/ckeditor.js"></script>

<script src="https://cdn.ckeditor.com/4.22.1/full-all/ckeditor.js"></script>
<script>
    CKEDITOR.replace('editor', {
        height: 300,
        extraPlugins: 'justify,colorbutton,sourcearea',
        removeButtons: '',
        toolbar: [
            { name: 'document', items: [ 'Source', '-', 'Preview', 'Print' ] },
            { name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', '-', 'Undo', 'Redo' ] },
            { name: 'editing', items: [ 'Find', 'Replace', '-', 'SelectAll' ] },
            { name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
            { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
            '/',
            { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat' ] },
            { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
            { name: 'links', items: [ 'Link', 'Unlink' ] },
            { name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule', 'SpecialChar' ] }
        ]
    });

    <?php if (!$isEdit): ?>
    document.addEventListener("DOMContentLoaded", function() {
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        document.getElementById('dateInput').value = `${yyyy}-${mm}-${dd}`;
    });
    <?php endif; ?>
</script>
</body>
</html>
