# Katalog Buku Perpustakaan Mini

Aplikasi katalog buku perpustakaan sederhana berbasis **PHP native**, dengan tampilan
bertema **Instagram** (feed kartu, story-ring kategori, tombol suka ala Instagram).

## Fitur

- **Katalog publik (`/`)** — feed buku ala Instagram, filter kategori (story rail), pencarian judul/penulis, tombol "suka" (AJAX like).
- **Detail buku (`/book.php?id=`)** — sinopsis, status ketersediaan, jumlah suka.
- **`/admin`** — login admin, dashboard ringkasan, CRUD buku, CRUD kategori.

## Struktur Folder

```
perpus-mini/
├── index.php                # Feed katalog publik
├── book.php                 # Detail buku
├── api/like.php              # Endpoint AJAX untuk tombol suka
├── admin/                    # Panel admin (login, dashboard, CRUD)
├── config/db.php              # Koneksi DB — baca dari ENV / file .env
├── includes/functions.php     # Helper & query
├── assets/css/style.css        # Tema Instagram
├── assets/js/app.js            # Interaksi like (AJAX)
├── database/schema.sql          # Struktur tabel + SELURUH data awal (siap pakai)
├── .env.example                  # Contoh format .env
└── .github/workflows/            # 2 metode CI/CD (lihat di bawah)
```

## Prinsip Deployment: DB Selalu Siap Dulu

Sama seperti proyek "BANK BINGBUNG", `database/schema.sql` **sudah berisi seluruh data awal**
(kategori, contoh buku, dan akun admin dengan password ter-hash bcrypt yang valid) — jadi
begitu file ini di-import, database langsung 100% siap dipakai tanpa langkah tambahan apapun.

**Urutan wajib:** Database harus sudah siap (schema + data awal ter-import) **SEBELUM**
aplikasi di-deploy ke server manapun — karena aplikasi langsung mencoba connect ke DB begitu
diakses. Ini berlaku untuk arsitektur apapun yang Anda pilih di bawah.

```sql
-- Dijalankan manual via CLI di server/VM Database
mysql -u root -p < database/schema.sql
```

Akun admin default: **username `admin`, password `Admin@123`** — segera ganti melalui
menu login admin (fitur ubah password bisa ditambahkan sebagai latihan lanjutan siswa).

## Arsitektur Deployment — Fleksibel, Bisa Dikembangkan

Aplikasi ini sengaja dibuat *stateless* terhadap lokasi database (dikoneksikan via ENV
`DB_HOST`, dst — lihat `config/db.php`), sehingga bisa dipasang di berbagai skala arsitektur
tanpa mengubah kode:

### Skala 1 — Single Server (semua jadi satu)
```
[ 1 VM/Server ]
  - Nginx/Apache + PHP-FPM (aplikasi)
  - MySQL/MariaDB (database)
```
Cocok untuk demo cepat / latihan pemula. `DB_HOST` cukup diisi `127.0.0.1`.

### Skala 2 — High Availability (1 LB + 3 Web + 1 DB)
```
                [ Load Balancer ]
                        │
        ┌───────────────┼───────────────┐
        ▼                ▼                ▼
   [ Web Server 1 ]  [ Web Server 2 ]  [ Web Server 3 ]
        └───────────────┬───────────────┘
                         ▼
                   [ Database Server ]
```
Setiap Web Server men-deploy kode yang **identik** (via CI/CD, lihat di bawah), semua
mengarah ke `DB_HOST` yang sama (IP privat VM Database). Load Balancer (mis. HAProxy/Nginx)
membagi trafik ke ketiganya — gunakan sticky session jika nanti ditambahkan fitur sesi
peminjaman per-user.

### Skala 3 — Container (Docker / Docker Compose / Kubernetes)
```
[ Container: app  ] --ENV DB_HOST=db--> [ Container: db (MySQL) ]
```
`config/db.php` sudah membaca ENV, jadi tinggal:
```bash
docker run -e DB_HOST=db -e DB_NAME=perpus_mini -e DB_USER=perpus_app -e DB_PASS=xxx ...
```
DB tetap harus di-init lebih dulu (mis. via `docker-entrypoint-initdb.d/schema.sql` pada image MySQL resmi, atau job migrasi terpisah yang jalan sebelum container app start).

**Intinya di ketiga skala:** database disiapkan & diisi data awal terlebih dahulu (baik manual
CLI, maupun otomatis saat container DB pertama kali dibuat) — aplikasi web tinggal
dikoneksikan ke sana, seberapa pun banyak instance web server-nya.

## CI/CD — 2 Metode (untuk latihan)

Kode aplikasi (bukan schema.sql — itu tetap manual via CLI) di-deploy otomatis lewat GitHub
Actions setiap kali push ke branch `main`. Tersedia **2 metode**, pilih salah satu (atau
keduanya sekaligus untuk perbandingan saat praktikum):

| | Metode 1: Hosted Runner | Metode 2: Self-Hosted Runner |
|---|---|---|
| File | `.github/workflows/deploy-method1-hosted-runner.yml` | `.github/workflows/deploy-method2-self-hosted-runner.yml` |
| Runner jalan di | Infrastruktur GitHub (`ubuntu-latest`) | Server Anda sendiri (didaftarkan manual) |
| Cara kirim file | SSH `rsync` pakai **username + password** (`sshpass`) — bukan SSH key | Copy lokal langsung (runner memang berada di server tujuan) |
| Perlu simpan password server di Secrets? | Ya | Tidak (untuk server tempat runner terpasang) |
| Cocok untuk | Latihan dasar CI/CD via SSH password | Latihan konsep self-hosted runner & agent-based deployment |

> ⚠️ **Catatan keamanan**: autentikasi SSH dengan password polos (`ubuntu` / `ubuntu`) **tidak
> aman untuk produksi**. Ini dipakai murni karena permintaan skenario praktikum kelas. Di
> dunia nyata, gunakan SSH key + `known_hosts` yang benar, atau OIDC/short-lived credentials.

### GitHub Secrets yang perlu disiapkan

Buka **repo GitHub → Settings → Secrets and variables → Actions → New repository secret**,
lalu tambahkan:

| Nama Secret | Contoh Nilai | Keterangan |
|---|---|---|
| `SERVER_HOST` | `perpus.contoh-sekolah.sch.id` atau IP publik | Domain publik / IP server tujuan (dipakai Metode 1) |
| `SERVER_PORT` | `22` | Port SSH |
| `SERVER_USER` | `ubuntu` | Username login server |
| `SERVER_PASS` | `ubuntu` | Password login server (⚠️ demo only) |
| `DEPLOY_PATH` | `/var/www/perpus-mini` | Folder tujuan deploy kode di server |
| `DB_HOST` | `10.10.10.20` | IP/hostname server database |
| `DB_PORT` | `3306` | Port database |
| `DB_NAME` | `perpus_mini` | Nama database |
| `DB_USER` | `perpus_app` | User aplikasi ke database |
| `DB_PASS` | *(password DB)* | Password user aplikasi database |

Ya, **URL/domain, username, dan password server bisa disimpan sebagai GitHub Secrets** —
itu justru cara yang benar agar kredensial tidak tertulis langsung di file workflow (`.yml`)
yang ikut ter-commit ke repository publik/privat. GitHub Secrets otomatis di-mask (disamarkan)
di log Actions setiap kali dipakai, jadi tidak akan tampil sebagai teks biasa di histori run.

### Alur kerja CI/CD singkatnya

1. Siswa/instruktur menyiapkan server (VM biasa, atau salah satu VM dari arsitektur HA) dan
   memastikan **database sudah di-import lebih dulu** via CLI (`mysql ... < schema.sql`).
2. Isi seluruh GitHub Secrets di atas.
3. Pilih salah satu workflow (Metode 1 atau 2) — bisa nonaktifkan yang tidak dipakai dengan
   menghapus/mengganti ekstensi filenya jadi `.yml.disabled`, atau biarkan keduanya aktif
   untuk latihan perbandingan (masing-masing punya trigger `push` ke `main` yang sama, jadi
   akan berjalan berbarengan jika keduanya aktif — cocok untuk demo perbandingan performa/alur).
4. `git push origin main` → workflow otomatis berjalan → kode aplikasi ter-deploy ke server
   → file `.env` otomatis dibuat di server dari nilai Secrets → PHP-FPM & Nginx di-restart.
5. Buka domain publik server di browser → katalog buku langsung tampil dengan data yang sudah
   diisi sejak langkah 1.

## Keamanan yang Tetap Diterapkan di Level Aplikasi

Meski autentikasi server sengaja dibuat sederhana untuk praktikum, aplikasi tetap menerapkan
praktik aman di levelnya sendiri:
- Password admin di-hash bcrypt (`password_hash`), bukan plaintext.
- Semua query pakai prepared statements (PDO) — aman dari SQL Injection.
- CSRF token di setiap form POST admin.
- Output di-escape (`htmlspecialchars`) — aman dari XSS.
- File `.env` di-`.gitignore` (tidak pernah ikut ter-commit) dan diblokir akses langsung via `.htaccess`.
