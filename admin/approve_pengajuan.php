<?php
session_start();

require_once '../db_connect.php';

// Pastikan admin sudah login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header("location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'])) {
    $event_id = intval($_POST['event_id']);
    $admin_id = $_SESSION['user_id'];
    $tanggal_verifikasi = date('Y-m-d H:i:s');
    $status = 'Approved';
    $catatan = '';

    // Cek apakah sudah ada entry di verifikasi_event
    $sql_check = "SELECT verifikasi_id FROM verifikasi_event WHERE event_id = ?";
    if ($stmt_check = mysqli_prepare($link, $sql_check)) {
        mysqli_stmt_bind_param($stmt_check, "i", $event_id);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);
        if (mysqli_stmt_num_rows($stmt_check) > 0) {
            // Sudah ada, update
            $sql_update = "UPDATE verifikasi_event SET status = ?, admin_id = ?, tanggal_verifikasi = ?, catatan = ? WHERE event_id = ?";
            if ($stmt_update = mysqli_prepare($link, $sql_update)) {
                mysqli_stmt_bind_param($stmt_update, "sissi", $status, $admin_id, $tanggal_verifikasi, $catatan, $event_id);
                mysqli_stmt_execute($stmt_update);
                mysqli_stmt_close($stmt_update);
            }
        } else {
            // Belum ada, insert
            $sql_insert = "INSERT INTO verifikasi_event (event_id, admin_id, tanggal_verifikasi, status, catatan) VALUES (?, ?, ?, ?, ?)";
            if ($stmt_insert = mysqli_prepare($link, $sql_insert)) {
                mysqli_stmt_bind_param($stmt_insert, "iisss", $event_id, $admin_id, $tanggal_verifikasi, $status, $catatan);
                mysqli_stmt_execute($stmt_insert);
                mysqli_stmt_close($stmt_insert);
            }
        }
        mysqli_stmt_close($stmt_check);
    }
    mysqli_close($link);
    header("location: approval.php?msg=approved");
    exit;
} else {
    header("location: approval.php?msg=error");
    exit;
} 