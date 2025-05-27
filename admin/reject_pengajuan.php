<?php
session_start();

// Include database connection file
require_once '../db_connect.php'; // Note the '../' to go up one directory

// Check if the admin is logged in and has the correct role
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header("location: login.php"); // Redirect to admin login page
    exit;
}

// Get the logged-in admin's ID
$admin_id = $_SESSION['user_id'];

// Check if event ID is provided
if (isset($_GET['id']) && !empty(trim($_GET['id']))) {
    $event_id = trim($_GET['id']);

    // Check if this event already exists in verifikasi_event
    $sql_check = "SELECT verifikasi_id FROM verifikasi_event WHERE event_id = ?";
    if ($stmt_check = mysqli_prepare($link, $sql_check)) {
        mysqli_stmt_bind_param($stmt_check, "i", $event_id);
        if (mysqli_stmt_execute($stmt_check)) {
            mysqli_stmt_store_result($stmt_check);

            if (mysqli_stmt_num_rows($stmt_check) == 1) {
                // Event already exists, update the status to Rejected
                $sql_update = "UPDATE verifikasi_event SET status = 'Rejected', admin_id = ?, tanggal_verifikasi = NOW() WHERE event_id = ?";
                if ($stmt_update = mysqli_prepare($link, $sql_update)) {
                    mysqli_stmt_bind_param($stmt_update, "ii", $admin_id, $event_id);
                    mysqli_stmt_execute($stmt_update);
                    mysqli_stmt_close($stmt_update);
                }
            } else {
                // Event does not exist, insert a new record with status Rejected
                $sql_insert = "INSERT INTO verifikasi_event (event_id, admin_id, tanggal_verifikasi, status) VALUES (?, ?, NOW(), 'Rejected')";
                if ($stmt_insert = mysqli_prepare($link, $sql_insert)) {
                    mysqli_stmt_bind_param($stmt_insert, "ii", $event_id, $admin_id);
                    mysqli_stmt_execute($stmt_insert);
                    mysqli_stmt_close($stmt_insert);
                }
            }
            mysqli_stmt_close($stmt_check);
        }
    }

    // Redirect back to the approval page
    header("location: approval.php");
    exit();

} else {
    // If event ID is not provided, redirect back to approval page
    header("location: approval.php");
    exit();
}

// Close database connection

?>