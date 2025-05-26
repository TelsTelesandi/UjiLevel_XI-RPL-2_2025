<?php
// Mulai session
session_start();

// Cek apakah user sudah login dan memiliki peran Admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_SESSION["role"]) || $_SESSION["role"] !== "Admin"){
    header("location: index.php");
    exit;
}

// Include file config
require_once "config.php";

// Proses parameter event_id dari URL
if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    $event_id = trim($_GET["id"]);
    
    // Siapkan query update
    $sql = "UPDATE event_pengajuan SET status = 'closed' WHERE event_id = ?";
    
    if($stmt = mysqli_prepare($link, $sql)){
        // Bind variables ke prepared statement
        mysqli_stmt_bind_param($stmt, "i", $param_event_id);
        
        // Set parameter
        $param_event_id = $event_id;
        
        // Execute statement
        if(mysqli_stmt_execute($stmt)){
            // Tambahkan log verifikasi
            $admin_id = $_SESSION["user_id"];
            $verifikasi_sql = "INSERT INTO verifikasi_event (event_id, admin_id, tanggal_verifikasi, Status, catatan_admin) 
                             VALUES (?, ?, NOW(), 'closed', 'Request ditutup oleh admin')";
            
            if($verifikasi_stmt = mysqli_prepare($link, $verifikasi_sql)){
                mysqli_stmt_bind_param($verifikasi_stmt, "ii", $event_id, $admin_id);
                mysqli_stmt_execute($verifikasi_stmt);
                mysqli_stmt_close($verifikasi_stmt);
            }
            
            header("location: admin_report.php");
            exit();
        } else{
            echo "Ada yang salah. Mohon coba lagi nanti.";
        }

        mysqli_stmt_close($stmt);
    }
    
    mysqli_close($link);
} else{
    header("location: admin_report.php");
    exit();
}
?> 