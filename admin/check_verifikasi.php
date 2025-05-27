<?php
require_once __DIR__ . '/../config.php';

echo "<h3>Data Event Pengajuan dengan Verifikasi:</h3>";
$query = "SELECT 
    ep.*,
    ve.tanggal_verifikasi,
    ve.catatan
FROM 
    event_pengajuan ep
    LEFT JOIN verifikasi_event ve ON ep.id = ve.event_id
ORDER BY 
    ep.tanggal_pengajuan DESC";

$result = $conn->query($query);
if ($result) {
    echo "<pre>";
    while ($row = $result->fetch_assoc()) {
        print_r($row);
    }
    echo "</pre>";
} else {
    echo "Error querying data: " . $conn->error;
}

echo "<h3>Struktur Tabel verifikasi_event:</h3>";
$query = "DESCRIBE verifikasi_event";
$result = $conn->query($query);
if ($result) {
    echo "<pre>";
    while ($row = $result->fetch_assoc()) {
        print_r($row);
    }
    echo "</pre>";
} else {
    echo "Error checking table structure: " . $conn->error;
}

$conn->close();
?> 