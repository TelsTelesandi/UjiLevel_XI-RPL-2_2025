<?php
session_start();
require_once '../config.php';
require_once 'check_admin.php';

// Database connection is already established in init.php as $conn
if (!isset($conn) || $conn->connect_error) {
    die("Connection failed: " . ($conn->connect_error ?? "Database connection not available"));
}

// Get date range
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Prepare query
$sql = "SELECT ep.tanggal_pengajuan, u.username, ep.judul_kegiatan, ep.event_ekskul, 
               ep.total_biaya, ep.status, ep.proposal
        FROM event_pengajuan ep 
        LEFT JOIN users u ON ep.user_id = u.user_id 
        WHERE ep.tanggal_pengajuan BETWEEN ? AND ?
        ORDER BY ep.tanggal_pengajuan DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="laporan_event_' . date('Y-m-d') . '.csv"');

// Create output stream
$output = fopen('php://output', 'w');

// Add UTF-8 BOM for proper Excel encoding
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Add CSV headers
fputcsv($output, array(
    'Tanggal Pengajuan',
    'Username',
    'Judul Kegiatan',
    'Event Ekskul',
    'Total Biaya',
    'Status',
    'File Proposal'
));

// Add data rows
while ($row = $result->fetch_assoc()) {
    $csvRow = array(
        date('d-m-Y', strtotime($row['tanggal_pengajuan'])),
        $row['username'],
        $row['judul_kegiatan'],
        $row['event_ekskul'],
        'Rp ' . number_format($row['total_biaya'], 0, ',', '.'),
        $row['status'],
        $row['proposal'] ? 'Ada' : 'Tidak Ada'
    );
    fputcsv($output, $csvRow);
}

// Close the output stream
fclose($output);

// Close database connection
$conn->close();
?> 