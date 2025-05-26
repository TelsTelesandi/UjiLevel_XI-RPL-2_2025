<?php
require_once 'db_connect.php';
session_start();

// Cek apakah user sudah login dan role-nya adalah 'admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Jika tidak, arahkan kembali ke halaman login
    header("Location: login.php");
    exit();
}

// Pastikan user_id ada di parameter URL
if (isset($_GET['user_id'])) {
    $user_id_to_delete = intval($_GET['user_id']); // Ambil user_id dan pastikan integer

    // Sebelum menghapus, cek apakah user ini adalah admin
    $sql_check_role = "SELECT role FROM users WHERE user_id = ?";
    $stmt_check_role = $conn->prepare($sql_check_role);
    if ($stmt_check_role) {
        $stmt_check_role->bind_param("i", $user_id_to_delete);
        $stmt_check_role->execute();
        $result_check_role = $stmt_check_role->get_result();

        if ($result_check_role->num_rows === 1) {
            $user = $result_check_role->fetch_assoc();
            if ($user['role'] === 'admin') {
                // Jangan hapus jika user adalah admin
                // Opsional: Set pesan error di session
                $_SESSION['error_message'] = "Tidak dapat menghapus akun admin.";
            } else {
                // User bukan admin, lanjutkan proses hapus
                // Hapus pengajuan event terkait user ini terlebih dahulu (untuk menjaga integritas referensi foreign key)
                 $sql_delete_events = "DELETE FROM event_pengajuan WHERE user_id = ?";
                 $stmt_delete_events = $conn->prepare($sql_delete_events);
                 if ($stmt_delete_events) {
                     $stmt_delete_events->bind_param("i", $user_id_to_delete);
                     $stmt_delete_events->execute();
                     $stmt_delete_events->close();
                 } // else handle error prepare delete events

                // Hapus verifikasi terkait pengajuan user ini (jika ada)
                 $sql_delete_verifications = "DELETE ve FROM verifikasi_event ve JOIN event_pengajuan ep ON ve.event_id = ep.event_id WHERE ep.user_id = ?";
                 $stmt_delete_verifications = $conn->prepare($sql_delete_verifications);
                 if ($stmt_delete_verifications) {
                     $stmt_delete_verifications->bind_param("i", $user_id_to_delete);
                     $stmt_delete_verifications->execute();
                     $stmt_delete_verifications->close();
                 } // else handle error prepare delete verifications

                // Hapus user dari tabel users
                $sql_delete_user = "DELETE FROM users WHERE user_id = ?";
                $stmt_delete_user = $conn->prepare($sql_delete_user);

                if ($stmt_delete_user) {
                    $stmt_delete_user->bind_param("i", $user_id_to_delete);
                    if ($stmt_delete_user->execute()) {
                        // Hapus berhasil
                        // Opsional: Set pesan sukses di session
                        $_SESSION['success_message'] = "Pengguna berhasil dihapus.";
                    } else {
                        // Handle error execute delete user
                         $_SESSION['error_message'] = "Error saat menghapus pengguna: " . $stmt_delete_user->error;
                    }
                    $stmt_delete_user->close();
                } else {
                    // Handle error prepare delete user
                     $_SESSION['error_message'] = "Error menyiapkan statement database.";
                }
            }
        } else {
            // User tidak ditemukan
            // Opsional: Set pesan error di session
             $_SESSION['error_message'] = "Pengguna tidak ditemukan.";
        }
        $stmt_check_role->close();
    } else {
        // Handle error prepare statement check role
         $_SESSION['error_message'] = "Error menyiapkan statement database.";
    }

    $conn->close();
}

// Arahkan kembali ke halaman manajemen pengguna
header("Location: admin_manage_users.php");
exit();
?> 