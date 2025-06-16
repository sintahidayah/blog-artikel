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

$search = $_GET['search'] ?? ''; // Untuk sidebar input pencarian
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($category_name) ?> - Tata's Artikel</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f3e9fc;
    }
    .navbar {
      background-color: #6f42c1;
    }
    .navbar-brand, .nav-link {
      color: white !important;
    }
    .blog-header {
      background: linear-gradient(to right, #9f5de2, #7f37c9);
      color: white;
      padding: 3rem 0;
      text-align: center;
      margin-bottom: 40px;
    }
    .content-container {
      padding: 40px 0;
    }
    .article-card {
      background: white;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 2px 15px rgba(0,0,0,0.05);
      margin-bottom: 20px;
    }
    .article-card img {
      width: 100%;
      height: 220px;
      object-fit: cover;
      border-radius: 8px;
      margin-bottom: 10px;
    }
    .article-title {
      font-size: 1.25rem;
      font-weight: bold;
    }
    .article-meta {
      font-size: 0.9rem;
      color: #777;
      margin-bottom: 10px;
    }
    footer {
      background-color: #6f42c1;
      color: white;
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
        min-width: 160px;
        max-width: max-content;
        z-index: 999;
      }

      .navbar-nav {
        flex-direction: column;
        text-align: left;
      }

      .navbar-nav .nav-link {
        color: #6f42c1 !important;
        padding: 8px 0;
        font-weight: 500;
        white-space: nowrap;
      }

      .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255,255,255,1%29' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
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
  <h1>Selamat Datang di Tata's Artikel</h1>
  <p class="lead">Temukan inspirasi dan wawasan baru di sini!</p>
</div>

<!-- KONTEN -->
<div class="container content-container">
  <h2 class="mb-4"><strong>Kategori: <?= htmlspecialchars($category_name) ?></strong></h2>
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
                  <?= date('d M Y, H:i', strtotime($row['datetime'])) ?> • <?= htmlspecialchars($row['author_name']) ?>
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

      <a href="blog.php" class="btn btn-outline-secondary mt-4 mb-5">&larr; Kembali ke Blog</a>
    </div>

    <!-- SIDEBAR -->
    <div class="col-lg-4">
      <div class="p-3 bg-white border rounded shadow-sm">
        
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
        <h5 class="fw-bold mb-2 mt-4" id="tentang">Tentang</h5>
        <p>Tata's Artikel adalah blog yang menyajikan informasi dan cerita menarik seputar gunung-gunung di Indonesia. 
          Mulai dari keindahan alam, status konservasi, hingga nilai budaya dan sejarahnya, semua dikemas untuk menambah wawasan 
          dan kecintaan terhadap kekayaan alam nusantara.</p>
      </div>
    </div>

    <!-- KONTAK -->
    <div class="container-fluid py-5" id="kontak">
      <div class="container">
        <div class="row">
          <div class="col-lg-8 mx-auto">
            <div class="sidebar-box text-center p-4 bg-white rounded shadow">
              <h4 class="fw-bold mb-3">Hubungi Kami</h4>
              <p>Jika Anda memiliki pertanyaan, saran, atau ingin berkolaborasi, jangan ragu untuk menghubungi kami melalui:</p>
              <p class="mb-1">
                <strong>Email:</strong>
                <a href="mailto:sintahidayahsnh@gmail.com" class="text-primary text-decoration-underline">sintahidayahsnh@gmail.com</a>
              </p>
              <p class="mb-1">
                <strong>Instagram:</strong>
                <a href="https://instagram.com/sintanrhdy" target="_blank" class="text-primary text-decoration-underline">@sintanrhdy</a>
              </p>
              <p>
                <strong>Twitter:</strong>
                <a href="https://twitter.com/flowriseay" target="_blank" class="text-primary text-decoration-underline">@flowriseay</a>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div> <!-- .row -->
</div> <!-- .container -->

<!-- FOOTER -->
<footer>
  &copy; <?= date('Y') ?> Tata's Artikel. All rights reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
