<?php
session_start();
include '../config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

if (isset($_POST['submit'])) {
    $user_id = $_SESSION['user_id'];
    $nama_event = mysqli_real_escape_string($conn, $_POST['nama_event']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    
    $query = "INSERT INTO event_pengajuan (user_id, nama_event, deskripsi, tanggal, status) 
              VALUES ('$user_id', '$nama_event', '$deskripsi', '$tanggal', 'pending')";
              
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Event berhasil diajukan!";
    } else {
        $_SESSION['error'] = "Gagal mengajukan event: " . mysqli_error($conn);
    }
    
    header("Location: dashboard_user.php");
    exit();
}

// If no form submission, redirect back to dashboard
header("Location: dashboard_user.php");
exit();
?> 