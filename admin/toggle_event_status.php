<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Anda harus login sebagai admin untuk mengakses halaman ini.";
    header("Location: ../auth/login.php");
    exit();
}

include '../config/db.php';

if (isset($_GET['id']) && isset($_GET['action'])) {
    $event_id = mysqli_real_escape_string($conn, $_GET['id']);
    $action = $_GET['action'];
    
    // Get current event status
    $check_query = mysqli_query($conn, "SELECT status FROM event_pengajuan WHERE event_id = '$event_id'");
    if ($check_query && $event = mysqli_fetch_assoc($check_query)) {
        if ($action === 'close' && $event['status'] !== 'closed') {
            // Close the event
            $update = mysqli_query($conn, "UPDATE event_pengajuan SET status = 'closed', closed_date = NOW() WHERE event_id = '$event_id'");
            if ($update) {
                $_SESSION['success'] = "Event berhasil ditutup.";
            } else {
                $_SESSION['error'] = "Gagal menutup event: " . mysqli_error($conn);
            }
        } elseif ($action === 'unclose' && $event['status'] === 'closed') {
            // Reopen the event to its previous status (we'll set it to completed as default)
            $update = mysqli_query($conn, "UPDATE event_pengajuan SET status = 'completed', closed_date = NULL WHERE event_id = '$event_id'");
            if ($update) {
                $_SESSION['success'] = "Event berhasil dibuka kembali.";
            } else {
                $_SESSION['error'] = "Gagal membuka event: " . mysqli_error($conn);
            }
        }
    } else {
        $_SESSION['error'] = "Event tidak ditemukan.";
    }
}

// Redirect back to dashboard
header("Location: dashboard_admin.php");
exit(); 