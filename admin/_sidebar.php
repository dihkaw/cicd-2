<?php $admin = current_admin(); ?>
<div class="admin-sidebar">
  <a href="/" class="ig-logo" style="text-decoration:none; color:inherit;">Perpus.mini</a>
  <nav>
    <a href="/admin/dashboard.php" class="<?= ($activePage ?? '') === 'dashboard' ? 'active' : '' ?>">Ringkasan</a>
    <a href="/admin/books.php" class="<?= ($activePage ?? '') === 'books' ? 'active' : '' ?>">Kelola Buku</a>
    <a href="/admin/categories.php" class="<?= ($activePage ?? '') === 'categories' ? 'active' : '' ?>">Kelola Kategori</a>
    <a href="/" target="_blank">Lihat Katalog Publik</a>
    <a href="/admin/logout.php">Keluar (<?= e($admin['username']) ?>)</a>
  </nav>
</div>
