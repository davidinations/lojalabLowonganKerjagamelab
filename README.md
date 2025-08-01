# PENTING (CARA PAKAI)

1. Buka terminal, lalu masuk ke direktori project ini.
2. Jalankan perintah `composer install` untuk menginstall semua dependensi.
3. Jalankan perintah `php scripts/migrate.php fresh` untuk menjalankan migrasi database.
   Data Data Yang Sudah Ada:
   - **Super Admin** <br>
     Username: `superadmin` <br>
     Password: `admin123`
   - **Admin** <br>
     Username: `admin` <br>
     Password: `admin123`
   - **Recruiter** <br>
     Username: `recruiter` <br>
     Password: `recruiter123`
   - **User** <br>
     Username: `jane_smith` <br>
     Password: `password123`
4. Jalankan perintah `composer start` untuk menjalankan aplikasi.
5. Buka browser dan akses `http://localhost:8080` untuk melihat aplikasi.
6. Bila Ingin Bantu Push Lewat WA, Cantumkan Email Github Anda Untuk Ditambahkan Lalu Saat Push Pastikan Menggunakan Branch Sendiri Supaya Tidak tertabrak Dengan Branch Aslinya (`master`).

# Tampilan Diagram Struktur Database

![Database Diagram](GambarDiagram.jpeg)
_Diagram di atas menunjukkan struktur database lengkap untuk aplikasi Lowongan Kerja Gamelab, termasuk semua tabel dan relasi antar tabel._

Database terdiri dari 11 tabel utama:

- **tbl_roles** - Menyimpan jenis peran (Super Admin, Admin, Recruiter)
- **tbl_users** - Data pengguna/pelamar kerja
- **tbl_admins** - Data administrator sistem
- **tbl_organizations** - Data perusahaan/organisasi
- **tbl_locations** - Data lokasi
- **tbl_typeLowongans** - Jenis/kategori lowongan kerja
- **tbl_lowongans** - Data lowongan kerja
- **tbl_applyLowongans** - Data lamaran pekerjaan
- **tbl_skills** - Daftar keahlian/skill
- **tbl_skill_users** - Hubungan many-to-many antara user dan skill
- **tbl_skill_typeLowongans** - Hubungan many-to-many antara jenis lowongan dan skill yang dibutuhkan

# Slim Framework 4 Skeleton Application

[![Coverage Status](https://coveralls.io/repos/github/slimphp/Slim-Skeleton/badge.svg?branch=master)](https://coveralls.io/github/slimphp/Slim-Skeleton?branch=master)

Use this skeleton application to quickly setup and start working on a new Slim Framework 4 application. This application uses the latest Slim 4 with Slim PSR-7 implementation and PHP-DI container implementation. It also uses the Monolog logger.

This skeleton application was built for Composer. This makes setting up a new Slim Framework application quick and easy.

## Install the Application

Run this command from the directory in which you want to install your new Slim Framework application. You will require PHP 7.4 or newer.

```bash
composer create-project slim/slim-skeleton [my-app-name]
```

Replace `[my-app-name]` with the desired directory name for your new application. You'll want to:

- Point your virtual host document root to your new application's `public/` directory.
- Ensure `logs/` is web writable.

To run the application in development, you can run these commands

```bash
cd [my-app-name]
composer start
```

Or you can use `docker-compose` to run the app with `docker`, so you can run these commands:

```bash
cd [my-app-name]
docker-compose up -d
```

After that, open `http://localhost:8080` in your browser.

Run this command in the application directory to run the test suite

```bash
composer test
```

That's it! Now go build something cool.
