<?php
session_start();

// Include database connection file
require_once '../db_connect.php'; // Note the '../' to go up one directory

// Check if the admin is logged in and has the correct role
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header("location: login.php"); // Redirect to admin login page
    exit;
}

// Fetch all submissions with verification status and user info
$laporan_data = [];
$sql_laporan = "SELECT 
                    e.event_id,
                    e.judul_event,
                    e.jenis_kegiatan,
                    e.total_pembiayaan,
                    u.username as user_pengaju,
                    v.status,
                    v.tanggal_verifikasi,
                    v.catatan,
                    a.username as admin_verifikator
                FROM 
                    event_pengajuan e
                JOIN 
                    users u ON e.user_id = u.user_id
                LEFT JOIN 
                    verifikasi_event v ON e.event_id = v.event_id
                LEFT JOIN
                    users a ON v.admin_id = a.user_id
                ORDER BY 
                    e.event_id DESC";

if ($result_laporan = mysqli_query($link, $sql_laporan)) {
    while ($row = mysqli_fetch_assoc($result_laporan)) {
        $laporan_data[] = $row;
    }
    mysqli_free_result($result_laporan);
}

// Close database connection
mysqli_close($link);

// Laporan content starts here
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Laporan</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            background-color: #f8fafd;
            display: flex;
        }
        .sidebar {
            width: 250px;
            background-color: #2c3b41;
            color: #b8c7ce;
            padding-top: 20px;
            height: 100vh;
            position: fixed;
            display: flex;
            flex-direction: column;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
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
            padding: 12px 20px;
            border-bottom: 1px solid #3a4b54;
        }
        .sidebar ul li:first-child {
             border-top: 1px solid #3a4b54;
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
            padding: 30px;
            flex-grow: 1;
        }
        .main-content h1 {
             margin-top: 0;
             color: #333;
             margin-bottom: 30px;
             font-size: 2em;
        }
        .report-section {
            background-color: #ffffff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        }
        .report-section h3 {
            margin-top: 0;
            margin-bottom: 20px;
            color: #333;
            font-size: 1.6em;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .report-section table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border: 1px solid #ddd;
        }
        .report-section th,
        .report-section td {
            border: 1px solid #ddd;
            padding: 10px 12px;
            text-align: left;
            vertical-align: top;
        }
        .report-section th {
            background-color: #f2f2f2;
            font-weight: bold;
            color: #555;
        }
        .report-section tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .report-section tbody tr:hover {
            background-color: #e9e9e9;
            transition: background-color 0.2s ease-in-out;
        }
         .status-tag {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8em;
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
        <h1>Laporan Pengajuan Event</h1>

        <div class="report-section">
            <h3>Data Pengajuan</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID Event</th>
                        <th>Judul Event</th>
                        <th>Jenis Kegiatan</th>
                        <th>Pembiayaan</th>
                        <th>Pengaju</th>
                        <th>Status Verifikasi</th>
                        <th>Tanggal Verifikasi</th>
                        <th>Admin Verifikator</th>
                        <th>Catatan Admin</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($laporan_data)): ?>
                        <?php foreach ($laporan_data as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['event_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['judul_event']); ?></td>
                                <td><?php echo htmlspecialchars($row['jenis_kegiatan']); ?></td>
                                <td>Rp <?php echo number_format($row['total_pembiayaan'] ?? 0, 0, ',', '.'); ?></td>
                                <td><?php echo htmlspecialchars($row['user_pengaju'] ?? 'N/A'); ?></td>
                                <td>
                                    <?php
                                        $status = htmlspecialchars($row['status'] ?? 'Pending');
                                        $status_class = 'status-' . str_replace(' ', '-', $status);
                                    ?>
                                    <span class="status-tag <?php echo $status_class; ?>"><?php echo $status; ?></span>
                                </td>
                                <td><?php echo htmlspecialchars($row['tanggal_verifikasi'] ?? 'Belum Diverifikasi'); ?></td>
                                <td><?php echo htmlspecialchars($row['admin_verifikator'] ?? 'Belum Diverifikasi'); ?></td>
                                <td><?php echo nl2br(htmlspecialchars($row['catatan'] ?? '-')); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9">Tidak ada data laporan pengajuan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</body>
</html> 