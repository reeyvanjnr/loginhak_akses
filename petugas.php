<?php
/**
 * ============================================================
 *  Dashboard Petugas
 * ============================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/components.php';

cekRole('petugas');

$loginTime = isset($_SESSION['login_time']) ? (int) $_SESSION['login_time'] : null;

$page_title    = 'Dashboard Petugas';
$page_subtitle = 'Selamat datang kembali, ' . $_SESSION['nama'] . '. Berikut ringkasan aktivitas operasional Anda.';
$active_menu   = 'dashboard';

include __DIR__ . '/header.php';

renderKpiCards([
    [
        'label' => 'Tugas Hari Ini',
        'value' => '8',
        'icon'  => 'bi-clipboard-check-fill',
        'theme' => 'red',
        'foot'  => '3 prioritas tinggi',
    ],
    [
        'label' => 'Pengajuan Baru',
        'value' => '5',
        'icon'  => 'bi-inbox-fill',
        'theme' => 'blue',
        'foot'  => 'Menunggu verifikasi',
    ],
    [
        'label' => 'Sedang Diproses',
        'value' => '3',
        'icon'  => 'bi-arrow-repeat',
        'theme' => 'blue-mid',
        'foot'  => 'Dalam penanganan',
    ],
    [
        'label' => 'Selesai',
        'value' => '12',
        'icon'  => 'bi-check2-all',
        'theme' => 'gray',
        'foot'  => 'Minggu ini',
    ],
]);
?>

<p class="data-note">
    <i class="bi bi-info-circle"></i>
    Kartu statistik petugas menampilkan ringkasan operasional contoh untuk keperluan tampilan dashboard.
</p>

<div class="row g-4 mt-1">
    <div class="col-xl-8">
        <?php
        renderAccessPanel('petugas', [
            ['title' => 'Dashboard Petugas', 'desc' => 'Melihat ringkasan aktivitas dan agenda operasional sekolah.'],
            ['title' => 'Profil Pribadi', 'desc' => 'Memperbarui nama lengkap serta mengganti password akun sendiri.'],
            ['title' => 'Akses Terbatas', 'desc' => 'Tidak dapat membuka halaman Daftar Pengguna yang khusus admin.'],
            ['title' => 'Navigasi Aman', 'desc' => 'Setiap halaman diperiksa ulang role-nya di sisi server.'],
            ['title' => 'Data Terlindungi', 'desc' => 'Password tersimpan sebagai hash dan output di-escape dari XSS.'],
        ]);
        ?>
    </div>

    <div class="col-xl-4">
        <div class="panel-card mb-4">
            <div class="panel-head compact">
                <div class="panel-title">
                    <span class="panel-icon"><i class="bi bi-list-task"></i></span>
                    <div>
                        <h2>Agenda Hari Ini</h2>
                        <p>Prioritas pekerjaan petugas</p>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div class="task-list">
                    <div class="task-item">
                        <span class="task-time">08.00</span>
                        <div class="task-body">
                            <strong>Verifikasi data siswa baru</strong>
                            <small>Berkas pendaftaran gelombang 2</small>
                        </div>
                        <span class="badge-soft badge-red">Prioritas</span>
                    </div>
                    <div class="task-item">
                        <span class="task-time">10.30</span>
                        <div class="task-body">
                            <strong>Rekap absensi kelas X</strong>
                            <small>Koordinasi dengan wali kelas</small>
                        </div>
                        <span class="badge-soft badge-blue">Proses</span>
                    </div>
                    <div class="task-item">
                        <span class="task-time">14.00</span>
                        <div class="task-body">
                            <strong>Susun laporan bulanan</strong>
                            <small>Dokumen administrasi sekolah</small>
                        </div>
                        <span class="badge-soft badge-gray">Menunggu</span>
                    </div>
                </div>
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

<div class="row g-4 mt-1">
    <div class="col-lg-7">
        <div class="panel-card h-100">
            <div class="panel-head">
                <div class="panel-title">
                    <span class="panel-icon"><i class="bi bi-clipboard2-data"></i></span>
                    <div>
                        <h2>Alur Kerja Petugas</h2>
                        <p>Tahapan operasional harian</p>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <span class="timeline-dot"></span>
                        <div>
                            <strong>Terima pengajuan</strong>
                            <small>Periksa kelengkapan berkas yang masuk dari unit terkait.</small>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <span class="timeline-dot"></span>
                        <div>
                            <strong>Verifikasi data</strong>
                            <small>Cocokkan data dengan arsip sekolah dan catat perubahan.</small>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <span class="timeline-dot"></span>
                        <div>
                            <strong>Serahkan hasil</strong>
                            <small>Teruskan laporan yang sudah selesai kepada administrator.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="panel-card h-100">
            <div class="panel-head">
                <div class="panel-title">
                    <span class="panel-icon"><i class="bi bi-shield-lock"></i></span>
                    <div>
                        <h2>Catatan Keamanan</h2>
                        <p>Praktik baik penggunaan akun</p>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <ul class="security-list">
                    <li><i class="bi bi-check2-circle"></i> Selalu logout setelah selesai menggunakan perangkat bersama.</li>
                    <li><i class="bi bi-check2-circle"></i> Gunakan password yang kuat dan tidak dipakai di layanan lain.</li>
                    <li><i class="bi bi-check2-circle"></i> Jangan membagikan akun kepada rekan kerja.</li>
                    <li><i class="bi bi-check2-circle"></i> Laporkan aktivitas mencurigakan kepada administrator.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
