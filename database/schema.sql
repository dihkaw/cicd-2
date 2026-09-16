-- ============================================================
-- KATALOG BUKU PERPUSTAKAAN MINI
-- Database Schema + Seed Data
-- Dijalankan manual via CLI SEBELUM aplikasi di-deploy (lihat README.md)
-- ============================================================

CREATE DATABASE IF NOT EXISTS perpus_mini CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE perpus_mini;

-- ------------------------------------------------------------
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(60) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    author VARCHAR(120) NOT NULL,
    category_id INT NULL,
    cover_color VARCHAR(7) NOT NULL DEFAULT '#dbdbdb', -- warna cover placeholder (tema kartu ala Instagram)
    synopsis TEXT NULL,
    year SMALLINT NULL,
    stock INT NOT NULL DEFAULT 1,
    is_available TINYINT(1) NOT NULL DEFAULT 1,
    likes INT NOT NULL DEFAULT 0, -- jumlah "suka" ala Instagram
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- SEED DATA (data awal langsung lengkap, sama seperti prinsip proyek BANK BINGBUNG)
-- ------------------------------------------------------------

-- Admin default -> username: admin | password: Admin@123 (hash bcrypt valid)
INSERT INTO admins (username, password, full_name) VALUES
('admin', '$2b$10$UA3qLAWITiInWoi9xYgBBeWPRnqcpRav1wL5m4sYInbL7enQ75KZG', 'Pustakawan Admin');

INSERT INTO categories (name) VALUES
('Fiksi'), ('Non-Fiksi'), ('Sains & Teknologi'), ('Sejarah'), ('Komik'), ('Pemrograman');

INSERT INTO books (title, author, category_id, cover_color, synopsis, year, stock, is_available, likes) VALUES
('Laskar Pelangi', 'Andrea Hirata', 1, '#f2a154', 'Kisah perjuangan anak-anak Belitung meraih pendidikan.', 2005, 3, 1, 128),
('Bumi Manusia', 'Pramoedya Ananta Toer', 1, '#5b8def', 'Novel sejarah tentang kehidupan pribumi di masa kolonial.', 1980, 2, 1, 210),
('Sapiens: A Brief History of Humankind', 'Yuval Noah Harari', 2, '#e05f5f', 'Perjalanan panjang evolusi manusia dari masa ke masa.', 2011, 4, 1, 342),
('Clean Code', 'Robert C. Martin', 6, '#3f9d63', 'Panduan menulis kode yang bersih dan mudah dipelihara.', 2008, 2, 1, 189),
('Atomic Habits', 'James Clear', 2, '#c96bd6', 'Membangun kebiasaan baik dengan perubahan kecil yang konsisten.', 2018, 5, 1, 401),
('Negeri 5 Menara', 'Ahmad Fuadi', 1, '#f2c94c', 'Perjalanan santri mengejar mimpi di pondok pesantren.', 2009, 0, 0, 97),
('Sejarah Indonesia Modern', 'M.C. Ricklefs', 4, '#6c757d', 'Sejarah panjang Indonesia dari masa kolonial hingga modern.', 2005, 2, 1, 54),
('One Piece Vol. 1', 'Eiichiro Oda', 5, '#e0895f', 'Petualangan Monkey D. Luffy mencari harta karun One Piece.', 1997, 6, 1, 512),
('Introduction to Algorithms', 'Thomas H. Cormen', 6, '#4a6fa5', 'Buku wajib untuk memahami dasar-dasar algoritma.', 2009, 1, 1, 76),
('Cosmos', 'Carl Sagan', 3, '#2f8f9d', 'Menjelajahi alam semesta melalui sains yang mudah dipahami.', 1980, 2, 1, 165);
