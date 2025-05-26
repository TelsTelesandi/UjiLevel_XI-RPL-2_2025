# Event Submission System

Sistem pengajuan penyelenggaraan event ekstrakurikuler berbasis PHP dengan fitur lengkap untuk user dan admin.

## Fitur

### User (Ketua Ekstrakurikuler)
- Login ke sistem
- Submit pengajuan event dengan upload proposal
- Melihat status pengajuan event
- Mengubah status request menjadi closed setelah event selesai
- Dashboard dengan ringkasan data pengajuan

### Admin (Bagian Kesiswaan)
- Login ke sistem admin
- CRUD management user
- Approval/rejection pengajuan event dengan catatan
- Dashboard dengan ringkasan seluruh data
- Laporan pengajuan dan verifikasi event dengan export CSV
- Filter laporan berdasarkan tanggal dan status

## Teknologi
- PHP 7.4+
- MySQL/MariaDB
- Tailwind CSS
- Font Awesome Icons
- Responsive Design

## Instalasi

1. **Setup Database**
   - Import file `database.sql` ke MySQL/MariaDB
   - Update konfigurasi database di `config/database.php`

2. **Setup Web Server**
   - Copy semua file ke direktori web server (htdocs/www)
   - Pastikan PHP dan MySQL sudah terinstall
   - Buat folder `uploads/proposals/` dengan permission write

3. **Akses Aplikasi**
   - Buka browser dan akses aplikasi
   - Login dengan akun default:
     - Admin: username `admin`, password `password`
     - User: username `user1`, password `password`

## Struktur Database

### Table: users
- user_id (PK, AI)
- username (VARCHAR, UNIQUE)
- password (VARCHAR, encrypted)
- role (VARCHAR: user/admin)
- nama_lengkap (VARCHAR)
- eskul (VARCHAR)
- created_at (TIMESTAMP)

### Table: event_pengajuan
- event_id (PK, AI)
- user_id (FK to users)
- judul_event (VARCHAR)
- jenis_kegiatan (VARCHAR)
- total_pembiayaan (VARCHAR)
- proposal (VARCHAR, file path)
- deskripsi (TEXT)
- tanggal_pengajuan (DATE)
- status (VARCHAR: menunggu/disetujui/ditolak)
- created_at (TIMESTAMP)

### Table: verifikasi_event
- verifikasi_id (PK, AI)
- event_id (FK to event_pengajuan)
- admin_id (FK to users)
- tanggal_verifikasi (VARCHAR)
- catatan_admin (TEXT)
- status (VARCHAR: closed/unclosed)
- created_at (TIMESTAMP)

## File Structure

\`\`\`
/
├── config/
│   ├── database.php          # Database configuration
│   └── session.php           # Session management
├── includes/
│   ├── header.php            # HTML header
│   ├── navbar.php            # Navigation bar
│   └── footer.php            # HTML footer
├── admin/
│   ├── users.php             # User management
│   ├── approvals.php         # Event approvals
│   └── reports.php           # Reports and analytics
├── uploads/
│   └── proposals/            # Uploaded proposal files
├── login.php                 # Login page
├── logout.php                # Logout handler
├── dashboard.php             # Main dashboard
├── submit_event.php          # Event submission form
├── my_events.php             # User's events list
├── index.php                 # Landing page
├── database.sql              # Database schema
└── README.md                 # This file
\`\`\`

## Keamanan
- Password di-hash menggunakan PHP password_hash()
- Session management untuk autentikasi
- Input validation dan sanitization
- File upload validation
- SQL prepared statements untuk mencegah injection

## Responsive Design
- Mobile-first approach
- Tailwind CSS untuk styling modern
- Font Awesome untuk icons
- Responsive tables dan forms

## Browser Support
- Chrome/Chromium
- Firefox
- Safari
- Edge

## License
MIT License - Free to use and modify
\`\`\`

```txt file=".htaccess" type="code"
RewriteEngine On

# Redirect to HTTPS (optional)
# RewriteCond %{HTTPS} off
# RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Hide .php extension
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^([^\.]+)$ $1.php [NC,L]

# Prevent access to config files
<Files "*.php">
    <RequireAll>
        Require all granted
    </RequireAll>
</Files>

<FilesMatch "^(config|includes)/">
    Order Allow,Deny
    Deny from all
</FilesMatch>

# Security headers
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>

# File upload security
<FilesMatch "\.(php|php3|php4|php5|phtml|pl|py|jsp|asp|sh|cgi)$">
    Order Allow,Deny
    Deny from all
</FilesMatch>

<Directory "uploads/">
    <FilesMatch "\.(php|php3|php4|php5|phtml|pl|py|jsp|asp|sh|cgi)$">
        Order Allow,Deny
        Deny from all
    </FilesMatch>
</Directory>
