<?php
session_start();
require 'database/config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Handle date filter
$start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days')); // Default: last 30 days
$end_date = $_GET['end_date'] ?? date('Y-m-d'); // Default: today
$sort = $_GET['sort'] ?? 'desc'; // Default: newest first
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1; // Current page, default to 1
$per_page = 5; // Number of records per page
$offset = ($page - 1) * $per_page; // Calculate offset for LIMIT

// Build WHERE clause for date range
$where = "WHERE e.tanggal_pengajuan BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59'";

// Build ORDER BY clause for sorting
$order = "ORDER BY e.tanggal_pengajuan $sort";

// Get total number of records
$total_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM event_pengajuan e JOIN users u ON e.user_id = u.user_id $where");
$total_rows = mysqli_fetch_assoc($total_query)['total'];
$total_pages = ceil($total_rows / $per_page);

// Ambil data untuk daftar pengajuan with pagination
$events_query = mysqli_query($conn, "SELECT e.*, u.nama_lengkap, u.ekskul 
                                    FROM event_pengajuan e 
                                    JOIN users u ON e.user_id = u.user_id 
                                    $where 
                                    $order 
                                    LIMIT $offset, $per_page");

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
    <title>Approval Kegiatan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #5C6BC0;
            --secondary-color: #90a4ae;
            --card-bg: #ffffff;
            --text-color: #2d3748;
            --shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            --border-color: #e0e0e0;
            --gradient-bg: linear-gradient(135deg, #6B48FF, #42A5F5);
        }

        body {
            font-family: 'Inter', 'Roboto', system-ui, -apple-system, sans-serif;
            background: #f8f9fa;
            margin: 0;
            padding: 0;
            color: var(--text-color);
            font-size: 0.95rem;
            line-height: 1.7;
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
            height: 100vh;
            background: #ffffff;
            color: #333;
            position: fixed;
            width: 220px;
            transition: all 0.3s;
            z-index: 1000;
            border-right: 1px solid var(--border-color);
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.05);
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid var(--border-color);
            background: var(--gradient-bg);
        }

        .sidebar-header img {
            max-width: 100%;
            height: auto;
        }

        .sidebar-menu {
            padding: 0;
            list-style: none;
        }

        .sidebar-menu li {
            padding: 12px 20px;
            transition: all 0.3s ease;
        }

        .sidebar-menu li:hover {
            background: #e8ecef;
            border-left: 4px solid var(--primary-color);
        }

        .sidebar-menu li a {
            color: var(--text-color);
            text-decoration: none;
            display: flex;
            align-items: center;
            font-size: 1rem;
            font-weight: 500;
        }

        .sidebar-menu li i {
            margin-right: 15px;
            width: 20px;
            text-align: center;
            color: var(--primary-color);
        }

        .sidebar-menu li.active {
            background: #e0e7ff;
            border-left: 4px solid var(--primary-color);
        }

        /* Backdrop for mobile */
        .backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .backdrop.active {
            display: block;
            opacity: 1;
        }

        /* Main Content */
        .main-content {
            margin-left: 220px;
            padding: 20px;
            min-height: 100vh;
            transition: all 0.3s;
        }

        /* Cards */
        .card {
            background: var(--card-bg);
            border-radius: 8px;
            border: 1px solid var(--border-color);
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: var(--shadow);
        }

        /* Table */
        .recent-table th[role="columnheader"] {
            cursor: pointer;
        }

        .recent-table th[role="columnheader"]:hover {
            background-color: #e0e7ff;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }

        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }

        .action-btn {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            background: var(--primary-color);
            color: white;
            border: none;
            transition: background 0.3s;
        }

        .action-btn:hover {
            background: #4b5aaf;
        }

        .btn-primary {
            background: var(--primary-color);
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            color: white;
            font-size: 0.9rem;
            font-weight: 500;
            transition: background 0.3s;
        }

        .btn-primary:hover {
            background: #4b5aaf;
        }

        .btn-reset {
            background: #dc3545;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            color: white;
            font-size: 0.9rem;
            font-weight: 500;
            transition: background 0.3s;
        }

        .btn-reset:hover {
            background: #c82333;
        }

        h2 {
            color: var(--text-color);
            font-size: 1.5rem;
            font-weight: 500;
            margin-bottom: 20px;
        }

        .date-filter {
            margin-bottom: 20px;
        }

        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
        }

        .pagination .btn {
            padding: 5px 15px;
        }

        .pagination .page-info {
            font-size: 0.9rem;
            color: var(--text-color);
        }

        /* Modal-specific styles */
        .modal-content {
            background: var(--card-bg);
            border-radius: 8px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow);
        }

        .modal-header {
            background: var(--primary-color);
            color: white;
            border-bottom: 1px solid var(--border-color);
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 500;
        }

        .modal-body {
            padding: 15px;
        }

        .form-label {
            font-size: 0.9rem;
            font-weight: 500;
            color: #555;
        }

        .form-control, .form-select {
            border-radius: 5px;
            border: 1px solid var(--border-color);
            padding: 8px;
            font-size: 0.9rem;
            font-weight: 400;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 5px rgba(75, 90, 175, 0.3);
        }

        .alert-success {
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 15px;
            font-size: 0.9rem;
            font-weight: 400;
            box-shadow: var(--shadow);
        }

        /* Responsive Styles */
        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
                width: 260px;
                z-index: 1001;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding-top: 60px; /* Space for hamburger icon */
            }

            .hamburger {
                display: block;
            }

            .date-filter {
                flex-direction: column;
                align-items: stretch;
            }

            .date-filter label {
                margin-bottom: 5px;
            }

            .date-filter .form-control {
                width: 100%;
                margin-right: 0;
                margin-bottom: 10px;
            }

            .date-filter button {
                width: 100%;
            }

            .table th, .table td {
                font-size: 0.85rem;
                padding: 8px;
            }

            .action-btn {
                font-size: 11px;
                padding: 4px 8px;
            }
        }

        @media (max-width: 576px) {
            h2 {
                font-size: 1.2rem;
            }

            .pagination .btn {
                padding: 4px 10px;
                font-size: 0.8rem;
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
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="resource/logo.png" alt="Logo">
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="admin_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
            </li>
            <li class="active">
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
            <h2>Approval Kegiatan</h2>

            <!-- Date Filter -->
            <div class="date-filter">
                <label for="start_date">Tanggal Mulai:</label>
                <input type="date" id="start_date" name="start_date" value="<?php echo $start_date; ?>" class="form-control d-inline-block" style="width: 150px; margin-right: 10px;">
                <label for="end_date">Tanggal Selesai:</label>
                <input type="date" id="end_date" name="end_date" value="<?php echo $end_date; ?>" class="form-control d-inline-block" style="width: 150px; margin-right: 10px;">
                <button onclick="filterByDate()" class="btn btn-primary">Filter</button>
                <button onclick="resetFilter()" class="btn btn-reset">Reset Filter</button>
            </div>

            <!-- Tabel Pengajuan Event -->
            <div class="card recent-table mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="eventTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Judul</th>
                                    <th>Ekskul</th>
                                    <th>Jenis</th>
                                    <th>Pembiayaan</th>
                                    <th>Status</th>
                                    <th role="columnheader" onclick="sortTable('tanggal_pengajuan')">Tanggal Pengajuan <span id="sortIndicator"><?php echo $sort === 'desc' ? '↓' : '↑'; ?></span></th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($event = mysqli_fetch_assoc($events_query)): ?>
                                <tr data-event-id="<?php echo $event['event_id']; ?>">
                                    <td><?php echo $event['event_id']; ?></td>
                                    <td><?php echo htmlspecialchars($event['judul_event']); ?></td>
                                    <td><?php echo htmlspecialchars($event['ekskul']); ?></td>
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

                                <?php if (mysqli_num_rows($events_query) == 0): ?>
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada data yang ditemukan</td>
                                </tr>
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

    <!-- Review Modal -->
    <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reviewModalLabel">Review Pengajuan: <span id="modalTitleEvent"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <form id="reviewForm">
                                <input type="hidden" name="event_id" id="event_id">

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Judul Event</label>
                                        <input type="text" class="form-control" id="judul_event" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Ekstrakulikuler</label>
                                        <input type="text" class="form-control" id="ekskul" readonly>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Jenis Kegiatan</label>
                                        <input type="text" class="form-control" id="jenis_kegiatan" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Total Pembiayaan</label>
                                        <input type="text" class="form-control" id="total_pembiayaan" readonly>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Deskripsi</label>
                                    <textarea class="form-control" id="deskripsi" rows="3" readonly></textarea>
                                </div>

                                <div class="mb-3" id="proposal_container">
                                    <label class="form-label">Proposal</label>
                                    <div id="proposal_link"></div>
                                </div>

                                <hr>

                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" id="status" class="form-select" required>
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
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </button>
                                    <button type="button" class="btn btn-primary" id="saveReview">
                                        <i class="fas fa-save"></i> Simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize datepicker
            $('#start_date').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true
            });
            $('#end_date').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true
            });

            // Toggle admin notes based on status
            function toggleAdminNotes() {
                const status = $('#status').val();
                const $catatan = $('#catatan');
                if (status === 'rejected' || status === 'pending') {
                    $catatan.prop('disabled', false);
                } else {
                    $catatan.prop('disabled', true);
                    $catatan.val(''); // Clear notes when disabled
                }
            }

            // Bind toggleAdminNotes to status change
            $('#status').change(toggleAdminNotes);

            // Filter by date
            window.filterByDate = function() {
                const startDate = $('#start_date').val();
                const endDate = $('#end_date').val();
                const url = `?start_date=${startDate}&end_date=${endDate}&sort=<?php echo $sort; ?>&page=1`; // Reset to page 1 on filter
                window.location.href = url;
            };

            // Reset filter
            window.resetFilter = function() {
                const defaultStartDate = '<?php echo date('Y-m-d', strtotime('-30 days')); ?>';
                const defaultEndDate = '<?php echo date('Y-m-d'); ?>';
                const url = `?start_date=${defaultStartDate}&end_date=${defaultEndDate}&sort=desc&page=1`;
                window.location.href = url;
            };

            // Sort table by Tanggal Pengajuan
            window.sortTable = function(column) {
                const currentSort = '<?php echo $sort; ?>';
                const newSort = currentSort === 'desc' ? 'asc' : 'desc';
                const url = `?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>&sort=${newSort}&page=1`; // Reset to page 1 on sort
                window.location.href = url;
            };

            // Sidebar toggle
            function toggleSidebar() {
                $('.sidebar').toggleClass('active');
                $('.backdrop').toggleClass('active');
                $('.hamburger').animate({ scale: '1.05' }, 100).animate({ scale: '1' }, 100); // Pulse effect
            }

            $('.hamburger').click(toggleSidebar);
            $('.backdrop').click(toggleSidebar);

            // Close sidebar on menu item click (mobile)
            $('.sidebar-menu li a').click(function() {
                if ($(window).width() < 991) {
                    toggleSidebar();
                }
            });

            // Hide hamburger in mobile view when modal is shown
            $('#reviewModal').on('show.bs.modal', function () {
                if ($(window).width() < 991) {
                    $('.hamburger').css('display', 'none');
                }
            });

            // Show hamburger in mobile view when modal is hidden
            $('#reviewModal').on('hide.bs.modal', function () {
                if ($(window).width() < 991) {
                    $('.hamburger').css('display', 'block');
                }
            });

            // Handle Review button click
            $('.review-btn').click(function() {
                const eventId = $(this).data('event-id');

                // Fetch event details via AJAX
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

                        // Populate modal fields
                        $('#event_id').val(response.event_id);
                        $('#judul_event').val(response.judul_event);
                        $('#ekskul').val(response.ekskul);
                        $('#jenis_kegiatan').val(response.jenis_kegiatan);
                        $('#total_pembiayaan').val('Rp ' + response.total_pembiayaan);
                        $('#deskripsi').val(response.deskripsi);
                        $('#status').val(response.status);
                        $('#catatan').val(response.catatan_admin);
                        $('#modalTitleEvent').text(response.judul_event);

                        // Handle proposal link
                        if (response.proposal) {
                            $('#proposal_link').html(
                                `<a href="Uploads/${response.proposal}" target="_blank" class="btn btn-sm btn-info">
                                    <i class="fas fa-file-pdf"></i> Lihat Proposal
                                </a>`
                            );
                        } else {
                            $('#proposal_link').html('<p class="text-muted">Tidak ada proposal</p>');
                        }

                        // Set initial state for admin notes
                        toggleAdminNotes();

                        // Show modal
                        $('#reviewModal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        alert('Error fetching event details: ' + error);
                    }
                });
            });

            // Handle form submission
            $('#saveReview').click(function() {
                const $button = $(this);
                $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

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

                        // Close modal
                        $('#reviewModal').modal('hide');

                        // Show success message
                        $('.container-fluid').prepend('<div class="alert alert-success">Pengajuan berhasil diproses!</div>');

                        // Update status badge in table
                        const eventId = $('#event_id').val();
                        const newStatus = $('#status').val();
                        const $row = $(`tr[data-event-id="${eventId}"]`);
                        $row.find('.status-badge')
                            .removeClass('status-pending status-approved status-rejected')
                            .addClass(`status-${newStatus}`)
                            .text(newStatus.charAt(0).toUpperCase() + newStatus.slice(1));

                        // Remove success message after 3 seconds
                        setTimeout(() => $('.alert-success').fadeOut('slow', () => $(this).remove()), 3000);
                    },
                    error: function(xhr, status, error) {
                        alert('Error saving review: ' + error);
                    },
                    complete: function() {
                        $button.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan');
                    }
                });
            });
        });
    </script>
</body>
</html> 