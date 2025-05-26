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

    // Catatan admin untuk penolakan
    $catatan_admin = "Pengajuan ditolak oleh Admin."; // Pesan default
    $status_verifikasi_event_pengajuan = "ditolak"; // Status di tabel event_pengajuan
    $status_verifikasi_verifikasi_event = "Ditolak"; // Status di tabel verifikasi_event

    // Mulai transaksi database
    mysqli_begin_transaction($link);

    try {
        // 1. Update status di tabel event_pengajuan menjadi 'ditolak' hanya jika statusnya 'menunggu'
        $sql_update = "UPDATE event_pengajuan SET status = ? WHERE event_id = ? AND status = 'menunggu'";
        if($stmt_update = mysqli_prepare($link, $sql_update)){
            mysqli_stmt_bind_param($stmt_update, "si", $status_verifikasi_event_pengajuan, $param_event_id_update);
            $param_event_id_update = $event_id;

            if(!mysqli_stmt_execute($stmt_update)){
                 throw new Exception("Gagal update status event.");
            }

            // Cek apakah ada baris yang terpengaruh (status sebelumnya memang 'menunggu')
            if (mysqli_stmt_affected_rows($stmt_update) == 0) {
                // Jika tidak ada baris terpengaruh, mungkin event_id tidak valid atau status sudah berubah
                 mysqli_rollback($link); // Batalkan transaksi
                 mysqli_stmt_close($stmt_update);
                // Redirect dengan pesan error atau info
                 header("location: admin_dashboard.php?status=error&message=Pengajuan tidak dapat ditolak. Status mungkin sudah berubah atau ID tidak valid.");
                 exit();
            }

             mysqli_stmt_close($stmt_update);
        } else {
             throw new Exception("Gagal prepare statement update event.");
        }

        // 2. Masukkan catatan verifikasi ke tabel verifikasi_event
        $sql_insert_verif = "INSERT INTO verifikasi_event (event_id, admin_id, tanggal_verifikasi, catatan_admin, Status) VALUES (?, ?, ?, ?, ?)";
        if($stmt_insert_verif = mysqli_prepare($link, $sql_insert_verif)){
            mysqli_stmt_bind_param($stmt_insert_verif, "iisss", $param_event_id_verif, $param_admin_id, $param_tanggal_verif, $param_catatan_admin, $param_status_verif);
            $param_event_id_verif = $event_id;
            $param_admin_id = $admin_id;
            $param_tanggal_verif = $tanggal_verifikasi;
            $param_catatan_admin = $catatan_admin;
            $param_status_verif = $status_verifikasi_verifikasi_event; // Gunakan nilai 'Ditolak'

             if(!mysqli_stmt_execute($stmt_insert_verif)){
                 throw new Exception("Gagal mencatat verifikasi event.");
            }
             mysqli_stmt_close($stmt_insert_verif);
        } else {
             throw new Exception("Gagal prepare statement insert verifikasi.");
        }

        // Jika semua query berhasil, commit transaksi
        mysqli_commit($link);

        // Redirect kembali ke dashboard admin dengan pesan sukses
        header("location: admin_dashboard.php?status=success&message=Pengajuan event berhasil ditolak.");
        exit();

    } catch (Exception $e) {
        // Jika terjadi error, rollback transaksi
        mysqli_rollback($link);

        // Redirect kembali ke dashboard admin dengan pesan error
        // Gunakan urlencode untuk memastikan pesan error aman di URL
        $error_message = urlencode("Terjadi kesalahan saat menolak pengajuan: " . $e->getMessage());
        header("location: admin_dashboard.php?status=error&message=" . $error_message);
        exit();
    }

    // Tutup koneksi (ini akan dijalankan setelah try-catch atau jika terjadi error sebelum transaksi)
    mysqli_close($link);

} else{
    // Jika parameter id tidak ada atau kosong
    // Redirect kembali ke dashboard admin dengan pesan error
     header("location: admin_dashboard.php?status=error&message=ID pengajuan tidak ditemukan.");
     exit();
}
?>  