<?php
/**
 * ============================================================
 *  Komponen Sidebar (reusable)
 *  Variabel opsional: $active_menu ('dashboard'|'pengguna'|'profil')
 * ============================================================
 */

if (!isset($active_menu)) {
    $active_menu = '';
}

$sidebarRole = (string) ($_SESSION['role'] ?? 'siswa');
$sidebarNama = (string) ($_SESSION['nama'] ?? 'Pengguna');
?>
<aside class="app-sidebar" id="appSidebar">
    <a href="dashboard.php" class="sidebar-brand">
        <span class="sidebar-brand-mark"><i class="bi bi-shield-lock-fill"></i></span>
        <span class="sidebar-brand-text">
            <strong><?= e(APP_SHORT) ?></strong>
            <small>Manajemen Akses</small>
        </span>
    </a>

    <div class="sidebar-user">
        <span class="sidebar-avatar"><?= e(initials($sidebarNama)) ?></span>
        <span class="sidebar-user-text">
            <strong><?= e($sidebarNama) ?></strong>
            <?= roleBadge($sidebarRole) ?>
        </span>
    </div>

    <nav class="sidebar-nav" aria-label="Menu utama">
        <span class="sidebar-section">Menu Utama</span>

        <a href="dashboard.php" class="sidebar-link <?= $active_menu === 'dashboard' ? 'active' : '' ?>">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Dashboard</span>
            <?php if ($active_menu === 'dashboard'): ?>
                <i class="bi bi-chevron-right sidebar-link-arrow"></i>
            <?php endif; ?>
        </a>

        <?php if ($sidebarRole === 'admin'): ?>
            <a href="daftar_pengguna.php" class="sidebar-link <?= $active_menu === 'pengguna' ? 'active' : '' ?>">
                <i class="bi bi-people-fill"></i>
                <span>Daftar Pengguna</span>
                <?php if ($active_menu === 'pengguna'): ?>
                    <i class="bi bi-chevron-right sidebar-link-arrow"></i>
                <?php endif; ?>
            </a>
        <?php endif; ?>

        <a href="profil.php" class="sidebar-link <?= $active_menu === 'profil' ? 'active' : '' ?>">
            <i class="bi bi-person-gear"></i>
            <span>Profil Saya</span>
            <?php if ($active_menu === 'profil'): ?>
                <i class="bi bi-chevron-right sidebar-link-arrow"></i>
            <?php endif; ?>
        </a>

        <span class="sidebar-section">Akun</span>

        <a href="logout.php" class="sidebar-link sidebar-link-logout">
            <i class="bi bi-box-arrow-right"></i>
            <span>Logout</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-footer-card">
            <i class="bi bi-shield-check"></i>
            <div>
                <strong>Akun Terlindungi</strong>
                <small>Session &amp; password aman</small>
            </div>
        </div>
        <p class="mb-0">&copy; <?= date('Y') ?> <?= e(APP_SHORT) ?> &middot; v<?= e(APP_VERSION) ?></p>
    </div>
</aside>
