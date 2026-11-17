<?php
/**
 * ============================================================
 *  Dashboard Siswa
 * ============================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/components.php';

cekRole('siswa');

$loginTime = isset($_SESSION['login_time']) ? (int) $_SESSION['login_time'] : null;

$page_title    = 'Dashboard Siswa';
$page_subtitle = 'Selamat datang, ' . $_SESSION['nama'] . '. Berikut ringkasan aktivitas belajar Anda.';
$active_menu   = 'dashboard';

include __DIR__ . '/header.php';

renderKpiCards([
    [
        'label' => 'Mata Pelajaran',
        'value' => '8',
        'icon'  => 'bi-journal-bookmark-fill',
        'theme' => 'red',
        'foot'  => 'Semester ganjil',
    ],
    [
        'label' => 'Tugas Aktif',
        'value' => '4',
        'icon'  => 'bi-clipboard-check-fill',
        'theme' => 'blue',
        'foot'  => '2 mendekati tenggat',
    ],
    [
        'label' => 'Rata-rata Nilai',
        'value' => '86.5',
        'icon'  => 'bi-graph-up-arrow',
        'theme' => 'blue-mid',
        'foot'  => 'Predikat baik',
    ],
    [
        'label' => 'Kehadiran',
        'value' => '96%',
        'icon'  => 'bi-calendar2-check-fill',
        'theme' => 'gray',
        'foot'  => 'Bulan berjalan',
    ],
]);
?>

<p class="data-note">
    <i class="bi bi-info-circle"></i>
    Kartu statistik siswa menampilkan ringkasan akademik contoh untuk keperluan tampilan dashboard.
</p>

<div class="row g-4 mt-1">
    <div class="col-xl-8">
        <?php
        renderAccessPanel('siswa', [
            ['title' => 'Dashboard Siswa', 'desc' => 'Melihat ringkasan informasi akademik dan agenda belajar.'],
            ['title' => 'Profil Saya', 'desc' => 'Mengubah nama lengkap dan mengganti password akun sendiri.'],
            ['title' => 'Akses Mandiri', 'desc' => 'Hanya dapat mengakses data milik akun yang sedang login.'],
            ['title' => 'Tanpa Menu Admin', 'desc' => 'Daftar Pengguna tidak muncul dan tetap diblokir oleh server untuk role siswa.'],
            ['title' => 'Akun Terlindungi', 'desc' => 'Password ter-hash dan session diperbarui setelah login berhasil.'],
        ]);
        ?>
    </div>

    <div class="col-xl-4">
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
                    <span class="panel-icon"><i class="bi bi-calendar3-week"></i></span>
                    <div>
                        <h2>Jadwal Hari Ini</h2>
                        <p>Mata pelajaran sesuai kelas Anda</p>
                    </div>
                </div>
                <span class="badge-soft badge-blue"><?= e(tanggalIndonesia()) ?></span>
            </div>
            <div class="panel-body p-0">
                <div class="schedule-list">
                    <div class="schedule-item">
                        <span class="schedule-time">07.30</span>
                        <span class="schedule-mark mark-blue"></span>
                        <div class="schedule-body">
                            <strong>Matematika</strong>
                            <small>Ruang X-1 &middot; Bpk. Andi</small>
                        </div>
                        <span class="badge-soft badge-gray">Selesai</span>
                    </div>
                    <div class="schedule-item">
                        <span class="schedule-time">09.00</span>
                        <span class="schedule-mark mark-red"></span>
                        <div class="schedule-body">
                            <strong>Bahasa Indonesia</strong>
                            <small>Ruang X-1 &middot; Ibu Sari</small>
                        </div>
                        <span class="badge-soft badge-red">Berlangsung</span>
                    </div>
                    <div class="schedule-item">
                        <span class="schedule-time">10.30</span>
                        <span class="schedule-mark mark-blue"></span>
                        <div class="schedule-body">
                            <strong>Pemrograman Web</strong>
                            <small>Lab Komputer 2 &middot; Bpk. Rudi</small>
                        </div>
                        <span class="badge-soft badge-blue">Berikutnya</span>
                    </div>
                    <div class="schedule-item">
                        <span class="schedule-time">13.00</span>
                        <span class="schedule-mark mark-gray"></span>
                        <div class="schedule-body">
                            <strong>Pendidikan Pancasila</strong>
                            <small>Ruang X-1 &middot; Ibu Dewi</small>
                        </div>
                        <span class="badge-soft badge-gray">Terjadwal</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="panel-card h-100">
            <div class="panel-head">
                <div class="panel-title">
                    <span class="panel-icon"><i class="bi bi-megaphone-fill"></i></span>
                    <div>
                        <h2>Pengumuman</h2>
                        <p>Informasi terbaru untuk siswa</p>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div class="announcement">
                    <span class="announcement-icon announcement-red"><i class="bi bi-exclamation-lg"></i></span>
                    <div>
                        <strong>Ujian Tengah Semester</strong>
                        <p>Ujian dimulai 24 November 2026. Pastikan seluruh administrasi sudah lunas.</p>
                        <small><i class="bi bi-clock me-1"></i> 2 hari lalu</small>
                    </div>
                </div>
                <div class="announcement">
                    <span class="announcement-icon announcement-blue"><i class="bi bi-code-slash"></i></span>
                    <div>
                        <strong>Tugas Praktik Pemrograman Web</strong>
                        <p>Kumpulkan proyek CRUD paling lambat hari Jumat pukul 23.59 WIB.</p>
                        <small><i class="bi bi-clock me-1"></i> 3 hari lalu</small>
                    </div>
                </div>
                <div class="announcement">
                    <span class="announcement-icon announcement-gray"><i class="bi bi-info-lg"></i></span>
                    <div>
                        <strong>Perubahan Jam Pelajaran</strong>
                        <p>Jam pelajaran hari Senin dimulai pukul 07.00 WIB mulai pekan depan.</p>
                        <small><i class="bi bi-clock me-1"></i> 5 hari lalu</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
