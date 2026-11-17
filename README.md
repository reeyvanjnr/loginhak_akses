# SIMPHA — Sistem Manajemen Pengguna & Hak Akses

Aplikasi web manajemen pengguna dan hak akses berbasis **PHP Native**, **PDO**, **MySQL**, dan **Bootstrap 5** dengan tema *Corporate Blue & Crimson Red*.

## Fitur

- Login aman dengan `password_verify()` dan regenerasi session (`session_regenerate_id(true)`).
- Proteksi halaman berbasis role: `admin`, `petugas`, `siswa`.
- Halaman **403 Forbidden** dan **419 Session Expired** yang rapi.
- Sidebar & header reusable dengan menu dinamis sesuai role.
- Dashboard berbeda untuk setiap role, lengkap dengan 4 kartu KPI.
- **Daftar Pengguna** khusus admin: pencarian, filter role, tambah, edit, dan hapus akun.
- **Profil Saya** untuk semua role: ubah nama lengkap dan ganti password (verifikasi password lama).
- Seluruh output di-escape dengan `htmlspecialchars()` dan query memakai PDO prepared statement.
- Proteksi CSRF pada semua form POST.

## Kebutuhan

- PHP **8.1+** dengan ekstensi `pdo_mysql` dan `mbstring`.
- MySQL / MariaDB (XAMPP, Laragon, atau server lain).
- Browser modern dengan koneksi internet untuk CDN Bootstrap & Bootstrap Icons.

## Cara Menjalankan (XAMPP)

1. Letakkan folder proyek di `C:\xampp\htdocs\login_hakakses`.
2. Jalankan **Apache** dan **MySQL** melalui XAMPP Control Panel.
3. Buka `http://localhost/login_hakakses/setup.php` di browser.
   Setup akan otomatis membuat database `db_login_hakakses`, tabel `users`, dan 3 akun awal.
4. Setelah pesan sukses muncul, **hapus file `setup.php`** demi keamanan.
5. Buka `http://localhost/login_hakakses/index.php` lalu masuk menggunakan salah satu akun demo.

Alternatif: impor `database.sql` melalui phpMyAdmin, kemudian tetap jalankan `setup.php`
untuk mengisi akun awal (setup bersifat idempotent, tidak membuat data ganda).

## Akun Demo

| Nama | Username | Password | Role |
| --- | --- | --- | --- |
| Administrator | `admin` | `admin123` | admin |
| Petugas Sekolah | `petugas` | `petugas123` | petugas |
| Siswa SMK | `siswa` | `siswa123` | siswa |

> Sangat disarankan mengganti password akun demo setelah aplikasi digunakan.

## Struktur File

```
login_hakakses/
├── index.php              # Halaman login split-screen
├── proses_login.php       # Validasi login & session
├── auth.php               # Session, cekLogin(), cekRole(), CSRF, helper
├── dashboard.php          # Router dashboard berdasarkan role
├── logout.php             # Hapus session & cookie
├── config.php             # Koneksi PDO & konstanta aplikasi
├── setup.php              # Setup database + akun awal (hapus setelah dipakai)
├── database.sql           # Skema database & tabel users
├── admin.php              # Dashboard admin
├── petugas.php            # Dashboard petugas
├── siswa.php              # Dashboard siswa
├── daftar_pengguna.php    # CRUD pengguna (khusus admin)
├── profil.php             # Profil & ganti password (semua role)
├── header.php             # Layout atas + topbar (reusable)
├── sidebar.php            # Sidebar dinamis (reusable)
├── footer.php             # Penutup layout + script
├── components.php         # Kartu KPI & panel akses reusable
├── assets/
│   ├── css/style.css      # Tema Corporate Blue & Crimson Red
│   ├── js/app.js          # Jam real-time, sidebar, filter, modal
│   └── img/favicon.svg    # Ikon aplikasi
└── README.md
```

## Catatan Keamanan

- Password disimpan sebagai hash (`PASSWORD_DEFAULT`), bukan teks biasa.
- Semua form POST dilindungi token CSRF.
- Admin tidak dapat menghapus akun sendiri atau menghapus/menurunkan administrator terakhir.
- Hapus `setup.php` setelah instalasi selesai.
