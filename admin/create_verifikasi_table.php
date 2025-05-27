<?php
require_once '../init.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Drop table if exists to recreate with correct structure
    $conn->query("DROP TABLE IF EXISTS verifikasi_event");

    // Create the verifikasi_event table with correct structure
    $sql = "CREATE TABLE verifikasi_event (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_id INT NOT NULL,
        status VARCHAR(50) NOT NULL DEFAULT 'Pending',
        tanggal_verifikasi DATE NOT NULL,
        catatan TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (event_id) REFERENCES event_pengajuan(id) ON DELETE CASCADE
    )";

    if ($conn->query($sql) === TRUE) {
        echo "Tabel verifikasi_event berhasil dibuat\n";
    } else {
        throw new Exception("Error creating table: " . $conn->error);
    }

    // Tambahkan indeks untuk optimasi
    $conn->query("CREATE INDEX IF NOT EXISTS idx_event_id ON verifikasi_event(event_id)");
    $conn->query("CREATE INDEX IF NOT EXISTS idx_status ON verifikasi_event(status)");
    echo "Indeks berhasil ditambahkan\n";

    // Copy status dari event_pengajuan ke verifikasi_event untuk data yang sudah ada
    $sql = "INSERT INTO verifikasi_event (event_id, status, tanggal_verifikasi, catatan)
            SELECT id, status, COALESCE(
                (SELECT ve.tanggal_verifikasi 
                 FROM verifikasi_event ve 
                 WHERE ve.event_id = ep.id), 
                CURRENT_DATE
            ), '' 
            FROM event_pengajuan ep
            WHERE NOT EXISTS (
                SELECT 1 
                FROM verifikasi_event ve 
                WHERE ve.event_id = ep.id
            )";
    
    if ($conn->query($sql) === TRUE) {
        echo "Data verifikasi berhasil disinkronkan\n";
    } else {
        throw new Exception("Error syncing data: " . $conn->error);
    }

    echo "Proses selesai!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} finally {
    $conn->close();
}
?> 