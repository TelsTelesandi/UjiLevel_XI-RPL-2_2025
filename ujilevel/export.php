<?php
session_start();
require 'database/config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Filter laporan
$filter = $_GET['filter'] ?? 'all';
$where = '';
if ($filter === 'approved') {
    $where = "WHERE status = 'approved'";
} elseif ($filter === 'rejected') {
    $where = "WHERE status = 'rejected'";
} elseif ($filter === 'pending') {
    $where = "WHERE status = 'pending'";
}

// Ambil data untuk laporan
$events_query = mysqli_query($conn, "SELECT e.*, u.nama_lengkap, u.ekskul 
                                    FROM event_pengajuan e 
                                    JOIN users u ON e.user_id = u.user_id 
                                    $where 
                                    ORDER BY e.tanggal_pengajuan DESC");

if (!$events_query) {
    die("Error in query: " . mysqli_error($conn));
}

// Set headers for Excel file
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Laporan_Pengajuan_Event_' . date('Ymd_His') . '.xls"');
header('Cache-Control: max-age=0');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pengajuan Event</title>
    <style>
        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            margin: 0;
            padding: 10px;
            color: #333333;
            background: #ffffff;
        }

        h1 {
            text-align: center;
            color: #3F51B5;
            font-size: 20pt;
            font-weight: bold;
            margin: 10px 0 5px;
            padding-bottom: 5px;
            border-bottom: 2px solid #3F51B5;
        }

        .report-meta {
            text-align: center;
            font-size: 10pt;
            color: #555555;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
            table-layout: fixed;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #999999;
            padding: 8px 10px;
            text-align: left;
            vertical-align: top;
            mso-number-format: "\@"; /* Treat all cells as text to preserve formatting */
        }

        th {
            background-color: #3F51B5;
            color: #ffffff;
            font-weight: bold;
            font-size: 11pt;
            text-align: center;
            text-transform: uppercase;
        }

        td {
            background-color: #ffffff;
        }

        tr:nth-child(even) td {
            background-color: #f7f7f7;
        }

        /* Column widths */
        th:nth-child(1), td:nth-child(1) { width: 8%; } /* ID */
        th:nth-child(2), td:nth-child(2) { width: 25%; } /* Judul */
        th:nth-child(3), td:nth-child(3) { width: 15%; } /* Ekskul */
        th:nth-child(4), td:nth-child(4) { width: 15%; } /* Jenis */
        th:nth-child(5), td:nth-child(5) { width: 15%; } /* Pembiayaan */
        th:nth-child(6), td:nth-child(6) { width: 12%; } /* Status */
        th:nth-child(7), td:nth-child(7) { width: 10%; } /* Tanggal */

        /* Pembiayaan formatting */
        td:nth-child(5) {
            mso-number-format: "\Rp \#,\#\#0";
            text-align: right;
        }

        /* Status badge styling */
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #856404;
            text-align: center;
        }

        .status-approved {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #155724;
            text-align: center;
        }

        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #721c24;
            text-align: center;
        }

        .no-data {
            text-align: center;
            font-style: italic;
            color: #666666;
            background-color: #f0f0f0;
            padding: 15px;
            font-size: 11pt;
        }
    </style>
</head>
<body>
    <h1>Laporan Pengajuan Event</h1>
    <div class="report-meta">
        Filter: <?php echo ucfirst($filter === 'all' ? 'Semua' : $filter); ?> | Generated on: <?php echo date('d F Y, H:i A'); ?> WIB
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Judul</th>
                <th>Ekskul</th>
                <th>Jenis</th>
                <th>Pembiayaan</th>
                <th>Status</th>
                <th>Tanggal Pengajuan</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($event = mysqli_fetch_assoc($events_query)): ?>
            <tr>
                <td><?php echo $event['event_id']; ?></td>
                <td><?php echo htmlspecialchars($event['judul_event']); ?></td>
                <td><?php echo htmlspecialchars($event['ekskul']); ?></td>
                <td><?php echo htmlspecialchars($event['jenis_kegiatan']); ?></td>
                <td>Rp <?php echo number_format($event['total_pembiayaan'], 0, ',', '.'); ?></td>
                <td class="status-<?php echo $event['status']; ?>">
                    <?php echo ucfirst($event['status']); ?>
                </td>
                <td><?php echo date('d/m/Y', strtotime($event['tanggal_pengajuan'])); ?></td>
            </tr>
            <?php endwhile; ?>
            <?php if (mysqli_num_rows($events_query) == 0): ?>
            <tr>
                <td colspan="7" class="no-data">Tidak ada data yang ditemukan</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
<?php
// Reset the result pointer to the start
mysqli_data_seek($events_query, 0);
?>