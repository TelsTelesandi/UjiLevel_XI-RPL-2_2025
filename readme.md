# Sistem Pengajuan Event Ekstrakurikuler

Aplikasi web untuk mengelola pengajuan event ekstrakurikuler sekolah dengan PHP native dan Tailwind CSS.

## Fitur

### Admin (Bagian Kesiswaan)
- Dashboard dengan statistik lengkap
- Manajemen user (CRUD)
- Approval/penolakan event dengan catatan
- Laporan dan export CSV
- Manajemen event

### User (Ketua Ekstrakurikuler)
- Dashboard personal
- Pengajuan event baru dengan upload proposal
- Tracking status pengajuan
- Menutup event setelah selesai
- Riwayat pengajuan

## Instalasi

### 1. Requirements
- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Web server (Apache/Nginx)

### 2. Database Setup
1. Buat database baru bernama `school_event_system`
2. Import file `database.sql` ke database
3. Sesuaikan konfigurasi database di `config/database.php`

### 3. File Permissions
```bash
chmod 755 uploads/
chmod 755 uploads/proposals/