<?php
/**
 * ============================================================
 *  Komponen Header + Layout Atas (reusable)
 *  Variabel yang disiapkan halaman:
 *    $page_title    (string) judul halaman
 *    $page_subtitle (string) deskripsi halaman (opsional)
 *    $active_menu   ('dashboard'|'pengguna'|'profil')
 * ============================================================
 */

if (!isset($page_title)) {
    $page_title = 'Dashboard';
}
if (!isset($page_subtitle)) {
    $page_subtitle = '';
}
if (!isset($active_menu)) {
    $active_menu = 'dashboard';
}

$headerRole = (string) ($_SESSION['role'] ?? 'siswa');
$headerNama = (string) ($_SESSION['nama'] ?? 'Pengguna');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= e(APP_NAME) ?>">
    <title><?= e($page_title) ?> &middot; <?= e(APP_SHORT) ?></title>
    <link rel="icon" type="image/svg+xml" href="assets/img/favicon.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="app-body">

    <?php include __DIR__ . '/sidebar.php'; ?>
    <div class="sidebar-backdrop" data-sidebar-close></div>

    <div class="app-main">
        <header class="topbar">
            <div class="topbar-left">
                <button type="button" class="topbar-toggle" data-sidebar-toggle aria-label="Buka menu sidebar">
                    <i class="bi bi-list"></i>
                </button>
                <div class="topbar-title">
                    <nav class="topbar-breadcrumb" aria-label="Breadcrumb">
                        <span>Beranda</span>
                        <i class="bi bi-chevron-right"></i>
                        <span><?= e($page_title) ?></span>
                    </nav>
                    <h1><?= e($page_title) ?></h1>
                    <?php if ($page_subtitle !== ''): ?>
                        <p><?= e($page_subtitle) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="topbar-right">
                <div class="topbar-datetime">
                    <span class="topbar-datetime-icon"><i class="bi bi-calendar3"></i></span>
                    <div>
                        <span id="liveDate"><?= e(tanggalIndonesia()) ?></span>
                        <strong id="liveClock"><?= date('H:i:s') ?> WIB</strong>
                    </div>
                </div>

                <div class="topbar-divider d-none d-md-block"></div>

                <div class="topbar-user">
                    <span class="topbar-avatar"><?= e(initials($headerNama)) ?></span>
                    <div class="topbar-user-text d-none d-md-flex">
                        <strong><?= e($headerNama) ?></strong>
                        <?= roleBadge($headerRole) ?>
                    </div>
                </div>

                <a href="logout.php" class="btn btn-logout" title="Keluar dari aplikasi">
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="d-none d-sm-inline">Logout</span>
                </a>
            </div>
        </header>

        <main class="app-content">
            <?php
            $flash = takeFlash();
            if ($flash):
                $flashType = (string) ($flash['type'] ?? 'info');
                $flashIcon = match ($flashType) {
                    'success' => 'bi-check-circle-fill',
                    'danger'  => 'bi-exclamation-octagon-fill',
                    'warning' => 'bi-exclamation-triangle-fill',
                    default   => 'bi-info-circle-fill',
                };
            ?>
                <div class="alert alert-<?= e($flashType) ?> alert-dismissible fade show app-alert" role="alert">
                    <i class="bi <?= e($flashIcon) ?> app-alert-icon"></i>
                    <div><?= e($flash['message'] ?? '') ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            <?php endif; ?>
