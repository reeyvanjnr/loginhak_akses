<?php
/**
 * ============================================================
 *  Autentikasi, Session, CSRF & Helper Umum
 * ============================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';

// ------------------------------------------------------------
// Session
// ------------------------------------------------------------
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    session_name('SIMPHA_SESSION');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// Header keamanan dasar
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

// ------------------------------------------------------------
// Helper umum
// ------------------------------------------------------------

/** Escape output untuk mencegah XSS. */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/** Apakah user sudah login? */
function isLoggedIn(): bool
{
    return isset($_SESSION['user_id'], $_SESSION['role'], $_SESSION['username']);
}

/** Redirect lalu hentikan eksekusi. */
function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

/** Simpan pesan flash ke session. */
function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/** Ambil sekaligus hapus pesan flash. */
function takeFlash(): ?array
{
    if (empty($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return is_array($flash) ? $flash : null;
}

/** Inisial nama untuk avatar. */
function initials(string $name): string
{
    $name = trim($name);
    if ($name === '') {
        return '?';
    }
    $parts = preg_split('/\s+/', $name) ?: [];
    $first = mb_substr($parts[0] ?? '', 0, 1, 'UTF-8');
    $last  = count($parts) > 1 ? mb_substr((string) end($parts), 0, 1, 'UTF-8') : '';
    return mb_strtoupper($first . $last, 'UTF-8');
}

/** Label role yang ramah dibaca. */
function roleLabel(string $role): string
{
    return match ($role) {
        'admin'   => 'Admin',
        'petugas' => 'Petugas',
        'siswa'   => 'Siswa',
        default   => 'Tidak Diketahui',
    };
}

/** Ikon Bootstrap untuk role. */
function roleIcon(string $role): string
{
    return match ($role) {
        'admin'   => 'bi-shield-fill-check',
        'petugas' => 'bi-person-badge-fill',
        'siswa'   => 'bi-mortarboard-fill',
        default   => 'bi-person-fill',
    };
}

/** Badge role siap tampil (HTML). */
function roleBadge(string $role): string
{
    return '<span class="badge-role badge-' . e($role) . '">'
        . '<i class="bi ' . e(roleIcon($role)) . '"></i> '
        . e(roleLabel($role))
        . '</span>';
}

/** Tanggal dalam format Indonesia, contoh: 17 November 2026 */
function tanggalIndonesia(?int $timestamp = null): string
{
    $bulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];
    $ts = $timestamp ?? time();
    return date('j', $ts) . ' ' . $bulan[(int) date('n', $ts)] . ' ' . date('Y', $ts);
}

// ------------------------------------------------------------
// Proteksi halaman
// ------------------------------------------------------------

/** Wajib login. */
function cekLogin(): void
{
    if (!isLoggedIn()) {
        setFlash('warning', 'Silakan login terlebih dahulu untuk mengakses halaman tersebut.');
        redirect('index.php');
    }
}

/**
 * Wajib login dan memiliki role tertentu.
 * Jika role tidak sesuai, kirim HTTP 403 + halaman error.
 */
function cekRole(string|array $roles): void
{
    cekLogin();

    $allowed = is_array($roles) ? $roles : [$roles];
    $current = (string) ($_SESSION['role'] ?? '');

    if (!in_array($current, $allowed, true)) {
        http_response_code(403);
        renderForbiddenPage($allowed);
        exit;
    }
}

/** Tampilan halaman 403 Forbidden yang rapi. */
function renderForbiddenPage(array $allowedRoles): never
{
    $currentRole = (string) ($_SESSION['role'] ?? '-');
    $nama        = (string) ($_SESSION['nama'] ?? 'Pengguna');
    $roleText    = implode(' / ', array_map('roleLabel', $allowedRoles));

    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>403 Forbidden &middot; <?= e(APP_SHORT) ?></title>
        <link rel="icon" type="image/svg+xml" href="assets/img/favicon.svg">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
        <link href="assets/css/style.css" rel="stylesheet">
    </head>
    <body class="error-screen">
        <div class="error-card text-center">
            <div class="error-illustration">
                <span class="error-shield"><i class="bi bi-shield-lock-fill"></i></span>
                <span class="error-code">403</span>
            </div>
            <h1>Akses Ditolak</h1>
            <p class="text-secondary mb-4">
                Halaman ini khusus untuk role <strong><?= e($roleText) ?></strong>.
                Akun <strong><?= e($nama) ?></strong> saat ini masuk sebagai
                <?= roleBadge($currentRole) ?> dan tidak memiliki hak akses.
            </p>
            <div class="d-flex flex-wrap gap-2 justify-content-center">
                <a href="dashboard.php" class="btn btn-primary rounded-3 px-4">
                    <i class="bi bi-grid-1x2-fill me-1"></i> Ke Dashboard
                </a>
                <a href="logout.php" class="btn btn-outline-secondary rounded-3 px-4">
                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                </a>
            </div>
            <p class="error-footnote mb-0">
                <i class="bi bi-info-circle me-1"></i>
                Hubungi administrator jika Anda merasa ini sebuah kesalahan.
            </p>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// ------------------------------------------------------------
// CSRF Protection
// ------------------------------------------------------------

/** Ambil (dan buat jika belum ada) token CSRF. */
function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return (string) $_SESSION['csrf_token'];
}

/** Input hidden token CSRF. */
function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrfToken()) . '">';
}

/** Validasi token CSRF dari request POST. */
function checkCsrf(): bool
{
    $token = $_POST['csrf_token'] ?? '';
    return is_string($token)
        && $token !== ''
        && !empty($_SESSION['csrf_token'])
        && hash_equals((string) $_SESSION['csrf_token'], $token);
}

/** Wajibkan token CSRF valid, jika tidak tampilkan error 419. */
function requireCsrf(): void
{
    if (!checkCsrf()) {
        http_response_code(419);
        ?>
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>419 Session Expired &middot; <?= e(APP_SHORT) ?></title>
            <link rel="icon" type="image/svg+xml" href="assets/img/favicon.svg">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
            <link href="assets/css/style.css" rel="stylesheet">
        </head>
        <body class="error-screen">
            <div class="error-card text-center">
                <div class="error-illustration">
                    <span class="error-shield"><i class="bi bi-hourglass-split"></i></span>
                    <span class="error-code">419</span>
                </div>
                <h1>Sesi Kedaluwarsa</h1>
                <p class="text-secondary mb-4">
                    Token keamanan formulir tidak valid atau sudah kedaluwarsa.
                    Silakan muat ulang halaman lalu kirim ulang formulir.
                </p>
                <div class="d-flex flex-wrap gap-2 justify-content-center">
                    <a href="dashboard.php" class="btn btn-danger rounded-3 px-4">
                        <i class="bi bi-arrow-clockwise me-1"></i> Muat Ulang Dashboard
                    </a>
                    <a href="index.php" class="btn btn-outline-secondary rounded-3 px-4">Ke Login</a>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}
