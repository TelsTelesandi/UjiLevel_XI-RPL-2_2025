<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

$auth = new Auth();
if (!$auth->isLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

$database = new Database();
$conn = $database->getConnection();

// Get user's events
$query = "SELECT * FROM event_pengajuan WHERE user_id = :user_id ORDER BY tanggal_pengajuan DESC";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get statistics
$stats_query = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'menunggu' THEN 1 ELSE 0 END) as menunggu,
    SUM(CASE WHEN status = 'disetujui' THEN 1 ELSE 0 END) as disetujui,
    SUM(CASE WHEN status = 'ditolak' THEN 1 ELSE 0 END) as ditolak
    FROM event_pengajuan WHERE user_id = :user_id";
$stats_stmt = $conn->prepare($stats_query);
$stats_stmt->bindParam(':user_id', $_SESSION['user_id']);
$stats_stmt->execute();
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User - Sistem Pengajuan Event</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .stat-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar text-white p-0">
                <div class="p-3">
                    <h4 class="mb-4">User Panel</h4>
                    <ul class="nav flex-column">
                        <li class="nav-item mb-2">
                            <a class="nav-link text-white active" href="dashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link text-white" href="submit_event.php">
                                <i class="fas fa-plus me-2"></i>Ajukan Event
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link text-white" href="my_events.php">
                                <i class="fas fa-calendar me-2"></i>Event Saya
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="../includes/logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Dashboard</h2>
                        <span class="text-muted">Selamat datang, <?php echo $_SESSION['nama_lengkap']; ?>!</span>
                    </div>
                    
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h5>Total Event</h5>
                                            <h3><?php echo $stats['total']; ?></h3>
                                        </div>
                                        <i class="fas fa-calendar fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h5>Menunggu</h5>
                                            <h3><?php echo $stats['menunggu']; ?></h3>
                                        </div>
                                        <i class="fas fa-clock fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h5>Disetujui</h5>
                                            <h3><?php echo $stats['disetujui']; ?></h3>
                                        </div>
                                        <i class="fas fa-check fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card bg-danger text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h5>Ditolak</h5>
                                            <h3><?php echo $stats['ditolak']; ?></h3>
                                        </div>
                                        <i class="fas fa-times fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Events -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Event Terbaru</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Judul Event</th>
                                            <th>Jenis Kegiatan</th>
                                            <th>Total Biaya</th>
                                            <th>Tanggal Pengajuan</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (array_slice($events, 0, 5) as $event): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($event['judul_event']); ?></td>
                                            <td><?php echo htmlspecialchars($event['jenis_kegiatan']); ?></td>
                                            <td>Rp <?php echo number_format($event['total_pembiayaan'], 0, ',', '.'); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($event['tanggal_pengajuan'])); ?></td>
                                            <td>
                                                <?php
                                                $badge_class = '';
                                                switch($event['status']) {
                                                    case 'menunggu': $badge_class = 'bg-warning'; break;
                                                    case 'disetujui': $badge_class = 'bg-success'; break;
                                                    case 'ditolak': $badge_class = 'bg-danger'; break;
                                                }
                                                ?>
                                                <span class="badge <?php echo $badge_class; ?>"><?php echo ucfirst($event['status']); ?></span>
                                            </td>
                                            <td>
                                                <a href="view_event.php?id=<?php echo $event['event_id']; ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <?php if ($event['status'] === 'menunggu'): ?>
                                                <a href="cancel_event.php?id=<?php echo $event['event_id']; ?>" 
                                                   class="btn btn-sm btn-outline-danger"
                                                   onclick="return confirm('Yakin ingin membatalkan pengajuan ini?')">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <?php if (count($events) > 5): ?>
                            <div class="text-center mt-3">
                                <a href="my_events.php" class="btn btn-primary">Lihat Semua Event</a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
