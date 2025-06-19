<?php
$conn = new mysqli("localhost", "root", "", "db_artikel");

$category_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil nama kategori
$category_name = "Kategori Tidak Ditemukan";
$category_result = $conn->query("SELECT name FROM category WHERE id = $category_id");
if ($row = $category_result->fetch_assoc()) {
    $category_name = $row['name'];
}

// Ambil artikel berdasarkan kategori
$sql = "
  SELECT a.id, a.title, a.date, a.picture, a.content,
         au.nickname AS author_name
  FROM article a
  LEFT JOIN article_category ac ON a.id = ac.article_id
  LEFT JOIN article_author aa ON a.id = aa.article_id
  LEFT JOIN author au ON aa.author_id = au.id
  WHERE ac.category_id = $category_id
  ORDER BY a.date DESC
";
$articles = $conn->query($sql);

$search = $_GET['search'] ?? '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($category_name) ?> - Tata's Artikel</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: rgb(40, 72, 52);
    }
    .navbar {
      background-color: rgb(230, 240, 239);
    }
    .navbar-brand, .nav-link {
      color: grey !important;
      font-weight: 500;
    }
    .navbar-brand {
      font-weight: 700;
    }
    .blog-header {
      background: linear-gradient(to right,rgb(93, 171, 226),rgb(216, 214, 218));
      background-image: url(../img/bg3.jpg);
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      color: white;
      padding: 3rem 0;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 400px;
      width: 100%;
      margin-bottom: 60px;
    }
    .content-container {
      padding: 40px 0;
    }
    .article-card {
      background: white;
      padding: 20px;
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.07);
      margin-bottom: 30px;
      transition: 0.3s;
    }
    .article-card:hover {
      transform: translateY(-6px);
    }
    .article-card img {
      width: 100%;
      height: 220px;
      object-fit: cover;
      border-radius: 10px;
      margin-bottom: 10px;
    }
    .article-title {
      font-size: 1.25rem;
      font-weight: 600;
    }
    .article-meta {
      font-size: 0.9rem;
      color: #777;
      margin-bottom: 10px;
    }
    .read-more {
      font-size: 0.9rem;
      text-decoration: none;
      color: rgb(0, 64, 133);
      font-weight: 500;
    }
    .sidebar-box {
      background: white;
      padding: 20px;
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.07);
    }
    footer {
      background-color: rgb(230, 240, 239);
      color: grey;
      text-align: center;
      padding: 15px;
      margin-top: 60px;
    }
    @media (max-width: 991.98px) {
      .navbar-collapse {
        position: absolute;
        right: 1rem;
        top: 100%;
        background-color: white;
        border-radius: 10px;
        padding: 10px 20px;
        margin-top: 10px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        width: auto;
        z-index: 999;
      }
      .navbar-nav {
        flex-direction: column;
        text-align: left;
      }
      .navbar-nav .nav-link {
        color: grey !important;
        padding: 8px 0;
        font-weight: 500;
      }
    }
  </style>
</head>
<body>

<!-- NAVIGASI -->
<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand fw-bold" href="blog.php">Tata's Artikel</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon text-white"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="blog.php">Beranda</a></li>
        <li class="nav-item"><a class="nav-link" href="#tentang">Tentang</a></li>
        <li class="nav-item"><a class="nav-link" href="#kontak">Kontak</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- HEADER -->
<div class="blog-header">
  <div class="container text-center">
    <h1 class="fw-bold mb-2"><?= htmlspecialchars($category_name) ?></h1>
    <p class="lead">Kategori Artikel: <?= htmlspecialchars($category_name) ?> di Tata's Artikel</p>
  </div>
</div>

<!-- KONTEN -->
<div class="container content-container">
  <div class="row">
    <!-- KONTEN ARTIKEL -->
    <div class="col-lg-8">
      <?php if ($articles->num_rows > 0): ?>
        <div class="row">
          <?php while ($row = $articles->fetch_assoc()): ?>
            <div class="col-md-6">
              <div class="article-card">
                <?php if ($row['picture'] && file_exists("img/" . $row['picture'])): ?>
                  <img src="img/<?= $row['picture'] ?>" alt="<?= htmlspecialchars($row['title']) ?>">
                <?php endif; ?>
                <div class="article-title">
                  <a href="artikel-detail.php?id=<?= $row['id'] ?>" style="text-decoration: none; color: #333;">
                    <?= htmlspecialchars($row['title']) ?>
                  </a>
                </div>
                <div class="article-meta">
                  <?= date('d M Y, H:i', strtotime($row['date'])) ?> • <?= htmlspecialchars($row['author_name']) ?>
                </div>
                <p><?= substr(strip_tags($row['content']), 0, 100) ?>...</p>
                <a href="artikel-detail.php?id=<?= $row['id'] ?>" class="read-more">→ Baca Selengkapnya</a>
              </div>
            </div>
          <?php endwhile; ?>
        </div>
      <?php else: ?>
        <div class="alert alert-warning">Belum ada artikel pada kategori ini.</div>
      <?php endif; ?>

      <a href="blog.php" class="btn btn-outline-light mt-4">&larr; Kembali ke Beranda</a>
    </div>

    <!-- SIDEBAR -->
    <div class="col-lg-4">
      <div class="sidebar-box">
        <!-- Form Pencarian -->
        <form action="blog.php" method="get" class="mb-3">
          <input type="text" name="search" class="form-control" placeholder="🔍 Cari artikel..." value="<?= htmlspecialchars($search) ?>">
        </form>

        <!-- Daftar Kategori -->
        <h5 class="fw-bold mb-2">Kategori</h5>
        <ul class="list-unstyled">
          <?php
            $kategori = $conn->query("SELECT * FROM category");
            while ($k = $kategori->fetch_assoc()):
          ?>
            <li>
              <a href="kategori-detail.php?id=<?= $k['id'] ?>" class="text-decoration-none text-primary">
                • <?= htmlspecialchars($k['name']) ?>
              </a>
            </li>
          <?php endwhile; ?>
        </ul>

        <!-- Tentang -->
        <h5 class="fw-bold mb-3 mt-4" id="tentang">Tentang</h5>
        <p class="text-muted">Tata's Artikel menyajikan kisah dan informasi seputar gunung-gunung Indonesia, budaya, konservasi, dan petualangan alam yang menginspirasi.</p>
      </div>
    </div>
  </div>

  <!-- KONTAK -->
  <div class="container-fluid py-5" id="kontak">
    <div class="container">
      <div class="row">
        <div class="col-lg-8 mx-auto">
          <div class="sidebar-box text-center p-4 bg-white rounded shadow">
            <h4 class="fw-bold mb-3">Hubungi Kami</h4>
            <p>Untuk pertanyaan, kolaborasi, atau saran, silakan hubungi kami:</p>
            <p><strong>Email:</strong> <a href="mailto:sintahidayahsnh@gmail.com" class="text-primary text-decoration-underline">sintahidayahsnh@gmail.com</a></p>
            <p><strong>Instagram:</strong> <a href="https://instagram.com/sintanrhdy" target="_blank" class="text-primary text-decoration-underline">@sintanrhdy</a></p>
            <p><strong>Twitter:</strong> <a href="https://twitter.com/flowriseay" target="_blank" class="text-primary text-decoration-underline">@flowriseay</a></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- FOOTER -->
<footer>
  &copy; <?= date('Y') ?> Tata's Artikel. All rights reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
