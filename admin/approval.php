<?php
session_start();

// Include database connection file
require_once '../db_connect.php'; // Note the '../' to go up one directory

// Check if the admin is logged in and has the correct role
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header("location: login.php"); // Redirect to admin login page
    exit;
}

// Display success or error message if redirected from delete_pengajuan.php
$message = '';
$message_class = '';
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'deleted') {
        $message = "Pengajuan berhasil dihapus.";
        $message_class = 'alert-success';
    } elseif ($_GET['msg'] === 'error' && isset($_GET['err'])) {
        $message = "Error: " . htmlspecialchars(urldecode($_GET['err']));
        $message_class = 'alert-danger';
    }
}

// Fetch pending pengajuan data
$pending_pengajuan = [];
// Select data from event_pengajuan and join with verifikasi_event to find pending ones
// Assuming verifikasi_event has a status column and links via event_id
// We need data from event_pengajuan like judul, jenis, pembiayaan
// And we need the status from verifikasi_event
// Let's fetch pengajuan that are either not in verifikasi_event or have status 'Pending'

$sql_pending = "SELECT ep.event_id, ep.judul_event, ep.jenis_kegiatan, ep.total_pembiayaan, ve.status
              FROM event_pengajuan ep
              LEFT JOIN verifikasi_event ve ON ep.event_id = ve.event_id
              WHERE ve.status IS NULL OR ve.status = 'Pending'
              ORDER BY ep.event_id ASC";

if ($result_pending = mysqli_query($link, $sql_pending)) {
    while ($row = mysqli_fetch_assoc($result_pending)) {
        $pending_pengajuan[] = $row;
    }
    mysqli_free_result($result_pending);
}

// Fetch approved pengajuan data
$approved_pengajuan = [];
$sql_approved = "SELECT ep.event_id, ep.judul_event, ep.jenis_kegiatan, ep.total_pembiayaan, ve.status, ve.tanggal_verifikasi, u.username as admin_username
               FROM event_pengajuan ep
               JOIN verifikasi_event ve ON ep.event_id = ve.event_id
               JOIN users u ON ve.admin_id = u.user_id
               WHERE ve.status = 'Approved'
               ORDER BY ve.tanggal_verifikasi DESC";

if ($result_approved = mysqli_query($link, $sql_approved)) {
    while ($row = mysqli_fetch_assoc($result_approved)) {
        $approved_pengajuan[] = $row;
    }
    mysqli_free_result($result_approved);
}

// Fetch rejected pengajuan data
$rejected_pengajuan = [];
$sql_rejected = "SELECT ep.event_id, ep.judul_event, ep.jenis_kegiatan, ep.total_pembiayaan, ve.status, ve.tanggal_verifikasi, u.username as admin_username
               FROM event_pengajuan ep
               JOIN verifikasi_event ve ON ep.event_id = ve.event_id
               JOIN users u ON ve.admin_id = u.user_id
               WHERE ve.status = 'Rejected'
               ORDER BY ve.tanggal_verifikasi DESC";

if ($result_rejected = mysqli_query($link, $sql_rejected)) {
    while ($row = mysqli_fetch_assoc($result_rejected)) {
        $rejected_pengajuan[] = $row;
    }
    mysqli_free_result($result_rejected);
}

// Close database connection
mysqli_close($link);

// Admin Approval content starts here
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Approval Kegiatan</title>
    <style>
        body {
            font-family: 'Arial', sans-serif; /* Changed font-family */
            margin: 0;
            background-color: #f8fafd; /* Consistent background */
            display: flex;
        }
        .sidebar {
            width: 250px;
            background-color: #2c3b41; /* Consistent sidebar color */
            color: #b8c7ce; /* Consistent text color */
            padding-top: 20px;
            height: 100vh;
            position: fixed;
            display: flex;
            flex-direction: column;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1); /* Add shadow to sidebar */
        }
        .sidebar h2 {
            text-align: center;
            color: #ffffff;
            margin-bottom: 30px;
            font-size: 1.8em;
            padding: 0 15px;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
            flex-grow: 1;
        }
        .sidebar ul li {
            padding: 12px 20px; /* Increased padding */
            border-bottom: 1px solid #3a4b54;
        }
        .sidebar ul li:first-child {
             border-top: 1px solid #3a4b54; /* Add top border */
        }
        .sidebar ul li a {
            color: #b8c7ce;
            text-decoration: none;
            display: block;
            font-size: 1.1em;
             transition: color 0.2s ease-in-out, background-color 0.2s ease-in-out;
        }
        .sidebar ul li a:hover {
            background-color: #3a4b54;
            color: #ffffff;
        }
         .sidebar ul li.active a {
            background-color: #007bff;
            color: white;
        }
        .main-content {
            margin-left: 250px;
            padding: 30px; /* Increased padding */
            flex-grow: 1;
        }
        .main-content h1 {
             margin-top: 0;
             color: #333;
             margin-bottom: 30px; /* Increased margin */
             font-size: 2em;
        }
        .approval-table-container {
            background-color: #ffffff; /* White background */
            padding: 25px; /* Adjusted padding */
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08); /* Softer and larger shadow */
            margin-bottom: 30px; /* Add margin bottom */
        }
        .approval-table-container h3 {
            margin-top: 0;
            margin-bottom: 20px;
            color: #333;
            font-size: 1.6em;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
         .approval-table-container table {
            width: 100%;
            border-collapse: collapse;
             margin-top: 15px; /* Adjusted margin */
             border: 1px solid #ddd; /* Add border to table */
        }
        .approval-table-container th,
        .approval-table-container td {
            border: 1px solid #ddd;
            padding: 10px 12px; /* Adjusted padding */
            text-align: left;
             vertical-align: top; /* Align content to top */
        }
        .approval-table-container th {
            background-color: #f2f2f2; /* Slightly darker header */
            font-weight: bold;
            color: #555;
        }
        .approval-table-container tbody tr:nth-child(even) {
            background-color: #f9f9f9; /* Lighter zebra striping */
        }
         .approval-table-container tbody tr:hover {
            background-color: #e9e9e9; /* Highlight on hover */
             transition: background-color 0.2s ease-in-out;
        }
        .logout-link {
            display: block;
            padding: 15px 20px;
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            text-align: center;
            font-size: 1.1em;
            margin-top: auto;
             transition: background-color 0.2s ease-in-out;
        }
         .logout-link:hover {
            background-color: #c82333;
        }
         .alert {
            padding: 12px; /* Increased padding */
            border-radius: 4px;
            margin-bottom: 20px; /* Increased margin */
             font-size: 1em; /* Adjusted font size */
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .status-tag {
            padding: 4px 8px; /* Adjusted padding */
            border-radius: 4px;
            font-size: 0.8em; /* Adjusted font size */
            color: white;
            display: inline-block;
            text-align: center;
            font-weight: bold;
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
         .action-links a {
             margin-right: 10px; /* Add space between links */
             text-decoration: none; /* Remove underline */
             color: #007bff; /* Blue color for links */
         }
         .action-links a:hover {
             text-decoration: underline; /* Add underline on hover */
         }
         .action-links .delete-form {
             display: inline-block; /* Make form inline */
         }
         .delete-button {
             background-color: #dc3545;
             color: white;
             padding: 5px 10px;
             border: none;
             border-radius: 4px;
             cursor: pointer;
             font-size: 0.9em;
             transition: background-color 0.2s ease-in-out;
         }
         .delete-button:hover {
             background-color: #c82333;
         }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>"><a href="dashboard.php">Dashboard</a></li>
            <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'manage_users.php') ? 'active' : ''; ?>"><a href="manage_users.php">Manajemen User</a></li>
            <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'approval.php') ? 'active' : ''; ?>"><a href="approval.php">Approval Kegiatan</a></li>
            <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'laporan.php') ? 'active' : ''; ?>"><a href="laporan.php">Laporan</a></li>
        </ul>
         <a href="logout.php" class="logout-link">Logout</a>
    </div>
    <div class="main-content">
        <h1>Approval Kegiatan</h1>

        <?php if (!empty($message)): ?>
            <div class="alert <?php echo $message_class; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="approval-table-container">
            <h3>Daftar Pengajuan Pending</h3>
            <table>
                <thead>
                    <tr>
                         <th>ID</th>
                        <th>Judul</th>
                        <th>Jenis</th>
                        <th>Pembiayaan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($pending_pengajuan)): ?>
                        <?php foreach ($pending_pengajuan as $pengajuan): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($pengajuan['event_id']); ?></td>
                                <td><?php echo htmlspecialchars($pengajuan['judul_event']); ?></td>
                                <td><?php echo htmlspecialchars($pengajuan['jenis_kegiatan']); ?></td>
                                <td>Rp <?php echo number_format($pengajuan['total_pembiayaan'] ?? 0, 0, ',', '.'); ?></td>
                                <td>
                                    <?php
                                        $status = htmlspecialchars($pengajuan['status'] ?? 'Pending');
                                        $status_class = 'status-' . str_replace(' ', '-', $status);
                                    ?>
                                    <span class="status-tag <?php echo $status_class; ?>"><?php echo $status; ?></span>
                                </td>
                                <td class="action-links">
                                    <a href="view_pengajuan.php?id=<?php echo $pengajuan['event_id']; ?>">Review</a>
                                    <!-- Tombol Approve langsung -->
                                    <form action="approve_pengajuan.php" method="post" style="display:inline-block; margin-left:5px;">
                                        <input type="hidden" name="event_id" value="<?php echo $pengajuan['event_id']; ?>">
                                        <button type="submit" class="approve-button" style="background-color:#28a745; color:white; border:none; padding:5px 10px; border-radius:4px; cursor:pointer;">Approve</button>
                                    </form>
                                    <a href="reject_pengajuan.php?id=<?php echo $pengajuan['event_id']; ?>" style="color: red; margin-left:5px;" onclick="return confirm('Apakah Anda yakin ingin menolak pengajuan ini?');">Reject</a>
                                    <form action="delete_pengajuan.php" method="post" class="delete-form" style="display:inline-block; margin-left:5px;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengajuan ini secara permanen?');">
                                        <input type="hidden" name="event_id" value="<?php echo $pengajuan['event_id']; ?>">
                                        <button type="submit" class="delete-button">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">Tidak ada pengajuan pending saat ini.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

         <div class="approval-table-container">
            <h3>Daftar Pengajuan Approved</h3>
            <table>
                <thead>
                    <tr>
                         <th>ID</th>
                        <th>Judul</th>
                        <th>Jenis</th>
                        <th>Pembiayaan</th>
                        <th>Status</th>
                         <th>Tanggal Verifikasi</th>
                         <th>Admin</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($approved_pengajuan)): ?>
                        <?php foreach ($approved_pengajuan as $pengajuan): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($pengajuan['event_id']); ?></td>
                                <td><?php echo htmlspecialchars($pengajuan['judul_event']); ?></td>
                                <td><?php echo htmlspecialchars($pengajuan['jenis_kegiatan']); ?></td>
                                <td>Rp <?php echo number_format($pengajuan['total_pembiayaan'] ?? 0, 0, ',', '.'); ?></td>
                                <td>
                                    <?php
                                        $status = htmlspecialchars($pengajuan['status'] ?? 'Approved');
                                        $status_class = 'status-' . str_replace(' ', '-', $status);
                                    ?>
                                    <span class="status-tag <?php echo $status_class; ?>"><?php echo $status; ?></span>
                                </td>
                                 <td><?php echo htmlspecialchars($pengajuan['tanggal_verifikasi'] ?? 'N/A'); ?></td>
                                 <td><?php echo htmlspecialchars($pengajuan['admin_username'] ?? 'N/A'); ?></td>
                                <td>
                                    <a href="view_pengajuan.php?id=<?php echo $pengajuan['event_id']; ?>">Review</a>
                                     <!-- Add Delete button -->
                                     <span style="margin-left: 10px;">|</span> <!-- Separator -->
                                     <form action="delete_pengajuan.php" method="post" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengajuan ini secara permanen?');">
                                        <input type="hidden" name="event_id" value="<?php echo $pengajuan['event_id']; ?>">
                                        <button type="submit" class="delete-button">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">Tidak ada pengajuan approved saat ini.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

         <div class="approval-table-container">
            <h3>Daftar Pengajuan Rejected</h3>
            <table>
                <thead>
                    <tr>
                         <th>ID</th>
                        <th>Judul</th>
                        <th>Jenis</th>
                        <th>Pembiayaan</th>
                        <th>Status</th>
                         <th>Tanggal Verifikasi</th>
                         <th>Admin</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($rejected_pengajuan)): ?>
                        <?php foreach ($rejected_pengajuan as $pengajuan): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($pengajuan['event_id']); ?></td>
                                <td><?php echo htmlspecialchars($pengajuan['judul_event']); ?></td>
                                <td><?php echo htmlspecialchars($pengajuan['jenis_kegiatan']); ?></td>
                                <td>Rp <?php echo number_format($pengajuan['total_pembiayaan'] ?? 0, 0, ',', '.'); ?></td>
                                <td>
                                    <?php
                                        $status = htmlspecialchars($pengajuan['status'] ?? 'Rejected');
                                        $status_class = 'status-' . str_replace(' ', '-', $status);
                                    ?>
                                    <span class="status-tag <?php echo $status_class; ?>"><?php echo $status; ?></span>
                                </td>
                                 <td><?php echo htmlspecialchars($pengajuan['tanggal_verifikasi'] ?? 'N/A'); ?></td>
                                 <td><?php echo htmlspecialchars($pengajuan['admin_username'] ?? 'N/A'); ?></td>
                                <td>
                                    <a href="view_pengajuan.php?id=<?php echo $pengajuan['event_id']; ?>">Review</a>
                                     <!-- Add Delete button -->
                                     <span style="margin-left: 10px;">|</span> <!-- Separator -->
                                     <form action="delete_pengajuan.php" method="post" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengajuan ini secara permanen?');">
                                        <input type="hidden" name="event_id" value="<?php echo $pengajuan['event_id']; ?>">
                                        <button type="submit" class="delete-button">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">Tidak ada pengajuan rejected saat ini.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</body>
</html>