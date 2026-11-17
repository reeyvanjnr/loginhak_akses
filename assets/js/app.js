/* ============================================================
   SIMPHA — Interaksi UI
   ============================================================ */

(function () {
    'use strict';

    /* --------------------------------------------------------
       1. Jam & tanggal real-time pada topbar
    -------------------------------------------------------- */
    var clockEl = document.getElementById('liveClock');
    var dateEl = document.getElementById('liveDate');

    if (clockEl || dateEl) {
        var formatDate = new Intl.DateTimeFormat('id-ID', {
            timeZone: 'Asia/Jakarta',
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });
        var formatTime = new Intl.DateTimeFormat('id-ID', {
            timeZone: 'Asia/Jakarta',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        });

        var updateClock = function () {
            var now = new Date();

            if (clockEl) {
                var parts = {};
                formatTime.formatToParts(now).forEach(function (part) {
                    parts[part.type] = part.value;
                });
                clockEl.textContent = parts.hour + ':' + parts.minute + ':' + parts.second + ' WIB';
            }
            if (dateEl) {
                dateEl.textContent = formatDate.format(now);
            }
        };

        updateClock();
        window.setInterval(updateClock, 1000);
    }

    /* --------------------------------------------------------
       2. Sidebar mobile
    -------------------------------------------------------- */
    var body = document.body;
    var sidebarButtons = document.querySelectorAll('[data-sidebar-toggle]');
    var sidebarClosers = document.querySelectorAll('[data-sidebar-close]');

    var openSidebar = function () {
        body.classList.add('sidebar-open');
    };

    var closeSidebar = function () {
        body.classList.remove('sidebar-open');
    };

    sidebarButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            body.classList.toggle('sidebar-open');
        });
    });

    sidebarClosers.forEach(function (closer) {
        closer.addEventListener('click', closeSidebar);
    });

    document.querySelectorAll('.app-sidebar .sidebar-link').forEach(function (link) {
        link.addEventListener('click', function () {
            if (window.innerWidth < 992) {
                closeSidebar();
            }
        });
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeSidebar();
        }
    });

    /* --------------------------------------------------------
       3. Tampilkan / sembunyikan password
    -------------------------------------------------------- */
    document.querySelectorAll('[data-toggle-password]').forEach(function (button) {
        button.addEventListener('click', function () {
            var targetId = button.getAttribute('data-toggle-password');
            var input = document.getElementById(targetId);
            if (!input) {
                return;
            }

            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';

            var icon = button.querySelector('i');
            if (icon) {
                icon.className = 'bi ' + (show ? 'bi-eye-slash' : 'bi-eye');
            }
            button.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
        });
    });

    /* --------------------------------------------------------
       4. Isi otomatis akun demo di halaman login
    -------------------------------------------------------- */
    document.querySelectorAll('[data-demo-username]').forEach(function (button) {
        button.addEventListener('click', function () {
            var usernameInput = document.getElementById('username');
            var passwordInput = document.getElementById('password');

            if (usernameInput) {
                usernameInput.value = button.getAttribute('data-demo-username') || '';
            }
            if (passwordInput) {
                passwordInput.value = button.getAttribute('data-demo-password') || '';
            }
            if (usernameInput) {
                usernameInput.focus();
            }
        });
    });

    /* --------------------------------------------------------
       5. Pencarian langsung pada tabel pengguna
    -------------------------------------------------------- */
    var searchInput = document.getElementById('userSearch');
    var usersTableBody = document.getElementById('usersTableBody');

    if (searchInput && usersTableBody) {
        var rows = Array.prototype.slice.call(usersTableBody.querySelectorAll('tr.user-row'));
        var noResultRow = document.getElementById('noSearchResult');
        var counter = document.getElementById('searchCount');
        var totalUsers = counter ? counter.getAttribute('data-total') || String(rows.length) : String(rows.length);

        var filterTable = function () {
            var query = searchInput.value.trim().toLowerCase();
            var visible = 0;

            rows.forEach(function (row) {
                var haystack = (row.getAttribute('data-search') || '').toLowerCase();
                var match = haystack.indexOf(query) !== -1;
                row.classList.toggle('d-none', !match);
                if (match) {
                    visible += 1;
                }
            });

            if (noResultRow) {
                noResultRow.classList.toggle('d-none', visible !== 0 || query === '');
            }

            if (counter) {
                counter.textContent = 'Menampilkan ' + visible + ' dari ' + totalUsers + ' pengguna';
            }
        };

        searchInput.addEventListener('input', filterTable);
    }

    /* --------------------------------------------------------
       6. Modal edit pengguna — isi data dari tombol
    -------------------------------------------------------- */
    var editModal = document.getElementById('editUserModal');

    if (editModal) {
        editModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            if (!button) {
                return;
            }

            var setValue = function (id, value) {
                var field = document.getElementById(id);
                if (field) {
                    field.value = value || '';
                }
            };

            setValue('editId', button.getAttribute('data-id'));
            setValue('editNama', button.getAttribute('data-nama'));
            setValue('editUsername', button.getAttribute('data-username'));
            setValue('editRole', button.getAttribute('data-role'));

            var passwordField = document.getElementById('editPassword');
            if (passwordField) {
                passwordField.value = '';
                passwordField.type = 'password';
            }

            var toggle = editModal.querySelector('[data-toggle-password="editPassword"] i');
            if (toggle) {
                toggle.className = 'bi bi-eye';
            }
        });
    }

    /* --------------------------------------------------------
       7. Modal hapus pengguna — isi nama target
    -------------------------------------------------------- */
    var deleteModal = document.getElementById('deleteUserModal');

    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            if (!button) {
                return;
            }

            var nameTarget = document.getElementById('deleteUserName');
            var usernameTarget = document.getElementById('deleteUserUsername');
            var idTarget = document.getElementById('deleteId');

            if (nameTarget) {
                nameTarget.textContent = button.getAttribute('data-nama') || '—';
            }
            if (usernameTarget) {
                usernameTarget.textContent = '@' + (button.getAttribute('data-username') || '—');
            }
            if (idTarget) {
                idTarget.value = button.getAttribute('data-id') || '';
            }
        });
    }

    /* --------------------------------------------------------
       8. Fokus otomatis pada modal tambah/edit
    -------------------------------------------------------- */
    var addModal = document.getElementById('addUserModal');
    if (addModal) {
        addModal.addEventListener('shown.bs.modal', function () {
            var field = document.getElementById('addNama');
            if (field) {
                field.focus();
            }
        });
    }

    if (editModal) {
        editModal.addEventListener('shown.bs.modal', function () {
            var field = document.getElementById('editNama');
            if (field) {
                field.focus();
            }
        });
    }

    /* --------------------------------------------------------
       9. Buka modal tambah otomatis dari tautan ?add=1
    -------------------------------------------------------- */
    if (addModal && window.bootstrap) {
        var params = new URLSearchParams(window.location.search);
        if (params.get('add') === '1') {
            window.bootstrap.Modal.getOrCreateInstance(addModal).show();

            if (window.history.replaceState) {
                params.delete('add');
                var query = params.toString();
                window.history.replaceState(null, '', window.location.pathname + (query ? '?' + query : '') + window.location.hash);
            }
        }
    }
})();
