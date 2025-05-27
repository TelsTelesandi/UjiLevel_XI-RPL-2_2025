<?php
// Koneksi ke database
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

try {
    // Buat tabel verifikasi_event jika belum ada
    $sql = "CREATE TABLE IF NOT EXISTS verifikasi_event (
        verifikasi_id INT AUTO_INCREMENT PRIMARY KEY,
        event_id INT NOT NULL,
        admin_id INT NOT NULL,
        tanggal_verifikasi DATETIME NOT NULL,
        catatan_admin TEXT,
        status ENUM('verified', 'canceled') NOT NULL DEFAULT 'verified',
        FOREIGN KEY (event_id) REFERENCES event_pengajuan(event_id) ON DELETE CASCADE,
        FOREIGN KEY (admin_id) REFERENCES users(user_id) ON DELETE CASCADE
    )";
    
    $db->exec($sql);
    echo "Tabel verifikasi_event berhasil dibuat!";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 