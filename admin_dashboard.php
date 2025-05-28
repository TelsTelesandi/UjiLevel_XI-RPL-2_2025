<?php
require_once 'config/database.php';
require_once 'config/session.php';

requireAdmin();

$database = new Database();
$db = $database->getConnection();

// Get admin statistics
$stats_query = "SELECT 
    COUNT(*) as total_pengajuan,
    SUM(CASE WHEN status = 'menunggu' THEN 1 ELSE 0 END) as menunggu,
    SUM(CASE WHEN status = 'disetujui' THEN 1 ELSE 0 END) as disetujui,
    SUM(CASE WHEN status = 'ditolak' THEN 1 ELSE 0 END) as ditolak
    FROM event_pengajuan";
$stats_stmt = $db->prepare($stats_query);
$stats_stmt->execute();
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

// Get total users
$users_query = "SELECT COUNT(*) as total_users FROM users WHERE role = 'user'";
$users_stmt = $db->prepare($users_query);
$users_stmt->execute();
$total_users = $users_stmt->fetch(PDO::FETCH_ASSOC)['total_users'];

// Get recent submissions
$recent_query = "SELECT ep.*, u.nama_lengkap FROM event_pengajuan ep 
                 JOIN users u ON ep.user_id = u.user_id 
                 ORDER BY ep.tanggal_pengajuan DESC LIMIT 5";
$recent_stmt = $db->prepare($recent_query);
$recent_stmt->execute();
$recent_submissions = $recent_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Event Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
        }
        .stat-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center text-white mb-4">
                        <i class="fas fa-user-shield fa-3x"></i>
                        <h5 class="mt-2"><?php echo $_SESSION['nama_lengkap']; ?></h5>
                        <small>Administrator</small>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-white active" href="admin_dashboard.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="users.php">
                                <i class="fas fa-users"></i> Kelola User
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="admin_pengajuan.php">
                                <i class="fas fa-file-alt"></i> Kelola Pengajuan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="verifikasi.php">
                                <i class="fas fa-check-circle"></i> Verifikasi Event
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="laporan.php">
                                <i class="fas fa-file-pdf"></i> Laporan
                            </a>
                        </li>
                        <li class="nav-item">
                            <form action="logout.php" method="post" style="display: inline;">
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard Admin</h1>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card text-center">
                            <div class="card-body">
                                <i class="fas fa-users fa-2x text-info mb-2"></i>
                                <h3 class="text-info"><?php echo $total_users; ?></h3>
                                <p class="text-muted">Total User</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card text-center">
                            <div class="card-body">
                                <i class="fas fa-file-alt fa-2x text-primary mb-2"></i>
                                <h3 class="text-primary"><?php echo $stats['total_pengajuan']; ?></h3>
                                <p class="text-muted">Total Pengajuan</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card text-center">
                            <div class="card-body">
                                <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                                <h3 class="text-warning"><?php echo $stats['menunggu']; ?></h3>
                                <p class="text-muted">Menunggu Verifikasi</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card text-center">
                            <div class="card-body">
                                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                <h3 class="text-success"><?php echo $stats['disetujui']; ?></h3>
                                <p class="text-muted">Disetujui</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Submissions -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-history"></i> Pengajuan Terbaru</h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($recent_submissions) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Pengaju</th>
                                            <th>Judul Event</th>
                                            <th>Jenis Kegiatan</th>
                                            <th>Tanggal Pengajuan</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_submissions as $submission): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($submission['nama_lengkap']); ?></td>
                                                <td><?php echo htmlspecialchars($submission['judul_event']); ?></td>
                                                <td><?php echo htmlspecialchars($submission['jenis_kegiatan']); ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($submission['tanggal_pengajuan'])); ?></td>
                                                <td>
                                                    <?php
                                                    $status_class = '';
                                                    switch($submission['status']) {
                                                        case 'menunggu': $status_class = 'warning'; break;
                                                        case 'disetujui': $status_class = 'success'; break;
                                                        case 'ditolak': $status_class = 'danger'; break;
                                                    }
                                                    ?>
                                                    <span class="badge bg-<?php echo $status_class; ?>">
                                                        <?php echo ucfirst($submission['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="admin_pengajuan.php?id=<?php echo $submission['event_id']; ?>" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i> Detail
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada pengajuan event</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>