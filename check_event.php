<?php
include 'config.php';

echo "<h3>Isi Tabel event_pengajuan:</h3>";
$query = "SELECT * FROM event_pengajuan";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    echo "<pre>";
    while ($row = $result->fetch_assoc()) {
        print_r($row);
        echo "\n-------------------\n";
    }
    echo "</pre>";
} else {
    echo "Tabel event_pengajuan kosong";
}

echo "\n\n<h3>Struktur Tabel event_pengajuan:</h3>";
$result = $conn->query("DESCRIBE event_pengajuan");
echo "<pre>";
while ($row = $result->fetch_assoc()) {
    print_r($row);
    echo "\n";
}
echo "</pre>";

$conn->close(); 