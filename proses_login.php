<?php
/**
 * ============================================================
 *  Proses Autentikasi Login
 * ============================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

requireCsrf();

$username = trim((string) ($_POST['username'] ?? ''));
$password = (string) ($_POST['password'] ?? '');

if ($username === '' || $password === '') {
    $_SESSION['old_username'] = $username;
    setFlash('danger', 'Username dan password wajib diisi.');
    redirect('index.php');
}

$stmt = $pdo->prepare(
    'SELECT id, nama, username, password, role FROM users WHERE username = ? LIMIT 1'
);
$stmt->execute([$username]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, (string) $user['password'])) {
    $_SESSION['old_username'] = $username;
    setFlash('danger', 'Username atau password salah. Silakan periksa kembali.');
    redirect('index.php');
}

// Cegah session fixation
session_regenerate_id(true);

$_SESSION['user_id']    = (int) $user['id'];
$_SESSION['nama']       = (string) $user['nama'];
$_SESSION['username']   = (string) $user['username'];
$_SESSION['role']       = (string) $user['role'];
$_SESSION['login_time'] = time();

unset($_SESSION['old_username']);

redirect('dashboard.php');
