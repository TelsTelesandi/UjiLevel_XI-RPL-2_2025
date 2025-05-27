<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once '../config.php';
require_once 'check_admin.php';

// Database connection is already established in init.php as $conn
if (!isset($conn) || $conn->connect_error) {
    die("Connection failed: " . ($conn->connect_error ?? "Database connection not available"));
}

// Get report data
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Get total users
$sql_users = "SELECT COUNT(*) as total_users FROM users";
$result_users = $conn->query($sql_users);
if (!$result_users) {
    die("Error in users query: " . $conn->error);
}
$total_users = $result_users->fetch_assoc()['total_users'];

// Get total events
$sql_events = "SELECT COUNT(*) as total_events FROM event_pengajuan";
$result_events = $conn->query($sql_events);
if (!$result_events) {
    die("Error in events query: " . $conn->error);
}
$total_events = $result_events->fetch_assoc()['total_events'];

// Get events by date range
$sql_events_range = "SELECT ep.*, u.username, v.tanggal_verifikasi, v.catatan 
                    FROM event_pengajuan ep 
                    LEFT JOIN users u ON ep.user_id = u.user_id 
                    LEFT JOIN verifikasi_event v ON ep.id = v.event_id
                    WHERE ep.tanggal_pengajuan BETWEEN ? AND ?
                    ORDER BY ep.tanggal_pengajuan DESC";
$stmt = $conn->prepare($sql_events_range);
if (!$stmt) {
    die("Error in preparing statement: " . $conn->error);
}
$stmt->bind_param("ss", $start_date, $end_date);
if (!$stmt->execute()) {
    die("Error in executing statement: " . $stmt->error);
}
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Events</title>
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
        }
    </style>
</head>
<body>
    <?php include '../components/header.php'; ?>
    
    <!-- Navigation Menu -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="report.php">
                            <i class="bi bi-file-earmark-text"></i> Reports
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Report Dashboard</h2>
        
        <!-- Summary Cards -->
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Users</h5>
                        <h2 class="card-text"><?php echo $total_users; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Events</h5>
                        <h2 class="card-text"><?php echo $total_events; ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Date Filter -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Filter Report</h5>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" name="start_date" value="<?= $start_date ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tanggal Akhir</label>
                        <input type="date" class="form-control" name="end_date" value="<?= $end_date ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Filter
                            </button>
                            <a href="export_report.php?start_date=<?= $start_date ?>&end_date=<?= $end_date ?>" class="btn btn-success">
                                <i class="bi bi-download"></i> Export
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Events Table -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Daftar Event</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Username</th>
                                <th>Judul Kegiatan</th>
                                <th>Event Ekskul</th>
                                <th>Total Biaya</th>
                                <th>Status</th>
                                <th>Proposal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            while ($row = $result->fetch_assoc()): 
                                $status = $row['status'] ?? 'Pending';
                                $status_class = match($status) {
                                    'Disetujui' => 'success',
                                    'Ditolak' => 'danger',
                                    'Selesai' => 'secondary',
                                    default => 'warning'
                                };
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= date('d/m/Y', strtotime($row['tanggal_pengajuan'])) ?></td>
                                <td><?= htmlspecialchars($row['username']) ?></td>
                                <td><?= htmlspecialchars($row['judul_kegiatan']) ?></td>
                                <td><?= htmlspecialchars($row['event_ekskul']) ?></td>
                                <td>Rp <?= number_format($row['total_biaya'], 0, ',', '.') ?></td>
                                <td>
                                    <span class="badge bg-<?= $status_class ?>">
                                        <?= $status ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if($row['proposal']): ?>
                                        <a href="../uploads/<?= htmlspecialchars($row['proposal']) ?>" class="btn btn-sm btn-info" target="_blank">
                                            <i class="bi bi-file-pdf"></i> Lihat
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#verifikasiModal<?= $row['id'] ?>">
                                        <i class="bi bi-check-circle"></i> Verifikasi
                                    </button>

                                    <!-- Modal Verifikasi -->
                                    <div class="modal fade" id="verifikasiModal<?= $row['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Verifikasi Event</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="verifikasi_event.php" method="POST">
                                                    <div class="modal-body">
                                                        <input type="hidden" name="event_id" value="<?= $row['id'] ?>">
                                                        
                                                        <div class="mb-3">
                                                            <label class="form-label">Status</label>
                                                            <select class="form-select" name="status" required>
                                                                <option value="Pending" <?= $status == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                                                <option value="Disetujui" <?= $status == 'Disetujui' ? 'selected' : '' ?>>Disetujui</option>
                                                                <option value="Ditolak" <?= $status == 'Ditolak' ? 'selected' : '' ?>>Ditolak</option>
                                                                <option value="Selesai" <?= $status == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                                                            </select>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <label class="form-label">Tanggal Verifikasi</label>
                                                            <input type="date" class="form-control" name="tanggal_verifikasi" 
                                                                value="<?= $row['tanggal_verifikasi'] ?? date('Y-m-d') ?>" required>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <label class="form-label">Catatan</label>
                                                            <textarea class="form-control" name="catatan" rows="3"><?= htmlspecialchars($row['catatan'] ?? '') ?></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
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
</body>
</html> 