<?php
require_once '../includes/auth_check.php';
require_once '../../config/database.php';

if ($_SESSION['role'] != 'admin') {
    header('Location: ../user/dashboard.php');
    exit();
}

// Ambil data event yang perlu diverifikasi
$stmt = $pdo->prepare("SELECT e.*, u.nama_lengkap FROM event_pengajuan e JOIN users u ON e.user_id = u.user_id WHERE e.status = 'menunggu'");
$stmt->execute();
$events = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_id = $_POST['event_id'];
    $status = $_POST['status'];
    $catatan = $_POST['catatan'];
    
    // Update status event
    $stmt = $pdo->prepare("UPDATE event_pengajuan SET status = ? WHERE event_id = ?");
    $stmt->execute([$status, $event_id]);
    
    // Tambahkan verifikasi
    $stmt = $pdo->prepare("INSERT INTO verifikasi_event (event_id, admin_id, tanggal_verifikasi, catatan_admin, Status) 
                          VALUES (?, ?, NOW(), ?, 'Closed')");
    $stmt->execute([$event_id, $_SESSION['user_id'], $catatan]);
    
    header('Location: verifikasi.php');
    exit();
}
?>
<!-- Tabel verifikasi event HTML -->