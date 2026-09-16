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
        if ($action === 'add') {
            $name = trim($_POST['name'] ?? '');
            if ($name === '') throw new Exception('Nama kategori wajib diisi.');
            $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
            $stmt->execute([$name]);
            $message = 'Kategori berhasil ditambahkan.';
        } elseif ($action === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            $message = 'Kategori berhasil dihapus. Buku terkait dipindahkan ke "Tanpa Kategori".';
        }
    } catch (Exception $e) {
        $message = 'Gagal: nama kategori mungkin sudah ada, atau ' . $e->getMessage();
        $messageType = 'danger';
    }
}

$categories = $pdo->query("SELECT c.*, (SELECT COUNT(*) FROM books b WHERE b.category_id = c.id) AS book_count FROM categories c ORDER BY c.name ASC")->fetchAll();
$activePage = 'categories';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola Kategori - Perpustakaan Mini</title>
<link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<div class="admin-shell">
  <?php include __DIR__ . '/_sidebar.php'; ?>
  <div class="admin-main">
    <div class="admin-header"><h1>Kelola Kategori</h1></div>

    <?php if ($message): ?>
      <div class="alert alert-<?= $messageType === 'danger' ? 'danger' : 'success' ?>"><?= e($message) ?></div>
    <?php endif; ?>

    <div class="card">
      <h3>Tambah Kategori</h3>
      <form method="POST" style="display:flex; gap:10px;">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action" value="add">
        <input type="text" name="name" placeholder="Nama kategori" required style="flex:1; padding:9px 12px; border:1px solid var(--ig-border); border-radius:4px;">
        <button type="submit" class="btn btn-primary">Tambah</button>
      </form>
    </div>

    <div class="card">
      <h3>Daftar Kategori</h3>
      <table>
        <thead><tr><th>Nama</th><th>Jumlah Buku</th><th>Aksi</th></tr></thead>
        <tbody>
        <?php foreach ($categories as $c): ?>
          <tr>
            <td><?= e($c['name']) ?></td>
            <td><?= (int)$c['book_count'] ?></td>
            <td>
              <form method="POST" style="display:inline;" onsubmit="return confirm('Hapus kategori ini?');">
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $c['id'] ?>">
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
