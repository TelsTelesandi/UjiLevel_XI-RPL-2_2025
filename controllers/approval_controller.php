<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

if (isset($_POST['approve'])) {
    $event_id = intval($_POST['event_id']);
    $catatan  = mysqli_real_escape_string($conn, $_POST['catatan_admin']);
    $admin_id = $_SESSION['user_id'];
    mysqli_query($conn, "UPDATE event_pengajuan SET status='disetujui' WHERE event_id=$event_id");
    mysqli_query($conn, "UPDATE verifikasi_event SET status='closed', admin_id=$admin_id, tanggal_verifikasi=NOW(), catatan_admin='$catatan' WHERE event_id=$event_id");    
    header("Location: ../views/approval_report.php?success=approve");
    exit;
}
if (isset($_POST['reject'])) {
    $event_id = intval($_POST['event_id']);
    $catatan  = mysqli_real_escape_string($conn, $_POST['catatan_admin']);
    $admin_id = $_SESSION['user_id'];
    mysqli_query($conn, "UPDATE event_pengajuan SET status='ditolak' WHERE event_id=$event_id");
    mysqli_query($conn, "UPDATE verifikasi_event SET status='closed', admin_id=$admin_id, tanggal_verifikasi=NOW(), catatan_admin='$catatan' WHERE event_id=$event_id");
    header("Location: ../views/approval_report.php?success=reject");
    exit;
}
?>
