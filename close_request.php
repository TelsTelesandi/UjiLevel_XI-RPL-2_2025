<?php
require_once 'db_connect.php';
session_start();

// Cek apakah user sudah login dan role-nya sesuai
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    // Jika tidak, arahkan kembali ke halaman login
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Pastikan event_id ada di parameter URL
if (isset($_GET['event_id'])) {
    $event_id = intval($_GET['event_id']); // Ambil event_id dan pastikan integer

    // Verifikasi bahwa pengajuan ini milik user yang sedang login
    $sql_check_owner = "SELECT event_id FROM event_pengajuan WHERE event_id = ? AND user_id = ?";
    $stmt_check_owner = $conn->prepare($sql_check_owner);
    if ($stmt_check_owner) {
        $stmt_check_owner->bind_param("ii", $event_id, $user_id);
        $stmt_check_owner->execute();
        $stmt_check_owner->store_result();

        if ($stmt_check_owner->num_rows === 1) {
            // Pengajuan ditemukan dan milik user ini, lanjutkan update status
            $new_status = 'closed';
            $sql_update_status = "UPDATE event_pengajuan SET status = ? WHERE event_id = ?";
            $stmt_update_status = $conn->prepare($sql_update_status);
            if ($stmt_update_status) {
                $stmt_update_status->bind_param("si", $new_status, $event_id);
                $stmt_update_status->execute();
                $stmt_update_status->close();
            } else {
                // Handle error prepare statement update
            }
        } else {
            // Pengajuan tidak ditemukan atau bukan milik user ini
            // Opsional: Tampilkan pesan error atau log aktivitas mencurigakan
        }
        $stmt_check_owner->close();
    } else {
        // Handle error prepare statement check owner
    }

    $conn->close();

    // Arahkan kembali ke halaman dashboard user
    header("Location: user_dashboard.php");
    exit();

} else {
    // Jika parameter event_id tidak ada, arahkan kembali
    header("Location: user_dashboard.php");
    exit();
}

?> 