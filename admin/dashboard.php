<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$totalBooks = db()->query("SELECT COUNT(*) c FROM books")->fetch()['c'];
$totalAvailable = db()->query("SELECT COUNT(*) c FROM books WHERE is_available = 1 AND stock > 0")->fetch()['c'];
$totalCategories = db()->query("SELECT COUNT(*) c FROM categories")->fetch()['c'];
$topLiked = db()->query("SELECT title, likes FROM books ORDER BY likes DESC LIMIT 5")->fetchAll();
$activePage = 'dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ringkasan Admin - Perpustakaan Mini</title>
<link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<div class="admin-shell">
  <?php include __DIR__ . '/_sidebar.php'; ?>
  <div class="admin-main">
    <div class="admin-header"><h1>Ringkasan Perpustakaan</h1></div>

    <div class="grid-3">
      <div class="card"><h3>Total Buku</h3><div style="font-size:28px; font-weight:700;"><?= (int)$totalBooks ?></div></div>
      <div class="card"><h3>Tersedia Dipinjam</h3><div style="font-size:28px; font-weight:700; color:#1e9e5a;"><?= (int)$totalAvailable ?></div></div>
      <div class="card"><h3>Total Kategori</h3><div style="font-size:28px; font-weight:700;"><?= (int)$totalCategories ?></div></div>
    </div>

    <div class="card">
      <h3>Buku Paling Disukai</h3>
      <table>
        <thead><tr><th>Judul</th><th>Suka</th></tr></thead>
        <tbody>
        <?php foreach ($topLiked as $t): ?>
          <tr><td><?= e($t['title']) ?></td><td>❤️ <?= (int)$t['likes'] ?></td></tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="card">
      <h3>Akses Cepat</h3>
      <a href="/admin/books.php" class="btn btn-primary">Kelola Buku</a>
      <a href="/admin/categories.php" class="btn btn-outline">Kelola Kategori</a>
    </div>
  </div>
</div>
</body>
</html>
