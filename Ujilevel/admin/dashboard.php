<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

$auth = new Auth();
if (!$auth->isLoggedIn() || !$auth->isAdmin()) {
    header("Location: ../login.php");
    exit();
}

$database = new Database();
$conn = $database->getConnection();

// Ambil semua event dari seluruh user
$query = "SELECT e.*, u.nama_lengkap FROM event_pengajuan e JOIN users u ON e.user_id = u.user_id ORDER BY tanggal_pengajuan DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Sistem Pengajuan Event</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .main-content {
            min-height: 100vh;
            background-color: #f5f6fa;
        }
        .nav-link {
            color: white;
            padding: 0.8rem 1rem;
            margin-bottom: 0.5rem;
            border-radius: 0.5rem;
            transition: all 0.3s;
        }
        .nav-link:hover, .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
        }
        .card {
            border: none;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 position-fixed sidebar">
                <div class="p-3">
                    <h4 class="text-white mb-4">Admin Panel</h4>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="dashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manage_events.php">
                                <i class="fas fa-calendar me-2"></i>Kelola Event
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manage_users.php">
                                <i class="fas fa-users me-2"></i>Kelola User
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="reports.php">
                                <i class="fas fa-file-alt me-2"></i>Laporan
                            </a>
                        </li>
                        <li class="nav-item mt-4">
                            <a class="nav-link text-danger" href="../includes/logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 ms-auto main-content">
                <div class="p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Dashboard Admin</h2>
                    </div>

                    <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show">
                        <?php 
                        echo $_SESSION['message'];
                        unset($_SESSION['message']);
                        unset($_SESSION['message_type']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <div class="card">
                        <div class="card-body">
                            <?php if (count($events) === 0): ?>
                                <p>Belum ada event yang diajukan.</p>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Nama User</th>
                                                <th>Judul Event</th>
                                                <th>Jenis Kegiatan</th>
                                                <th>Total Biaya</th>
                                                <th>Tanggal Pengajuan</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($events as $event): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($event['nama_lengkap']); ?></td>
                                                <td><?php echo htmlspecialchars($event['judul_event']); ?></td>
                                                <td><?php echo htmlspecialchars($event['jenis_kegiatan']); ?></td>
                                                <td>Rp <?php echo number_format($event['total_pembiayaan'], 0, ',', '.'); ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($event['tanggal_pengajuan'])); ?></td>
                                                <td>
                                                    <span class="badge <?php 
                                                        echo $event['status'] === 'disetujui' ? 'bg-success' : 
                                                            ($event['status'] === 'ditolak' ? 'bg-danger' : 'bg-warning'); 
                                                    ?>">
                                                        <?php echo ucfirst($event['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#detailModal<?php echo $event['event_id']; ?>">
                                                        <i class="fas fa-eye"></i> Detail
                                                    </button>
                                                </td>
                                            </tr>

                                            <!-- Modal Detail Event -->
                                            <div class="modal fade" id="detailModal<?php echo $event['event_id']; ?>" tabindex="-1">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-primary text-white">
                                                            <h5 class="modal-title">Detail Event</h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row mb-3">
                                                                <div class="col-md-4 fw-bold">Nama Pengaju</div>
                                                                <div class="col-md-8"><?php echo htmlspecialchars($event['nama_lengkap']); ?></div>
                                                            </div>
                                                            <div class="row mb-3">
                                                                <div class="col-md-4 fw-bold">Judul Event</div>
                                                                <div class="col-md-8"><?php echo htmlspecialchars($event['judul_event']); ?></div>
                                                            </div>
                                                            <div class="row mb-3">
                                                                <div class="col-md-4 fw-bold">Jenis Kegiatan</div>
                                                                <div class="col-md-8"><?php echo htmlspecialchars($event['jenis_kegiatan']); ?></div>
                                                            </div>
                                                            <div class="row mb-3">
                                                                <div class="col-md-4 fw-bold">Total Biaya</div>
                                                                <div class="col-md-8">Rp <?php echo number_format($event['total_pembiayaan'], 0, ',', '.'); ?></div>
                                                            </div>
                                                            <div class="row mb-3">
                                                                <div class="col-md-4 fw-bold">Tanggal Pengajuan</div>
                                                                <div class="col-md-8"><?php echo date('d/m/Y', strtotime($event['tanggal_pengajuan'])); ?></div>
                                                            </div>
                                                            <div class="row mb-3">
                                                                <div class="col-md-4 fw-bold">Status</div>
                                                                <div class="col-md-8">
                                                                    <span class="badge <?php 
                                                                        echo $event['status'] === 'disetujui' ? 'bg-success' : 
                                                                            ($event['status'] === 'ditolak' ? 'bg-danger' : 'bg-warning'); 
                                                                    ?>">
                                                                        <?php echo ucfirst($event['status']); ?>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-3">
                                                                <div class="col-md-4 fw-bold">Deskripsi</div>
                                                                <div class="col-md-8"><?php echo nl2br(htmlspecialchars($event['deskripsi'])); ?></div>
                                                            </div>
                                                            <?php if($event['proposal']): ?>
                                                            <div class="row mb-3">
                                                                <div class="col-md-4 fw-bold">Proposal</div>
                                                                <div class="col-md-8">
                                                                    <a href="../uploads/proposals/<?php echo $event['proposal']; ?>" 
                                                                       download="Proposal_<?php echo htmlspecialchars($event['judul_event']); ?>.pdf" 
                                                                       class="btn btn-success">
                                                                        <i class="fas fa-download"></i> Download Proposal
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="modal-footer justify-content-between">
                                                            <div>
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                            </div>
                                                            <?php if($event['status'] === 'menunggu'): ?>
                                                            <div>
                                                                <a href="approve_event.php?id=<?php echo $event['event_id']; ?>&action=reject" 
                                                                   class="btn btn-danger me-2"
                                                                   onclick="return confirm('Apakah Anda yakin ingin menolak event ini?')">
                                                                    <i class="fas fa-times"></i> Tolak
                                                                </a>
                                                                <a href="approve_event.php?id=<?php echo $event['event_id']; ?>&action=approve" 
                                                                   class="btn btn-success"
                                                                   onclick="return confirm('Apakah Anda yakin ingin menyetujui event ini?')">
                                                                    <i class="fas fa-check"></i> Setujui
                                                                </a>
                                                            </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
