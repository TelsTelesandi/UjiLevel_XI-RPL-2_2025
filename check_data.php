<?php
include 'config.php';

echo "<h3>Data di Tabel event_pengajuan:</h3>";
$query = "SELECT ep.*, u.username 
          FROM event_pengajuan ep 
          LEFT JOIN users u ON ep.user_id = u.user_id 
          ORDER BY ep.tanggal_pengajuan DESC";
$result = $conn->query($query);

if (!$result) {
    echo "Error in query: " . $conn->error;
} else {
    if ($result->num_rows > 0) {
        echo "<pre>";
        while ($row = $result->fetch_assoc()) {
            print_r($row);
            echo "\n-------------------\n";
        }
        echo "</pre>";
    } else {
        echo "Tidak ada data event pengajuan<br>";
    }
}

echo "<h3>Data di Tabel users:</h3>";
$result = $conn->query("SELECT * FROM users");
if ($result->num_rows > 0) {
    echo "<pre>";
    while ($row = $result->fetch_assoc()) {
        // Hide password
        $row['password'] = '********';
        print_r($row);
        echo "\n-------------------\n";
    }
    echo "</pre>";
} else {
    echo "Tidak ada data users<br>";
}

$conn->close(); 