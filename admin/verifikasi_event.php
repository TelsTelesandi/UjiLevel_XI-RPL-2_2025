<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../init.php';
require_once 'check_admin.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug log file
$log_file = __DIR__ . '/debug.log';

// Only process if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debug: Log POST data
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - POST data received: " . print_r($_POST, true) . "\n", FILE_APPEND);

    if (!isset($_POST['event_id']) || !isset($_POST['status']) || !isset($_POST['tanggal_verifikasi'])) {
        $_SESSION['error'] = "Data tidak lengkap";
        file_put_contents($log_file, date('Y-m-d H:i:s') . " - Missing required fields. POST data: " . print_r($_POST, true) . "\n", FILE_APPEND);
        header("Location: dashboard.php");
        exit();
    }

    $event_id = $_POST['event_id'];
    $status = $_POST['status'];
    $tanggal_verifikasi = $_POST['tanggal_verifikasi'];
    $catatan = isset($_POST['catatan']) ? $_POST['catatan'] : '';

    try {
        // Start transaction
        $conn->begin_transaction();

        // Debug: Log connection status
        file_put_contents($log_file, date('Y-m-d H:i:s') . " - Database connection status: " . ($conn->connect_error ? $conn->connect_error : "Connected") . "\n", FILE_APPEND);

        // Update status di tabel event_pengajuan
        $stmt = $conn->prepare("UPDATE event_pengajuan SET status = ? WHERE id = ?");
        if (!$stmt) {
            throw new Exception("Error preparing event_pengajuan update: " . $conn->error);
        }
        $stmt->bind_param("si", $status, $event_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Gagal mengupdate status event: " . $stmt->error);
        }

        // Debug: Log first update
        file_put_contents($log_file, date('Y-m-d H:i:s') . " - Updated event_pengajuan status successfully for event_id: $event_id\n", FILE_APPEND);

        // Check if verifikasi record exists
        $check = $conn->prepare("SELECT id FROM verifikasi_event WHERE event_id = ?");
        if (!$check) {
            throw new Exception("Error preparing verification check: " . $conn->error);
        }
        $check->bind_param("i", $event_id);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            // Update existing verification
            $update = $conn->prepare("UPDATE verifikasi_event SET status = ?, tanggal_verifikasi = ?, catatan = ?, updated_at = CURRENT_TIMESTAMP WHERE event_id = ?");
            if (!$update) {
                throw new Exception("Error preparing verifikasi update: " . $conn->error);
            }
            $update->bind_param("sssi", $status, $tanggal_verifikasi, $catatan, $event_id);
            if (!$update->execute()) {
                throw new Exception("Gagal mengupdate data verifikasi: " . $update->error);
            }
            file_put_contents($log_file, date('Y-m-d H:i:s') . " - Updated existing verification record\n", FILE_APPEND);
        } else {
            // Insert new verification
            $insert = $conn->prepare("INSERT INTO verifikasi_event (event_id, status, tanggal_verifikasi, catatan) VALUES (?, ?, ?, ?)");
            if (!$insert) {
                throw new Exception("Error preparing verifikasi insert: " . $conn->error);
            }
            $insert->bind_param("isss", $event_id, $status, $tanggal_verifikasi, $catatan);
            if (!$insert->execute()) {
                throw new Exception("Gagal menyimpan data verifikasi: " . $insert->error);
            }
            file_put_contents($log_file, date('Y-m-d H:i:s') . " - Inserted new verification record\n", FILE_APPEND);
        }

        $conn->commit();
        $_SESSION['success'] = "Verifikasi berhasil disimpan";
        file_put_contents($log_file, date('Y-m-d H:i:s') . " - Transaction committed successfully\n", FILE_APPEND);

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Error: " . $e->getMessage();
        file_put_contents($log_file, date('Y-m-d H:i:s') . " - Error in verifikasi_event.php: " . $e->getMessage() . "\n", FILE_APPEND);
    }

    // Close all statements
    if (isset($stmt)) $stmt->close();
    if (isset($check)) $check->close();
    if (isset($update)) $update->close();
    if (isset($insert)) $insert->close();

    // Debug: Log before redirect
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - Redirecting to dashboard...\n", FILE_APPEND);
    
    // Redirect back to dashboard
    header("Location: dashboard.php");
    exit();
} else {
    // If accessed directly without POST, redirect to dashboard
    header("Location: dashboard.php");
    exit();
} 