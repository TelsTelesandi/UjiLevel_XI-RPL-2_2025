<?php
// Mulai session
session_start();

// Cek apakah user sudah login, jika tidak, redirect ke halaman login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: index.php");
    exit;
}

// Cek apakah user adalah Admin, jika ya, redirect ke halaman admin dashboard (Admin tidak perlu menutup request user secara individual dari sini)
if(isset($_SESSION["role"]) && $_SESSION["role"] === "Admin") {
    header("location: admin_dashboard.php"); // Atau halaman lain yang sesuai
    exit;
}

// Include file config
require_once "config.php";

// Proses parameter event_id dari URL
if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    // Get URL parameter
    $event_id = trim($_GET["id"]);
    
    // Siapkan query update
    // Pastikan pengajuan ini milik user yang sedang login DAN statusnya belum 'closed'
    $sql = "UPDATE event_pengajuan SET status = 'closed' WHERE event_id = ? AND user_id = ? AND status != 'closed'";
    
    if($stmt = mysqli_prepare($link, $sql)){
        // Bind variables ke prepared statement sebagai parameter
        mysqli_stmt_bind_param($stmt, "ii", $param_event_id, $param_user_id);
        
        // Set parameter
        $param_event_id = $event_id;
        $param_user_id = $_SESSION["user_id"];
        
        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)){
            // Redirect ke halaman dashboard pengguna
            header("location: user_dashboard.php");
            exit();
        } else{
            echo "Ada yang salah. Mohon coba lagi nanti.";
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($link);

} else{
    // Jika parameter id tidak ada atau kosong
    header("location: user_dashboard.php");
    exit();
}
?> 