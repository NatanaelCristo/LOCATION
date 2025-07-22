-- SQL script to create admin table
-- Database: dste9565_headoffice

CREATE TABLE IF NOT EXISTS `admin` (
  `wa` varchar(20) NOT NULL COMMENT 'WhatsApp number as primary key',
  `nama` varchar(100) NOT NULL COMMENT 'Full name of admin',
  `password` varchar(255) NOT NULL COMMENT 'Encrypted password using bcrypt',
  `cabang` varchar(50) NOT NULL COMMENT 'Branch name',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`wa`),
  INDEX `idx_cabang` (`cabang`),
  INDEX `idx_nama` (`nama`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample data (optional)
-- INSERT INTO `admin` (`wa`, `nama`, `password`, `cabang`) VALUES
-- ('628123456789', 'Admin Jakarta', '$2y$10$example_hash_here', 'Jakarta'),
-- ('628987654321', 'Admin Tangerang', '$2y$10$example_hash_here', 'Tangerang');