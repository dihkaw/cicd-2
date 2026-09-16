<?php
require_once __DIR__ . '/includes/functions.php';

$id = (int)($_GET['id'] ?? 0);
$book = get_book($id);
if (!$book) { http_response_code(404); die('Buku tidak ditemukan.'); }
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($book['title']) ?> - Perpustakaan Mini</title>
<link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<div class="ig-nav">
  <a href="/" class="ig-logo">Perpus.mini</a>
  <div class="ig-nav-links"><a href="/">&larr; Kembali</a></div>
</div>

<div class="detail-wrap">
  <div class="detail-card">
    <div class="detail-cover" style="background:<?= e($book['cover_color']) ?>;"><?= e(initials($book['title'])) ?></div>
    <h1><?= e($book['title']) ?></h1>
    <p class="author">oleh <?= e($book['author']) ?></p>

    <div class="detail-meta">
      <span>Kategori: <strong><?= e($book['category_name'] ?? '-') ?></strong></span>
      <span>Tahun: <strong><?= e((string)($book['year'] ?? '-')) ?></strong></span>
      <span>Stok: <strong><?= (int)$book['stock'] ?></strong></span>
      <span>Suka: <strong id="detailLikes"><?= (int)$book['likes'] ?></strong></span>
    </div>

    <span class="stock-badge <?= $book['is_available'] && $book['stock'] > 0 ? 'stock-available' : 'stock-unavailable' ?>">
      <?= $book['is_available'] && $book['stock'] > 0 ? 'Tersedia untuk dipinjam' : 'Sedang dipinjam / stok habis' ?>
    </span>

    <p class="synopsis" style="margin-top:16px;"><?= nl2br(e($book['synopsis'] ?? 'Belum ada sinopsis.')) ?></p>

    <button type="button" class="btn btn-primary like-btn" data-id="<?= $book['id'] ?>" data-detail="1">🤍 Suka</button>
  </div>
</div>

<script src="/assets/js/app.js"></script>
</body>
</html>
