<?php
/**
 * ============================================================
 *  Komponen UI Reusable untuk Dashboard
 * ============================================================
 */

declare(strict_types=1);

/**
 * Render 4 kartu statistik (KPI).
 * Setiap item: label, value, icon, theme (red|blue|blue-mid|gray), foot.
 */
function renderKpiCards(array $cards): void
{
    ?>
    <div class="row g-3 kpi-row">
        <?php foreach ($cards as $card): ?>
            <div class="col-6 col-xl-3">
                <div class="kpi-card kpi-<?= e($card['theme'] ?? 'blue') ?>">
                    <span class="kpi-icon"><i class="bi <?= e($card['icon'] ?? 'bi-graph-up') ?>"></i></span>
                    <span class="kpi-label"><?= e($card['label'] ?? '') ?></span>
                    <span class="kpi-value"><?= e($card['value'] ?? '0') ?></span>
                    <span class="kpi-foot">
                        <i class="bi bi-arrow-up-right"></i>
                        <?= e($card['foot'] ?? '') ?>
                    </span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
}

/**
 * Render panel informasi hak akses sesuai role.
 * Setiap item: title, desc (opsional).
 */
function renderAccessPanel(string $role, array $items): void
{
    ?>
    <div class="panel-card h-100">
        <div class="panel-head">
            <div class="panel-title">
                <span class="panel-icon"><i class="bi bi-shield-check"></i></span>
                <div>
                    <h2>Hak Akses <?= e(roleLabel($role)) ?></h2>
                    <p>Fitur yang dapat Anda gunakan pada sistem ini</p>
                </div>
            </div>
            <?= roleBadge($role) ?>
        </div>
        <div class="panel-body d-flex flex-column">
            <ul class="access-list">
                <?php foreach ($items as $item): ?>
                    <li>
                        <span class="access-check"><i class="bi bi-check-lg"></i></span>
                        <div>
                            <strong><?= e($item['title'] ?? '') ?></strong>
                            <?php if (!empty($item['desc'])): ?>
                                <small><?= e($item['desc']) ?></small>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="access-note mt-auto">
                <i class="bi bi-lock-fill"></i>
                <span>Hak akses melekat pada role <strong><?= e(roleLabel($role)) ?></strong> dan selalu diperiksa di sisi server.</span>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Kartu ringkas informasi akun yang sedang login.
 */
function renderAccountCard(string $nama, string $username, string $role, ?int $loginTime = null): void
{
    ?>
    <div class="panel-card account-card">
        <div class="panel-head compact">
            <div class="panel-title">
                <span class="panel-icon"><i class="bi bi-person-vcard"></i></span>
                <div>
                    <h2>Informasi Akun</h2>
                    <p>Sesi login Anda saat ini</p>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <div class="account-identity">
                <span class="account-avatar"><?= e(initials($nama)) ?></span>
                <div>
                    <strong><?= e($nama) ?></strong>
                    <small>@<?= e($username) ?></small>
                </div>
            </div>
            <div class="account-meta">
                <div>
                    <span>Role</span>
                    <?= roleBadge($role) ?>
                </div>
                <div>
                    <span>Status</span>
                    <span class="status-active"><span class="status-dot"></span> Aktif</span>
                </div>
                <div>
                    <span>Login</span>
                    <strong class="small"><?= e($loginTime ? tanggalIndonesia($loginTime) . ', ' . date('H:i', $loginTime) . ' WIB' : '-') ?></strong>
                </div>
            </div>
            <a href="profil.php" class="btn btn-outline-primary w-100 rounded-3 mt-3">
                <i class="bi bi-person-gear me-1"></i> Kelola Profil
            </a>
        </div>
    </div>
    <?php
}
