<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

$auth = new Auth();
if (!$auth->isLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

$database = new Database();
$conn = $database->getConnection();

if (isset($_GET['id'])) {
    $event_id = $_GET['id'];
    
    // Check if event belongs to user and is still pending
    $query = "SELECT * FROM event_pengajuan WHERE event_id = :event_id AND user_id = :user_id AND status = 'menunggu'";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':event_id', $event_id);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        // Delete the event
        $delete_query = "DELETE FROM event_pengajuan WHERE event_id = :event_id";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bindParam(':event_id', $event_id);
        
        if ($delete_stmt->execute()) {
            $_SESSION['message'] = 'Event berhasil dibatalkan!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Gagal membatalkan event!';
            $_SESSION['message_type'] = 'error';
        }
    } else {
        $_SESSION['message'] = 'Event tidak ditemukan atau tidak dapat dibatalkan!';
        $_SESSION['message_type'] = 'error';
    }
}

header("Location: my_events.php");
exit();
?>
