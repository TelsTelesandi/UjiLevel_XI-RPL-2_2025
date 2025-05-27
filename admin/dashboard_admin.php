<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Anda harus login sebagai admin untuk mengakses halaman ini.";
    header("Location: ../auth/login.php");
    exit();
}

include '../config/db.php';

// Welcome message
$admin_name = $_SESSION['nama_lengkap'];

// Get total events count
$total_events_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM event_pengajuan");
if (!$total_events_query) {
    $_SESSION['error'] = "Error menghitung jumlah event: " . mysqli_error($conn);
}
$total_events = $total_events_query ? mysqli_fetch_assoc($total_events_query)['total'] : 0;

// Get recent events with error checking
$query = mysqli_query($conn, "SELECT ep.*, u.nama_lengkap 
                             FROM event_pengajuan ep 
                             JOIN users u ON ep.user_id = u.user_id 
                             ORDER BY ep.event_id DESC");

// Check for query error
if (!$query) {
    $_SESSION['error'] = "Error mengambil data event: " . mysqli_error($conn);
}

// Debug information
$num_rows = $query ? mysqli_num_rows($query) : 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin - Aplikasi Ekskul</title>
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
            display: block;
            padding: 0;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: var(--text-light);
            min-height: 100vh;
        }

        .page-wrapper {
            min-height: 100vh;
            padding-top: 80px;
        }

        .main-content {
            padding: 2rem 0;
        }

        .nav-modern {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
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

        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.2);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            color: var(--text-light);
        }

        .stat-label {
            color: var(--text-light);
            opacity: 0.9;
        }

        .event-list {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 2rem;
            margin-top: 2rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--text-dark);
        }

        .modern-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .modern-table th {
            background: var(--primary-color);
            color: var(--text-light);
            padding: 1rem;
            text-align: left;
        }

        .modern-table td {
            padding: 1rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .modern-table tr:hover {
            background: rgba(0, 0, 0, 0.02);
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
            color: white;
            margin-bottom: 1rem;
        }

        .btn-primary {
            background: var(--info-color);
        }

        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 10px;
            color: var(--text-light);
        }

        .alert-success {
            background: rgba(39, 174, 96, 0.9);
        }

        .alert-error {
            background: rgba(231, 76, 60, 0.9);
        }

        .action-buttons {
            margin-bottom: 2rem;
        }

        .action-buttons .btn {
            margin-right: 0.5rem;
        }

        .btn-info {
            background: var(--info-color);
        }

        .btn-warning {
            background: var(--warning-color);
        }

        .btn-danger {
            background: var(--danger-color);
        }

        .text-center {
            text-align: center;
        }

        .action-buttons td {
            white-space: nowrap;
        }

        .action-buttons .btn {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }

        .status-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
            color: white;
        }

        .status-pending {
            background: var(--warning-color);
        }

        .status-rejected {
            background: var(--danger-color);
        }

        .status-approved {
            background: var(--success-color);
        }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <!-- Navigation -->
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

        <!-- Main Content -->
        <main class="main-content">
            <div class="container">
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <?php echo htmlspecialchars($_SESSION['success']); ?>
                        <?php unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-error">
                        <?php echo htmlspecialchars($_SESSION['error']); ?>
                        <?php unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <h1>Selamat Datang, <?php echo htmlspecialchars($admin_name); ?>!</h1>
                
                <div class="action-buttons">
                    <a href="create_event.php" class="btn btn-primary">Tambah Event Baru</a>
                </div>

                <div class="dashboard-stats">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $total_events; ?></div>
                        <div class="stat-label">Total Event</div>
                    </div>
                </div>

                <div class="event-list">
                    <h2>Daftar Event</h2>
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Event</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Pengaju</th>
                                <th>File</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($query && mysqli_num_rows($query) > 0) {
                                while ($row = mysqli_fetch_assoc($query)) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['event_id']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['nama_event']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['tanggal']) . "</td>";
                                    echo "<td><span class='status-badge status-" . htmlspecialchars($row['status']) . "'>" . 
                                         ucfirst(htmlspecialchars($row['status'])) . "</span></td>";
                                    echo "<td>" . htmlspecialchars($row['nama_lengkap']) . "</td>";
                                    echo "<td>";
                                    if (!empty($row['file_path'])) {
                                        $file_name = basename($row['file_path']);
                                        echo "<a href='../uploads/" . htmlspecialchars($file_name) . "' class='btn btn-primary btn-sm' target='_blank'>Download</a>";
                                    } else {
                                        echo "<span class='text-muted'>Tidak ada file</span>";
                                    }
                                    echo "</td>";
                                    echo "<td class='action-buttons'>";
                                    echo "<a href='view_event.php?id=" . $row['event_id'] . "' class='btn btn-info'>Lihat</a> ";
                                    echo "<a href='edit_event.php?id=" . $row['event_id'] . "' class='btn btn-warning'>Edit</a> ";
                                    echo "<a href='delete_event.php?id=" . $row['event_id'] . "' class='btn btn-danger' onclick='return confirm(\"Apakah Anda yakin ingin menghapus event ini?\")'>Hapus</a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7' class='text-center'>Tidak ada event yang ditemukan</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
