<?php
/**
 * ============================================================
 *  Profil Saya - Dapat diakses semua role
 * ============================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

cekLogin();

$userId = (int) ($_SESSION['user_id'] ?? 0);

$stmt = $pdo->prepare('SELECT id, nama, username, password, role FROM users WHERE id = ? LIMIT 1');
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION = [];
    session_destroy();
    redirect('index.php');
}

// ------------------------------------------------------------
// Proses update profil
// ------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!checkCsrf()) {
        setFlash('danger', 'Token keamanan tidak valid. Silakan muat ulang halaman dan coba lagi.');
        redirect('profil.php');
    }

    $action = (string) ($_POST['action'] ?? '');

    // ---------------- Ubah nama lengkap ----------------
    if ($action === 'ubah_nama') {
        $nama = trim((string) ($_POST['nama'] ?? ''));

        if ($nama === '') {
            setFlash('danger', 'Nama lengkap tidak boleh kosong.');
        } elseif (mb_strlen($nama, 'UTF-8') > 100) {
            setFlash('danger', 'Nama lengkap maksimal 100 karakter.');
        } else {
            $update = $pdo->prepare('UPDATE users SET nama = ? WHERE id = ?');
            $update->execute([$nama, $userId]);

            $_SESSION['nama'] = $nama;
            setFlash('success', 'Nama lengkap berhasil diperbarui menjadi "' . $nama . '".');
        }

        redirect('profil.php');
    }

    // ---------------- Ganti password ----------------
    if ($action === 'ganti_password') {
        $passwordLama     = (string) ($_POST['password_lama'] ?? '');
        $passwordBaru     = (string) ($_POST['password_baru'] ?? '');
        $passwordKonfirm  = (string) ($_POST['password_konfirmasi'] ?? '');

        if ($passwordLama === '' || $passwordBaru === '' || $passwordKonfirm === '') {
            setFlash('danger', 'Seluruh kolom password wajib diisi.');
        } elseif (!password_verify($passwordLama, (string) $user['password'])) {
            setFlash('danger', 'Password lama tidak sesuai. Periksa kembali.');
        } elseif (strlen($passwordBaru) < 6) {
            setFlash('danger', 'Password baru minimal 6 karakter.');
        } elseif ($passwordBaru !== $passwordKonfirm) {
            setFlash('danger', 'Konfirmasi password baru tidak cocok.');
        } elseif ($passwordBaru === $passwordLama) {
            setFlash('warning', 'Password baru harus berbeda dari password lama.');
        } else {
            $update = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
            $update->execute([password_hash($passwordBaru, PASSWORD_DEFAULT), $userId]);

            setFlash('success', 'Password berhasil diganti. Gunakan password baru pada login berikutnya.');
        }

        redirect('profil.php');
    }

    setFlash('danger', 'Aksi tidak dikenali.');
    redirect('profil.php');
}

$loginTime = isset($_SESSION['login_time']) ? (int) $_SESSION['login_time'] : null;

$page_title    = 'Profil Saya';
$page_subtitle = 'Kelola informasi akun dan keamanan password Anda.';
$active_menu   = 'profil';

include __DIR__ . '/header.php';
?>

<div class="row g-4">
    <!-- Kartu profil -->
    <div class="col-xl-4">
        <div class="panel-card profile-card">
            <div class="profile-cover">
                <span class="profile-cover-icon"><i class="bi bi-person-badge-fill"></i></span>
            </div>
            <div class="profile-body">
                <span class="profile-avatar"><?= e(initials((string) $user['nama'])) ?></span>
                <h2><?= e($user['nama']) ?></h2>
                <p class="profile-username">@<?= e($user['username']) ?></p>
                <div class="mb-4"><?= roleBadge((string) $user['role']) ?></div>

                <div class="profile-meta">
                    <div>
                        <span>ID Pengguna</span>
                        <strong>#<?= (int) $user['id'] ?></strong>
                    </div>
                    <div>
                        <span>Status Akun</span>
                        <strong class="status-active"><span class="status-dot"></span> Aktif</strong>
                    </div>
                    <div>
                        <span>Login Terakhir</span>
                        <strong class="small">
                            <?= e($loginTime ? tanggalIndonesia($loginTime) . ', ' . date('H:i', $loginTime) . ' WIB' : '-') ?>
                        </strong>
                    </div>
                </div>

                <div class="profile-security-note">
                    <i class="bi bi-shield-check"></i>
                    <span>Akun ini dilindungi oleh session server dan password ter-hash.</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Form pengaturan -->
    <div class="col-xl-8">
        <!-- Ubah nama -->
        <div class="panel-card mb-4">
            <div class="panel-head">
                <div class="panel-title">
                    <span class="panel-icon"><i class="bi bi-person-vcard-fill"></i></span>
                    <div>
                        <h2>Ubah Nama Lengkap</h2>
                        <p>Nama ini akan tampil pada dashboard dan header aplikasi</p>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <form method="post" action="profil.php" novalidate>
                    <?= csrfField() ?>
                    <input type="hidden" name="action" value="ubah_nama">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="readonlyUsername" class="form-label">Username</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control bg-light" id="readonlyUsername" value="<?= e($user['username']) ?>" readonly>
                                <span class="input-group-text bg-light" title="Hanya dapat diubah oleh administrator">
                                    <i class="bi bi-lock-fill text-muted"></i>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="readonlyRole" class="form-label">Role</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi <?= e(roleIcon((string) $user['role'])) ?>"></i></span>
                                <input type="text" class="form-control bg-light" id="readonlyRole" value="<?= e(roleLabel((string) $user['role'])) ?>" readonly>
                                <span class="input-group-text bg-light" title="Hanya dapat diubah oleh administrator">
                                    <i class="bi bi-lock-fill text-muted"></i>
                                </span>
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="nama" class="form-label">Nama Lengkap</label>
                            <input
                                type="text"
                                class="form-control"
                                id="nama"
                                name="nama"
                                value="<?= e($user['nama']) ?>"
                                maxlength="100"
                                placeholder="Masukkan nama lengkap"
                                autocomplete="name"
                                required
                            >
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Username dan role bersifat read-only. Hubungi administrator jika perlu diubah.
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-danger rounded-3 px-4">
                            <i class="bi bi-check-lg me-1"></i> Simpan Nama
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Ganti password -->
        <div class="panel-card">
            <div class="panel-head">
                <div class="panel-title">
                    <span class="panel-icon"><i class="bi bi-key-fill"></i></span>
                    <div>
                        <h2>Ganti Password</h2>
                        <p>Verifikasi password lama sebelum membuat password baru</p>
                    </div>
                </div>
                <span class="badge-soft badge-blue"><i class="bi bi-shield-lock me-1"></i> Aman</span>
            </div>
            <div class="panel-body">
                <form method="post" action="profil.php" novalidate>
                    <?= csrfField() ?>
                    <input type="hidden" name="action" value="ganti_password">

                    <div class="mb-3">
                        <label for="passwordLama" class="form-label">Password Lama <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="passwordLama" name="password_lama" placeholder="Masukkan password saat ini" autocomplete="current-password" required>
                            <button class="btn btn-outline-secondary" type="button" data-toggle-password="passwordLama" aria-label="Tampilkan password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="passwordBaru" class="form-label">Password Baru <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-key"></i></span>
                                <input type="password" class="form-control" id="passwordBaru" name="password_baru" placeholder="Minimal 6 karakter" autocomplete="new-password" required>
                                <button class="btn btn-outline-secondary" type="button" data-toggle-password="passwordBaru" aria-label="Tampilkan password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="passwordKonfirmasi" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-key-fill"></i></span>
                                <input type="password" class="form-control" id="passwordKonfirmasi" name="password_konfirmasi" placeholder="Ulangi password baru" autocomplete="new-password" required>
                                <button class="btn btn-outline-secondary" type="button" data-toggle-password="passwordKonfirmasi" aria-label="Tampilkan password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="password-hint mt-3">
                        <i class="bi bi-lightbulb"></i>
                        <span>Gunakan kombinasi huruf besar, huruf kecil, angka, dan simbol agar password lebih kuat.</span>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-danger rounded-3 px-4">
                            <i class="bi bi-shield-lock me-1"></i> Simpan Password Baru
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
