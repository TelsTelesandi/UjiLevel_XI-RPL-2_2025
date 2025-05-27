<?php
session_start();

// Include database connection file
require_once '../db_connect.php'; // Note the '../' to go up one directory

// Check if the admin is logged in and has the correct role
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header("location: login.php"); // Redirect to admin login page
    exit;
}

$pengajuan_details = null;
$verification_status = null;
$event_id = null;
$error = '';
$success = '';

// Check if event_id is provided in the URL
if (isset($_GET['id']) && !empty(trim($_GET['id']))) {
    $event_id = trim($_GET['id']);

    // Fetch pengajuan details
    $sql_details = "SELECT judul_event, jenis_kegiatan, total_pembiayaan, proposal, dokumen_pendukung FROM event_pengajuan WHERE event_id = ?";

    if ($stmt_details = mysqli_prepare($link, $sql_details)) {
        mysqli_stmt_bind_param($stmt_details, "i", $param_event_id);
        $param_event_id = $event_id;

        if (mysqli_stmt_execute($stmt_details)) {
            $result_details = mysqli_stmt_get_result($stmt_details);
            $pengajuan_details = mysqli_fetch_assoc($result_details);
            mysqli_free_result($result_details);
        } else {
            $error = "Error fetching pengajuan details.";
        }
        mysqli_stmt_close($stmt_details);
    } else {
         $error = "Error preparing pengajuan details statement.";
    }

     // Fetch verification status if exists
    $sql_status = "SELECT status FROM verifikasi_event WHERE event_id = ?";
    if ($stmt_status = mysqli_prepare($link, $sql_status)) {
        mysqli_stmt_bind_param($stmt_status, "i", $param_event_id);
        $param_event_id = $event_id;
        if (mysqli_stmt_execute($stmt_status)) {
            $result_status = mysqli_stmt_get_result($stmt_status);
            if ($row_status = mysqli_fetch_assoc($result_status)) {
                $verification_status = $row_status['status'];
            }
            mysqli_free_result($result_status);
        } else {
             $error = "Error fetching verification status.";
        }
        mysqli_stmt_close($stmt_status);
    } else {
        $error = "Error preparing verification status statement.";
    }

} else {
    // If no event_id is provided, redirect back to approval list
    header("location: approval.php");
    exit;
}

// --- Handle Approval/Rejection ---
// Re-open connection if it was closed after the GET request
// This is a simple approach; in a larger app, use persistent connection or better management
if (!isset($link) || !is_object($link)) {
     require_once '../db_connect.php';
}

// Add handling for delete action
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['event_id'], $_POST['action']) && $_POST['action'] === 'Delete') {
    $post_event_id = $_POST['event_id'];

    // Fetch file paths before deleting the database entry
    $sql_get_files = "SELECT proposal, dokumen_pendukung FROM event_pengajuan WHERE event_id = ?";
    $proposal_file = '';
    $dokumen_file = '';
    if ($stmt_get_files = mysqli_prepare($link, $sql_get_files)) {
        mysqli_stmt_bind_param($stmt_get_files, "i", $post_event_id);
        if (mysqli_stmt_execute($stmt_get_files)) {
            mysqli_stmt_bind_result($stmt_get_files, $proposal_file, $dokumen_file);
            mysqli_stmt_fetch($stmt_get_files);
        }
        mysqli_stmt_close($stmt_get_files);
    }

    // Delete related entries in verifikasi_event first (due to foreign key constraint)
    $sql_delete_verification = "DELETE FROM verifikasi_event WHERE event_id = ?";
    if ($stmt_delete_verification = mysqli_prepare($link, $sql_delete_verification)) {
        mysqli_stmt_bind_param($stmt_delete_verification, "i", $post_event_id);
        mysqli_stmt_execute($stmt_delete_verification);
        mysqli_stmt_close($stmt_delete_verification);
    }

    // Delete the entry from event_pengajuan
    $sql_delete_event = "DELETE FROM event_pengajuan WHERE event_id = ?";
    if ($stmt_delete_event = mysqli_prepare($link, $sql_delete_event)) {
        mysqli_stmt_bind_param($stmt_delete_event, "i", $post_event_id);
        if (mysqli_stmt_execute($stmt_delete_event)) {
            // Delete uploaded files from server
            if (!empty($proposal_file) && file_exists('../' . $proposal_file)) {
                unlink('../' . $proposal_file);
            }
            if (!empty($dokumen_file) && file_exists('../' . $dokumen_file)) {
                unlink('../' . $dokumen_file);
            }

            // Redirect back to approval list after successful deletion
            header("location: approval.php?msg=deleted");
            exit;
        } else {
            $error = "Error deleting pengajuan: " . mysqli_error($link);
        }
        mysqli_stmt_close($stmt_delete_event);
    } else {
        $error = "Error preparing delete statement.";
    }
}

// --- Handle Approval/Rejection (existing code) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['event_id'], $_POST['action']) && ($_POST['action'] === 'Approve' || $_POST['action'] === 'Reject')) {
    $post_event_id = $_POST['event_id'];
    $action = $_POST['action']; // 'Approve' or 'Reject'
    $admin_id = $_SESSION['user_id']; // Assuming admin_id is stored in session
    $current_date = date('Y-m-d H:i:s'); // Current timestamp
    $new_status = ($action === 'Approve') ? 'Approved' : 'Rejected';
    $admin_note = trim($_POST['admin_note'] ?? ''); // Get admin note, default to empty string

    // Check if a verification entry already exists for this event_id
    $sql_check_verification = "SELECT COUNT(*) FROM verifikasi_event WHERE event_id = ?";
    if ($stmt_check = mysqli_prepare($link, $sql_check_verification)) {
        mysqli_stmt_bind_param($stmt_check, "i", $post_event_id);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_bind_result($stmt_check, $count);
        mysqli_stmt_fetch($stmt_check);
        mysqli_stmt_close($stmt_check);

        if ($count > 0) {
            // Entry exists, update it
            $sql_update = "UPDATE verifikasi_event SET status = ?, admin_id = ?, tanggal_verifikasi = ?, catatan = ? WHERE event_id = ?";
            if ($stmt_update = mysqli_prepare($link, $sql_update)) {
                // Corrected: s (status), i (admin_id), s (tanggal_verifikasi), s (catatan), i (event_id in WHERE)
                mysqli_stmt_bind_param($stmt_update, "sissi", $new_status, $admin_id, $current_date, $admin_note, $post_event_id); // <-- Added $admin_note and corresponding type 's'
                if (mysqli_stmt_execute($stmt_update)) {
                    $success = "Pengajuan status updated to " . $new_status;
                     // Refresh status after update
                    $verification_status = $new_status;
                } else {
                    $error = "Error updating pengajuan status: " . mysqli_error($link);
                }
                mysqli_stmt_close($stmt_update);
            } else {
                 $error = "Error preparing update statement.";
            }
        } else {
            // No entry exists, insert a new one
            $sql_insert = "INSERT INTO verifikasi_event (event_id, admin_id, tanggal_verifikasi, status, catatan) VALUES (?, ?, ?, ?, ?)";
             if ($stmt_insert = mysqli_prepare($link, $sql_insert)) {
                // Corrected: i (event_id), i (admin_id), s (tanggal_verifikasi), s (status), s (catatan)
                mysqli_stmt_bind_param($stmt_insert, "iisss", $post_event_id, $admin_id, $current_date, $new_status, $admin_note); // <-- Added $admin_note and corresponding type 's' (Line approx 98 originally)
                 if (mysqli_stmt_execute($stmt_insert)) {
                    $success = "Pengajuan status set to " . $new_status;
                    // Refresh status after insert
                    $verification_status = $new_status;
                 } else {
                    $error = "Error inserting verification entry: " . mysqli_error($link);
                }
                 mysqli_stmt_close($stmt_insert);
             } else {
                $error = "Error preparing insert statement.";
            }
        }
    } else {
        $error = "Error checking verification entry.";
    }

     // After handling the action, redirect back or show updated status
    // For now, we'll show the updated status on the same page and refetch details to show updated status
    // Re-fetch verification status after update/insert
     // Need to re-establish connection if it was closed
    if (!isset($link) || !is_object($link)) {
         require_once '../db_connect.php';
    }

     $sql_status_recheck = "SELECT status FROM verifikasi_event WHERE event_id = ?";
    if ($stmt_status_recheck = mysqli_prepare($link, $sql_status_recheck)) {
        mysqli_stmt_bind_param($stmt_status_recheck, "i", $post_event_id);
        if (mysqli_stmt_execute($stmt_status_recheck)) {
            $result_status_recheck = mysqli_stmt_get_result($stmt_status_recheck);
            if ($row_status_recheck = mysqli_fetch_assoc($result_status_recheck)) {
                $verification_status = $row_status_recheck['status'];
            }
            mysqli_free_result($result_status_recheck);
        }
        mysqli_stmt_close($stmt_status_recheck);
    }
}

// Close database connection if it's still open
if (isset($link) && is_object($link)) {
     mysqli_close($link);
}

// Admin View Pengajuan content starts here
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Review Pengajuan</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            background-color: #f8f9fa; /* Lighter background */
            display: flex;
        }
        .sidebar {
            width: 250px;
            background-color: #343a40;
            color: white;
            padding-top: 20px;
            height: 100vh;
            position: fixed;
             display: flex;
             flex-direction: column;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
             font-size: 1.8em;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
             flex-grow: 1;
        }
        .sidebar ul li {
            padding: 12px 20px; /* Increased padding */
            border-bottom: 1px solid #495057; /* Slightly lighter border */
        }
        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: block;
             font-size: 1.1em;
        }
        .sidebar ul li a:hover {
            background-color: #495057;
        }
         .sidebar ul li.active a {
            background-color: #007bff;
            color: white;
        }
         .logout-link {
            display: block;
            padding: 15px 20px;
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            text-align: center;
            margin-top: auto; /* Push to bottom */
             font-size: 1.1em;
        }
         .logout-link:hover {
            background-color: #c82333;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
            flex-grow: 1;
        }
        .main-content h1 {
            margin-top: 0;
            margin-bottom: 20px;
             color: #333;
        }
        .pengajuan-details {
            background-color: #fff;
            padding: 30px; /* Increased padding */
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .pengajuan-details h3 {
            margin-top: 0;
            margin-bottom: 25px; /* More space below title */
            color: #007bff; /* Highlight title color */
             border-bottom: 1px solid #eee;
             padding-bottom: 15px;
        }
        .detail-item {
            margin-bottom: 15px; /* Space between detail items */
            display: flex; /* Use flexbox for alignment */
            align-items: center;
        }
        .detail-item strong {
            display: inline-block;
            width: 180px; /* Increased width for labels */
            margin-right: 15px;
            color: #555; /* Darker label color */
        }
         .file-link {
            display: inline-block;
            background-color: #007bff; /* Blue background for file links */
            color: white; /* White text */
            padding: 8px 15px; /* Padding */
            border-radius: 4px; /* Rounded corners */
            text-decoration: none; /* Remove underline */
            margin-top: 5px; /* Space above if multiple links */
             font-size: 0.95em;
        }
        .file-link:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }
         .status-tag {
            padding: 5px 10px; /* More padding */
            border-radius: 4px; /* Slightly more rounded */
            font-size: 0.9em;
            color: white;
            display: inline-block; /* Make it a block for padding */
            text-align: center;
        }
        .status-Belum-Diverifikasi {
             background-color: #6c757d; /* Gray for not verified */
        }
        .status-Pending {
            background-color: #ffc107; /* Yellow */
        }
        .status-Approved {
            background-color: #28a745; /* Green */
        }
        .status-Rejected {
            background-color: #dc3545; /* Red */
        }
        .action-buttons {
             margin-top: 25px; /* Space above buttons */
             padding-top: 20px;
             border-top: 1px solid #eee;
        }
        .action-buttons button {
            padding: 10px 20px; /* Increased padding */
            margin-right: 15px; /* More space between buttons */
            border: none;
            border-radius: 4px;
            cursor: pointer;
            color: white;
             font-size: 1em;
        }
        .approve-button {
            background-color: #28a745;
        }
        .approve-button:hover {
            background-color: #218838;
        }
        .reject-button {
            background-color: #dc3545;
        }
        .reject-button:hover {
            background-color: #c82333;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
         .success {
            color: green;
            margin-bottom: 15px;
        }
         .back-link {
            display: inline-block;
            margin-top: 20px; /* Space above link */
            color: #007bff;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }

    </style>
</head>
<body>
     <div class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="#">Manajemen User</a></li>
            <li><a href="approval.php">Approval Kegiatan</a></li>
            <li><a href="#">Laporan</a></li>
        </ul>
         <a href="logout.php" class="logout-link">Logout</a>
    </div>
    <div class="main-content">
        <h1>Review Pengajuan Kegiatan</h1>

        <?php if (!empty($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
         <?php if (!empty($success)): ?>
            <p class="success"><?php echo $success; ?></p>
        <?php endif; ?>

        <?php if ($pengajuan_details): ?>
            <div class="pengajuan-details">
                <h3>Detail Pengajuan (ID: <?php echo htmlspecialchars($event_id); ?>)</h3>
                
                <div class="detail-item">
                    <strong>Judul Event:</strong> 
                    <span><?php echo htmlspecialchars($pengajuan_details['judul_event']); ?></span>
                </div>
                <div class="detail-item">
                    <strong>Jenis Kegiatan:</strong> 
                    <span><?php echo htmlspecialchars($pengajuan_details['jenis_kegiatan']); ?></span>
                </div>
                <div class="detail-item">
                    <strong>Total Pembiayaan:</strong> 
                    <span>Rp <?php echo number_format($pengajuan_details['total_pembiayaan'] ?? 0, 0, ',', '.'); ?></span>
                </div>
                <div class="detail-item">
                    <strong>Status Verifikasi:</strong> 
                    <span>
                        <?php
                            $display_status = $verification_status ?? 'Belum Diverifikasi';
                             $status_class = 'status-' . str_replace(' ', '-', $display_status);
                        ?>
                        <span class="status-tag <?php echo $status_class; ?>"><?php echo $display_status; ?></span>
                    </span>
                </div>
                
                <div class="detail-item">
                    <strong>Proposal:</strong> 
                    <span>
                        <?php if (!empty($pengajuan_details['proposal'])): ?>
                            <a href="../<?php echo htmlspecialchars($pengajuan_details['proposal']); ?>" download class="file-link"><?php echo basename($pengajuan_details['proposal']); ?></a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </span>
                </div>
                <div class="detail-item">
                    <strong>Dokumen Pendukung:</strong> 
                    <span>
                        <?php if (!empty($pengajuan_details['dokumen_pendukung'])): ?>
                            <a href="../<?php echo htmlspecialchars($pengajuan_details['dokumen_pendukung']); ?>" download class="file-link"><?php echo basename($pengajuan_details['dokumen_pendukung']); ?></a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </span>
                </div>

                <!-- Add textarea for admin note -->
                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="admin_note" style="display: block; margin-bottom: 5px; color: #555;">Catatan Admin:</label>
                    <textarea id="admin_note" name="admin_note" rows="4" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 1em;"></textarea>
                </div>

                <div class="action-buttons">
                    <?php if ($verification_status === 'Pending' || $verification_status === null || $verification_status === 'Belum Diverifikasi'): // Show buttons if pending or not yet verified ?>
                        <form action="" method="post" style="display: inline-block;">
                            <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
                             <!-- Add textarea for admin note -->
                            <div class="form-group" style="margin-bottom: 10px;">
                                <label for="admin_note" style="display: block; margin-bottom: 5px; color: #555; font-weight: normal; font-size: 0.9em;">Catatan (Opsional):</label>
                                <textarea id="admin_note" name="admin_note" rows="3" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 1em; resize: vertical;"></textarea>
                            </div>
                            <button type="submit" name="action" value="Approve" class="approve-button">Approve</button>
                        </form>
                        <form action="" method="post" style="display: inline-block; margin-left: 10px;">
                            <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
                             <!-- Add textarea for admin note for reject action as well if needed -->
                             <!-- 
                             <div class="form-group" style="margin-bottom: 10px;">
                                 <label for="admin_note_reject" style="display: block; margin-bottom: 5px; color: #555; font-weight: normal; font-size: 0.9em;">Catatan (Opsional):</label>
                                 <textarea id="admin_note_reject" name="admin_note" rows="3" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 1em; resize: vertical;"></textarea>
                             </div>
                             -->
                            <button type="submit" name="action" value="Reject" class="reject-button">Reject</button>
                        </form>
                        <!-- Add Delete button -->
                        <form action="" method="post" style="display: inline-block; margin-left: 10px;" onsubmit="return confirm('Are you sure you want to delete this submission?');">
                            <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
                            <button type="submit" name="action" value="Delete" class="reject-button" style="background-color: #f44336;">Delete</button> <!-- Using reject-button style for now, can add a new class -->
                        </form>
                    <?php else: ?>
                        <p>This submission has been <?php echo $verification_status; ?>.</p>
                         <!-- Display the admin note if available -->
                        <?php 
                            // Re-fetch verification status and note after update/insert or on page load if already verified
                            // Need to re-establish connection if it was closed
                            if (!isset($link) || !is_object($link)) {
                                require_once '../db_connect.php';
                            }
                            $sql_fetch_note = "SELECT catatan FROM verifikasi_event WHERE event_id = ?";
                            $admin_note_display = '';
                            if ($stmt_fetch_note = mysqli_prepare($link, $sql_fetch_note)) {
                                mysqli_stmt_bind_param($stmt_fetch_note, "i", $event_id);
                                mysqli_stmt_execute($stmt_fetch_note);
                                mysqli_stmt_bind_result($stmt_fetch_note, $fetched_note);
                                if(mysqli_stmt_fetch($stmt_fetch_note)){
                                    $admin_note_display = $fetched_note;
                                }
                                mysqli_stmt_close($stmt_fetch_note);
                            }
                             // Close database connection if it's still open after fetching
                            if (isset($link) && is_object($link)) {
                                mysqli_close($link);
                            }
                        ?>
                        <?php if (!empty($admin_note_display)): ?>
                            <p><strong>Catatan Admin:</strong> <?php echo htmlspecialchars($admin_note_display); ?></p>
                        <?php endif; ?>

                    <?php endif; ?>
                </div>

            </div>
        <?php else: ?>
            <p>Pengajuan not found.</p>
        <?php endif; ?>

         <p><a href="approval.php" class="back-link">Back to Approval List</a></p>

         <?php
        // Add other admin-specific content here
        ?>

    </div>
</body>
</html> 