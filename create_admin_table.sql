-- SQL script to create admin table
-- Run this on your dstmobil.com database

CREATE TABLE IF NOT EXISTS `admin` (
  `wa` varchar(20) NOT NULL PRIMARY KEY,
  `nama` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `cabang` varchar(50) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add index for better performance
CREATE INDEX idx_cabang ON admin(cabang);
CREATE INDEX idx_nama ON admin(nama);