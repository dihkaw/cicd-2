<?php
require_once __DIR__ . '/../includes/functions.php';

if (current_admin()) { header('Location: /admin/dashboard.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = db()->prepare("SELECT * FROM admins WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    if ($admin && password_verify($password, $admin['password'])) {
        unset($admin['password']);
        $_SESSION['admin'] = $admin;
        header('Location: /admin/dashboard.php'); exit;
    }
    $error = 'Username atau password salah.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login Admin - Perpustakaan Mini</title>
<link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<div class="ig-login-wrap">
  <div class="ig-login-card">
    <a href="/" class="ig-logo" style="text-decoration:none; color:inherit;">Perpus.mini</a>
    <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
    <form method="POST">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <div class="form-group"><input type="text" name="username" placeholder="Username" required autofocus></div>
      <div class="form-group"><input type="password" name="password" placeholder="Password" required></div>
      <button type="submit" class="btn btn-primary btn-block">Masuk</button>
    </form>
  </div>
</div>
</body>
</html>
