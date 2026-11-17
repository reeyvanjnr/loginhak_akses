<?php
/**
 * ============================================================
 *  Dashboard Administrator
 * ============================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/components.php';

cekRole('admin');

// Statistik pengguna
$counts = ['admin' => 0, 'petugas' => 0, 'siswa' => 0];
foreach ($pdo->query('SELECT role, COUNT(*) AS total FROM users GROUP BY role')->fetchAll() as $row) {
    if (isset($counts[$row['role']])) {
        $counts[$row['role']] = (int) $row['total'];
    }
}
$totalUsers = array_sum($counts);

// Pengguna terbaru
$latestUsers = $pdo->query(
    'SELECT id, nama, username, role FROM users ORDER BY id DESC LIMIT 5'
)->fetchAll();

$loginTime = isset($_SESSION['login_time']) ? (int) $_SESSION['login_time'] : null;

$page_title    = 'Dashboard Admin';
$page_subtitle = 'Selamat datang kembali, ' . $_SESSION['nama'] . '. Berikut ringkasan sistem hari ini.';
$active_menu   = 'dashboard';

include __DIR__ . '/header.php';

renderKpiCards([
    [
        'label' => 'Total Pengguna',
        'value' => $totalUsers,
        'icon'  => 'bi-people-fill',
        'theme' => 'red',
        'foot'  => 'Seluruh akun terdaftar',
    ],
    [
        'label' => 'Administrator',
        'value' => $counts['admin'],
        'icon'  => 'bi-shield-fill-check',
        'theme' => 'blue',
        'foot'  => 'Akses penuh sistem',
    ],
    [
        'label' => 'Petugas',
        'value' => $counts['petugas'],
        'icon'  => 'bi-person-badge-fill',
        'theme' => 'blue-mid',
        'foot'  => 'Akses operasional',
    ],
    [
        'label' => 'Siswa',
        'value' => $counts['siswa'],
        'icon'  => 'bi-mortarboard-fill',
        'theme' => 'gray',
        'foot'  => 'Akses mandiri',
    ],
]);
?>

<div class="row g-4 mt-1">
    <div class="col-xl-8">
        <?php
        renderAccessPanel('admin', [
            ['title' => 'Kelola Pengguna', 'desc' => 'Tambah, ubah, dan hapus akun pengguna melalui halaman Daftar Pengguna.'],
            ['title' => 'Atur Role & Hak Akses', 'desc' => 'Tentukan peran admin, petugas, atau siswa untuk setiap akun.'],
            ['title' => 'Pantau Statistik Sistem', 'desc' => 'Lihat ringkasan jumlah pengguna dan distribusi role secara real-time.'],
            ['title' => 'Akses Seluruh Dashboard', 'desc' => 'Menu Daftar Pengguna hanya tersedia untuk role administrator.'],
            ['title' => 'Keamanan Data', 'desc' => 'Semua password disimpan dalam bentuk hash dan query memakai prepared statement.'],
        ]);
        ?>
    </div>

    <div class="col-xl-4">
        <div class="panel-card mb-4">
            <div class="panel-head compact">
                <div class="panel-title">
                    <span class="panel-icon"><i class="bi bi-lightning-charge-fill"></i></span>
                    <div>
                        <h2>Aksi Cepat</h2>
                        <p>Menu yang sering digunakan</p>
                    </div>
                </div>
            </div>
            <div class="panel-body quick-actions">
                <a href="daftar_pengguna.php?add=1" class="quick-action">
                    <span class="quick-icon quick-red"><i class="bi bi-person-plus-fill"></i></span>
                    <span>
                        <strong>Tambah Pengguna</strong>
                        <small>Buat akun baru</small>
                    </span>
                    <i class="bi bi-arrow-right ms-auto"></i>
                </a>
                <a href="daftar_pengguna.php" class="quick-action">
                    <span class="quick-icon quick-blue"><i class="bi bi-people-fill"></i></span>
                    <span>
                        <strong>Kelola Pengguna</strong>
                        <small>Lihat &amp; ubah data</small>
                    </span>
                    <i class="bi bi-arrow-right ms-auto"></i>
                </a>
                <a href="profil.php" class="quick-action">
                    <span class="quick-icon quick-gray"><i class="bi bi-person-gear"></i></span>
                    <span>
                        <strong>Profil Saya</strong>
                        <small>Perbarui akun</small>
                    </span>
                    <i class="bi bi-arrow-right ms-auto"></i>
                </a>
            </div>
        </div>

        <?php renderAccountCard(
            (string) $_SESSION['nama'],
            (string) $_SESSION['username'],
            (string) $_SESSION['role'],
            $loginTime
        ); ?>
    </div>
</div>

<div class="panel-card mt-4">
    <div class="panel-head">
        <div class="panel-title">
            <span class="panel-icon"><i class="bi bi-clock-history"></i></span>
            <div>
                <h2>Pengguna Terbaru</h2>
                <p>5 akun terakhir yang ditambahkan</p>
            </div>
        </div>
        <a href="daftar_pengguna.php" class="btn btn-sm btn-outline-primary rounded-pill px-3">
            Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>

    <?php if ($latestUsers): ?>
        <div class="table-responsive">
            <table class="table table-modern align-middle mb-0">
                <thead>
                    <tr>
                        <th>Pengguna</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($latestUsers as $user): ?>
                        <tr>
                            <td>
                                <div class="user-cell">
                                    <span class="avatar avatar-<?= e($user['role']) ?>"><?= e(initials((string) $user['nama'])) ?></span>
                                    <div>
                                        <strong><?= e($user['nama']) ?></strong>
                                        <small>ID #<?= (int) $user['id'] ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><code class="username-code"><?= e($user['username']) ?></code></td>
                            <td><?= roleBadge((string) $user['role']) ?></td>
                            <td><span class="status-active"><span class="status-dot"></span> Aktif</span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <span class="empty-icon"><i class="bi bi-people"></i></span>
            <h3>Belum ada pengguna</h3>
            <p>Tambahkan pengguna pertama melalui halaman Daftar Pengguna.</p>
            <a href="daftar_pengguna.php" class="btn btn-danger rounded-3">
                <i class="bi bi-plus-lg me-1"></i> Tambah Pengguna
            </a>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/footer.php'; ?>
