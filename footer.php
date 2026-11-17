<?php
/**
 * ============================================================
 *  Penutup Layout (reusable)
 * ============================================================
 */
?>
        </main>

        <footer class="app-footer">
            <span>&copy; <?= date('Y') ?> <?= e(APP_NAME) ?></span>
            <span class="app-footer-sep"></span>
            <span>Dibangun dengan PHP Native, PDO &amp; Bootstrap 5</span>
            <span class="ms-auto d-none d-md-inline"><?= e(APP_SHORT) ?> v<?= e(APP_VERSION) ?></span>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>
