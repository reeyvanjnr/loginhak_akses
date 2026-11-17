<?php
/**
 * ============================================================
 *  Konfigurasi Utama Aplikasi
 *  Sistem Manajemen Pengguna & Hak Akses
 * ============================================================
 */

declare(strict_types=1);

date_default_timezone_set('Asia/Jakarta');

// Identitas aplikasi
define('APP_NAME', 'Sistem Manajemen Pengguna & Hak Akses');
define('APP_SHORT', 'SIMPHA');
define('APP_VERSION', '1.0.0');

// Konfigurasi database
define('DB_HOST', 'localhost');
define('DB_NAME', 'db_login_hakakses');
define('DB_USER', 'root');
define('DB_PASS', '');

/**
 * Membuat koneksi PDO ke database MySQL.
 * Jika database / tabel belum tersedia, tampilkan halaman
 * panduan menuju setup.php.
 */
try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );

    // Pastikan tabel utama sudah tersedia
    $pdo->query('SELECT 1 FROM users LIMIT 1');
} catch (PDOException $e) {
    http_response_code(500);
    $errorMessage = $e->getMessage();
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Database Belum Siap &middot; <?= APP_SHORT ?></title>
        <link rel="icon" type="image/svg+xml" href="assets/img/favicon.svg">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
        <link href="assets/css/style.css" rel="stylesheet">
    </head>
    <body class="setup-page">
        <div class="setup-wrap">
            <div class="setup-card text-center">
                <div class="setup-head">
                    <span class="setup-head-icon"><i class="bi bi-database-exclamation"></i></span>
                    <h1>Database Belum Siap</h1>
                    <p>Aplikasi belum dapat terhubung ke database.</p>
                </div>
                <div class="setup-body">
                    <div class="alert alert-danger text-start">
                        <strong><i class="bi bi-exclamation-octagon me-1"></i> Detail kesalahan:</strong>
                        <div class="small mt-2 text-break"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></div>
                    </div>
                    <p class="text-secondary mb-4">
                        Pastikan MySQL di XAMPP sudah berjalan, lalu jalankan file
                        <strong>setup.php</strong> untuk membuat database, tabel, dan akun awal.
                    </p>
                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                        <a href="setup.php" class="btn btn-danger rounded-3 px-4">
                            <i class="bi bi-gear-fill me-1"></i> Jalankan Setup
                        </a>
                        <a href="index.php" class="btn btn-outline-secondary rounded-3 px-4">
                            <i class="bi bi-arrow-clockwise me-1"></i> Coba Lagi
                        </a>
                    </div>
                    <hr class="my-4">
                    <p class="small text-muted mb-0">
                        Konfigurasi saat ini: host <code><?= DB_HOST ?></code>,
                        database <code><?= DB_NAME ?></code>, user <code><?= DB_USER ?></code>.
                        Ubah pada file <code>config.php</code> bila diperlukan.
                    </p>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}
