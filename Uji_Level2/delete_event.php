<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];
    
    try {
        // Start transaction
        $pdo->beginTransaction();
        
        // First delete related records in verifikasi_event table
        $stmt = $pdo->prepare("DELETE FROM verifikasi_event WHERE event_id = ?");
        $stmt->execute([$event_id]);
        
        // Then delete the event
        $stmt = $pdo->prepare("DELETE FROM event_pengajuan WHERE event_id = ?");
        $stmt->execute([$event_id]);
        
        // Commit transaction
        $pdo->commit();
        
        header("Location: dashboard.php?delete_success=1");
        exit();
    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        header("Location: view_event.php?id=" . $event_id . "&delete_error=1");
        exit();
    }
} else {
    header("Location: dashboard.php");
    exit();
} 