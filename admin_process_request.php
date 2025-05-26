<?php
// Aktifkan error reporting PHP secara programmatic di awal skrip
// Ini sebagai cadangan jika pengaturan php.ini tidak sepenuhnya berlaku
error_reporting(E_ALL);
ini_set('display_errors', 'On');
ini_set('display_startup_errors', 'On');

echo "DEBUG: Script starts.\n"; // DEBUG 1

require_once 'db_connect.php';

echo "DEBUG: db_connect.php included.\n"; // DEBUG 2

// --- Tambahkan pengecekan koneksi database di sini ---
if ($conn->connect_error) {
    die("DEBUG: Koneksi database gagal total: " . $conn->connect_error); // DEBUG 3
}

echo "DEBUG: Database connected.\n"; // DEBUG 4

session_start();

echo "DEBUG: Session started.\n"; // DEBUG 5

// Cek apakah user sudah login dan role-nya adalah 'admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Jika tidak, arahkan kembali ke halaman login
    echo "DEBUG: Not logged in as admin or invalid role. Redirecting.\n"; // DEBUG 6
    header("Location: login.php");
    exit();
}

echo "DEBUG: Admin logged in. User ID: " . $_SESSION['user_id'] . ".\n"; // DEBUG 7

// Data admin yang sedang login
$admin_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0; // Ambil user_id, pastikan integer, default 0 jika tidak ada

// Pastikan event_id dan action ada di parameter URL
if (isset($_GET['event_id']) && isset($_GET['action'])) {
    echo "DEBUG: event_id and action parameters found.\n"; // DEBUG 8
    $event_id = intval($_GET['event_id']); // Ambil event_id dan pastikan integer
    $action = $_GET['action'];

    echo "DEBUG: event_id=" . $event_id . ", action=" . $action . ".\n"; // DEBUG 9

    $new_status = '';
    $verification_status = '';
    $admin_note = ''; // Catatan admin, bisa dikembangkan nanti

    // Validasi event_id: Pastikan event_id yang diterima valid dan ada di database
    echo "DEBUG: Checking event validity.\n"; // DEBUG 10
    $sql_check_event = "SELECT event_id FROM event_pengajuan WHERE event_id = ? LIMIT 1";
    $stmt_check_event = $conn->prepare($sql_check_event);
    if ($stmt_check_event) {
        echo "DEBUG: Statement prepared successfully.\n"; // DEBUG 11
        $stmt_check_event->bind_param("i", $event_id);
        echo "DEBUG: Parameter bound.\n"; // DEBUG 12
        if (!$stmt_check_event->execute()) {
            echo "DEBUG: Execute failed: " . $stmt_check_event->error . "\n"; // DEBUG 13
            $_SESSION['error_message'] = "Error saat memeriksa event: " . $stmt_check_event->error;
            $stmt_check_event->close();
            header("Location: admin_view_requests.php");
            exit();
        }
        echo "DEBUG: Query executed.\n"; // DEBUG 14
        $stmt_check_event->store_result();
        echo "DEBUG: Result stored. Number of rows: " . $stmt_check_event->num_rows . "\n"; // DEBUG 15
        if ($stmt_check_event->num_rows === 0) {
            // Event tidak ditemukan, arahkan kembali dengan pesan error
            $_SESSION['error_message'] = "Pengajuan event dengan ID tersebut tidak ditemukan.";
            $stmt_check_event->close(); // Tutup statement sebelum exit
            header("Location: admin_view_requests.php");
            exit();
        }
        $stmt_check_event->close(); // Tutup statement
        echo "DEBUG: Event validation completed successfully.\n"; // DEBUG 16
    } else {
        // Gagal menyiapkan statement check event
        echo "DEBUG: Failed to prepare statement: " . $conn->error . "\n"; // DEBUG 17
        $_SESSION['error_message'] = "Error saat menyiapkan pengecekan event: " . $conn->error;
        header("Location: admin_view_requests.php");
        exit();
    }

    if ($action === 'approve') {
        $new_status = 'disetujui';
        $verification_status = 'disetujui';
        $admin_note = 'Pengajuan disetujui.';
        echo "DEBUG: Action is approve, status set to: " . $new_status . "\n"; // DEBUG 42
    } elseif ($action === 'reject') {
        $new_status = 'ditolak';
        $verification_status = 'ditolak';
        $admin_note = 'Pengajuan ditolak.';
        echo "DEBUG: Action is reject, status set to: " . $new_status . "\n"; // DEBUG 43
    } elseif ($action === 'delete') {
        // Logika delete
        $conn->begin_transaction();
        $success = true;
        $error_message = '';

        try {
            // Hapus entri terkait di tabel verifikasi_event
            $sql_delete_verifikasi = "DELETE FROM verifikasi_event WHERE event_id = ?";
            $stmt_delete_verifikasi = $conn->prepare($sql_delete_verifikasi);
            if ($stmt_delete_verifikasi) {
                $stmt_delete_verifikasi->bind_param("i", $event_id);
                if (!$stmt_delete_verifikasi->execute()) {
                    $success = false;
                    $error_message = "Error menghapus data verifikasi terkait: " . $stmt_delete_verifikasi->error;
                }
                $stmt_delete_verifikasi->close();
            } else {
                $success = false;
                $error_message = "Error menyiapkan statement hapus verifikasi: " . $conn->error;
            }

            // Hapus pengajuan event
            if ($success) {
                $sql_delete_event = "DELETE FROM event_pengajuan WHERE event_id = ?";
                $stmt_delete_event = $conn->prepare($sql_delete_event);
                if ($stmt_delete_event) {
                    $stmt_delete_event->bind_param("i", $event_id);
                    if (!$stmt_delete_event->execute()) {
                        $success = false;
                        $error_message = "Error menghapus pengajuan event: " . $stmt_delete_event->error;
                    }
                    $stmt_delete_event->close();
                } else {
                     $success = false;
                     $error_message = "Error menyiapkan statement hapus event: " . $conn->error;
                }
            }

            if ($success) {
                $conn->commit();
                $_SESSION['success_message'] = "Pengajuan event berhasil dihapus."; // Pesan sukses delete
            } else {
                $conn->rollback();
                $_SESSION['error_message'] = "Gagal menghapus pengajuan event: " . $error_message; // Gabungkan pesan error
            }

        } catch (Exception $e) {
            $conn->rollback();
            error_log("Delete Transaction failed: " . $e->getMessage());
            $_SESSION['error_message'] = "Terjadi kesalahan transaksi hapus: " . $e->getMessage();
        }
        // Redirect setelah operasi delete
        header("Location: admin_view_requests.php");
        exit();
    }

    echo "DEBUG: Current action value: '" . $action . "'\n"; // DEBUG 44
    echo "DEBUG: Current new_status value: '" . $new_status . "'\n"; // DEBUG 45
    echo "DEBUG: Current verification_status value: '" . $verification_status . "'\n"; // DEBUG 47

    // Hapus kondisi elseif dan ganti dengan if terpisah
    if ($action === 'approve' || $action === 'reject') {
        echo "DEBUG: Starting approve/reject process.\n"; // DEBUG 18
        // Mulai transaksi database
        $conn->begin_transaction();
        echo "DEBUG: Transaction started.\n"; // DEBUG 19
        $success = true;
        $error_message = ''; // Reset error_message

        try {
            echo "DEBUG: Updating event status to: " . $new_status . "\n"; // DEBUG 20
            // Update status di tabel event_pengajuan
            $sql_update_event = "UPDATE event_pengajuan SET status = ? WHERE event_id = ?";
            $stmt_update_event = $conn->prepare($sql_update_event);
            if ($stmt_update_event) {
                echo "DEBUG: Update statement prepared.\n"; // DEBUG 21
                $stmt_update_event->bind_param("si", $new_status, $event_id);
                echo "DEBUG: Update parameters bound.\n"; // DEBUG 22
                if (!$stmt_update_event->execute()) {
                    $success = false;
                    $error_message = "Error updating event status: " . $stmt_update_event->error;
                    echo "DEBUG: Update failed: " . $error_message . "\n"; // DEBUG 23
                } else {
                    echo "DEBUG: Event status updated successfully.\n"; // DEBUG 24
                }
                $stmt_update_event->close();
            } else {
                $success = false;
                $error_message = "Error preparing update statement: " . $conn->error;
                echo "DEBUG: Failed to prepare update statement: " . $error_message . "\n"; // DEBUG 25
            }

            // Jika update event berhasil, masukkan data ke tabel verifikasi_event
            if ($success && !empty($admin_id) && $admin_id > 0) {
                echo "DEBUG: Starting verification insert process.\n"; // DEBUG 26
                $tanggal_verifikasi = date('Y-m-d'); // Tanggal hari ini

                // Cek apakah sudah ada entri verifikasi untuk event ini
                $sql_check_existing_verif = "SELECT verifikasi_id FROM verifikasi_event WHERE event_id = ? LIMIT 1";
                $stmt_check_existing_verif = $conn->prepare($sql_check_existing_verif);
                if($stmt_check_existing_verif) {
                    echo "DEBUG: Check existing verification statement prepared.\n"; // DEBUG 27
                    $stmt_check_existing_verif->bind_param("i", $event_id);
                    $stmt_check_existing_verif->execute();
                    $stmt_check_existing_verif->store_result();
                    echo "DEBUG: Existing verification check completed. Rows found: " . $stmt_check_existing_verif->num_rows . "\n"; // DEBUG 28

                    if ($stmt_check_existing_verif->num_rows == 0) {
                        echo "DEBUG: No existing verification found, proceeding with insert.\n"; // DEBUG 29
                        // Belum ada verifikasi, lakukan INSERT
                        $sql_insert_verifikasi = "INSERT INTO verifikasi_event (event_id, admin_id, tanggal_verifikasi, catatan_admin, Status) VALUES (?, ?, ?, ?, ?)";
                        $stmt_insert_verifikasi = $conn->prepare($sql_insert_verifikasi);
                        if ($stmt_insert_verifikasi) {
                            echo "DEBUG: Insert verification statement prepared.\n"; // DEBUG 30
                            $bind_types = "iisss";
                            $stmt_insert_verifikasi->bind_param($bind_types, $event_id, $admin_id, $tanggal_verifikasi, $admin_note, $verification_status);
                            echo "DEBUG: Insert parameters bound.\n"; // DEBUG 31

                            if (!$stmt_insert_verifikasi->execute()) {
                                $success = false;
                                $error_message = "Error inserting verification data: " . $stmt_insert_verifikasi->error;
                                echo "DEBUG: Insert failed: " . $error_message . "\n"; // DEBUG 32
                            } else {
                                echo "DEBUG: Verification data inserted successfully.\n"; // DEBUG 33
                            }
                            $stmt_insert_verifikasi->close();
                        } else {
                            $success = false;
                            $error_message = "Error preparing verification insert statement: " . $conn->error;
                            echo "DEBUG: Failed to prepare insert statement: " . $error_message . "\n"; // DEBUG 34
                        }
                    } else {
                        echo "DEBUG: Existing verification found, skipping insert.\n"; // DEBUG 35
                    }
                    $stmt_check_existing_verif->close();
                } else {
                    $success = false;
                    $error_message = "Error preparing check existing verification statement: " . $conn->error;
                    echo "DEBUG: Failed to prepare check statement: " . $error_message . "\n"; // DEBUG 36
                }
            } elseif ($success && (empty($admin_id) || $admin_id <= 0)) {
                $success = false;
                $error_message = "Invalid Admin ID in session. Cannot record verification.";
                echo "DEBUG: Invalid admin ID: " . $admin_id . "\n"; // DEBUG 37
            }

            // Commit atau Rollback transaksi
            if ($success) {
                echo "DEBUG: Committing transaction.\n"; // DEBUG 38
                $conn->commit();
                $_SESSION['success_message'] = "Pengajuan event berhasil " . ($action === 'approve' ? 'disetujui' : 'ditolak') . ".";
            } else {
                echo "DEBUG: Rolling back transaction due to error: " . $error_message . "\n"; // DEBUG 39
                $conn->rollback();
                $_SESSION['error_message'] = "Gagal memproses pengajuan: " . $error_message;
            }

        } catch (Exception $e) {
            echo "DEBUG: Exception caught: " . $e->getMessage() . "\n"; // DEBUG 40
            $conn->rollback();
            error_log("Approve/Reject Transaction failed: " . $e->getMessage());
            $_SESSION['error_message'] = "Terjadi kesalahan transaksi: " . $e->getMessage();
        }
        // Redirect setelah operasi approve/reject
        echo "DEBUG: Redirecting to admin_view_requests.php\n"; // DEBUG 41
        header("Location: admin_view_requests.php");
        exit();
    } else {
        echo "DEBUG: Invalid action: " . $action . "\n"; // DEBUG 46
        $_SESSION['error_message'] = "Aksi tidak valid: " . $action;
        header("Location: admin_view_requests.php");
        exit();
    }
} else {
    // Parameter tidak lengkap
    $_SESSION['error_message'] = "Parameter tidak lengkap.";
    header("Location: admin_view_requests.php");
    exit();
}

// Tutup koneksi di akhir skrip (ini hanya akan tercapai jika tidak ada exit() sebelumnya, yang seharusnya tidak terjadi sekarang)
$conn->close();

?> 