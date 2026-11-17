<?php
/**
 * ============================================================
 *  Router Dashboard berdasarkan role
 * ============================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

cekLogin();

switch ($_SESSION['role']) {
    case 'admin':
        redirect('admin.php');
    case 'petugas':
        redirect('petugas.php');
    case 'siswa':
        redirect('siswa.php');
    default:
        // Role tidak dikenal: bersihkan session
        $_SESSION = [];
        session_destroy();
        redirect('index.php');
}
