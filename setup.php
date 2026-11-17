<?php
/**
 * ============================================================
 *  Setup Otomatis Database & Akun Awal
 *  Buka file ini sekali melalui browser, lalu HAPUS.
 * ============================================================
 */

declare(strict_types=1);

date_default_timezone_set('Asia/Jakarta');

$DB_HOST = 'localhost';
$DB_NAME = 'db_login_hakakses';
$DB_USER = 'root';
$DB_PASS = '';

$seeds = [
    ['nama' => 'Administrator',   'username' => 'admin',    'password' => 'admin123',    'role' => 'admin'],
    ['nama' => 'Petugas Sekolah', 'username' => 'petugas',  'password' => 'petugas123',  'role' => 'petugas'],
    ['nama' => 'Siswa SMK',       'username' => 'siswa',    'password' => 'siswa123',    'role' => 'siswa'],
];

$success    = false;
$error      = null;
$inserted   = 0;
$totalUsers = 0;

try {
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    // 1. Koneksi ke server MySQL (tanpa memilih database)
    $server = new PDO(
        "mysql:host={$DB_HOST};charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        $options
    );

    // 2. Buat database jika belum ada
    $server->exec(
        "CREATE DATABASE IF NOT EXISTS `{$DB_NAME}`
         CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
    );

    // 3. Koneksi ke database
    $pdo = new PDO(
        "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        $options
    );

    // 4. Buat tabel users jika belum ada
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS `users` (
            `id`       INT          NOT NULL AUTO_INCREMENT,
            `nama`     VARCHAR(100) NOT NULL,
            `username` VARCHAR(50)  NOT NULL,
            `password` VARCHAR(255) NOT NULL,
            `role`     ENUM('admin','petugas','siswa') NOT NULL DEFAULT 'siswa',
            PRIMARY KEY (`id`),
            UNIQUE KEY `username` (`username`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );

    // 5. Isi akun awal (tanpa menimpa akun yang sudah ada)
    $stmt = $pdo->prepare(
        'INSERT IGNORE INTO users (nama, username, password, role) VALUES (?, ?, ?, ?)'
    );

    foreach ($seeds as $seed) {
        $stmt->execute([
            $seed['nama'],
            $seed['username'],
            password_hash($seed['password'], PASSWORD_DEFAULT),
            $seed['role'],
        ]);
        $inserted += $stmt->rowCount();
    }

    $totalUsers = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    $success    = true;
} catch (PDOException $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Setup Database &middot; SIMPHA</title>
    <link rel="icon" type="image/svg+xml" href="assets/img/favicon.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="setup-page">
    <div class="setup-wrap">
        <div class="setup-card">
            <div class="setup-head">
                <span class="setup-head-icon">
                    <i class="bi <?= $success ? 'bi-check2-circle' : 'bi-exclamation-triangle' ?>"></i>
                </span>
                <h1><?= $success ? 'Setup Berhasil' : 'Setup Gagal' ?></h1>
                <p><?= $success
                    ? 'Database, tabel, dan akun awal berhasil disiapkan.'
                    : 'Terjadi kesalahan saat menyiapkan database.' ?></p>
            </div>

            <div class="setup-body">
                <?php if ($success): ?>
                    <div class="alert alert-success d-flex gap-2 align-items-start">
                        <i class="bi bi-check-circle-fill mt-1"></i>
                        <div>
                            <strong>Database <code><?= htmlspecialchars($DB_NAME, ENT_QUOTES, 'UTF-8') ?></code> siap digunakan.</strong>
                            <div class="small mt-1">
                                <?= (int) $inserted ?> akun baru ditambahkan &middot;
                                total <strong><?= (int) $totalUsers ?> pengguna</strong> di database.
                                <?php if ($inserted === 0): ?>
                                    Akun awal sudah pernah dibuat, tidak ada data ganda.
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="setup-steps">
                        <div class="setup-step">
                            <span><i class="bi bi-1-circle-fill"></i></span>
                            <div>
                                <strong>Database dibuat</strong>
                                <small>Nama database: db_login_hakakses (utf8mb4)</small>
                            </div>
                        </div>
                        <div class="setup-step">
                            <span><i class="bi bi-2-circle-fill"></i></span>
                            <div>
                                <strong>Tabel users dibuat</strong>
                                <small>Kolom: id, nama, username, password, role</small>
                            </div>
                        </div>
                        <div class="setup-step">
                            <span><i class="bi bi-3-circle-fill"></i></span>
                            <div>
                                <strong>Akun awal terisi</strong>
                                <small>Password disimpan menggunakan password_hash()</small>
                            </div>
                        </div>
                    </div>

                    <h2 class="setup-subtitle">Akun Demo</h2>
                    <div class="table-responsive">
                        <table class="table table-modern align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Username</th>
                                    <th>Password</th>
                                    <th>Role</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($seeds as $seed): ?>
                                    <tr>
                                        <td class="fw-semibold"><?= htmlspecialchars($seed['nama'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><code><?= htmlspecialchars($seed['username'], ENT_QUOTES, 'UTF-8') ?></code></td>
                                        <td><code><?= htmlspecialchars($seed['password'], ENT_QUOTES, 'UTF-8') ?></code></td>
                                        <td><span class="badge-role badge-<?= htmlspecialchars($seed['role'], ENT_QUOTES, 'UTF-8') ?>">
                                            <?= htmlspecialchars(ucfirst($seed['role']), ENT_QUOTES, 'UTF-8') ?>
                                        </span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex flex-wrap gap-2 justify-content-center mt-4">
                        <a href="index.php" class="btn btn-danger rounded-3 px-4 py-2">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Buka Halaman Login
                        </a>
                        <a href="database.sql" class="btn btn-outline-secondary rounded-3 px-4 py-2" download>
                            <i class="bi bi-filetype-sql me-1"></i> Unduh database.sql
                        </a>
                    </div>

                    <div class="alert alert-warning d-flex gap-2 align-items-start mt-4 mb-0">
                        <i class="bi bi-shield-exclamation mt-1"></i>
                        <div>
                            <strong>Penting untuk keamanan:</strong>
                            hapus file <code>setup.php</code> setelah proses ini selesai agar tidak
                            dapat dijalankan ulang oleh orang lain.
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger">
                        <strong><i class="bi bi-exclamation-octagon me-1"></i> Gagal menyiapkan database</strong>
                        <div class="small mt-2 text-break"><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></div>
                    </div>
                    <div class="alert alert-light border small mb-4">
                        <strong>Periksa hal berikut:</strong>
                        <ul class="mb-0 mt-1 ps-3">
                            <li>MySQL sudah berjalan melalui XAMPP Control Panel.</li>
                            <li>Host <code>localhost</code>, user <code>root</code>, password kosong sesuai default XAMPP.</li>
                            <li>Port MySQL tidak bentrok dengan aplikasi lain.</li>
                        </ul>
                    </div>
                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                        <a href="setup.php" class="btn btn-danger rounded-3 px-4">
                            <i class="bi bi-arrow-clockwise me-1"></i> Coba Lagi
                        </a>
                        <a href="index.php" class="btn btn-outline-secondary rounded-3 px-4">Ke Login</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <p class="setup-footer">
            &copy; <?= date('Y') ?> Sistem Manajemen Pengguna &amp; Hak Akses
        </p>
    </div>
</body>
</html>
