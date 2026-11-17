-- ============================================================
--  Sistem Manajemen Pengguna & Hak Akses
--  Database : db_login_hakakses
--  Engine   : MySQL / MariaDB
-- ============================================================
--  Catatan:
--  File ini hanya membuat struktur database & tabel.
--  Untuk mengisi 3 akun awal (admin, petugas, siswa) secara
--  otomatis beserta password yang sudah di-hash, buka:
--      http://localhost/login_hakakses/setup.php
-- ============================================================

CREATE DATABASE IF NOT EXISTS `db_login_hakakses`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `db_login_hakakses`;

-- Struktur tabel users
CREATE TABLE IF NOT EXISTS `users` (
  `id`       INT          NOT NULL AUTO_INCREMENT,
  `nama`     VARCHAR(100) NOT NULL,
  `username` VARCHAR(50)  NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role`     ENUM('admin','petugas','siswa') NOT NULL DEFAULT 'siswa',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;
