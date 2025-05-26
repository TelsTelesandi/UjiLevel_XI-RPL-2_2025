<?php
session_start();
require 'database/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = (int)$_SESSION['user_id']; // Cast to integer for safety

// Fetch user details
$user_query = mysqli_query($conn, "SELECT nama_lengkap, username, ekskul FROM users WHERE user_id = $user_id");
if (!$user_query) {
    die("Error fetching user details: " . mysqli_error($conn));
}
$user = mysqli_fetch_assoc($user_query);
if (!$user) {
    die("User not found.");
}
$nama_lengkap = htmlspecialchars($user['nama_lengkap']);
$username = htmlspecialchars($user['username']);
$ekskul = htmlspecialchars($user['ekskul'] ?? 'Tidak ada ekskul');

// Function to handle file uploads
if (!function_exists('handleFileUpload')) {
    function handleFileUpload($file) {
        if (empty($file['name'])) {
            return ['success' => true, 'filename' => null];
        }

        $target_dir = "Uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        $target_file = $target_dir . basename($file['name']);
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validate file type and size (max 5MB)
        if ($fileType !== 'pdf') {
            return ['success' => false, 'error' => 'File harus berformat PDF!'];
        }
        if ($file['size'] > 5 * 1024 * 1024) {
            return ['success' => false, 'error' => 'Ukuran file maksimal 5MB!'];
        }

        // Check MIME type for additional security
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        if ($mime !== 'application/pdf') {
            return ['success' => false, 'error' => 'File harus berupa PDF yang valid!'];
        }

        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            return ['success' => true, 'filename' => basename($file['name'])];
        }
        return ['success' => false, 'error' => 'Gagal mengunggah file!'];
    }
}

// Handle form submission for new event request
$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $judul_event = mysqli_real_escape_string($conn, $_POST['judul_event']);
    $jenis_kegiatan = mysqli_real_escape_string($conn, $_POST['jenis_kegiatan']);
    $total_pembiayaan = (float)$_POST['total_pembiayaan'];
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi'] ?? '');

    $uploadResult = handleFileUpload($_FILES['proposal'] ?? []);
    if ($uploadResult['success']) {
        $proposal = $uploadResult['filename'];
        $sql = "INSERT INTO event_pengajuan (user_id, judul_event, jenis_kegiatan, total_pembiayaan, deskripsi, proposal, tanggal_pengajuan, status) 
                VALUES (?, ?, ?, ?, ?, ?, NOW(), 'pending')";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'issdss', $user_id, $judul_event, $jenis_kegiatan, $total_pembiayaan, $deskripsi, $proposal);
        if (mysqli_stmt_execute($stmt)) {
            $success = '<div class="alert alert-success">Pengajuan berhasil ditambahkan!</div>';
        } else {
            $error = '<div class="alert alert-danger">Gagal menambahkan pengajuan: ' . mysqli_error($conn) . '</div>';
        }
        mysqli_stmt_close($stmt);
    } else {
        $error = '<div class="alert alert-danger">' . $uploadResult['error'] . '</div>';
    }
    // Redirect to refresh the page and prevent form resubmission
    header("Location: user_dashboard.php");
    exit();
}

// Handle status update to closed
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $event_id = (int)$_POST['event_id'];
    // Add condition to prevent closing if status is 'pending'
    $sql = "UPDATE event_pengajuan SET status = 'closed' WHERE event_id = ? AND user_id = ? AND status != 'pending'";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ii', $event_id, $user_id);
    if (mysqli_stmt_execute($stmt)) {
        $success = '<div class="alert alert-success">Status berhasil diubah ke closed!</div>';
    } else {
        $error = '<div class="alert alert-danger">Gagal mengubah status: ' . mysqli_error($conn) . '</div>';
    }
    mysqli_stmt_close($stmt);
    // Redirect to refresh the page
    header("Location: user_dashboard.php");
    exit();
}

// Get statistics
function getCount($conn, $sql) {
    $result = mysqli_query($conn, $sql);
    return $result ? mysqli_fetch_assoc($result)['total'] : 0;
}

$total = getCount($conn, "SELECT COUNT(*) as total FROM event_pengajuan WHERE user_id = $user_id");
$pending = getCount($conn, "SELECT COUNT(*) as total FROM event_pengajuan WHERE user_id = $user_id AND status = 'pending'");
$approved = getCount($conn, "SELECT COUNT(*) as total FROM event_pengajuan WHERE user_id = $user_id AND status = 'approved'");
$rejected = getCount($conn, "SELECT COUNT(*) as total FROM event_pengajuan WHERE user_id = $user_id AND status = 'rejected'");
$closed = getCount($conn, "SELECT COUNT(*) as total FROM event_pengajuan WHERE user_id = $user_id AND status = 'closed'");

// Handle date filter for user events
$start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days')); // Default: last 30 days
$end_date = $_GET['end_date'] ?? date('Y-m-d'); // Default: today
$sort = $_GET['sort'] ?? 'desc'; // Default: newest first
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1; // Current page, default to 1
$per_page = 5; // Number of records per page
$offset = ($page - 1) * $per_page; // Calculate offset for LIMIT

// Build WHERE clause for date range and user
$where = "WHERE e.user_id = $user_id AND tanggal_pengajuan BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59'";

// Get total number of user events
$total_events_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM event_pengajuan e $where");
$total_rows = mysqli_fetch_assoc($total_events_query)['total'];
$total_pages = ceil($total_rows / $per_page);

// Get user events with pagination
$events_query = mysqli_query($conn, "SELECT e.*, v.catatan_admin 
                                    FROM event_pengajuan e 
                                    LEFT JOIN verifikasi_event v ON e.event_id = v.event_id 
                                    AND v.verifikasi_id = (SELECT MAX(verifikasi_id) FROM verifikasi_event WHERE event_id = e.event_id)
                                    $where 
                                    ORDER BY e.tanggal_pengajuan $sort 
                                    LIMIT $offset, $per_page");
if (!$events_query) {
    die("Error in query: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengguna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet">
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

        /* Base Styles */
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

        /* Layout */
        .main-content {
            margin-left: 220px;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
            padding: 1.5rem;
        }

        .container-fluid {
            padding: 0;
        }

        /* Welcome Card */
        .welcome-card {
            background: var(--card-bg);
            border-radius: 0.5rem;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            gap: 1rem;
            position: relative;
            overflow: hidden;
        }

        .welcome-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--gradient-bg);
            opacity: 0.1;
            z-index: 0;
        }

        .welcome-card > * {
            position: relative;
            z-index: 1;
        }

        .avatar {
            width: 50px;
            height: 50px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 700;
            color: #fff;
            text-transform: uppercase;
            flex-shrink: 0;
        }

        .welcome-text h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
            color: var(--text-color);
        }

        .welcome-text p {
            font-size: 0.9rem;
            margin: 0.25rem 0 0;
            color: #6b7280;
        }

        /* Cards */
        .card {
            background: var(--card-bg);
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

        .btn-primary:disabled {
            background: #94a3b8;
            cursor: not-allowed;
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

        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.85rem;
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
            display: inline-block;
        }

        .status-pending { background: #fef3c7; color: #92400e; }
        .status-approved { background: #d1fae5; color: #065f46; }
        .status-rejected { background: #fee2e2; color: #991b1b; }
        .status-closed { background: #d1fae5; color: #065f46; }

        /* Alerts */
        .alert {
            border-radius: 0.375rem;
            padding: 0.75rem;
            font-size: 0.9rem;
            box-shadow: var(--shadow);
        }

        /* Typography */
        h2 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.25rem;
        }

        h4 {
            font-size: 1.1rem;
            font-weight: 500;
            margin-bottom: 1rem;
        }

        .form-label {
            font-size: 0.9rem;
            font-weight: 500;
            color: #4b5563;
        }

        .form-control, .form-select {
            border-radius: 0.375rem;
            border: 1px solid var(--border-color);
            padding: 0.5rem;
            font-size: 0.9rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 5px rgba(92, 107, 192, 0.3);
        }

        /* Date Filter */
        .date-filter {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .date-filter .form-control {
            max-width: 150px;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.75rem;
            margin-top: 1.5rem;
        }

        .pagination .btn {
            padding: 0.375rem 0.75rem;
            font-size: 0.9rem;
        }

        .pagination .page-info {
            font-size: 0.9rem;
            color: #4b5563;
        }

        /* Mobile Responsive Styles */
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

            .mobile-menu {
                display: none; /* Hide mobile menu as sidebar is used */
            }
        }

        @media (max-width: 767px) {
            .container-fluid {
                padding: 1rem;
            }

            h2 {
                font-size: 1.3rem;
            }

            h4 {
                font-size: 1rem;
            }

            .welcome-card {
                flex-direction: column;
                align-items: flex-start;
                padding: 1rem;
            }

            .welcome-text h3 {
                font-size: 1.2rem;
            }

            .welcome-text p {
                font-size: 0.85rem;
            }

            .avatar {
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
            }

            .stat-card .card-value {
                font-size: 1.5rem;
            }

            .form-control, .form-select {
                font-size: 0.85rem;
                padding: 0.5rem;
            }

            .btn-primary, .btn-secondary {
                font-size: 0.85rem;
                padding: 0.5rem 1rem;
                width: 100%;
            }

            .date-filter {
                flex-direction: column;
                align-items: stretch;
            }

            .date-filter .form-control {
                max-width: none;
            }

            .date-filter button {
                margin-top: 0.5rem;
            }

            /* Responsive Table */
            .recent-table .table {
                display: none;
            }

            .recent-table .table-responsive {
                padding: 0;
            }

            .event-card {
                display: block;
                background: #fff;
                border: 1px solid var(--border-color);
                border-radius: 0.5rem;
                padding: 1rem;
                margin-bottom: 1rem;
                box-shadow: var(--shadow);
            }

            .event-card p {
                margin: 0.5rem 0;
                font-size: 0.85rem;
            }

            .event-card .status-badge {
                display: inline-block;
                margin: 0.5rem 0;
            }

            .event-card .action-buttons {
                margin-top: 0.5rem;
            }

            .event-card .action-buttons .btn {
                width: 100%;
            }
        }

        @media (max-width: 576px) {
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

            .pagination .btn {
                font-size: 0.8rem;
            }

            .pagination .page-info {
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <!-- Backdrop -->
    <div class="backdrop"></div>

    <!-- Hamburger Menu -->
    <button class="hamburger" aria-label="Buka menu navigasi">
        <span></span>
        <span></span>
        <span></span>
    </button>

    <!-- Sidebar -->
    <div class="sidebar" aria-label="Navigasi utama">
        <div class="sidebar-header">
            <img src="resource/logo.png" alt="Logo">
        </div>
        <ul class="sidebar-menu">
            <li class="active">
                <a href="user_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
            </li>
            <li>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <h2>Dashboard Pengguna</h2>

            <?php echo $success; ?>
            <?php echo $error; ?>

            <!-- Welcome Card -->
            <div class="welcome-card">
                <?php
                $name_parts = explode(' ', $nama_lengkap);
                $initials = '';
                foreach ($name_parts as $part) {
                    if (trim($part) !== '') {
                        $initials .= strtoupper(substr($part, 0, 1));
                    }
                }
                ?>
                <div class="avatar"><?= $initials ?: strtoupper(substr($username, 0, 1)) ?></div>
                <div class="welcome-text">
                    <h3>Selamat Datang, <?= $nama_lengkap ?>!</h3>
                    <p><strong>Username:</strong> @<?= $username ?></p>
                    <p><strong>Ekstrakurikuler:</strong> <?= $ekskul ?></p>
                    <p><strong>Waktu:</strong> <?= date('l, d F Y, H:i', time()) ?> WIB</p>
                </div>
            </div>

            <!-- Stat Cards -->
            <div class="card">
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

            <!-- Button to Toggle New Event Form -->
            <div class="mb-4">
                <button type="button" class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#newEventForm" aria-expanded="false" aria-controls="newEventForm">
                    <i class="fas fa-plus me-2"></i>Buat Pengajuan Baru
                </button>
            </div>

            <!-- Collapsible New Event Form -->
            <div class="collapse collapse-form" id="newEventForm">
                <div class="card">
                    <div class="card-body">
                        <h4>Buat Pengajuan Baru</h4>
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="create">
                            <div class="mb-3">
                                <label class="form-label">Judul Event</label>
                                <input type="text" name="judul_event" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jenis Kegiatan</label>
                                <input type="text" name="jenis_kegiatan" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Total Pembiayaan (Rp)</label>
                                <input type="number" name="total_pembiayaan" class="form-control" min="0" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Unggah Proposal (PDF, maks 5MB)</label>
                                <input type="file" name="proposal" class="form-control" accept=".pdf">
                            </div>
                            <button type="submit" class="btn btn-primary">Submit Pengajuan</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Date Filter -->
            <div class="date-filter">
                <label for="start_date" class="form-label">Tanggal Mulai:</label>
                <input type="date" id="start_date" name="start_date" value="<?php echo $start_date; ?>" class="form-control">
                <label for="end_date" class="form-label">Tanggal Selesai:</label>
                <input type="date" id="end_date" name="end_date" value="<?php echo $end_date; ?>" class="form-control">
                <button onclick="filterByDate()" class="btn btn-primary">Filter</button>
                <button onclick="resetFilter()" class="btn btn-secondary">Reset</button>
            </div>

            <!-- Recent Applications -->
            <div class="card recent-table">
                <div class="card-header">
                    <h4>Daftar Pengajuan Saya</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Jenis</th>
                                    <th>Pembiayaan</th>
                                    <th>Deskripsi</th>
                                    <th>Status</th>
                                    <th>Catatan Admin</th>
                                    <th>Proposal</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($event = mysqli_fetch_assoc($events_query)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($event['judul_event']) ?></td>
                                    <td><?= htmlspecialchars($event['jenis_kegiatan']) ?></td>
                                    <td>Rp <?= number_format($event['total_pembiayaan'], 0, ',', '.') ?></td>
                                    <td><?= htmlspecialchars($event['deskripsi'] ?? '-') ?></td>
                                    <td>
                                        <span class="status-badge status-<?= htmlspecialchars($event['status']) ?>">
                                            <?= ucfirst(htmlspecialchars($event['status'])) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($event['catatan_admin'] ?? '-') ?></td>
                                    <td>
                                        <?php if ($event['proposal']): ?>
                                            <a href="Uploads/<?= htmlspecialchars($event['proposal']) ?>" target="_blank" class="btn btn-sm btn-secondary">Lihat</a>
                                        <?php else: ?>
                                            <span class="text-muted">Tidak ada</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($event['tanggal_pengajuan'])) ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <?php if ($event['status'] !== 'closed'): ?>
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="action" value="update_status">
                                                    <input type="hidden" name="event_id" value="<?= $event['event_id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-primary" <?= $event['status'] === 'pending' ? 'disabled' : '' ?>>Tandai Selesai</button>
                                                </form>
                                            <?php else: ?>
                                                <span class="text-muted">Selesai</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Mobile Card View -->
                                <div class="event-card d-md-none">
                                    <p><strong>Judul:</strong> <?= htmlspecialchars($event['judul_event']) ?></p>
                                    <p><strong>Jenis:</strong> <?= htmlspecialchars($event['jenis_kegiatan']) ?></p>
                                    <p><strong>Pembiayaan:</strong> Rp <?= number_format($event['total_pembiayaan'], 0, ',', '.') ?></p>
                                    <p><strong>Deskripsi:</strong> <?= htmlspecialchars($event['deskripsi'] ?? '-') ?></p>
                                    <p><strong>Status:</strong> 
                                        <span class="status-badge status-<?= htmlspecialchars($event['status']) ?>">
                                            <?= ucfirst(htmlspecialchars($event['status'])) ?>
                                        </span>
                                    </p>
                                    <p><strong>Catatan Admin:</strong> <?= htmlspecialchars($event['catatan_admin'] ?? '-') ?></p>
                                    <p><strong>Proposal:</strong> 
                                        <?php if ($event['proposal']): ?>
                                            <a href="Uploads/<?= htmlspecialchars($event['proposal']) ?>" target="_blank" class="btn btn-sm btn-secondary">Lihat</a>
                                        <?php else: ?>
                                            <span class="text-muted">Tidak ada</span>
                                        <?php endif; ?>
                                    </p>
                                    <p><strong>Tanggal:</strong> <?= date('d/m/Y', strtotime($event['tanggal_pengajuan'])) ?></p>
                                    <div class="action-buttons">
                                        <?php if ($event['status'] !== 'closed'): ?>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="action" value="update_status">
                                                <input type="hidden" name="event_id" value="<?= $event['event_id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-primary" <?= $event['status'] === 'pending' ? 'disabled' : '' ?>>Tandai Selesai</button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted">Selesai</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                                <?php if (mysqli_num_rows($events_query) == 0): ?>
                                <tr>
                                    <td colspan="9" class="text-center">Tidak ada pengajuan</td>
                                </tr>
                                <div class="event-card d-md-none text-center">
                                    <p>Tidak ada pengajuan</p>
                                </div>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>&sort=<?php echo $sort; ?>&page=<?php echo $page - 1; ?>" class="btn btn-secondary">Previous</a>
                <?php endif; ?>
                <span class="page-info">Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>
                <?php if ($page < $total_pages): ?>
                    <a href="?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>&sort=<?php echo $sort; ?>&page=<?php echo $page + 1; ?>" class="btn btn-primary">Next</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize datepicker
            $('#start_date, #end_date').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
                orientation: 'bottom auto' // Ensure datepicker is usable on mobile
            });

            // Filter by date
            window.filterByDate = function() {
                const startDate = $('#start_date').val();
                const endDate = $('#end_date').val();
                const url = `?start_date=${startDate}&end_date=${endDate}&sort=<?php echo $sort; ?>&page=1`;
                window.location.href = url;
            };

            // Reset filter
            window.resetFilter = function() {
                const defaultStartDate = '<?php echo date('Y-m-d', strtotime('-30 days')); ?>';
                const defaultEndDate = '<?php echo date('Y-m-d'); ?>';
                const url = `?start_date=${defaultStartDate}&end_date=${defaultEndDate}&sort=desc&page=1`;
                window.location.href = url;
            };

            // Sidebar and backdrop toggle
            function toggleSidebar() {
                $('.sidebar').toggleClass('active');
                $('.backdrop').toggleClass('active');
                $('.hamburger').animate({ scale: '1.05' }, 100).animate({ scale: '1' }, 100); // Pulse effect
            }

            $('.hamburger').click(toggleSidebar);
            $('.backdrop').click(toggleSidebar);

            // Close sidebar on menu item click (mobile)
            $('.sidebar-menu li a').click(function() {
                if ($(window).width() < 992) {
                    toggleSidebar();
                }
            });

            // Sidebar active state
            $('.sidebar-menu li').click(function() {
                $('.sidebar-menu li').removeClass('active');
                $(this).addClass('active');
            });

            // Auto-dismiss alerts after 3 seconds
            setTimeout(() => {
                $('.alert').fadeOut('slow', function() { $(this).remove(); });
            }, 3000);
        });
    </script>
</body>
</html>