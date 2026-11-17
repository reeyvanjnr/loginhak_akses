<?php
/**
 * ============================================================
 *  Daftar Pengguna - Khusus Administrator
 * ============================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

cekRole('admin');

$allowedRoles = ['admin', 'petugas', 'siswa'];
$currentId    = (int) ($_SESSION['user_id'] ?? 0);

/**
 * Validasi data pengguna. Mengembalikan array pesan error.
 */
function validasiPengguna(
    string $nama,
    string $username,
    string $role,
    array $allowedRoles,
    ?string $password = null
): array {
    $errors = [];

    if ($nama === '') {
        $errors[] = 'Nama lengkap wajib diisi.';
    } elseif (mb_strlen($nama, 'UTF-8') > 100) {
        $errors[] = 'Nama lengkap maksimal 100 karakter.';
    }

    if ($username === '') {
        $errors[] = 'Username wajib diisi.';
    } elseif (!preg_match('/^[A-Za-z0-9._-]{3,50}$/', $username)) {
        $errors[] = 'Username harus 3-50 karakter dan hanya boleh berisi huruf, angka, titik, garis bawah, atau strip.';
    }

    if (!in_array($role, $allowedRoles, true)) {
        $errors[] = 'Role yang dipilih tidak valid.';
    }

    if ($password !== null && strlen($password) < 6) {
        $errors[] = 'Password minimal 6 karakter.';
    }

    return $errors;
}

// ------------------------------------------------------------
// Proses aksi POST (Tambah / Edit / Hapus)
// ------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!checkCsrf()) {
        setFlash('danger', 'Token keamanan tidak valid. Silakan muat ulang halaman dan coba lagi.');
        redirect('daftar_pengguna.php');
    }

    $action = (string) ($_POST['action'] ?? '');

    try {
        // ---------------- TAMBAH PENGGUNA ----------------
        if ($action === 'tambah') {
            $nama     = trim((string) ($_POST['nama'] ?? ''));
            $username = trim((string) ($_POST['username'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');
            $role     = (string) ($_POST['role'] ?? '');

            $errors = validasiPengguna($nama, $username, $role, $allowedRoles, $password);
            if ($errors) {
                setFlash('danger', implode(' ', $errors));
                redirect('daftar_pengguna.php');
            }

            $check = $pdo->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
            $check->execute([$username]);
            if ($check->fetch()) {
                setFlash('danger', 'Username "' . $username . '" sudah digunakan. Pilih username lain.');
                redirect('daftar_pengguna.php');
            }

            $stmt = $pdo->prepare(
                'INSERT INTO users (nama, username, password, role) VALUES (?, ?, ?, ?)'
            );
            $stmt->execute([
                $nama,
                $username,
                password_hash($password, PASSWORD_DEFAULT),
                $role,
            ]);

            setFlash('success', 'Pengguna "' . $nama . '" berhasil ditambahkan dengan role ' . roleLabel($role) . '.');
            redirect('daftar_pengguna.php');
        }

        // ---------------- EDIT PENGGUNA ----------------
        if ($action === 'edit') {
            $id       = (int) (filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT) ?: 0);
            $nama     = trim((string) ($_POST['nama'] ?? ''));
            $username = trim((string) ($_POST['username'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');
            $role     = (string) ($_POST['role'] ?? '');

            $stmt = $pdo->prepare('SELECT id, role FROM users WHERE id = ? LIMIT 1');
            $stmt->execute([$id]);
            $target = $stmt->fetch();

            if (!$target) {
                setFlash('danger', 'Data pengguna tidak ditemukan.');
                redirect('daftar_pengguna.php');
            }

            $errors = validasiPengguna(
                $nama,
                $username,
                $role,
                $allowedRoles,
                $password !== '' ? $password : null
            );

            if ((int) $target['id'] === $currentId && $role !== 'admin') {
                $errors[] = 'Anda tidak dapat menurunkan role akun sendiri.';
            }

            if ((int) $target['id'] !== $currentId && $target['role'] === 'admin' && $role !== 'admin') {
                $adminCount = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
                if ($adminCount <= 1) {
                    $errors[] = 'Sistem harus memiliki minimal satu administrator.';
                }
            }

            if ($errors) {
                setFlash('danger', implode(' ', $errors));
                redirect('daftar_pengguna.php');
            }

            $check = $pdo->prepare('SELECT id FROM users WHERE username = ? AND id <> ? LIMIT 1');
            $check->execute([$username, $id]);
            if ($check->fetch()) {
                setFlash('danger', 'Username "' . $username . '" sudah digunakan oleh pengguna lain.');
                redirect('daftar_pengguna.php');
            }

            if ($password !== '') {
                $stmt = $pdo->prepare(
                    'UPDATE users SET nama = ?, username = ?, role = ?, password = ? WHERE id = ?'
                );
                $stmt->execute([
                    $nama,
                    $username,
                    $role,
                    password_hash($password, PASSWORD_DEFAULT),
                    $id,
                ]);
            } else {
                $stmt = $pdo->prepare('UPDATE users SET nama = ?, username = ?, role = ? WHERE id = ?');
                $stmt->execute([$nama, $username, $role, $id]);
            }

            // Sinkronkan session bila admin mengubah akunnya sendiri
            if ($id === $currentId) {
                $_SESSION['nama']     = $nama;
                $_SESSION['username'] = $username;
            }

            setFlash('success', 'Data pengguna "' . $nama . '" berhasil diperbarui.');
            redirect('daftar_pengguna.php');
        }

        // ---------------- HAPUS PENGGUNA ----------------
        if ($action === 'hapus') {
            $id = (int) (filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT) ?: 0);

            if ($id === $currentId) {
                setFlash('danger', 'Anda tidak dapat menghapus akun yang sedang digunakan.');
                redirect('daftar_pengguna.php');
            }

            $stmt = $pdo->prepare('SELECT id, nama, role FROM users WHERE id = ? LIMIT 1');
            $stmt->execute([$id]);
            $target = $stmt->fetch();

            if (!$target) {
                setFlash('danger', 'Data pengguna tidak ditemukan.');
                redirect('daftar_pengguna.php');
            }

            if ($target['role'] === 'admin') {
                $adminCount = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
                if ($adminCount <= 1) {
                    setFlash('danger', 'Akun administrator terakhir tidak boleh dihapus.');
                    redirect('daftar_pengguna.php');
                }
            }

            $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
            $stmt->execute([$id]);

            setFlash('success', 'Pengguna "' . $target['nama'] . '" berhasil dihapus.');
            redirect('daftar_pengguna.php');
        }

        setFlash('danger', 'Aksi tidak dikenali.');
        redirect('daftar_pengguna.php');
    } catch (PDOException $e) {
        $message = $e->getCode() === '23000'
            ? 'Username sudah digunakan atau data melanggar aturan database.'
            : 'Terjadi kesalahan pada database. Silakan coba lagi.';
        setFlash('danger', $message);
        redirect('daftar_pengguna.php');
    }
}

// ------------------------------------------------------------
// Statistik & daftar pengguna
// ------------------------------------------------------------
$counts = ['admin' => 0, 'petugas' => 0, 'siswa' => 0];
foreach ($pdo->query('SELECT role, COUNT(*) AS total FROM users GROUP BY role')->fetchAll() as $row) {
    if (isset($counts[$row['role']])) {
        $counts[$row['role']] = (int) $row['total'];
    }
}
$totalUsers = array_sum($counts);

$q          = trim((string) ($_GET['q'] ?? ''));
$roleFilter = (string) ($_GET['role'] ?? '');
if (!in_array($roleFilter, $allowedRoles, true)) {
    $roleFilter = '';
}

$where  = [];
$params = [];

if ($q !== '') {
    $where[]  = '(nama LIKE ? OR username LIKE ?)';
    $like     = '%' . $q . '%';
    $params[] = $like;
    $params[] = $like;
}
if ($roleFilter !== '') {
    $where[]  = 'role = ?';
    $params[] = $roleFilter;
}

$sql = 'SELECT id, nama, username, role FROM users';
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= " ORDER BY FIELD(role, 'admin', 'petugas', 'siswa'), nama ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();

$hasFilter = $q !== '' || $roleFilter !== '';

$page_title    = 'Daftar Pengguna';
$page_subtitle = 'Kelola seluruh akun, role, dan hak akses pengguna sistem.';
$active_menu   = 'pengguna';

include __DIR__ . '/header.php';
?>

<!-- Ringkasan statistik -->
<div class="row g-3">
    <div class="col-6 col-xl-3">
        <div class="stat-tile">
            <span class="stat-icon stat-red"><i class="bi bi-people-fill"></i></span>
            <div>
                <span>Total Pengguna</span>
                <strong><?= $totalUsers ?></strong>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-tile">
            <span class="stat-icon stat-blue"><i class="bi bi-shield-fill-check"></i></span>
            <div>
                <span>Administrator</span>
                <strong><?= $counts['admin'] ?></strong>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-tile">
            <span class="stat-icon stat-blue-mid"><i class="bi bi-person-badge-fill"></i></span>
            <div>
                <span>Petugas</span>
                <strong><?= $counts['petugas'] ?></strong>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-tile">
            <span class="stat-icon stat-gray"><i class="bi bi-mortarboard-fill"></i></span>
            <div>
                <span>Siswa</span>
                <strong><?= $counts['siswa'] ?></strong>
            </div>
        </div>
    </div>
</div>

<!-- Bar filter & pencarian -->
<div class="panel-card filter-card mt-4">
    <form method="get" action="daftar_pengguna.php" class="filter-form">
        <div class="filter-search">
            <i class="bi bi-search"></i>
            <input
                type="search"
                name="q"
                id="userSearch"
                value="<?= e($q) ?>"
                placeholder="Cari nama atau username..."
                autocomplete="off"
                aria-label="Cari pengguna"
            >
            <?php if ($q !== ''): ?>
                <a href="daftar_pengguna.php<?= $roleFilter !== '' ? '?role=' . e($roleFilter) : '' ?>"
                   class="filter-clear" title="Hapus kata kunci">
                    <i class="bi bi-x-lg"></i>
                </a>
            <?php endif; ?>
        </div>

        <select name="role" class="form-select filter-role" aria-label="Filter role">
            <option value="">Semua Role</option>
            <option value="admin" <?= $roleFilter === 'admin' ? 'selected' : '' ?>>Admin</option>
            <option value="petugas" <?= $roleFilter === 'petugas' ? 'selected' : '' ?>>Petugas</option>
            <option value="siswa" <?= $roleFilter === 'siswa' ? 'selected' : '' ?>>Siswa</option>
        </select>

        <button type="submit" class="btn btn-outline-primary filter-btn">
            <i class="bi bi-funnel me-1"></i> Filter
        </button>

        <?php if ($hasFilter): ?>
            <a href="daftar_pengguna.php" class="btn btn-light filter-btn border">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
            </a>
        <?php endif; ?>

        <button type="button" class="btn btn-danger btn-add ms-auto" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="bi bi-plus-lg me-1"></i> Tambah Pengguna
        </button>
    </form>
</div>

<!-- Tabel pengguna -->
<div class="panel-card table-card mt-4">
    <div class="panel-head">
        <div class="panel-title">
            <span class="panel-icon"><i class="bi bi-table"></i></span>
            <div>
                <h2>Tabel Pengguna</h2>
                <p id="searchCount" data-total="<?= $totalUsers ?>">
                    Menampilkan <?= count($users) ?> dari <?= $totalUsers ?> pengguna
                </p>
            </div>
        </div>
        <span class="badge-soft badge-blue">
            <i class="bi bi-database me-1"></i> <?= $totalUsers ?> data
        </span>
    </div>

    <?php if ($users): ?>
        <div class="table-responsive">
            <table class="table table-modern user-table align-middle mb-0" id="usersTable">
                <thead>
                    <tr>
                        <th class="cell-no">No</th>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody">
                    <?php foreach ($users as $index => $user): ?>
                        <?php $isSelf = (int) $user['id'] === $currentId; ?>
                        <tr class="user-row" data-search="<?= e(mb_strtolower($user['nama'] . ' ' . $user['username'] . ' ' . $user['role'], 'UTF-8')) ?>">
                            <td class="cell-no"><?= $index + 1 ?></td>
                            <td>
                                <div class="user-cell">
                                    <span class="avatar avatar-<?= e($user['role']) ?>"><?= e(initials((string) $user['nama'])) ?></span>
                                    <div>
                                        <strong>
                                            <?= e($user['nama']) ?>
                                            <?php if ($isSelf): ?>
                                                <span class="badge-self">Anda</span>
                                            <?php endif; ?>
                                        </strong>
                                        <small>ID #<?= (int) $user['id'] ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><code class="username-code"><?= e($user['username']) ?></code></td>
                            <td><?= roleBadge((string) $user['role']) ?></td>
                            <td class="cell-actions">
                                <button
                                    type="button"
                                    class="btn btn-icon btn-icon-edit"
                                    title="Edit <?= e($user['nama']) ?>"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editUserModal"
                                    data-id="<?= (int) $user['id'] ?>"
                                    data-nama="<?= e($user['nama']) ?>"
                                    data-username="<?= e($user['username']) ?>"
                                    data-role="<?= e($user['role']) ?>"
                                >
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <?php if ($isSelf): ?>
                                    <button type="button" class="btn btn-icon btn-icon-delete" disabled title="Tidak dapat menghapus akun sendiri">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                <?php else: ?>
                                    <button
                                        type="button"
                                        class="btn btn-icon btn-icon-delete"
                                        title="Hapus <?= e($user['nama']) ?>"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteUserModal"
                                        data-id="<?= (int) $user['id'] ?>"
                                        data-nama="<?= e($user['nama']) ?>"
                                        data-username="<?= e($user['username']) ?>"
                                    >
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr id="noSearchResult" class="d-none">
                        <td colspan="5">
                            <div class="table-inline-empty">
                                <i class="bi bi-search"></i>
                                <span>Tidak ada pengguna yang cocok dengan pencarian.</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <span class="empty-icon"><i class="bi <?= $hasFilter ? 'bi-search' : 'bi-people' ?>"></i></span>
            <h3><?= $hasFilter ? 'Pengguna tidak ditemukan' : 'Belum ada pengguna' ?></h3>
            <p>
                <?= $hasFilter
                    ? 'Tidak ada data yang cocok dengan filter. Coba kata kunci atau role lain.'
                    : 'Tambahkan akun pertama untuk mulai mengelola hak akses sistem.' ?>
            </p>
            <?php if ($hasFilter): ?>
                <a href="daftar_pengguna.php" class="btn btn-outline-primary rounded-3">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                </a>
            <?php else: ?>
                <button type="button" class="btn btn-danger rounded-3" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Pengguna
                </button>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<!-- ======================================================
     MODAL TAMBAH PENGGUNA
======================================================= -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-modern">
            <div class="modal-header">
                <div class="modal-heading">
                    <span class="modal-icon modal-icon-red"><i class="bi bi-person-plus-fill"></i></span>
                    <div>
                        <h2 id="addUserTitle">Tambah Pengguna</h2>
                        <p>Buat akun baru dan tentukan hak aksesnya.</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form method="post" action="daftar_pengguna.php" novalidate>
                <?= csrfField() ?>
                <input type="hidden" name="action" value="tambah">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="addNama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="addNama" name="nama" maxlength="100" placeholder="Contoh: Budi Santoso" required>
                    </div>
                    <div class="mb-3">
                        <label for="addUsername" class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="addUsername" name="username" maxlength="50" placeholder="Contoh: budi.santoso" autocomplete="off" required>
                        <div class="form-text">3-50 karakter: huruf, angka, titik, garis bawah, atau strip.</div>
                    </div>
                    <div class="mb-3">
                        <label for="addPassword" class="form-label">Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="addPassword" name="password" placeholder="Minimal 6 karakter" autocomplete="new-password" required>
                            <button class="btn btn-outline-secondary" type="button" data-toggle-password="addPassword" aria-label="Tampilkan password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-1">
                        <label for="addRole" class="form-label">Role <span class="text-danger">*</span></label>
                        <select class="form-select" id="addRole" name="role" required>
                            <option value="" selected disabled>Pilih role pengguna</option>
                            <option value="admin">Admin &mdash; akses penuh</option>
                            <option value="petugas">Petugas &mdash; akses operasional</option>
                            <option value="siswa">Siswa &mdash; akses mandiri</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border rounded-3 px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-3 px-4">
                        <i class="bi bi-plus-lg me-1"></i> Simpan Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ======================================================
     MODAL EDIT PENGGUNA
======================================================= -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-modern">
            <div class="modal-header">
                <div class="modal-heading">
                    <span class="modal-icon modal-icon-blue"><i class="bi bi-pencil-square"></i></span>
                    <div>
                        <h2 id="editUserTitle">Edit Pengguna</h2>
                        <p>Perbarui data akun dan hak akses.</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form method="post" action="daftar_pengguna.php" novalidate>
                <?= csrfField() ?>
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="editId" value="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editNama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editNama" name="nama" maxlength="100" required>
                    </div>
                    <div class="mb-3">
                        <label for="editUsername" class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editUsername" name="username" maxlength="50" autocomplete="off" required>
                    </div>
                    <div class="mb-3">
                        <label for="editPassword" class="form-label">Password Baru <span class="text-muted">(opsional)</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="editPassword" name="password" placeholder="Kosongkan jika tidak diganti" autocomplete="new-password">
                            <button class="btn btn-outline-secondary" type="button" data-toggle-password="editPassword" aria-label="Tampilkan password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="form-text">Isi minimal 6 karakter hanya jika ingin mengganti password.</div>
                    </div>
                    <div class="mb-1">
                        <label for="editRole" class="form-label">Role <span class="text-danger">*</span></label>
                        <select class="form-select" id="editRole" name="role" required>
                            <option value="admin">Admin &mdash; akses penuh</option>
                            <option value="petugas">Petugas &mdash; akses operasional</option>
                            <option value="siswa">Siswa &mdash; akses mandiri</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border rounded-3 px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-3 px-4">
                        <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ======================================================
     MODAL HAPUS PENGGUNA
======================================================= -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content modal-modern">
            <div class="modal-body text-center p-4">
                <span class="delete-warning-icon"><i class="bi bi-trash3-fill"></i></span>
                <h2 id="deleteUserTitle" class="h5 mb-2">Hapus Pengguna?</h2>
                <p class="text-secondary small mb-1">
                    Akun <strong id="deleteUserName">&mdash;</strong>
                    <span class="d-block"><code id="deleteUserUsername">&mdash;</code></span>
                </p>
                <p class="text-secondary small mb-4">Tindakan ini permanen dan tidak dapat dibatalkan.</p>
                <form method="post" action="daftar_pengguna.php">
                    <?= csrfField() ?>
                    <input type="hidden" name="action" value="hapus">
                    <input type="hidden" name="id" id="deleteId" value="">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light border rounded-3 flex-fill" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger rounded-3 flex-fill">
                            <i class="bi bi-trash3 me-1"></i> Hapus
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
