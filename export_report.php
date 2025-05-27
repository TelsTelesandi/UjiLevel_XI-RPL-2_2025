<?php
include 'config.php';
include 'session.php';
if ($_SESSION['role'] != 'admin') die("Akses ditolak");

// Set header for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="laporan_event.xls"');
header('Cache-Control: max-age=0');

// Get all events
$result = $conn->query("SELECT * FROM event ORDER BY tanggal_event DESC");

// Create Excel header
echo "Data Pengajuan Event\n\n";
echo "No.\tNama Event\tTanggal\tPenyelenggara\tDeskripsi\tStatus\n";

// Output data
$no = 1;
while ($row = $result->fetch_assoc()) {
    echo $no . "\t";
    echo $row['nama_event'] . "\t";
    echo $row['tanggal_event'] . "\t";
    echo $row['penyelenggara'] . "\t";
    echo $row['deskripsi'] . "\t";
    echo ($row['status'] ?? 'Pending') . "\n";
    $no++;
}
?> 