<?php
include 'config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Check if event_id is provided
if (!isset($_GET['event_id'])) {
    $_SESSION['error'] = "ID Event tidak ditemukan.";
    header("Location: dashboard_admin.php");
    exit;
}

$event_id = $_GET['event_id'];

// Delete the event
$stmt = $conn->prepare("DELETE FROM event_pengajuan WHERE event_id = ?");
$stmt->bind_param("i", $event_id);

if ($stmt->execute()) {
    $_SESSION['success'] = "Event berhasil dihapus.";
} else {
    $_SESSION['error'] = "Gagal menghapus event.";
}

header("Location: dashboard_admin.php");
exit;
