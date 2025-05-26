<?php
include 'koneksi.php';

// SQL untuk menambahkan kolom baru
$sql = "ALTER TABLE events 
        ADD COLUMN catatan_admin TEXT NULL AFTER status,
        ADD COLUMN tanggal_verifikasi DATETIME NULL AFTER catatan_admin";

if ($conn->query($sql) === TRUE) {
    echo "Tabel events berhasil diupdate!";
} else {
    echo "Error updating table: " . $conn->error;
}

$conn->close();
?> 