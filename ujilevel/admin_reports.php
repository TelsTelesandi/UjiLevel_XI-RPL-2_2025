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

// Get statistics for stat cards
$total_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM event_pengajuan");
$total = mysqli_fetch_assoc($total_query)['total'];

$pending_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM event_pengajuan WHERE status = 'pending'");
$pending = mysqli_fetch_assoc($pending_query)['total'];

$approved_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM event_pengajuan WHERE status = 'approved'");
$approved = mysqli_fetch_assoc($approved_query)['total'];

$rejected_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM event_pengajuan WHERE status = 'rejected'");
$rejected = mysqli_fetch_assoc($rejected_query)['total'];

// Ambil data untuk laporan
$events_query = mysqli_query($conn, "SELECT e.*, u.nama_lengkap, u.ekskul 
                                    FROM event_pengajuan e 
                                    JOIN users u ON e.user_id = u.user_id 
                                    $where 
                                    ORDER BY e.tanggal_pengajuan DESC");

// Check if query was successful
if (!$events_query) {
    die("Error in query: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pengajuan Event</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4b5aaf;
            --secondary-color: #6c757d;
            --card-bg: #ffffff;
            --text-color: #2d3748;
            --border-color: #e2e8f0;
            --gradient-bg: linear-gradient(135deg, #6B48FF, #42A5F5);
            --shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            --hover-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            --backdrop-bg: rgba(0, 0, 0, 0.5);
        }

        body {
            font-family: 'Inter', 'Roboto', system-ui, sans-serif;
            background: #f4f6f9;
            margin: 0;
            color: var(--text-color);
            font-size: 0.95rem;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        /* Hamburger Menu */
        .hamburger {
            display: none;
            position: fixed;
            top: 1rem;
            left: 1rem;
            width: 36px;
            height: 32px;
            background: #ffffff;
            border: none;
            border-radius: 4px;
            padding: 6px;
            cursor: pointer;
            z-index: 1100;
            box-shadow: 0 0 8px rgba(107, 72, 255, 0.4);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .hamburger:hover {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 0 12px rgba(107, 72, 255, 0.6);
        }

        .hamburger span {
            position: absolute;
            left: 6px;
            width: calc(100% - 12px);
            height: 4px;
            background: linear-gradient(90deg, #6B48FF, #42A5F5);
            border-radius: 2px;
            transition: transform 0.3s ease;
            box-shadow: 0 0 5px rgba(107, 72, 255, 0.3);
        }

        .hamburger span:nth-child(1) { top: 6px; }
        .hamburger span:nth-child(2) { top: 14px; }
        .hamburger span:nth-child(3) { top: 22px; }

        .hamburger:hover span {
            transform: scale(1.1) rotate(5deg);
        }

        /* Layout */
        .container-fluid {
            padding: 1.5rem;
        }

        .main-content {
            margin-left: 220px;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 220px;
            height: 100vh;
            background: var(--card-bg);
            border-right: 1px solid var(--border-color);
            transform: translateX(0);
            transition: transform 0.3s ease;
            z-index: 900;
        }

        .sidebar-header {
            padding: 1.25rem;
            text-align: center;
            background: var(--gradient-bg);
            border-bottom: 1px solid var(--border-color);
        }

        .sidebar-header img {
            max-width: 100%;
            height: auto;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu li {
            padding: 0.75rem 1.25rem;
            transition: background 0.2s ease;
        }

        .sidebar-menu li:hover {
            background: #f1f5f9;
            border-left: 3px solid var(--primary-color);
        }

        .sidebar-menu li.active {
            background: #e0e7ff;
            border-left: 3px solid var(--primary-color);
        }

        .sidebar-menu li a {
            color: var(--text-color);
            text-decoration: none;
            display: flex;
            align-items: center;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .sidebar-menu li i {
            margin-right: 0.75rem;
            width: 1.25rem;
            color: var(--primary-color);
            text-align: center;
        }

        /* Backdrop */
        .backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--backdrop-bg);
            z-index: 899;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .backdrop.active {
            display: block;
            opacity: 1;
        }

        /* Cards */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow);
            transition: box-shadow 0.2s ease;
        }

        .card:hover {
            box-shadow: var(--hover-shadow);
        }

        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            padding: 1rem;
            box-shadow: var(--shadow);
            text-align: center;
            transition: transform 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .stat-card .card-title {
            font-size: 0.85rem;
            color: #6b7280;
            margin-bottom: 0.5rem;
        }

        .stat-card .card-value {
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--text-color);
        }

        /* Table */
        .recent-table {
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .recent-table .table {
            margin-bottom: 0;
        }

        .recent-table th {
            background: #f8fafc;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 0.75rem;
            border-bottom: 2px solid var(--border-color);
        }

        .recent-table td {
            padding: 0.75rem;
            font-size: 0.9rem;
            vertical-align: middle;
            border-top: 1px solid var(--border-color);
        }

        .recent-table tr:nth-child(even) {
            background: #f9fafb;
        }

        .recent-table tr:hover {
            background: #f1f5f9;
        }

        .status-badge {
            padding: 0.375rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-pending { background: #fef3c7; color: #92400e; }
        .status-approved { background: #d1fae5; color: #065f46; }
        .status-rejected { background: #fee2e2; color: #991b1b; }

        /* Buttons */
        .btn-primary {
            background: var(--primary-color);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.9rem;
            font-weight: 500;
            color: white;
            transition: background 0.2s ease, transform 0.2s ease;
        }

        .btn-primary:hover {
            background: #3b4a99;
            transform: translateY(-1px);
        }

        .btn-outline-primary {
            border-color: var(--primary-color);
            color: var(--primary-color);
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.9rem;
            font-weight: 500;
            transition: background 0.2s ease, color 0.2s ease;
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: white;
        }

        .btn-success {
            background: #28a745;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.9rem;
            font-weight: 500;
            transition: background 0.2s ease;
        }

        .btn-success:hover {
            background: #218838;
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #374151;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.9rem;
            font-weight: 500;
            transition: background 0.2s ease;
        }

        .btn-secondary:hover {
            background: #d1d5db;
        }

        /* Typography */
        h2 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.25rem;
        }

        h4 {
            font-size: 1.25rem;
            font-weight: 500;
            margin-bottom: 1rem;
        }

        h5 {
            font-size: 1.1rem;
            font-weight: 500;
            margin-bottom: 1rem;
        }

        /* Responsive Styles */
        @media (max-width: 991px) {
            .main-content {
                margin-left: 0;
                padding-top: 3.5rem; /* Space for hamburger icon */
            }

            .sidebar {
                transform: translateX(-100%);
                width: 260px;
                z-index: 901;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .hamburger {
                display: block;
            }
        }

        @media (max-width: 767px) {
            .container-fluid {
                padding: 1rem;
            }

            h2 {
                font-size: 1.3rem;
            }

            h4, h5 {
                font-size: 1rem;
            }

            .stat-card {
                padding: 0.75rem;
            }

            .stat-card .card-value {
                font-size: 1.5rem;
            }

            .recent-table th,
            .recent-table td {
                font-size: 0.85rem;
                padding: 0.5rem;
            }

            .btn-primary,
            .btn-outline-primary,
            .btn-success,
            .btn-secondary {
                font-size: 0.85rem;
                padding: 0.375rem 0.75rem;
            }

            .table-responsive {
                font-size: 0.85rem;
            }
        }

        @media (max-width: 576px) {
            .stat-card {
                margin-bottom: 0.75rem;
            }

            .hamburger {
                width: 32px;
                height: 28px;
                padding: 5px;
            }

            .hamburger span {
                height: 3px;
                left: 5px;
                width: calc(100% - 10px);
            }

            .hamburger span:nth-child(1) { top: 5px; }
            .hamburger span:nth-child(2) { top: 12px; }
            .hamburger span:nth-child(3) { top: 19px; }
        }

        /* Print Styles */
        @media print {
            /* Hide non-essential elements */
            .no-print,
            .sidebar,
            .card-header,
            .btn,
            .alert,
            .hamburger,
            .backdrop {
                display: none !important;
            }

            /* Page setup */
            @page {
                size: A4 portrait;
                margin: 20mm 15mm;
            }

            /* Reset body for print */
            body {
                font-family: 'Inter', 'Roboto', Arial, sans-serif;
                color: #000;
                background: #fff;
                font-size: 10pt;
                line-height: 1.4;
                margin: 0;
            }

            /* Main content adjustments */
            .main-content {
                margin: 0;
                padding: 0;
                width: 100%;
            }

            .container-fluid {
                padding: 0;
                margin: 0;
                width: 100%;
            }

            /* Print header */
            .print-header {
                text-align: center;
                margin-bottom: 15mm;
                padding: 10mm 0;
                border-bottom: 2px solid #333;
                display: block;
                page-break-after: avoid;
                position: running(header);
            }

            .print-header img {
                display: block;
                margin: 0 auto;
                max-width: 80px;
                height: auto;
                margin-bottom: 5mm;
            }

            .print-header h2 {
                font-size: 16pt;
                font-weight: 700;
                margin: 0;
                color: #000;
                text-transform: uppercase;
                letter-spacing: 0.5pt;
            }

            .print-header .print-subtitle {
                font-size: 11pt;
                font-weight: 400;
                color: #333;
                margin: 2mm 0;
                font-style: italic;
            }

            .print-header .print-date {
                font-size: 10pt;
                color: #333;
                margin: 0;
            }

            /* Print footer */
            .print-footer {
                text-align: center;
                font-size: 9pt;
                color: #444;
                padding-top: 5mm;
                border-top: 1px solid #ccc;
                margin-top: 15mm;
                display: block;
                page-break-before: avoid;
                position: running(footer);
            }

            .print-footer p {
                margin: 2mm 0;
                line-height: 1.3;
            }

            .print-footer .page-number::after {
                content: counter(page);
            }

            .print-footer .total-pages::after {
                content: counter(pages);
            }

            /* Define running header and footer */
            @page {
                @top-center {
                    content: element(header);
                }
                @bottom-center {
                    content: element(footer);
                }
            }

            /* Statistics Table for Print */
            .print-statistics {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 10mm;
                font-size: 9pt;
                page-break-after: avoid;
                border: 1px solid #333;
            }

            .print-statistics th,
            .print-statistics td {
                border: 1px solid #333;
                padding: 6pt 8pt;
                text-align: center;
                vertical-align: middle;
            }

            .print-statistics th {
                background: #e0e0e0;
                font-weight: 700;
                font-size: 9pt;
                text-transform: uppercase;
                color: #000;
            }

            .print-statistics td {
                background: #fff;
                font-weight: 500;
                font-size: 9pt;
            }

            .print-statistics tr:nth-child(even) td {
                background: #f9f9f9;
            }

            /* Main Table styling */
            .recent-table {
                width: 100%;
                border-collapse: collapse;
                margin: 10mm 0;
                font-size: 9pt;
                page-break-before: auto;
                page-break-after: auto;
                border: 1px solid #333;
            }

            .recent-table th,
            .recent-table td {
                border: 1px solid #333;
                padding: 8pt 10pt;
                vertical-align: top;
                text-align: left;
            }

            .recent-table th {
                background: #e0e0e0;
                font-weight: 700;
                font-size: 9pt;
                text-transform: uppercase;
                color: #000;
                letter-spacing: 0.5pt;
            }

            .recent-table td {
                background: #fff;
                line-height: 1.3;
            }

            .recent-table tr:nth-child(even) td {
                background: #f9f9f9;
            }

            /* Column alignments */
            .recent-table th:nth-child(1),
            .recent-table td:nth-child(1),
            .recent-table th:nth-child(6),
            .recent-table td:nth-child(6) {
                text-align: center;
            }

            .recent-table th:nth-child(5),
            .recent-table td:nth-child(5) {
                text-align: right;
            }

            /* Column widths */
            .recent-table th:nth-child(1),
            .recent-table td:nth-child(1) { width: 6%; } /* ID */
            .recent-table th:nth-child(2),
            .recent-table td:nth-child(2) { width: 30%; } /* Judul */
            .recent-table th:nth-child(3),
            .recent-table td:nth-child(3) { width: 20%; } /* Ekskul */
            .recent-table th:nth-child(4),
            .recent-table td:nth-child(4) { width: 14%; } /* Jenis */
            .recent-table th:nth-child(5),
            .recent-table td:nth-child(5) { width: 14%; } /* Pembiayaan */
            .recent-table th:nth-child(6),
            .recent-table td:nth-child(6) { width: 10%; } /* Status */
            .recent-table th:nth-child(7),
            .recent-table td:nth-child(7) { width: 12%; } /* Tanggal */

            /* Prevent text overflow */
            .recent-table td {
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: normal;
                word-break: break-word;
                max-width: 0;
            }

            /* Status badges */
            .status-badge {
                display: inline-block;
                padding: 4pt 10pt;
                border-radius: 12pt;
                font-size: 8pt;
                font-weight: 600;
                text-align: center;
                border: 1px solid;
                line-height: 1.2;
            }

            .status-pending {
                background: #fff3cd;
                color: #664d03;
                border-color: #664d03;
            }

            .status-approved {
                background: #d4edda;
                color: #155724;
                border-color: #155724;
            }

            .status-rejected {
                background: #f8d7da;
                color: #721c24;
                border-color: #721c24;
            }

            /* No-data styling */
            .recent-table td.text-center {
                text-align: center;
                font-style: italic;
                color: #555;
                background: #f0f0f0;
                padding: 12pt;
                font-size: 10pt;
            }

            /* Page break control */
            .recent-table tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            .recent-table thead {
                display: table-header-group;
            }

            .recent-table tbody {
                display: table-row-group;
            }

            /* Ensure table doesn't overlap with footer */
            .recent-table::after {
                content: "";
                display: block;
                height: 15mm;
            }
        }
    </style>
</head>
<body>
    <!-- Backdrop -->
    <div class="backdrop"></div>

    <!-- Sidebar -->
    <div class="sidebar" aria-label="Navigasi utama">
        <div class="sidebar-header">
            <img src="resource/logo.png" alt="Logo">
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="admin_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
            </li>
            <li>
                <a href="admin_event.php"><i class="fas fa-check-circle"></i> Approval Kegiatan</a>
            </li>
            <li>
                <a href="admin_users.php"><i class="fas fa-users"></i> Manajemen User</a>
            </li>
            <li class="active">
                <a href="admin_reports.php"><i class="fas fa-file-alt"></i> Laporan</a>
            </li>
            <li>
                <a href="#" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Hamburger Menu -->
        <button class="hamburger" aria-label="Buka menu navigasi">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <div class="container-fluid">
            <h2>Laporan Pengajuan Event</h2>
            
            <!-- Stat Cards -->
            <div class="card mb-4">
                <div class="card-body">
                    <h4>Statistik Pengajuan</h4>
                    <div class="row g-3">
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="stat-card">
                                <h5 class="card-title">Total Pengajuan</h5>
                                <h2 class="card-value"><?= $total; ?></h2>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="stat-card">
                                <h5 class="card-title">Pending</h5>
                                <h2 class="card-value"><?= $pending; ?></h2>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="stat-card">
                                <h5 class="card-title">Approved</h5>
                                <h2 class="card-value"><?= $approved; ?></h2>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="stat-card">
                                <h5 class="card-title">Rejected</h5>
                                <h2 class="card-value"><?= $rejected; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter -->
            <div class="mb-3 no-print">
                <a href="?filter=all" class="btn btn-sm <?= $filter === 'all' ? 'btn-primary' : 'btn-outline-primary' ?>">Semua</a>
                <a href="?filter=approved" class="btn btn-sm <?= $filter === 'approved' ? 'btn-primary' : 'btn-outline-primary' ?>">Approved</a>
                <a href="?filter=rejected" class="btn btn-sm <?= $filter === 'rejected' ? 'btn-primary' : 'btn-outline-primary' ?>">Rejected</a>
                <a href="?filter=pending" class="btn btn-sm <?= $filter === 'pending' ? 'btn-primary' : 'btn-outline-primary' ?>">Pending</a>
            </div>
            
            <!-- Statistics Table for Print -->
            <div class="print-statistics">
                <table>
                    <thead>
                        <tr>
                            <th>Total Pengajuan</th>
                            <th>Pending</th>
                            <th>Approved</th>
                            <th>Rejected</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $total; ?></td>
                            <td><?= $pending; ?></td>
                            <td><?= $approved; ?></td>
                            <td><?= $rejected; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Tabel Laporan -->
            <div class="card recent-table mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Daftar Pengajuan Event</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
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
                                    <td><?= $event['event_id'] ?></td>
                                    <td><?= htmlspecialchars($event['judul_event']) ?></td>
                                    <td><?= htmlspecialchars($event['ekskul']) ?></td>
                                    <td><?= htmlspecialchars($event['jenis_kegiatan']) ?></td>
                                    <td>Rp <?= number_format($event['total_pembiayaan'], 0, ',', '.') ?></td>
                                    <td>
                                        <span class="status-badge status-<?= $event['status'] ?>">
                                            <?= ucfirst($event['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($event['tanggal_pengajuan'])) ?></td>
                                </tr>
                                <?php endwhile; ?>
                                
                                <?php if (mysqli_num_rows($events_query) == 0): ?>
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data yang ditemukan</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Tombol Export -->
            <div class="no-print">
                <a href="export.php?type=excel&filter=<?= $filter ?>" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="fas fa-print"></i> Print
                </button>
                <a href="admin_dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            <!-- Print Header -->
            <div class="print-header">
                <img src="resource/logo.png" alt="Logo">
                <h2>Laporan Pengajuan Event</h2>
                <p class="print-subtitle">Sistem Manajemen Kegiatan Ekstrakurikuler</p>
                <p class="print-date">Tanggal Cetak: <?= date('d/m/Y H:i'); ?></p>
            </div>

            <!-- Print Footer -->
            <div class="print-footer">
                <p>Filter: <?= ucfirst($filter === 'all' ? 'Semua' : $filter); ?></p>
                <p>Laporan ini dicetak dari sistem otomatis</p>
                <p>Halaman <span class="page-number"></span> dari <span class="total-pages"></span></p>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Sidebar and backdrop toggle
            function toggleSidebar() {
                $('.sidebar').toggleClass('active');
                $('.backdrop').toggleClass('active');
                $('.hamburger').animate({ scale: '1.05' }, 100).animate({ scale: '1' }, 100); // Pulse effect
            }

            $('.hamburger').click(toggleSidebar);
            $('.backdrop').click(toggleSidebar);

            // Close sidebar on menu item click (mobile), except logout
            $('.sidebar-menu li a').not('.logout-link').click(function() {
                if ($(window).width() < 992) {
                    toggleSidebar();
                }
            });

            // Sidebar active state
            $('.sidebar-menu li').click(function() {
                $('.sidebar-menu li').removeClass('active');
                $(this).addClass('active');
            });

            // Handle logout confirmation
            $('.logout-link').click(function(e) {
                e.preventDefault();
                if (confirm('Apakah Anda yakin untuk logout?')) {
                    window.location.href = 'logout.php';
                }
            });
        });
    </script>
</body>
</html>