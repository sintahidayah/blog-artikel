<?php
$conn = new mysqli("localhost", "root", "", "db_artikel");

// Handle pencarian dan filter kategori
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$filterKategori = isset($_GET['kategori']) ? intval($_GET['kategori']) : 0;

$where = "";
if ($search) {
    $where .= " AND a.title LIKE '%$search%'";
}
if ($filterKategori) {
    $where .= " AND c.id = $filterKategori";
}

$sql = "
    SELECT a.id, a.title, a.date, a.content, a.picture,
           c.name AS category_name,
           au.nickname AS author_name
    FROM article a
    LEFT JOIN article_category ac ON a.id = ac.article_id
    LEFT JOIN category c ON ac.category_id = c.id
    LEFT JOIN article_author aa ON a.id = aa.article_id
    LEFT JOIN author au ON aa.author_id = au.id
    WHERE 1=1 $where
    ORDER BY a.date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Blog Artikel</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color:rgb(40, 72, 52);
    }
    .navbar {
      background-color:rgb(230, 240, 239);
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
      display: inline-flex;
      text-align: center;
      align-items: center;
      justify-content: center;
      height: 400px; /* Atur tinggi sesuai kebutuhan */
      width: 100%;
      margin-bottom: 100px;
    }
    .blog-card {
      background: white;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.07);
      transition: all 0.3s ease-in-out;
    }
    .blog-card:hover {
      transform: translateY(-8px);
    }
    .blog-card img {
      width: 100%;
      height: 200px;
      object-fit: cover;
    }
    .blog-card-body {
      padding: 20px;
    }
    .blog-title {
      font-size: 1.2rem;
      font-weight: 600;
      margin-bottom: 0.5rem;
      color: #333;
      text-decoration: none;
    }
    .blog-meta {
      font-size: 0.9rem;
      color: #777;
      margin-bottom: 0.5rem;
    }
    .badge-category {
      background-color: #ff6b6b;
      color: white;
      font-size: 0.75rem;
      padding: 4px 8px;
      border-radius: 5px;
      display: inline-block;
      margin-bottom: 8px;
    }
    .read-more {
      font-size: 0.9rem;
      text-decoration: none;
      color:rgb(0, 64, 133);
      font-weight: 500;
    }
    .sidebar-box {
      background: white;
      padding: 20px;
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.07);
    }
    footer {
      background-color:rgb(230, 240, 239);
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
        min-width: 160px;
        max-width: max-content;
        z-index: 999;
      }

      .navbar-nav {
        flex-direction: column;
        text-align: left;
      }

      .navbar-nav .nav-link {
        color: rgb(230, 240, 239)!important;
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
    <a class="navbar-brand" href="blog.php">Tata's Artikel</a>
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
  <p>Temukan inspirasi dan wawasan baru di sini!</p>
</div>

</div>

<!-- KONTEN -->
<div class="container mb-5">
  <div class="row g-4">
    <!-- Artikel -->
    <div class="col-lg-8">
      <h3 class="mb-4 fw-bold">📰 Artikel Terbaru</h3>
      <div class="row g-4">
        <?php while ($row = $result->fetch_assoc()): ?>
        <div class="col-md-6">
          <div class="blog-card">
            <a href="artikel-detail.php?id=<?= $row['id'] ?>">
              <?php if ($row['picture'] && file_exists("img/" . $row['picture'])): ?>
                <img src="img/<?= $row['picture'] ?>" alt="<?= htmlspecialchars($row['title']) ?>">
              <?php endif; ?>
            </a>
            <div class="blog-card-body">
              <span class="badge-category"><?= htmlspecialchars($row['category_name']) ?></span>
              <a href="artikel-detail.php?id=<?= $row['id'] ?>" class="blog-title">
                <?= htmlspecialchars($row['title']) ?>
              </a>
              <div class="blog-meta">
                <?= date('d M Y', strtotime($row['date'])) ?> • <?= htmlspecialchars($row['author_name']) ?>
              </div>
              <p><?= substr(strip_tags($row['content']), 0, 100) ?>...</p>
              <a href="artikel-detail.php?id=<?= $row['id'] ?>" class="read-more">→ Baca Selengkapnya</a>
            </div>
          </div>
        </div>
        <?php endwhile; ?>
      </div>
    </div>

    <!-- SIDEBAR -->
    <div class="col-lg-4">
      <div class="sidebar-box mb-4">
        <form action="blog.php" method="get" class="mb-3">
          <input type="text" name="search" class="form-control" placeholder="🔍 Cari artikel..." value="<?= htmlspecialchars($search) ?>">
        </form>

        <!-- Daftar Kategori -->
        <h5>Kategori</h5>
          <ul class="list-unstyled">
            <?php
              $kategori = $conn->query("SELECT * FROM category");
              while ($k = $kategori->fetch_assoc()):
            ?>
            <li>
              <a href="kategori-detail.php?id=<?= $k['id'] ?>" class="text-decoration-none text-primary">•
              <?= htmlspecialchars($k['name']) ?>
              </a>
            </li>
            <?php endwhile; ?>
          </ul>

        <h5 class="fw-bold mb-2" id="tentang">Tentang</h5>
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
  </div>
</div>

<!-- FOOTER -->
<footer>
  &copy; <?= date('Y') ?> Tata's Artikel. All rights reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
