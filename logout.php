<?php
/**
 * ============================================================
 *  Logout - hancurkan session & cookie
 * ============================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

// Kosongkan seluruh data session
$_SESSION = [];

// Hapus cookie session di browser
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        [
            'expires'  => time() - 42000,
            'path'     => $params['path'] ?: '/',
            'domain'   => $params['domain'] ?? '',
            'secure'   => (bool) ($params['secure'] ?? false),
            'httponly' => (bool) ($params['httponly'] ?? true),
            'samesite' => $params['samesite'] ?? 'Lax',
        ]
    );
}

// Hancurkan session di server
session_destroy();

redirect('index.php?logout=1');
