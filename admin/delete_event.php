<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Anda harus login sebagai admin untuk mengakses halaman ini.";
    header("Location: ../auth/login.php");
    exit();
}

include '../config/db.php';

// Check if event ID is provided
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "ID Event tidak ditemukan!";
    header("Location: dashboard_admin.php");
    exit();
}

$event_id = mysqli_real_escape_string($conn, $_GET['id']);

// Delete the event
$query = "DELETE FROM event_pengajuan WHERE event_id = '$event_id'";

if (mysqli_query($conn, $query)) {
    $_SESSION['success'] = "Event berhasil dihapus!";
} else {
    $_SESSION['error'] = "Error menghapus event: " . mysqli_error($conn);
}

header("Location: dashboard_admin.php");
exit(); 