<?php
// Mulai session
session_start();

// Cek apakah user sudah login dan memiliki peran Admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_SESSION["role"]) || $_SESSION["role"] !== "Admin"){
    // Jika tidak, redirect ke halaman login
    header("location: index.php");
    exit;
}

// Include file config
require_once "config.php";

// Proses parameter event_id dari URL
if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    // Get URL parameter
    $event_id = trim($_GET["id"]);
    $admin_id = $_SESSION["user_id"];
    $tanggal_verifikasi = date("Y-m-d H:i:s"); // Ambil tanggal dan waktu saat ini
    $catatan_admin = "Pengajuan disetujui."; // Catatan default untuk persetujuan
    $status_verifikasi = "Closed"; // Status verifikasi di tabel verifikasi_event
    
    // Mulai transaksi database
    mysqli_begin_transaction($link);

    try {
        // 1. Update status di tabel event_pengajuan menjadi 'disetujui'
        $sql_update = "UPDATE event_pengajuan SET status = 'disetujui' WHERE event_id = ? AND status = 'menunggu'";
        if($stmt_update = mysqli_prepare($link, $sql_update)){
            mysqli_stmt_bind_param($stmt_update, "i", $param_event_id_update);
            $param_event_id_update = $event_id;

            if(!mysqli_stmt_execute($stmt_update)){
                 throw new Exception("Gagal update status event.");
            }
             mysqli_stmt_close($stmt_update);
        } else {
             throw new Exception("Gagal prepare statement update event.");
        }

        // 2. Masukkan catatan verifikasi ke tabel verifikasi_event
        // Asumsi struktur verifikasi_event adalah verifikasi_id, event_id, admin_id, tanggal_verifikasi, catatan_admin, Status
        $sql_insert_verif = "INSERT INTO verifikasi_event (event_id, admin_id, tanggal_verifikasi, catatan_admin, Status) VALUES (?, ?, ?, ?, ?)";
        if($stmt_insert_verif = mysqli_prepare($link, $sql_insert_verif)){
            mysqli_stmt_bind_param($stmt_insert_verif, "iisss", $param_event_id_verif, $param_admin_id, $param_tanggal_verif, $param_catatan_admin, $param_status_verif);
            $param_event_id_verif = $event_id;
            $param_admin_id = $admin_id;
            $param_tanggal_verif = $tanggal_verifikasi;
            $param_catatan_admin = $catatan_admin;
            $param_status_verif = $status_verifikasi; // Gunakan nilai 'Closed' sesuai struktur tabel Anda

             if(!mysqli_stmt_execute($stmt_insert_verif)){
                 throw new Exception("Gagal mencatat verifikasi event.");
            }
             mysqli_stmt_close($stmt_insert_verif);
        } else {
             throw new Exception("Gagal prepare statement insert verifikasi.");
        }
        
        // Jika semua query berhasil, commit transaksi
        mysqli_commit($link);

        // Redirect kembali ke dashboard admin atau halaman detail event
        header("location: view_event.php?id=" . $event_id); // Redirect ke halaman detail event
        exit();

    } catch (Exception $e) {
        // Jika terjadi error, rollback transaksi
        mysqli_rollback($link);
        echo "Error: " . $e->getMessage();
         // Opsional: Log error ke file
         // error_log("Error approving event ID " . $event_id . ": " . $e->getMessage());
    }

    // Tutup koneksi
    mysqli_close($link);

} else{
    // Jika parameter id tidak ada atau kosong
     header("location: admin_dashboard.php");
     exit();
}
?> 