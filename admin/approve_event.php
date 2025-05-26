<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

$auth = new Auth();
if (!$auth->isLoggedIn() || !$auth->isAdmin()) {
    header("Location: ../login.php");
    exit();
}

$database = new Database();
$conn = $database->getConnection();

if (isset($_GET['id']) && isset($_GET['action'])) {
    $event_id = $_GET['id'];
    $action = $_GET['action'];
    
    $status = ($action === 'approve') ? 'disetujui' : 'ditolak';
    
    // Update event status
    $query = "UPDATE event_pengajuan SET status = :status WHERE event_id = :event_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':event_id', $event_id);
    
    if ($stmt->execute()) {
        // Add verification record
        $verify_query = "INSERT INTO verifikasi_event (event_id, admin_id, tanggal_verifikasi, catatan_admin, status) 
                        VALUES (:event_id, :admin_id, :tanggal_verifikasi, :catatan_admin, :status)";
        $verify_stmt = $conn->prepare($verify_query);
        $verify_stmt->bindParam(':event_id', $event_id);
        $verify_stmt->bindParam(':admin_id', $_SESSION['user_id']);
        $verify_stmt->bindParam(':tanggal_verifikasi', date('Y-m-d'));
        $catatan = ($action === 'approve') ? 'Event disetujui' : 'Event ditolak';
        $verify_stmt->bindParam(':catatan_admin', $catatan);
        $verify_status = ($action === 'approve') ? 'Close' : 'Closed';
        $verify_stmt->bindParam(':status', $verify_status);
        $verify_stmt->execute();
        
        $_SESSION['message'] = 'Event berhasil ' . (($action === 'approve') ? 'disetujui' : 'ditolak') . '!';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Gagal memproses event!';
        $_SESSION['message_type'] = 'error';
    }
}

header("Location: manage_events.php");
exit();
?>
