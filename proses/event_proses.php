<?php
session_start();
include '../config/database.php';
include '../includes/auth_check.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../user/dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['id'])) {
    $event_id = $_POST['id'];
    $action = $_POST['action'];
    
    // Validasi action
    if (!in_array($action, ['approve', 'reject'])) {
        $_SESSION['error'] = "Aksi tidak valid!";
        header("Location: ../admin/events.php");
        exit();
    }
    
    // Update status event
    $status = $action == 'approve' ? 'approved' : 'rejected';
    
    try {
        $stmt = $conn->prepare("UPDATE event_pengajuan SET status = ? WHERE id = ?");
        $stmt->execute([$status, $event_id]);
        
        $_SESSION['success'] = "Event berhasil di" . ($action == 'approve' ? 'setujui' : 'tolak');
    } catch (PDOException $e) {
        $_SESSION['error'] = "Terjadi kesalahan: " . $e->getMessage();
    }
    
    header("Location: ../admin/events.php");
    exit();
}

header("Location: ../admin/dashboard.php");
exit();
?>