<?php
/**
 * ============================================================
 *  Halaman Login
 * ============================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}

$flash       = takeFlash();
$oldUsername = (string) ($_SESSION['old_username'] ?? '');
unset($_SESSION['old_username']);
$logoutNotice = isset($_GET['logout']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login &middot; <?= e(APP_SHORT) ?> | <?= e(APP_NAME) ?></title>
    <link rel="icon" type="image/svg+xml" href="assets/img/favicon.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="login-page">

    <div class="login-decor login-decor-1"></div>
    <div class="login-decor login-decor-2"></div>

    <main class="login-card">
        <div class="row g-0 w-100 h-100">

            <!-- ==============================
                 SISI KIRI - PANEL BIRU / FORM
            =============================== -->
            <div class="col-lg-6 login-left">
                <div class="login-brand">
                    <span class="login-brand-mark"><i class="bi bi-shield-lock-fill"></i></span>
                    <div class="login-brand-text">
                        <strong><?= e(APP_SHORT) ?></strong>
                        <small>Manajemen Akses</small>
                    </div>
                </div>

                <div class="login-welcome">
                    <span class="login-eyebrow">SELAMAT DATANG</span>
                    <h1>Selamat Datang!</h1>
                    <p>Masuk ke akun Anda untuk mengakses dashboard sesuai hak akses yang dimiliki.</p>
                </div>

                <?php if ($logoutNotice): ?>
                    <div class="alert alert-light login-alert d-flex align-items-center gap-2" role="alert">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Anda telah berhasil keluar. Sampai jumpa!</span>
                    </div>
                <?php endif; ?>

                <?php if ($flash): ?>
                    <div class="alert alert-<?= e($flash['type'] ?? 'danger') ?> login-alert d-flex align-items-start gap-2" role="alert">
                        <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                        <span><?= e($flash['message'] ?? '') ?></span>
                    </div>
                <?php endif; ?>

                <form action="proses_login.php" method="post" class="login-form" autocomplete="on" novalidate>
                    <?= csrfField() ?>

                    <div class="mb-3">
                        <label for="username" class="login-label">Username</label>
                        <div class="input-group login-input-group">
                            <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                            <input
                                type="text"
                                class="form-control login-input"
                                id="username"
                                name="username"
                                placeholder="Masukkan username Anda"
                                value="<?= e($oldUsername) ?>"
                                autocomplete="username"
                                required
                                autofocus
                            >
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="login-label">Password</label>
                        <div class="input-group login-input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input
                                type="password"
                                class="form-control login-input"
                                id="password"
                                name="password"
                                placeholder="Masukkan password"
                                autocomplete="current-password"
                                required
                            >
                            <button class="btn login-eye" type="button" data-toggle-password="password" aria-label="Tampilkan password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3 login-meta">
                        <span><i class="bi bi-shield-check me-1"></i> Koneksi aman</span>
                        <span>Lupa password? Hubungi admin</span>
                    </div>

                    <button type="submit" class="btn btn-login w-100">
                        Masuk Sekarang
                        <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </form>

                <div class="login-demo">
                    <div class="login-demo-head">
                        <i class="bi bi-info-circle-fill"></i>
                        <span>Akun demo — klik untuk mengisi otomatis</span>
                    </div>
                    <div class="login-demo-list">
                        <?php
                        $demoAccounts = [
                            ['username' => 'admin',   'password' => 'admin123',   'role' => 'admin'],
                            ['username' => 'petugas', 'password' => 'petugas123', 'role' => 'petugas'],
                            ['username' => 'siswa',   'password' => 'siswa123',   'role' => 'siswa'],
                        ];
                        foreach ($demoAccounts as $demo):
                        ?>
                            <button
                                type="button"
                                class="login-demo-chip"
                                data-demo-username="<?= e($demo['username']) ?>"
                                data-demo-password="<?= e($demo['password']) ?>"
                            >
                                <i class="bi <?= e(roleIcon($demo['role'])) ?>"></i>
                                <span><?= e($demo['username']) ?></span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <p class="login-copyright mb-0">&copy; <?= date('Y') ?> <?= e(APP_NAME) ?></p>
            </div>

            <!-- ==============================
                 SISI KANAN - PANEL PUTIH / VISUAL
            =============================== -->
            <div class="col-lg-6 login-right">
                <div class="login-logo">
                    <div class="login-logo-mark">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                    <div class="login-logo-text">
                        <strong><?= e(APP_SHORT) ?></strong>
                        <small>Official System</small>
                    </div>
                </div>

                <div class="login-illustration">
                    <svg viewBox="0 0 440 330" role="img" aria-label="Ilustrasi laptop dengan perisai keamanan">
                        <defs>
                            <linearGradient id="screenGrad" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0" stop-color="#0D52A5"/>
                                <stop offset="1" stop-color="#1976D2"/>
                            </linearGradient>
                            <linearGradient id="shieldGrad" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0" stop-color="#EF5350"/>
                                <stop offset="1" stop-color="#C62828"/>
                            </linearGradient>
                            <filter id="softShadow" x="-30%" y="-30%" width="160%" height="160%">
                                <feDropShadow dx="0" dy="10" stdDeviation="12" flood-color="#0D52A5" flood-opacity="0.18"/>
                            </filter>
                        </defs>

                        <!-- Dekorasi latar -->
                        <circle cx="72" cy="84" r="7" fill="#DCE9F8"/>
                        <circle cx="374" cy="118" r="5" fill="#F7C5C5"/>
                        <circle cx="392" cy="236" r="9" fill="#E3EDF9"/>
                        <circle cx="52" cy="220" r="4" fill="#1976D2" opacity=".35"/>
                        <path d="M50 150h16M58 142v16" stroke="#B9D2EE" stroke-width="3" stroke-linecap="round"/>
                        <path d="M384 60h14M391 53v14" stroke="#F0B4B4" stroke-width="3" stroke-linecap="round"/>

                        <!-- Bayangan laptop -->
                        <ellipse cx="220" cy="292" rx="150" ry="14" fill="#DFE9F4"/>

                        <!-- Layar laptop -->
                        <g filter="url(#softShadow)">
                            <rect x="82" y="62" width="276" height="188" rx="18" fill="#0D52A5"/>
                            <rect x="92" y="72" width="256" height="168" rx="10" fill="url(#screenGrad)"/>
                            <rect x="104" y="84" width="232" height="144" rx="8" fill="#F4F7FA"/>

                            <!-- Jendela aplikasi: titik kontrol -->
                            <circle cx="116" cy="95" r="3.2" fill="#D32F2F"/>
                            <circle cx="127" cy="95" r="3.2" fill="#F0B429"/>
                            <circle cx="138" cy="95" r="3.2" fill="#43A047"/>
                            <rect x="104" y="103" width="232" height="1.5" fill="#E3EAF2"/>

                            <!-- Sidebar mini -->
                            <rect x="112" y="112" width="48" height="106" rx="6" fill="#0D52A5" opacity=".12"/>
                            <rect x="118" y="120" width="36" height="4" rx="2" fill="#0D52A5" opacity=".6"/>
                            <rect x="118" y="132" width="30" height="4" rx="2" fill="#0D52A5" opacity=".3"/>
                            <rect x="118" y="144" width="34" height="4" rx="2" fill="#D32F2F" opacity=".75"/>
                            <rect x="118" y="156" width="26" height="4" rx="2" fill="#0D52A5" opacity=".3"/>
                            <rect x="118" y="168" width="32" height="4" rx="2" fill="#0D52A5" opacity=".3"/>
                            <rect x="118" y="180" width="22" height="4" rx="2" fill="#0D52A5" opacity=".3"/>

                            <!-- Kartu KPI mini -->
                            <rect x="170" y="112" width="74" height="34" rx="5" fill="#D32F2F"/>
                            <rect x="178" y="121" width="26" height="4" rx="2" fill="#fff" opacity=".65"/>
                            <rect x="178" y="130" width="38" height="7" rx="3" fill="#fff" opacity=".95"/>

                            <rect x="250" y="112" width="76" height="34" rx="5" fill="#0D52A5"/>
                            <rect x="258" y="121" width="26" height="4" rx="2" fill="#fff" opacity=".65"/>
                            <rect x="258" y="130" width="32" height="7" rx="3" fill="#fff" opacity=".95"/>

                            <!-- Grafik batang -->
                            <rect x="170" y="156" width="156" height="54" rx="6" fill="#fff" stroke="#E3EAF2"/>
                            <rect x="180" y="181" width="12" height="20" rx="3" fill="#9FB8D6"/>
                            <rect x="199" y="172" width="12" height="29" rx="3" fill="#1976D2"/>
                            <rect x="218" y="187" width="12" height="14" rx="3" fill="#9FB8D6"/>
                            <rect x="237" y="164" width="12" height="37" rx="3" fill="#D32F2F"/>
                            <rect x="256" y="178" width="12" height="23" rx="3" fill="#1976D2"/>
                            <rect x="275" y="168" width="12" height="33" rx="3" fill="#0D52A5"/>
                            <rect x="294" y="183" width="12" height="18" rx="3" fill="#9FB8D6"/>
                        </g>

                        <!-- Badge perisai keamanan -->
                        <g transform="translate(316 176)">
                            <path d="M32 0 64 12v24c0 23-14 39-32 48C14 75 0 59 0 36V12z" fill="url(#shieldGrad)"/>
                            <path d="M32 6 58 16v20c0 19-11 33-26 41-15-8-26-22-26-41V16z" fill="none" stroke="#fff" stroke-opacity=".28" stroke-width="2"/>
                            <rect x="21" y="33" width="22" height="19" rx="4" fill="#fff"/>
                            <path d="M26 33v-6a6 6 0 0 1 12 0v6" fill="none" stroke="#fff" stroke-width="4" stroke-linecap="round"/>
                            <circle cx="32" cy="42" r="3" fill="#D32F2F"/>
                        </g>

                        <!-- Badge centang -->
                        <g transform="translate(66 178)">
                            <circle cx="24" cy="24" r="24" fill="#fff"/>
                            <circle cx="24" cy="24" r="19" fill="#0D52A5"/>
                            <path d="m15 24 6 6 12-13" fill="none" stroke="#fff" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </g>

                        <!-- Basis laptop -->
                        <path d="M62 250h316l24 26H38z" fill="#D7E3F0"/>
                        <path d="M38 276h364l-8 8H46z" fill="#C2D4E6"/>
                        <rect x="188" y="256" width="64" height="7" rx="3.5" fill="#B4C9DD"/>
                    </svg>
                </div>

                <div class="login-app-title">
                    <span class="login-eyebrow blue">SISTEM TERPADU</span>
                    <h2>Sistem Manajemen<br>Pengguna &amp; Hak Akses</h2>
                    <span class="title-underline"></span>
                    <p>Kelola akun, peran, dan keamanan akses dalam satu platform yang rapi.</p>
                </div>
            </div>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>
