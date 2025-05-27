<?php
session_start();

// Include database connection file
require_once '../db_connect.php'; // Adjust path as necessary

// Check if the admin is logged in and has the correct role
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header("location: login.php"); // Redirect to admin login page
    exit;
}

// Check if event_id is provided via POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['event_id'])) {
    $event_id = trim($_POST['event_id']);

    // Fetch file paths before deleting the database entry
    $sql_get_files = "SELECT proposal, dokumen_pendukung FROM event_pengajuan WHERE event_id = ?";
    $proposal_file = '';
    $dokumen_file = '';
    if ($stmt_get_files = mysqli_prepare($link, $sql_get_files)) {
        mysqli_stmt_bind_param($stmt_get_files, "i", $event_id);
        if (mysqli_stmt_execute($stmt_get_files)) {
            mysqli_stmt_bind_result($stmt_get_files, $proposal_file, $dokumen_file);
            mysqli_stmt_fetch($stmt_get_files);
        }
        mysqli_stmt_close($stmt_get_files);
    }

    // Start transaction for data integrity
    mysqli_begin_transaction($link);

    $delete_success = true;

    // Delete related entries in verifikasi_event first (due to foreign key constraint)
    $sql_delete_verification = "DELETE FROM verifikasi_event WHERE event_id = ?";
    if ($stmt_delete_verification = mysqli_prepare($link, $sql_delete_verification)) {
        mysqli_stmt_bind_param($stmt_delete_verification, "i", $event_id);
        if (!mysqli_stmt_execute($stmt_delete_verification)) {
            $delete_success = false;
            $error = "Error deleting verification entry: " . mysqli_error($link);
        }
        mysqli_stmt_close($stmt_delete_verification);
    } else {
        $delete_success = false;
        $error = "Error preparing verification delete statement.";
    }

    // Delete the entry from event_pengajuan if verification deletion was successful or not needed
    if ($delete_success) {
        $sql_delete_event = "DELETE FROM event_pengajuan WHERE event_id = ?";
        if ($stmt_delete_event = mysqli_prepare($link, $sql_delete_event)) {
            mysqli_stmt_bind_param($stmt_delete_event, "i", $event_id);
            if (mysqli_stmt_execute($stmt_delete_event)) {
                // Delete uploaded files from server
                $upload_base_dir = '../'; // Relative path from admin/ to root
                if (!empty($proposal_file) && file_exists($upload_base_dir . $proposal_file)) {
                    if (!unlink($upload_base_dir . $proposal_file)){
                         // Log file deletion error, but don't fail the main operation
                         error_log("Error deleting proposal file: " . $upload_base_dir . $proposal_file);
                    }
                }
                if (!empty($dokumen_file) && file_exists($upload_base_dir . $dokumen_file)) {
                     if (!unlink($upload_base_dir . $dokumen_file)){
                         // Log file deletion error, but don't fail the main operation
                          error_log("Error deleting dokumen pendukung file: " . $upload_base_dir . $dokumen_file);
                    }
                }
                
                // Commit transaction
                mysqli_commit($link);

                // Redirect back to approval list after successful deletion
                header("location: approval.php?msg=deleted");
                exit;
            } else {
                $delete_success = false;
                $error = "Error deleting pengajuan: " . mysqli_error($link);
                 // Rollback transaction on error
                mysqli_rollback($link);
            }
            mysqli_stmt_close($stmt_delete_event);
        } else {
            $delete_success = false;
            $error = "Error preparing pengajuan delete statement.";
             // Rollback transaction on error
            mysqli_rollback($link);
        }
    } else {
         // Rollback transaction if verification deletion failed
         mysqli_rollback($link);
    }


} else {
    // If event_id not provided or not POST method, redirect back with error
    $error = "Invalid request.";
}

// Close database connection
mysqli_close($link);

// Redirect back to approval list with error message if any
if (isset($error)) {
     header("location: approval.php?msg=error&err=" . urlencode($error));
     exit;
}

?> 