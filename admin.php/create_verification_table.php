<?php
require_once '../koneksi.php';

// Tambah kolom untuk verifikasi di tabel events
$query = "ALTER TABLE events 
          ADD COLUMN admin_id INT,
          ADD COLUMN tanggal_verifikasi DATETIME,
          ADD COLUMN catatan_admin TEXT,
          ADD FOREIGN KEY (admin_id) REFERENCES users(id)";

if ($conn->query($query)) {
    echo "Tabel events berhasil diupdate dengan kolom verifikasi!";
} else {
    echo "Error: " . $conn->error;
}
?> 