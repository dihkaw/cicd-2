<?php
require_once __DIR__ . '/includes/functions.php';

$categoryId = isset($_GET['cat']) ? (int)$_GET['cat'] : null;
$search = trim($_GET['q'] ?? '');
$categories = get_categories();
$books = get_books($categoryId ?: null, $search ?: null);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Katalog Buku Perpustakaan Mini</title>
<link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<div class="ig-nav">
  <a href="/" class="ig-logo">Perpus.mini</a>
  <form class="ig-search" method="GET" action="/">
    <input type="text" name="q" value="<?= e($search) ?>" placeholder="Cari judul atau penulis...">
  </form>
  <div class="ig-nav-links">
    <a href="/admin/login.php">Admin</a>
  </div>
</div>

<div class="story-rail">
  <a href="/" class="story-chip <?= !$categoryId ? 'active' : '' ?>">
    <div class="story-ring"><div class="story-ring-inner">📚</div></div>
    <span>Semua</span>
  </a>
  <?php foreach ($categories as $c): ?>
  <a href="/?cat=<?= $c['id'] ?>" class="story-chip <?= $categoryId === (int)$c['id'] ? 'active' : '' ?>">
    <div class="story-ring"><div class="story-ring-inner"><?= e(mb_substr($c['name'],0,2)) ?></div></div>
    <span><?= e($c['name']) ?></span>
  </a>
  <?php endforeach; ?>
</div>

<div class="feed">
  <?php if (!$books): ?>
    <div class="card" style="text-align:center; color:var(--ig-gray);">Tidak ada buku ditemukan.</div>
  <?php endif; ?>

  <?php foreach ($books as $b): ?>
  <div class="post-card">
    <div class="post-header">
      <div class="post-avatar"><?= e(initials($b['author'])) ?></div>
      <div class="meta">
        <strong><?= e($b['author']) ?></strong>
        <span><?= e($b['category_name'] ?? 'Tanpa Kategori') ?><?= $b['year'] ? ' • ' . e($b['year']) : '' ?></span>
      </div>
    </div>

    <a href="/book.php?id=<?= $b['id'] ?>" class="post-cover" style="background:<?= e($b['cover_color']) ?>;">
      <?= e(initials($b['title'])) ?>
    </a>

    <div class="post-actions">
      <button type="button" class="like-btn" data-id="<?= $b['id'] ?>">🤍</button>
      <a href="/book.php?id=<?= $b['id'] ?>" style="text-decoration:none; color:inherit; font-size:20px;">💬</a>
    </div>

    <div class="post-body">
      <div class="likes"><span class="like-count" data-id="<?= $b['id'] ?>"><?= (int)$b['likes'] ?></span> suka</div>
      <div class="caption">
        <strong><?= e($b['title']) ?></strong>
        <?= e(mb_strimwidth($b['synopsis'] ?? '', 0, 90, '...')) ?>
      </div>
      <div class="tag">#<?= e(str_replace(' ', '', $b['category_name'] ?? 'buku')) ?></div>
      <br>
      <span class="stock-badge <?= $b['is_available'] && $b['stock'] > 0 ? 'stock-available' : 'stock-unavailable' ?>">
        <?= $b['is_available'] && $b['stock'] > 0 ? 'Tersedia (' . (int)$b['stock'] . ')' : 'Dipinjam / Habis' ?>
      </span>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<script src="/assets/js/app.js"></script>
</body>
</html>
