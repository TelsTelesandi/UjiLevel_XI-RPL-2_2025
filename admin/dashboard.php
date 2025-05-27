<?php
session_start();

// Include database connection file
require_once '../db_connect.php'; // Note the '../' to go up one directory

// Check if the admin is logged in and has the correct role
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header("location: login.php"); // Redirect to admin login page
    exit;
}

// Fetch recent notes from the 'verifikasi_event' table
$notes = [];
$sql_notes = "SELECT v.*, u.username, e.judul_event FROM verifikasi_event v 
              JOIN users u ON v.admin_id = u.user_id 
              JOIN event_pengajuan e ON v.event_id = e.event_id
              WHERE v.catatan IS NOT NULL AND v.catatan != ''
              ORDER BY v.tanggal_verifikasi DESC LIMIT 5";
if ($result_notes = mysqli_query($link, $sql_notes)) {
    while ($row = mysqli_fetch_assoc($result_notes)) {
        $notes[] = $row;
    }
    mysqli_free_result($result_notes);
}

// Fetch statistics
$stats = [
    'total' => 0,
    'pending' => 0,
    'approved' => 0,
    'rejected' => 0
];

// Get total submissions
$sql_total = "SELECT COUNT(*) as total FROM event_pengajuan";
if ($result = mysqli_query($link, $sql_total)) {
    $row = mysqli_fetch_assoc($result);
    $stats['total'] = $row['total'];
    mysqli_free_result($result);
}

// Get pending submissions
$sql_pending = "SELECT COUNT(*) as pending FROM event_pengajuan e 
                LEFT JOIN verifikasi_event v ON e.event_id = v.event_id 
                WHERE v.status IS NULL OR v.status = 'Pending'";
if ($result = mysqli_query($link, $sql_pending)) {
    $row = mysqli_fetch_assoc($result);
    $stats['pending'] = $row['pending'];
    mysqli_free_result($result);
}

// Get approved submissions
$sql_approved = "SELECT COUNT(*) as approved FROM verifikasi_event WHERE status = 'Approved'";
if ($result = mysqli_query($link, $sql_approved)) {
    $row = mysqli_fetch_assoc($result);
    $stats['approved'] = $row['approved'];
    mysqli_free_result($result);
}

// Get rejected submissions
$sql_rejected = "SELECT COUNT(*) as rejected FROM verifikasi_event WHERE status = 'Rejected'";
if ($result = mysqli_query($link, $sql_rejected)) {
    $row = mysqli_fetch_assoc($result);
    $stats['rejected'] = $row['rejected'];
    mysqli_free_result($result);
}

// Fetch recent submissions
$recent_submissions = [];
$sql_recent = "SELECT e.*, v.status, v.tanggal_verifikasi 
               FROM event_pengajuan e 
               LEFT JOIN verifikasi_event v ON e.event_id = v.event_id 
               ORDER BY e.event_id DESC LIMIT 5";
if ($result = mysqli_query($link, $sql_recent)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $recent_submissions[] = $row;
    }
    mysqli_free_result($result);
}

// Close database connection
mysqli_close($link);

// Admin dashboard content starts here
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: 'Arial', sans-serif; /* Adjusted font-family */
            margin: 0;
            background-color: #f8fafd; /* Very light blueish background */
            display: flex;
        }
        .sidebar {
            width: 250px;
            background-color: #2c3b41; /* Darker sidebar */
            color: #b8c7ce; /* Lighter text color */
            padding-top: 20px;
            height: 100vh;
            position: fixed;
            display: flex; /* Use flex for layout */
            flex-direction: column; /* Stack items vertically */
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1); /* Add shadow to sidebar */
        }
        .sidebar h2 {
            text-align: center;
            color: #ffffff; /* White color for title */
            margin-bottom: 30px;
            font-size: 1.8em;
            padding: 0 15px; /* Add padding to title */
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
            flex-grow: 1; /* Allow ul to grow and push logout down */
        }
        .sidebar ul li {
            padding: 12px 20px; /* Increased padding */
            border-bottom: 1px solid #3a4b54; /* Slightly lighter border */
        }
        .sidebar ul li:first-child {
             border-top: 1px solid #3a4b54; /* Add top border to first item */
        }
        .sidebar ul li a {
            color: #b8c7ce; /* Lighter text color */
            text-decoration: none;
            display: block;
            font-size: 1.1em;
            transition: color 0.2s ease-in-out, background-color 0.2s ease-in-out;
        }
        .sidebar ul li a:hover {
            background-color: #3a4b54; /* Hover background */
            color: #ffffff; /* White text on hover */
        }
         .sidebar ul li.active a {
            background-color: #007bff; /* Highlight active link */
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
             font-size: 2em; /* Slightly larger font */
        }
        .stats-section {
             background-color: #ffffff; /* White background */
             padding: 25px; /* Adjusted padding */
             border-radius: 8px;
             box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08); /* Softer and larger shadow */
             margin-bottom: 40px; /* Increased margin */
        }
         .stats-section h2 {
             margin-top: 0;
             margin-bottom: 20px;
             color: #333;
             font-size: 1.6em;
             border-bottom: 1px solid #eee;
             padding-bottom: 10px;
        }
        .stats-boxes {
            display: flex;
            gap: 20px;
        }
        .stat-box {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05); /* Subtle shadow for individual boxes */
            flex: 1;
            text-align: center;
            border: 1px solid #e0e0e0;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out; /* Add hover effect */
        }
         .stat-box:hover {
            transform: translateY(-5px); /* Lift effect on hover */
             box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1); /* Increase shadow on hover */
        }
        .stat-box h3 {
            margin-top: 0;
            color: #555;
            font-size: 1.1em;
        }
        .stat-box p {
            font-size: 2.5em;
            margin: 10px 0 0;
            color: #000;
            font-weight: bold;
        }
        .detail-button-container {
             text-align: center;
             margin-top: 30px;
        }
        .detail-button {
            background-color: #007bff;
            color: white;
            padding: 12px 30px; /* Increased padding */
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
             font-size: 1.1em;
             transition: background-color 0.2s ease-in-out;
        }
         .detail-button:hover {
            background-color: #0056b3;
        }
        .recent-submissions-section {
            background-color: #ffffff;
             padding: 25px; /* Adjusted padding */
             border-radius: 8px;
             box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08); /* Softer and larger shadow */
        }
        .recent-submissions-section h3 {
            margin-top: 0;
            margin-bottom: 20px;
             color: #333;
             font-size: 1.6em;
             border-bottom: 1px solid #eee;
             padding-bottom: 10px;
        }
        .recent-submissions-section table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .recent-submissions-section th,
        .recent-submissions-section td {
            border: 1px solid #ddd;
            padding: 12px 15px; /* Adjusted padding */
            text-align: left;
             vertical-align: middle;
        }
        .recent-submissions-section th {
            background-color: #f2f2f2; /* Slightly darker header */
            font-weight: bold;
            color: #555;
        }
         .recent-submissions-section tbody tr:nth-child(even) {
            background-color: #f9f9f9; /* Lighter zebra striping */
        }
         .recent-submissions-section tbody tr:hover {
            background-color: #e0e0e0; /* Slightly darker highlight on hover */
            transition: background-color 0.2s ease-in-out; /* Smooth hover transition */
        }
         .status-tag {
            padding: 5px 10px; /* Adjusted padding */
            border-radius: 4px;
            font-size: 0.85em; /* Slightly smaller font */
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
         .action-button {
            background-color: #007bff;
            color: white;
            padding: 7px 12px; /* Adjusted padding */
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
             font-size: 0.85em; /* Slightly smaller font */
             transition: background-color 0.2s ease-in-out;
        }
        .action-button:hover {
            background-color: #0056b3;
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
             transition: background-color 0.2s ease-in-out; /* Add hover effect */
        }
         .logout-link:hover {
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
            <li><a href="#">Laporan</a></li>
        </ul>
         <a href="logout.php" class="logout-link">Logout</a>
    </div>
    <div class="main-content">
        <h1>Dashboard Admin</h1>

        <div class="stats-section">
            <h2>Statistik Pengajuan</h2>
            <div class="stats-boxes">
                <div class="stat-box">
                    <h3>Total Pengajuan</h3>
                    <p><?php echo $stats['total']; ?></p>
                </div>
                <div class="stat-box">
                    <h3>Pending</h3>
                    <p><?php echo $stats['pending']; ?></p>
                </div>
                <div class="stat-box">
                    <h3>Approved</h3>
                    <p><?php echo $stats['approved']; ?></p>
                </div>
                <div class="stat-box">
                    <h3>Rejected</h3>
                    <p><?php echo $stats['rejected']; ?></p>
                </div>
            </div>
             <div class="detail-button-container">
                 <a href="approval.php" class="detail-button">Lihat Detail</a>
             </div>
        </div>

        <div class="recent-submissions-section">
            <h3>Pengajuan Terbaru</h3>
            <table>
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Jenis</th>
                        <th>Pembiayaan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recent_submissions)): ?>
                        <?php foreach ($recent_submissions as $submission): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($submission['judul_event']); ?></td>
                                <td><?php echo htmlspecialchars($submission['jenis_kegiatan']); ?></td>
                                <td>Rp <?php echo number_format($submission['total_pembiayaan'] ?? 0, 0, ',', '.'); ?></td>
                                <td>
                                    <?php
                                        $status = htmlspecialchars($submission['status'] ?? 'Pending'); // Default to Pending if status is null (LEFT JOIN)
                                        $status_class = 'status-' . str_replace(' ', '-', $status);
                                    ?>
                                    <span class="status-tag <?php echo $status_class; ?>"><?php echo $status; ?></span>
                                </td>
                                <td><a href="view_pengajuan.php?id=<?php echo $submission['event_id']; ?>" class="action-button">Review</a></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No recent submissions found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

         <?php
        // Add your admin-specific dashboard content here
        ?>

    </div>
</body>
</html> 