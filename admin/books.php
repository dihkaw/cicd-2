<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$pdo = db();
$message = '';
$messageType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'add' || $action === 'edit') {
            $title = trim($_POST['title'] ?? '');
            $author = trim($_POST['author'] ?? '');
            $categoryId = (int)($_POST['category_id'] ?? 0) ?: null;
            $coverColor = $_POST['cover_color'] ?? '#dbdbdb';
            $synopsis = trim($_POST['synopsis'] ?? '');
            $year = (int)($_POST['year'] ?? 0) ?: null;
            $stock = (int)($_POST['stock'] ?? 0);
            $isAvailable = isset($_POST['is_available']) ? 1 : 0;

            if ($title === '' || $author === '') throw new Exception('Judul dan penulis wajib diisi.');

            if ($action === 'add') {
                $stmt = $pdo->prepare("INSERT INTO books (title, author, category_id, cover_color, synopsis, year, stock, is_available) VALUES (?,?,?,?,?,?,?,?)");
                $stmt->execute([$title, $author, $categoryId, $coverColor, $synopsis, $year, $stock, $isAvailable]);
                $message = 'Buku berhasil ditambahkan.';
            } else {
                $id = (int)$_POST['id'];
                $stmt = $pdo->prepare("UPDATE books SET title=?, author=?, category_id=?, cover_color=?, synopsis=?, year=?, stock=?, is_available=? WHERE id=?");
                $stmt->execute([$title, $author, $categoryId, $coverColor, $synopsis, $year, $stock, $isAvailable, $id]);
                $message = 'Buku berhasil diperbarui.';
            }
        } elseif ($action === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
            $stmt->execute([$id]);
            $message = 'Buku berhasil dihapus.';
        }
    } catch (Exception $e) {
        $message = $e->getMessage();
        $messageType = 'danger';
    }
}

$editBook = null;
if (isset($_GET['edit'])) $editBook = get_book((int)$_GET['edit']);

$books = get_books();
$categories = get_categories();
$activePage = 'books';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola Buku - Perpustakaan Mini</title>
<link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<div class="admin-shell">
  <?php include __DIR__ . '/_sidebar.php'; ?>
  <div class="admin-main">
    <div class="admin-header"><h1>Kelola Buku</h1></div>

    <?php if ($message): ?>
      <div class="alert alert-<?= $messageType === 'danger' ? 'danger' : 'success' ?>"><?= e($message) ?></div>
    <?php endif; ?>

    <div class="card">
      <h3><?= $editBook ? 'Edit Buku: ' . e($editBook['title']) : 'Tambah Buku Baru' ?></h3>
      <form method="POST">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action" value="<?= $editBook ? 'edit' : 'add' ?>">
        <?php if ($editBook): ?><input type="hidden" name="id" value="<?= $editBook['id'] ?>"><?php endif; ?>

        <div class="grid-2">
          <div class="form-group">
            <label>Judul</label>
            <input type="text" name="title" value="<?= e($editBook['title'] ?? '') ?>" required>
          </div>
          <div class="form-group">
            <label>Penulis</label>
            <input type="text" name="author" value="<?= e($editBook['author'] ?? '') ?>" required>
          </div>
          <div class="form-group">
            <label>Kategori</label>
            <select name="category_id">
              <option value="">-- Tanpa Kategori --</option>
              <?php foreach ($categories as $c): ?>
                <option value="<?= $c['id'] ?>" <?= ($editBook['category_id'] ?? null) == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Warna Cover</label>
            <input type="color" name="cover_color" value="<?= e($editBook['cover_color'] ?? '#dbdbdb') ?>" style="height:40px;">
          </div>
          <div class="form-group">
            <label>Tahun Terbit</label>
            <input type="number" name="year" value="<?= e((string)($editBook['year'] ?? '')) ?>" min="1900" max="2100">
          </div>
          <div class="form-group">
            <label>Stok</label>
            <input type="number" name="stock" value="<?= e((string)($editBook['stock'] ?? 1)) ?>" min="0" required>
          </div>
        </div>

        <div class="form-group">
          <label>Sinopsis</label>
          <textarea name="synopsis" rows="3"><?= e($editBook['synopsis'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
          <label><input type="checkbox" name="is_available" <?= ($editBook['is_available'] ?? 1) ? 'checked' : '' ?> style="width:auto;"> Tersedia untuk dipinjam</label>
        </div>

        <button type="submit" class="btn btn-primary"><?= $editBook ? 'Simpan Perubahan' : 'Tambah Buku' ?></button>
        <?php if ($editBook): ?><a href="/admin/books.php" class="btn btn-outline">Batal</a><?php endif; ?>
      </form>
    </div>

    <div class="card">
      <h3>Daftar Buku</h3>
      <table>
        <thead><tr><th>Cover</th><th>Judul</th><th>Penulis</th><th>Kategori</th><th>Stok</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
        <?php foreach ($books as $b): ?>
          <tr>
            <td><span class="color-swatch" style="background:<?= e($b['cover_color']) ?>;"></span></td>
            <td><?= e($b['title']) ?></td>
            <td><?= e($b['author']) ?></td>
            <td><?= e($b['category_name'] ?? '-') ?></td>
            <td><?= (int)$b['stock'] ?></td>
            <td><span class="badge" style="background:<?= $b['is_available'] ? '#e7f7ee' : '#fde8e8' ?>; color:<?= $b['is_available'] ? '#1e9e5a' : '#c1272d' ?>;"><?= $b['is_available'] ? 'Aktif' : 'Nonaktif' ?></span></td>
            <td>
              <a href="/admin/books.php?edit=<?= $b['id'] ?>" class="btn btn-sm btn-outline">Edit</a>
              <form method="POST" style="display:inline;" onsubmit="return confirm('Hapus buku ini?');">
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $b['id'] ?>">
                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
