<?php
require_once 'config/database.php';
require_once 'config/session.php';

requireLogin();

$database = new Database();
$db = $database->getConnection();

$user_id = $_SESSION['user_id'];

// Handle cancellation
if (isset($_POST['cancel_event'])) {
    $event_id = $_POST['event_id'];
    
    $cancel_query = "UPDATE event_pengajuan SET status = 'ditolak' WHERE event_id = ? AND user_id = ? AND status = 'menunggu'";
    $cancel_stmt = $db->prepare($cancel_query);
    $cancel_stmt->execute([$event_id, $user_id]);
    
    header("Location: riwayat.php");
    exit();
}

// Get all submissions
$query = "SELECT * FROM event_pengajuan WHERE user_id = ? ORDER BY tanggal_pengajuan DESC";
$stmt = $db->prepare($query);
$stmt->execute([$user_id]);
$submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pengajuan - Event Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
                        <i class="fas fa-user-circle fa-3x"></i>
                        <h5 class="mt-2"><?php echo $_SESSION['nama_lengkap']; ?></h5>
                        <small>User</small>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-white" href="user_dashboard.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="user_pengajuan.php">
                                <i class="fas fa-plus-circle"></i> Ajukan Event
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white active" href="riwayat.php">
                                <i class="fas fa-history"></i> Riwayat Pengajuan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="./logout.php">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Riwayat Pengajuan</h1>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-history"></i> Semua Pengajuan Event</h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($submissions) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Judul Event</th>
                                            <th>Jenis Kegiatan</th>
                                            <th>Total Pembiayaan</th>
                                            <th>Tanggal Pengajuan</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($submissions as $submission): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($submission['judul_event']); ?></td>
                                                <td><?php echo htmlspecialchars($submission['jenis_kegiatan']); ?></td>
                                                <td>
                                                    <?php
                                                    if (array_key_exists('Total_pembiayaan', $submission)) {
                                                        $totalPembiayaan = $submission['total_pembiayaan'];
                                                    } else {
                                                        $totalPembiayaan = 0; // Nilai default jika tidak ada
                                                    }
                                                    echo 'Rp ' . number_format($totalPembiayaan, 0, ',', '.');
                                                    ?>
                                                </td>
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
                                                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detailModal<?php echo $submission['event_id']; ?>">
                                                        <i class="fas fa-eye"></i> Detail
                                                    </button>
                                                    <?php if ($submission['status'] == 'menunggu'): ?>
                                                        <form method="POST" style="display: inline;">
                                                            <input type="hidden" name="event_id" value="<?php echo $submission['event_id']; ?>">
                                                            <button type="submit" name="cancel_event" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin membatalkan pengajuan ini?')">
                                                                <i class="fas fa-times"></i> Batalkan
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>

                                            <!-- Detail Modal -->
                                            <div class="modal fade" id="detailModal<?php echo $submission['event_id']; ?>" tabindex="-1">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Detail Pengajuan Event</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <strong>Judul Event:</strong><br>
                                                                    <?php echo htmlspecialchars($submission['judul_event']); ?>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <strong>Jenis Kegiatan:</strong><br>
                                                                    <?php echo htmlspecialchars($submission['jenis_kegiatan']); ?>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <strong>Total Pembiayaan:</strong><br>
                                                                    <?php echo number_format($submission['total_pembiayaan'], 0, ',', '.'); ?>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <strong>Tanggal Pengajuan:</strong><br>
                                                                    <?php echo date('d/m/Y', strtotime($submission['tanggal_pengajuan'])); ?>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            <strong>Deskripsi:</strong><br>
                                                            <?php echo nl2br(htmlspecialchars($submission['deskripsi'])); ?>
                                                            <hr>
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada pengajuan event</p>
                                <a href="user_pengajuan.php" class="btn btn-primary">Ajukan Event Pertama</a>
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