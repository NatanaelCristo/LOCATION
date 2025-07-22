-- Script untuk membuat tabel admin
-- Database: dstmobil.com

CREATE TABLE IF NOT EXISTS `admin` (
  `wa` VARCHAR(15) NOT NULL PRIMARY KEY COMMENT 'Nomor WhatsApp sebagai primary key',
  `nama` VARCHAR(100) NOT NULL COMMENT 'Nama lengkap admin',
  `password` VARCHAR(255) NOT NULL COMMENT 'Password yang di-hash dengan bcrypt',
  `cabang` VARCHAR(50) NOT NULL COMMENT 'Cabang tempat admin bertugas',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Waktu pembuatan akun',
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Waktu terakhir diupdate'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabel untuk menyimpan data admin';

-- Index untuk optimasi pencarian
CREATE INDEX idx_admin_cabang ON admin(cabang);
CREATE INDEX idx_admin_nama ON admin(nama);

-- Contoh data admin (password: "admin123" yang sudah di-hash dengan bcrypt)
INSERT INTO `admin` (`wa`, `nama`, `password`, `cabang`) VALUES
('628123456789', 'Admin Jakarta', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jakarta'),
('628987654321', 'Admin Surabaya', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Surabaya'),
('628111222333', 'Admin Bandung', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Bandung');

-- Perintah untuk melihat struktur tabel
-- DESCRIBE admin;

-- Perintah untuk melihat data
-- SELECT wa, nama, cabang, created_at FROM admin;