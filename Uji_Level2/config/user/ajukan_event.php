<?php
require_once '../includes/auth_check.php';
require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $_POST['judul_event'];
    $jenis = $_POST['jenis_kegiatan'];
    $biaya = $_POST['Total_pembiyaan'];
    $deskripsi = $_POST['deskripsi'];
    
    // Handle file upload
    $proposal = $_FILES['Proposal']['name'];
    $target = "../../uploads/" . basename($proposal);
    move_uploaded_file($_FILES['Proposal']['tmp_name'], $target);
    
    $stmt = $pdo->prepare("INSERT INTO event_pengajuan (user_id, judul_event, jenis_kegiatan, Total_pembiyaan, Proposal, deskripsi, tanggal_pengajuan, status) 
                          VALUES (?, ?, ?, ?, ?, ?, NOW(), 'menunggu')");
    $stmt->execute([$_SESSION['user_id'], $judul, $jenis, $biaya, $proposal, $deskripsi]);
    
    header('Location: status_pengajuan.php');
    exit();
}
?>
<!-- Form pengajuan event HTML -->