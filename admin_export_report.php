<?php
require_once 'db_connect.php';
session_start();

// Cek apakah user sudah login dan role-nya adalah 'admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Jika tidak, arahkan kembali ke halaman login
    header("Location: login.php");
    exit();
}

// Ambil data pengajuan event beserta info user dan verifikasi dari database
$sql = "SELECT ";
$sql .= "ep.event_id, ep.judul_event, ep.jenis_kegiatan, ep.total_pembiyaan, ep.Proposal, ep.deskripsi, ep.tanggal_pengajuan, ep.status as event_status, ";
$sql .= "u.username, u.nama_lengkap, u.Ekskul, ";
$sql .= "ve.tanggal_verifikasi, ve.catatan_admin, ve.Status as verification_status ";
$sql .= "FROM event_pengajuan ep ";
$sql .= "JOIN users u ON ep.user_id = u.user_id ";
$sql .= "LEFT JOIN verifikasi_event ve ON ep.event_id = ve.event_id "; // Gunakan LEFT JOIN untuk menyertakan pengajuan yang belum diverifikasi
$sql .= "ORDER BY ep.tanggal_pengajuan DESC, ve.tanggal_verifikasi DESC";

$result = $conn->query($sql);

$report_data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $report_data[] = $row;
    }
}

$conn->close();

// Output CSV
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="laporan_pengajuan_event_' . date('Ymd') . '.csv"');

$output = fopen('php://output', 'w');

// Header CSV
fputcsv($output, array('ID Event', 'Judul Event', 'Jenis Kegiatan', 'Total Pembiayaan', 'Pengaju', 'Ekskul', 'Tanggal Pengajuan', 'Status Pengajuan', 'Tanggal Verifikasi', 'Status Verifikasi', 'Catatan Admin'));

// Data CSV
foreach ($report_data as $row) {
    fputcsv($output, array(
        $row['event_id'],
        $row['judul_event'],
        $row['jenis_kegiatan'],
        $row['total_pembiyaan'],
        $row['nama_lengkap'],
        $row['Ekskul'],
        $row['tanggal_pengajuan'],
        ucfirst($row['event_status']),
        $row['tanggal_verifikasi'] ?? 'Belum Diverifikasi',
        ucfirst($row['verification_status'] ?? 'N/A'),
        $row['catatan_admin'] ?? '-'
    ));
}

fclose($output);
exit();

?> 