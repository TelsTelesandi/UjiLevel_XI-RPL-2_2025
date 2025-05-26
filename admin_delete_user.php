<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Mulai session
session_start();

// Cek apakah user sudah login dan memiliki peran Admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_SESSION["role"]) || $_SESSION["role"] !== "Admin"){
    // Jika tidak, redirect ke halaman login
    header("location: index.php");
    exit;
}

// Include file config
require_once "config.php";

// Process delete operation after confirmation
if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    // Get the user ID
    $param_id = trim($_GET["id"]);

    // Start a transaction
    mysqli_begin_transaction($link);

    try {
        // 1. Get event IDs associated with the user from event_pengajuan
        // We need these event_ids to delete related entries in verifikasi_event
        $sql_select_event_ids = "SELECT event_id FROM event_pengajuan WHERE user_id = ?";
        if($stmt_select_event_ids = mysqli_prepare($link, $sql_select_event_ids)){
            mysqli_stmt_bind_param($stmt_select_event_ids, "i", $param_id);
            if(mysqli_stmt_execute($stmt_select_event_ids)){
                $result_event_ids = mysqli_stmt_get_result($stmt_select_event_ids);
                $event_ids = [];
                while ($row = mysqli_fetch_assoc($result_event_ids)) {
                    $event_ids[] = $row['event_id'];
                }
                mysqli_free_result($result_event_ids);
            } else {
                 throw new Exception("Oops! Ada yang salah saat mengambil ID event terkait: " . mysqli_error($link));
            }
            mysqli_stmt_close($stmt_select_event_ids);
        } else {
             throw new Exception("Oops! Ada yang salah saat menyiapkan statement ambil ID event: " . mysqli_error($link));
        }

        // 2. Delete related entries in verifikasi_event using the collected event_ids
        if (!empty($event_ids)) {
            // Create a string of placeholders for the IN clause
            $placeholders = implode(',', array_fill(0, count($event_ids), '?'));
            // Need to ensure we only delete verifikasi_event entries whose event_id is in the list AND associated with this user's events
            // Although the foreign key should handle the event_id check, explicitly linking to user_id via event_pengajuan in a subquery is safer if schema complexity increases.
            // However, a simpler approach given the foreign key constraint is to just delete from verifikasi_event where event_id is in the list.
            $sql_delete_verifikasi = "DELETE FROM verifikasi_event WHERE event_id IN ($placeholders)";

            if($stmt_delete_verifikasi = mysqli_prepare($link, $sql_delete_verifikasi)){
                 // Bind parameters (all are integers 'i')
                // Use call_user_func_array for binding a dynamic number of parameters
                call_user_func_array('mysqli_stmt_bind_param', array_merge([$stmt_delete_verifikasi], array_merge([str_repeat('i', count($event_ids))], $event_ids)));

                if(!mysqli_stmt_execute($stmt_delete_verifikasi)){
                     throw new Exception("Oops! Ada yang salah saat menghapus verifikasi event terkait: " . mysqli_error($link));
                }
                mysqli_stmt_close($stmt_delete_verifikasi);
            } else {
                 throw new Exception("Oops! Ada yang salah saat menyiapkan statement hapus verifikasi event: " . mysqli_error($link));
            }
        }

        // 3. Delete related entries in event_pengajuan for the user
        $sql_delete_events = "DELETE FROM event_pengajuan WHERE user_id = ?";

        if($stmt_delete_events = mysqli_prepare($link, $sql_delete_events)){
            mysqli_stmt_bind_param($stmt_delete_events, "i", $param_id);

            if(!mysqli_stmt_execute($stmt_delete_events)){
                throw new Exception("Oops! Ada yang salah saat menghapus pengajuan event terkait: " . mysqli_error($link));
            }
            mysqli_stmt_close($stmt_delete_events);

        } else{
            throw new Exception("Oops! Ada yang salah saat menyiapkan statement hapus pengajuan event: " . mysqli_error($link));
        }

        // 4. Delete the user from users
        $sql_delete_user = "DELETE FROM users WHERE user_id = ?";

        if($stmt_delete_user = mysqli_prepare($link, $sql_delete_user)){
            mysqli_stmt_bind_param($stmt_delete_user, "i", $param_id);

            if(mysqli_stmt_execute($stmt_delete_user)){
                // If all deletions are successful, commit the transaction
                mysqli_commit($link);
                // User and related records deleted successfully. Redirect.
                header("location: admin_manage_users.php");
                exit();
            } else{
                throw new Exception("Oops! Ada yang salah saat menghapus pengguna: " . mysqli_error($link));
            }
            mysqli_stmt_close($stmt_delete_user);

        } else{
            throw new Exception("Oops! Ada yang salah saat menyiapkan statement hapus pengguna: " . mysqli_error($link));
        }

    } catch (Exception $e) {
        // An error occurred, rollback the transaction
        mysqli_rollback($link);
        // Output the error message
        echo "Error: " . $e->getMessage();
    } finally {
        // Close the database connection
        mysqli_close($link);
    }

} else{
    // If id parameter was not passed, redirect to manage users page
    header("location: admin_manage_users.php");
    exit();
}
?> 