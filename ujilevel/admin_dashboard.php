<?php
session_start();
require 'database/config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Handle date filter
$start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
$end_date = $_GET['end_date'] ?? date('Y-m-d');
$sort = $_GET['sort'] ?? 'desc';
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 5;
$offset = ($page - 1) * $per_page;

// Build WHERE clause for date range
$where = "WHERE tanggal_pengajuan BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59'";

// Get statistics
$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM event_pengajuan"))['total'];
$pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM event_pengajuan WHERE status = 'pending'"))['total'];
$approved = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM event_pengajuan WHERE status = 'approved'"))['total'];
$rejected = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM event_pengajuan WHERE status = 'rejected'"))['total'];
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users"))['total'];
$admins = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'admin'"))['total'];
$regular_users = $total_users - $admins;

// Get recent applications with pagination
$total_rows = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM event_pengajuan $where"))['total'];
$total_pages = ceil($total_rows / $per_page);
$recent_events = mysqli_query($conn, "SELECT * FROM event_pengajuan $where ORDER BY tanggal_pengajuan $sort LIMIT $offset, $per_page") or die("Error in query: " . mysqli_error($conn));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
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

        .hamburger span:nth-child(1) { top: 6px; transform: rotate(0deg); }
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

        .action-btn {
            padding: 0.375rem 0.75rem;
            font-size: 0.85rem;
            border-radius: 0.375rem;
            background: var(--primary-color);
            color: white;
            border: none;
        }

        .action-btn:hover {
            background: #3b4a99;
        }

        /* Date Filter */
        .date-filter {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .date-filter .form-label {
            margin: 0;
            font-size: 0.9rem;
            font-weight: 500;
            color: #4b5563;
        }

        .date-filter .form-control {
            font-size: 0.9rem;
            padding: 0.5rem;
            border-radius: 0.375rem;
            border: 1px solid var(--border-color);
            max-width: 150px;
        }

        /* Modal */
        .modal-content {
            border-radius: 0.5rem;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow);
        }

        .modal-header {
            background: var(--primary-color);
            color: white;
            border-bottom: none;
            padding: 1rem;
        }

        .modal-title {
            font-size: 1.1rem;
            font-weight: 500;
        }

        .modal-body {
            padding: 1.25rem;
        }

        .modal .form-label {
            font-size: 0.9rem;
            font-weight: 500;
            color: #4b5563;
        }

        .modal .form-control,
        .modal .form-select {
            font-size: 0.9rem;
            padding: 0.5rem;
            border-radius: 0.375rem;
        }

        /* Card Footer for Detail Button */
        .recent-table .card-footer {
            display: flex;
            justify-content: flex-end;
            padding: 0.75rem;
            background: none;
            border-top: 1px solid var(--border-color);
        }

        /* Typography */
        h2 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.25rem;
        }

        h5 {
            font-size: 1.1rem;
            font-weight: 500;
            margin-bottom: 1rem;
        }

        /* Alerts */
        .alert-success {
            border-radius: 0.375rem;
            padding: 0.75rem;
            font-size: 0.9rem;
            box-shadow: var(--shadow);
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.75rem;
            margin-top: 1.5rem;
        }

        .pagination .page-info {
            font-size: 0.9rem;
            color: #4b5563;
        }

        /* Responsive Styles */
        @media (max-width: 991px) {
            .main-content {
                margin-left: 0;
                padding-top: 3.5rem;
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

            h5 {
                font-size: 1rem;
            }

            .stat-card .card-value {
                font-size: 1.5rem;
            }

            .table th,
            .table td {
                font-size: 0.85rem;
                padding: 0.5rem;
            }

            .action-btn {
                font-size: 0.8rem;
                padding: 0.25rem 0.5rem;
            }

            .modal-body {
                padding: 1rem;
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
                width: 100%;
            }
        }

        @media (max-width: 576px) {
            .pagination .btn {
                padding: 0.375rem 0.75rem;
                font-size: 0.85rem;
            }

            .pagination .page-info {
                font-size: 0.8rem;
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
            <li class="active">
                <a href="admin_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
            </li>
            <li>
                <a href="admin_event.php"><i class="fas fa-check-circle"></i> Approval Kegiatan</a>
            </li>
            <li>
                <a href="admin_users.php"><i class="fas fa-users"></i> Manajemen User</a>
            </li>
            <li>
                <a href="admin_reports.php"><i class="fas fa-file-alt"></i> Laporan</a>
            </li>
            <li>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
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
            <h2>Dashboard Admin</h2>

            <!-- Statistik Pengajuan -->
            <div class="card">
                <div class="card-body">
                    <h5>Statistik Pengajuan</h5>
                    <div class="row g-3">
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="stat-card">
                                <h6 class="card-title">Total Pengajuan</h6>
                                <div class="card-value"><?php echo $total; ?></div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="stat-card">
                                <h6 class="card-title">Pending</h6>
                                <div class="card-value"><?php echo $pending; ?></div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="stat-card">
                                <h6 class="card-title">Approved</h6>
                                <div class="card-value"><?php echo $approved; ?></div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="stat-card">
                                <h6 class="card-title">Rejected</h6>
                                <div class="card-value"><?php echo $rejected; ?></div>
                            </div>
                        </div>
                    </div>
                    <a href="admin_event.php" class="btn btn-primary mt-3">Lihat Detail</a>
                </div>
            </div>

            <!-- Statistik Pengguna -->
            <div class="card">
                <div class="card-body">
                    <h5>Statistik Pengguna</h5>
                    <div class="row g-3">
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="stat-card">
                                <h6 class="card-title">Total User</h6>
                                <div class="card-value"><?php echo $total_users; ?></div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="stat-card">
                                <h6 class="card-title">Admin</h6>
                                <div class="card-value"><?php echo $admins; ?></div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="stat-card">
                                <h6 class="card-title">User</h6>
                                <div class="card-value"><?php echo $regular_users; ?></div>
                            </div>
                        </div>
                    </div>
                    <a href="admin_users.php" class="btn btn-primary mt-3">Lihat Detail</a>
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

            <!-- Pengajuan Terbaru -->
            <div class="card recent-table">
                <div class="card-body">
                    <h5>Pengajuan Terbaru</h5>
                    <div class="table-responsive">
                        <table class="table" id="eventTable">
                            <thead>
                                <tr>
                                    <th scope="col">Judul</th>
                                    <th scope="col">Jenis</th>
                                    <th scope="col">Pembiayaan</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Tanggal</th>
                                    <th scope="col">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($event = mysqli_fetch_assoc($recent_events)): ?>
                                <tr data-event-id="<?php echo $event['event_id']; ?>">
                                    <td><?php echo htmlspecialchars($event['judul_event']); ?></td>
                                    <td><?php echo htmlspecialchars($event['jenis_kegiatan']); ?></td>
                                    <td>Rp <?php echo number_format($event['total_pembiayaan'], 0, ',', '.'); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $event['status']; ?>">
                                            <?php echo ucfirst($event['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($event['tanggal_pengajuan'])); ?></td>
                                    <td>
                                        <button class="btn btn-sm action-btn review-btn" data-event-id="<?php echo $event['event_id']; ?>">
                                            <i class="fas fa-eye"></i> Review
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                                <?php if (!mysqli_num_rows($recent_events)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada pengajuan terbaru</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="admin_event.php" class="btn btn-primary btn-sm">Lihat Detail</a>
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

    <!-- Review Modal -->
    <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reviewModalLabel">Review Pengajuan: <span id="modalTitleEvent"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="reviewForm">
                        <input type="hidden" name="event_id" id="event_id">
                        <div class="row mb-3">
                            <div class="col-12 col-md-6 mb-3 mb-md-0">
                                <label class="form-label">Judul Event</label>
                                <input type="text" class="form-control" id="judul_event" readonly>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Ekstrakulikuler</label>
                                <input type="text" class="form-control" id="ekskul" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12 col-md-6 mb-3 mb-md-0">
                                <label class="form-label">Jenis Kegiatan</label>
                                <input type="text" class="form-control" id="jenis_kegiatan" readonly>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Total Pembiayaan</label>
                                <input type="text" class="form-control" id="total_pembiayaan" readonly>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" rows="3" readonly></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Proposal</label>
                            <div id="proposal_link"></div>
                        </div>
                        <hr class="my-3">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" id="statusSelect" class="form-select" required>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan Admin</label>
                            <textarea name="catatan" id="catatan" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
                            </button>
                            <button type="button" class="btn btn-primary" id="saveReview">
                                <i class="fas fa-save me-1"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize datepickers
            $('#start_date, #end_date').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
                orientation: 'bottom auto'
            });

            // Sidebar and backdrop toggle
            function toggleSidebar() {
                $('.sidebar').toggleClass('active');
                $('.backdrop').toggleClass('active');
                $('.hamburger').animate({ scale: '1.05' }, 100).animate({ scale: '1' }, 100);
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

            // Hide hamburger in mobile view when modal is shown
            $('#reviewModal').on('show.bs.modal', function () {
                if ($(window).width() < 992) {
                    $('.hamburger').css('display', 'none');
                }
            });

            // Show hamburger in mobile view when modal is hidden
            $('#reviewModal').on('hide.bs.modal', function () {
                if ($(window).width() < 992) {
                    $('.hamburger').css('display', 'block');
                }
            });

            // Toggle admin notes based on status
            function toggleAdminNotes() {
                const status = $('#statusSelect').val();
                const $catatan = $('#catatan');
                if (status === 'rejected' || status === 'pending') {
                    $catatan.prop('disabled', false);
                } else {
                    $catatan.prop('disabled', true);
                    $catatan.val(''); // Clear notes when disabled
                }
            }

            // Bind toggleAdminNotes to status change
            $('#statusSelect').change(toggleAdminNotes);

            // Review button handler
            $('.review-btn').click(function() {
                const eventId = $(this).data('event-id');
                $.ajax({
                    url: 'fetch_event.php',
                    type: 'GET',
                    data: { id: eventId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.error) {
                            alert(response.error);
                            return;
                        }
                        $('#event_id').val(response.event_id);
                        $('#judul_event').val(response.judul_event);
                        $('#ekskul').val(response.ekskul);
                        $('#jenis_kegiatan').val(response.jenis_kegiatan);
                        $('#total_pembiayaan').val('Rp ' + response.total_pembiayaan);
                        $('#deskripsi').val(response.deskripsi);
                        $('#statusSelect').val(response.status);
                        $('#catatan').val(response.catatan_admin);
                        $('#modalTitleEvent').text(response.judul_event);
                        $('#proposal_link').html(response.proposal
                            ? `<a href="Uploads/${response.proposal}" target="_blank" class="btn btn-sm btn-info"><i class="fas fa-file-pdf me-1"></i> Lihat Proposal</a>`
                            : '<p class="text-muted">Tidak ada proposal</p>');
                        toggleAdminNotes(); // Set initial state based on status
                        $('#reviewModal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        alert('Gagal memuat detail: ' + error);
                    }
                });
            });

            // Save review handler
            $('#saveReview').click(function() {
                const $button = $(this);
                $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...');

                // Enable catatan field before serialization to ensure it's included
                $('#catatan').prop('disabled', false);
                const formData = $('#reviewForm').serialize();
                // Re-disable catatan if necessary after serialization
                toggleAdminNotes();

                $.ajax({
                    url: 'admin_approval.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.error) {
                            alert(response.error);
                            return;
                        }
                        $('#reviewModal').modal('hide');
                        $('.container-fluid').prepend('<div class="alert alert-success">Pengajuan berhasil diproses!</div>');
                        const eventId = $('#event_id').val();
                        const newStatus = $('#statusSelect').val();
                        $(`tr[data-event-id="${eventId}"] .status-badge`)
                            .removeClass('status-pending status-approved status-rejected')
                            .addClass(`status-${newStatus}`)
                            .text(newStatus.charAt(0).toUpperCase() + newStatus.slice(1));
                        setTimeout(() => $('.alert-success').fadeOut('slow', function() { $(this).remove(); }), 3000);
                    },
                    error: function(xhr, status, error) {
                        alert('Gagal menyimpan: ' + error);
                    },
                    complete: function() {
                        $button.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Simpan');
                    }
                });
            });

            // Date filter functions
            window.filterByDate = function() {
                const startDate = $('#start_date').val();
                const endDate = $('#end_date').val();
                window.location.href = `?start_date=${startDate}&end_date=${endDate}&sort=<?php echo $sort; ?>&page=1`;
            };

            window.resetFilter = function() {
                const defaultStartDate = '<?php echo date('Y-m-d', strtotime('-30 days')); ?>';
                const defaultEndDate = '<?php echo date('Y-m-d'); ?>';
                window.location.href = `?start_date=${defaultStartDate}&end_date=${defaultEndDate}&sort=desc&page=1`;
            };
        });
    </script>
</body>
</html>