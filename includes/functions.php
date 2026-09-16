<?php
require_once __DIR__ . '/../config/db.php';

if (session_status() === PHP_SESSION_NONE) session_start();

function e(?string $s): string { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

function current_admin(): ?array { return $_SESSION['admin'] ?? null; }

function require_admin(): void
{
    if (!current_admin()) { header('Location: /admin/login.php'); exit; }
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}

function csrf_check(): void
{
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(403); die('Invalid CSRF token.');
    }
}

function get_categories(): array
{
    return db()->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
}

function get_books(?int $categoryId = null, ?string $search = null): array
{
    $sql = "SELECT b.*, c.name AS category_name FROM books b LEFT JOIN categories c ON c.id = b.category_id WHERE 1=1";
    $params = [];
    if ($categoryId) { $sql .= " AND b.category_id = ?"; $params[] = $categoryId; }
    if ($search) { $sql .= " AND (b.title LIKE ? OR b.author LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
    $sql .= " ORDER BY b.created_at DESC";
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function get_book(int $id): ?array
{
    $stmt = db()->prepare("SELECT b.*, c.name AS category_name FROM books b LEFT JOIN categories c ON c.id = b.category_id WHERE b.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}

function initials(string $title): string
{
    $words = preg_split('/\s+/', trim($title));
    $s = '';
    foreach (array_slice($words, 0, 2) as $w) $s .= mb_strtoupper(mb_substr($w, 0, 1));
    return $s ?: '?';
}
