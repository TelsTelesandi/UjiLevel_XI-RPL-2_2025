<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include '../config/db.php';

// Get date range from request
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01'); // First day of current month
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');   // Last day of current month

// Get statistics
$stats_query = mysqli_query($conn, "
    SELECT COUNT(*) as total_events
    FROM event_pengajuan
    WHERE DATE(created_at) BETWEEN '$start_date' AND '$end_date'
");
$stats = mysqli_fetch_assoc($stats_query);

// Get events for the period
$events_query = mysqli_query($conn, "
    SELECT 
        ep.*,
        u.nama_lengkap as pengaju,
        u.email as email_pengaju
    FROM event_pengajuan ep
    LEFT JOIN users u ON ep.user_id = u.user_id
    WHERE DATE(ep.created_at) BETWEEN '$start_date' AND '$end_date'
    ORDER BY ep.created_at DESC
");

// Handle export
if (isset($_GET['export']) && $_GET['export'] === 'excel') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="event_report_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // Add UTF-8 BOM for proper Excel encoding
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // CSV Headers
    fputcsv($output, array(
        'Nama Event',
        'Pengaju',
        'Email Pengaju',
        'Tanggal Event',
        'Tanggal Pengajuan',
        'Deskripsi'
    ));
    
    // CSV Data
    while ($row = mysqli_fetch_assoc($events_query)) {
        fputcsv($output, array(
            $row['nama_event'],
            $row['pengaju'],
            $row['email_pengaju'],
            $row['tanggal'],
            $row['created_at'],
            $row['deskripsi']
        ));
    }
    
    fclose($output);
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Event - Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #e74c3c;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --info-color: #3498db;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --danger-color: #e74c3c;
            --text-light: #ffffff;
            --text-dark: #2c3e50;
        }

        body {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            min-height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            color: var(--text-dark);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .report-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 2rem;
            margin-top: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-box {
            background: rgba(255, 255, 255, 0.9);
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
            border: 1px solid rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .stat-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--secondary-color);
            font-size: 0.9rem;
        }

        .filters {
            background: rgba(255, 255, 255, 0.9);
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }

        .filter-group {
            display: flex;
            gap: 1rem;
            align-items: center;
            margin-bottom: 1rem;
        }

        .filter-group label {
            min-width: 100px;
        }

        input[type="date"] {
            padding: 0.5rem;
            border: 1px solid rgba(0, 0, 0, 0.2);
            border-radius: 5px;
            font-size: 1rem;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: var(--info-color);
            color: white;
        }

        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        .btn-export {
            background: var(--success-color);
            color: white;
        }

        .btn-export:hover {
            background: #219a52;
            transform: translateY(-2px);
        }

        .btn-print {
            background: var(--primary-color);
            color: white;
        }

        .btn-print:hover {
            background: #1a252f;
            transform: translateY(-2px);
        }

        @media print {
            @page {
                size: A4 landscape;
                margin: 1cm;
            }

            body {
                background: white;
                margin: 0;
                padding: 0;
            }

            .nav-modern,
            .filters,
            .btn,
            .stats-grid {
                display: none !important;
            }

            .container {
                padding: 0;
                margin: 0;
                width: 100%;
                max-width: none;
            }

            .report-card {
                box-shadow: none;
                padding: 0;
                margin: 0;
                background: white;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                font-size: 10pt;
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            th, td {
                border: 1px solid #000;
                padding: 6px;
                text-align: left;
                vertical-align: top;
            }

            th {
                background: #f0f0f0 !important;
                color: #000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                font-weight: bold;
            }

            .print-header {
                display: block !important;
                text-align: center;
                margin-bottom: 20px;
                page-break-after: avoid;
            }

            .print-header h1 {
                font-size: 16pt;
                margin: 0 0 10px 0;
            }

            .print-info {
                margin-bottom: 20px;
                font-size: 10pt;
            }

            .print-info p {
                margin: 5px 0;
            }

            /* Mengatur lebar kolom tabel */
            table th:nth-child(1), /* Nama Event */
            table td:nth-child(1) {
                width: 20%;
            }

            table th:nth-child(2), /* Pengaju */
            table td:nth-child(2) {
                width: 15%;
            }

            table th:nth-child(3), /* Email */
            table td:nth-child(3) {
                width: 20%;
            }

            table th:nth-child(4), /* Tanggal Event */
            table td:nth-child(4) {
                width: 10%;
            }

            table th:nth-child(5), /* Tanggal Pengajuan */
            table td:nth-child(5) {
                width: 10%;
            }

            table th:nth-child(6), /* Deskripsi */
            table td:nth-child(6) {
                width: 15%;
            }

            table th:nth-child(7), /* File */
            table td:nth-child(7) {
                width: 10%;
            }

            /* Mengatur teks yang terlalu panjang */
            td {
                word-wrap: break-word;
                max-width: 0;
            }

            /* Menambahkan nomor halaman */
            .page-break {
                page-break-after: always;
            }

            /* Footer dengan nomor halaman */
            @page {
                @bottom-center {
                    content: "Halaman " counter(page) " dari " counter(pages);
                    font-size: 10pt;
                }
            }
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        th {
            background: var(--primary-color);
            color: white;
        }

        tr:hover {
            background: rgba(0, 0, 0, 0.02);
        }

        .nav-modern {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            padding: 0.5rem 0;
        }

        .nav-list {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 0;
            align-items: center;
        }

        .nav-brand {
            color: var(--text-light);
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: bold;
            padding: 1rem;
            margin-right: 2rem;
        }

        .nav-link {
            color: var(--text-light);
            text-decoration: none;
            padding: 0.8rem 1.2rem;
            border-radius: 5px;
            transition: all 0.3s ease;
            margin: 0 0.5rem;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .nav-right {
            margin-left: auto;
        }

        .page-wrapper {
            padding-top: 80px;
        }
    </style>
</head>
<body>
    <nav class="nav-modern">
        <div class="container">
            <ul class="nav-list">
                <li><a href="dashboard_admin.php" class="nav-brand">Admin Panel</a></li>
                <li><a href="dashboard_admin.php" class="nav-link">Dashboard</a></li>
                <li><a href="reports.php" class="nav-link">Laporan</a></li>
                <li class="nav-right"><a href="../auth/logout.php" class="nav-link">Keluar</a></li>
            </ul>
        </div>
    </nav>

    <div class="page-wrapper">
        <div class="container">
            <div class="filters">
                <form method="GET" class="filter-group">
                    <label>Periode:</label>
                    <input type="date" name="start_date" value="<?php echo $start_date; ?>">
                    <label>sampai</label>
                    <input type="date" name="end_date" value="<?php echo $end_date; ?>">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="?export=excel&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" 
                       class="btn btn-export">Export to Excel</a>
                    <button type="button" onclick="window.print()" class="btn btn-print">Print Document</button>
                </form>
            </div>

            <div class="print-header" style="display: none;">
                <h1>Laporan Event Ekskul</h1>
                <div class="print-info">
                    <p>Periode: <?php echo date('d F Y', strtotime($start_date)) . ' - ' . date('d F Y', strtotime($end_date)); ?></p>
                    <p>Tanggal Cetak: <?php echo date('d F Y H:i:s'); ?></p>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-box">
                    <div class="stat-number"><?php echo $stats['total_events']; ?></div>
                    <div class="stat-label">Total Events</div>
                </div>
            </div>

            <div class="report-card">
                <h2>Daftar Event</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Nama Event</th>
                            <th>Pengaju</th>
                            <th>Email</th>
                            <th>Tanggal Event</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Deskripsi</th>
                            <th>File</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($event = mysqli_fetch_assoc($events_query)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($event['nama_event']); ?></td>
                            <td><?php echo htmlspecialchars($event['pengaju']); ?></td>
                            <td><?php echo htmlspecialchars($event['email_pengaju']); ?></td>
                            <td><?php echo htmlspecialchars($event['tanggal']); ?></td>
                            <td><?php echo htmlspecialchars($event['created_at']); ?></td>
                            <td><?php echo htmlspecialchars($event['deskripsi']); ?></td>
                            <td>
                                <?php if (!empty($event['file_path'])): ?>
                                    <a href="../uploads/<?php echo htmlspecialchars(basename($event['file_path'])); ?>" 
                                       class="btn btn-primary btn-sm" 
                                       target="_blank">Download</a>
                                <?php else: ?>
                                    <span class="text-muted">Tidak ada file</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html> 