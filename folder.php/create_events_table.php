<?php
include 'koneksi.php';

// Hapus tabel jika sudah ada untuk menghindari konflik
$drop_table = "DROP TABLE IF EXISTS events";
$conn->query($drop_table);

// Buat tabel baru
$sql = "CREATE TABLE events (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    judul_event VARCHAR(255) NOT NULL,
    jenis_kegiatan VARCHAR(50) NOT NULL,
    total_pembiayaan DECIMAL(15,2) NOT NULL,
    proposal_path VARCHAR(255),
    deskripsi TEXT NOT NULL,
    tanggal_pengajuan DATETIME NOT NULL,
    status VARCHAR(20) DEFAULT 'Pending'
)";

if ($conn->query($sql) === TRUE) {
    echo "Tabel events berhasil dibuat!";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?> 