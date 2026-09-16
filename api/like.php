<?php
require_once __DIR__ . '/../includes/functions.php';
header('Content-Type: application/json');

$id = (int)($_POST['id'] ?? 0);
if (!$id) { echo json_encode(['success' => false]); exit; }

$stmt = db()->prepare("UPDATE books SET likes = likes + 1 WHERE id = ?");
$stmt->execute([$id]);

$stmt2 = db()->prepare("SELECT likes FROM books WHERE id = ?");
$stmt2->execute([$id]);
$row = $stmt2->fetch();

echo json_encode(['success' => true, 'likes' => $row ? (int)$row['likes'] : 0]);
