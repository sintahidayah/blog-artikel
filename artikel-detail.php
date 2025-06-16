<?php
$conn = new mysqli("localhost", "root", "", "db_artikel");

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil artikel utama
$sql = "
  SELECT a.id, a.title, a.date, a.content, a.picture,
         c.id AS category_id,
         c.name AS category_name,
         au.nickname AS author_name
  FROM article a
  LEFT JOIN article_category ac ON a.id = ac.article_id
  LEFT JOIN category c ON ac.category_id = c.id
  LEFT JOIN article_author aa ON a.id = aa.article_id
  LEFT JOIN author au ON aa.author_id = au.id
  WHERE a.id = $id
  LIMIT 1";
$result = $conn->query($sql);
$article = $result->fetch_assoc();

// Ambil artikel terkait berdasarkan kategori
$related = [];
if ($article) {
  $category_id = intval($article['category_id']);
  $related = $conn->query("
    SELECT a.id, a.title 
    FROM article a
    LEFT JOIN article_category ac ON a.id = ac.article_id
    WHERE ac.category_id = $category_id AND a.id != $id
    ORDER BY RAND() LIMIT 5
  ");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title><?= $article ? htmlspecialchars($article['title']) : 'Artikel Tidak Ditemukan' ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
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
    .content-container {
      padding: 40px 0;
    }
    .article-detail {
      background: white;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }
    .article-img {
      width: 100%;
      height: 450px;
      object-fit: cover;
      border-radius: 10px;
      margin-bottom: 20px;
    }
    article {
        font-family: 'Arial', sans-serif;
        line-height: 1.6;
    }
    article ol {
        padding-left: 1.5rem;
        margin-bottom: 1rem;
    }
    article li {
        margin-bottom: 0.5rem;
    }
    article strong {
        font-weight: bold;
    }
    article em {
        font-style: italic;
    }
    article h1, article h2, article h3, article h4 {
        font-weight: bold;
        margin-top: 1.5rem;
        margin-bottom: 1rem;
    }
    .article-title {
      font-size: 2rem;
      font-weight: 700;
      margin-bottom: 10px;
    }
    .article-meta {
      font-size: 0.95rem;
      color: #777;
      margin-bottom: 20px;
    }
    .sidebar {
      background: white;
      padding: 25px;
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }
    .sidebar h5 {
      font-weight: 600;
      margin-bottom: 15px;
    }
    .related-title {
      font-size: 0.95rem;
      margin-bottom: 10px;
    }
    .article-category {
      background-color: #ff6b6b;
      color: white;
      font-size: 0.8rem;
      padding: 4px 10px;
      border-radius: 6px;
      display: inline-block;
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

<!-- KONTEN UTAMA + SIDEBAR -->
<div class="container content-container">
  <div class="row g-4">
    <!-- DETAIL ARTIKEL -->
    <div class="col-lg-8">
      <?php if ($article): ?>
        <div class="article-detail">
          <?php if ($article['picture'] && file_exists("img/" . $article['picture'])): ?>
            <img src="img/<?= $article['picture'] ?>" class="article-img" alt="<?= htmlspecialchars($article['title']) ?>">
          <?php endif; ?>
          <div class="article-category">
            <a href="kategori-detail.php?id=<?= $article['category_id'] ?>" style="color: white; text-decoration: none;">
            <?= htmlspecialchars($article['category_name']) ?>
            </a>
          </div>
          <h1 class="article-title"><?= htmlspecialchars($article['title']) ?></h1>
          <div class="article-meta">
           <?= date('d M Y, H:i', strtotime($article['date'])) ?> • <?= htmlspecialchars($article['author_name']) ?>
          </div>
          <div class="article-content">
            <?= $article['content']; ?>
          </div>
          <a href="blog.php" class="btn btn-outline-secondary mt-4">&larr; Kembali ke Blog</a>
        </div>
      <?php else: ?>
        <div class="alert alert-warning">Artikel tidak ditemukan.</div>
      <?php endif; ?>
    </div>

    <!-- SIDEBAR -->
    <div class="col-lg-4">
      <div class="sidebar">
        <!-- Search -->
        <h5>Pencarian</h5>
        <form action="blog.php" method="get" class="mb-4">
          <input type="text" name="search" class="form-control" placeholder="Cari artikel...">
        </form>

        <!-- Artikel Terkait -->
        <h5>Artikel Terkait</h5>
        <?php if ($related && $related->num_rows > 0): ?>
          <?php while ($r = $related->fetch_assoc()): ?>
            <div class="related-title">
              <a href="artikel-detail.php?id=<?= $r['id'] ?>">
                <?= htmlspecialchars($r['title']) ?>
              </a>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <p class="text-muted">Tidak ada artikel terkait.</p>
        <?php endif; ?>
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
  </div>
</div>

<!-- FOOTER -->
<footer>
  &copy; <?= date('Y') ?> Tata's Artikel. All rights reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
