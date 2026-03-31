# Vihara Management System (MVP)

Aplikasi Laravel untuk manajemen pelaksanaan kegiatan, donasi, dan absensi berbasis kode registrasi (QR payload).

## Fitur MVP

- Auth + registrasi umat + aktivasi email
- RBAC 5 role: Superadmin, Admin, Owner, Petugas, Umat
- Manajemen kegiatan dan pendaftaran kegiatan
- Check-in peserta (kode registrasi + walk-in manual)
- Donasi online Midtrans + verifikasi admin + kwitansi PDF
- Laporan donasi + export PDF
- Log login dan log aktivitas
- Integrasi notifikasi Discord webhook dasar

## Setup cepat

```bash
composer install
php artisan migrate:fresh --seed
npm install
npm run dev
php artisan serve
```

## Akun default seed

- Email: `superadmin@vihara.test`
- Password: `password123`

## Command migrasi data lama

```bash
php artisan app:migrate-pengguna
```

Command ini memigrasikan data dari tabel `pengguna` ke `users` dan memasang role sesuai kolom `peran`.
