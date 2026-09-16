<?php
/**
 * Koneksi database dibaca dari ENVIRONMENT VARIABLE agar cocok dipakai
 * lintas arsitektur deployment (1 server, HA, container) dan lintas
 * metode CI/CD (GitHub Actions hosted runner / self-hosted runner).
 *
 * ENV di-set melalui:
 *   - GitHub Secrets -> diteruskan workflow -> ditulis ke file .env di server saat deploy, ATAU
 *   - langsung sebagai environment variable web server / container (docker -e / docker-compose).
 *
 * DB (schema.sql) WAJIB sudah di-import terlebih dahulu secara manual via CLI
 * SEBELUM aplikasi ini di-deploy — lihat README.md.
 */

// Muat file .env jika ada (dibuat otomatis oleh workflow CI/CD saat deploy)
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
        [$k, $v] = explode('=', $line, 2);
        putenv(trim($k) . '=' . trim($v));
    }
}

define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'perpus_mini');
define('DB_USER', getenv('DB_USER') ?: 'perpus_app');
define('DB_PASS', getenv('DB_PASS') ?: 'CHANGE_ME');

function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            http_response_code(500);
            die('Koneksi database gagal. Pastikan schema.sql sudah di-import dan ENV DB sudah benar. (' . htmlspecialchars($e->getMessage()) . ')');
        }
    }
    return $pdo;
}
