<?php
include 'config.php';
include 'session.php';

// Redirect jika user belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user's statistics
$user_id = $_SESSION['user_id'];
$stats = [
    'total_submissions' => 0,
    'pending' => 0,
    'approved' => 0,
    'rejected' => 0
];

$query = "SELECT status, COUNT(*) as count FROM event_pengajuan WHERE user_id = ? GROUP BY status";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $stats['total_submissions']++;
    switch ($row['status']) {
        case 'Pending':
            $stats['pending'] = $row['count'];
            break;
        case 'Disetujui':
            $stats['approved'] = $row['count'];
            break;
        case 'Ditolak':
            $stats['rejected'] = $row['count'];
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .content-container {
            padding: 20px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .stats-card {
            background: linear-gradient(45deg, #4e73df 0%, #224abe 100%);
            color: white;
        }
        .stats-icon {
            font-size: 2rem;
            opacity: 0.7;
        }
        .welcome-section {
            background: linear-gradient(45deg, #2196F3 0%, #1976D2 100%);
            color: white;
            padding: 40px 0;
            margin-bottom: 30px;
        }
        .status-badge {
            padding: 8px 12px;
            font-weight: 500;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .status-pending {
            background: linear-gradient(45deg, #ffc107 0%, #ff9800 100%);
            color: #fff;
        }
        .status-approved {
            background: linear-gradient(45deg, #28a745 0%, #20c997 100%);
            color: #fff;
        }
        .status-rejected {
            background: linear-gradient(45deg, #dc3545 0%, #c82333 100%);
            color: #fff;
        }
        .status-completed {
            background: linear-gradient(45deg, #6c757d 0%, #495057 100%);
            color: #fff;
        }
        .table th {
            font-weight: 600;
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }
        .table td {
            vertical-align: middle;
            padding: 1rem 0.75rem;
        }
    </style>
</head>
<body>
    <?php include 'components/header.php'; ?>

    <div class="welcome-section">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1>Selamat Datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
                    <p class="lead">Kelola pengajuan event ekstrakurikuler Anda di sini.</p>
                </div>
                <a href="logout.php" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin logout?');">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>
    </div>

    <div class="container content-container">
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0">Total Pengajuan</h6>
                                <h3 class="mt-2 mb-0"><?php echo $stats['total_submissions']; ?></h3>
                            </div>
                            <div class="stats-icon">
                                <i class="bi bi-file-text"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0">Menunggu</h6>
                                <h3 class="mt-2 mb-0"><?php echo $stats['pending']; ?></h3>
                            </div>
                            <div class="stats-icon">
                                <i class="bi bi-clock"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0">Disetujui</h6>
                                <h3 class="mt-2 mb-0"><?php echo $stats['approved']; ?></h3>
                            </div>
                            <div class="stats-icon">
                                <i class="bi bi-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0">Ditolak</h6>
                                <h3 class="mt-2 mb-0"><?php echo $stats['rejected']; ?></h3>
                            </div>
                            <div class="stats-icon">
                                <i class="bi bi-x-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Quick Actions</h5>
                        <div class="d-flex gap-2">
                            <a href="submit_event.php" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Ajukan Event Baru
                            </a>
                            <a href="#eventList" class="btn btn-outline-primary">
                                <i class="bi bi-list-ul"></i> Lihat Pengajuan Saya
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Submissions -->
        <div class="card" id="eventList">
            <div class="card-header">
                <h5 class="card-title mb-0">Pengajuan Event Terbaru</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Judul Kegiatan</th>
                                <th>Event Ekskul</th>
                                <th>Total Biaya</th>
                                <th>Status</th>
                                <th>Tanggal Verifikasi</th>
                                <th>Catatan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT ep.*, v.tanggal_verifikasi, v.catatan 
                                     FROM event_pengajuan ep 
                                     LEFT JOIN verifikasi_event v ON ep.id = v.event_id 
                                     WHERE ep.user_id = ? 
                                     ORDER BY ep.tanggal_pengajuan DESC LIMIT 5";
                            $stmt = $conn->prepare($query);
                            $stmt->bind_param("i", $user_id);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            while ($row = $result->fetch_assoc()):
                                $status = $row['status'] ?? 'Pending';
                                $status_class = match($status) {
                                    'Disetujui' => 'status-approved',
                                    'Ditolak' => 'status-rejected',
                                    'Selesai' => 'status-completed',
                                    default => 'status-pending'
                                };
                                $status_icon = match($status) {
                                    'Disetujui' => 'bi-check-circle-fill',
                                    'Ditolak' => 'bi-x-circle-fill',
                                    'Selesai' => 'bi-check-square-fill',
                                    default => 'bi-clock-fill'
                                };
                            ?>
                            <tr>
                                <td><?php echo date('d-m-Y', strtotime($row['tanggal_pengajuan'])); ?></td>
                                <td><?php echo htmlspecialchars($row['judul_kegiatan']); ?></td>
                                <td><?php echo htmlspecialchars($row['event_ekskul']); ?></td>
                                <td>Rp <?php echo number_format($row['total_biaya'], 0, ',', '.'); ?></td>
                                <td>
                                    <span class="status-badge <?php echo $status_class; ?>">
                                        <i class="bi <?php echo $status_icon; ?>"></i>
                                        <?php echo $status; ?>
                                    </span>
                                </td>
                                <td><?php echo $row['tanggal_verifikasi'] ? date('d-m-Y', strtotime($row['tanggal_verifikasi'])) : '-'; ?></td>
                                <td>
                                    <?php if($row['catatan']): ?>
                                        <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars($row['catatan']); ?>">
                                            <i class="bi bi-info-circle"></i> Lihat
                                        </button>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($row['proposal']): ?>
                                        <a href="uploads/<?php echo htmlspecialchars($row['proposal']); ?>" class="btn btn-sm btn-info" target="_blank">
                                            <i class="bi bi-file-earmark-pdf"></i> Lihat Proposal
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    </script>
</body>
</html> 