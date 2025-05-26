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

// Ambil semua event
$query = "SELECT ep.*, u.nama_lengkap, u.ekskul 
          FROM event_pengajuan ep 
          JOIN users u ON ep.user_id = u.user_id 
          ORDER BY ep.tanggal_pengajuan DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Proses persetujuan/penolakan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = $_POST['event_id'];
    $status = $_POST['status'];
    $catatan = $_POST['catatan'] ?? '';

    $query = "UPDATE event_pengajuan SET status = :status, catatan = :catatan WHERE event_id = :event_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':catatan', $catatan);
    $stmt->bindParam(':event_id', $event_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Status event berhasil diperbarui.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Gagal memperbarui status event.";
        $_SESSION['message_type'] = "danger";
    }

    header("Location: manage_events.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Event - Admin Panel</title>
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
        .dokumentasi-preview {
            max-width: 100px;
            cursor: pointer;
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
                            <a class="nav-link" href="dashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="manage_events.php">
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
                    <h2 class="mb-4">Kelola Event</h2>

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
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Judul Event</th>
                                            <th>Pengaju</th>
                                            <th>Ekstrakurikuler</th>
                                            <th>Jenis Kegiatan</th>
                                            <th>Total Biaya</th>
                                            <th>Tanggal</th>
                                            <th>Status</th>
                                            <th>Dokumentasi</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $no = 1;
                                        foreach ($events as $event): 
                                        ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td><?php echo htmlspecialchars($event['judul_event']); ?></td>
                                            <td><?php echo htmlspecialchars($event['nama_lengkap']); ?></td>
                                            <td><?php echo htmlspecialchars($event['ekskul']); ?></td>
                                            <td><?php echo htmlspecialchars($event['jenis_kegiatan']); ?></td>
                                            <td>Rp <?php echo number_format($event['total_pembiayaan'], 0, ',', '.'); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($event['tanggal_pengajuan'])); ?></td>
                                            <td>
                                                <span class="badge <?php 
                                                    echo $event['status'] === 'disetujui' ? 'bg-success' : 
                                                        ($event['status'] === 'ditolak' ? 'bg-danger' : 
                                                        ($event['status'] === 'selesai' ? 'bg-info' : 'bg-warning')); 
                                                ?>">
                                                    <?php echo ucfirst($event['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if($event['status'] === 'selesai' && $event['foto_dokumentasi']): ?>
                                                <img src="../uploads/dokumentasi/<?php echo $event['foto_dokumentasi']; ?>" 
                                                     class="dokumentasi-preview"
                                                     data-bs-toggle="modal" 
                                                     data-bs-target="#fotoModal<?php echo $event['event_id']; ?>"
                                                     alt="Dokumentasi Event">
                                                <?php else: ?>
                                                <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($event['status'] === 'menunggu'): ?>
                                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" 
                                                        data-bs-target="#actionModal<?php echo $event['event_id']; ?>">
                                                    <i class="fas fa-edit me-1"></i>Tindakan
                                                </button>
                                                <?php else: ?>
                                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" 
                                                        data-bs-target="#detailModal<?php echo $event['event_id']; ?>">
                                                    <i class="fas fa-eye me-1"></i>Detail
                                                </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>

                                        <!-- Modal Foto -->
                                        <?php if($event['status'] === 'selesai' && $event['foto_dokumentasi']): ?>
                                        <div class="modal fade" id="fotoModal<?php echo $event['event_id']; ?>" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Dokumentasi Event: <?php echo htmlspecialchars($event['judul_event']); ?></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <img src="../uploads/dokumentasi/<?php echo $event['foto_dokumentasi']; ?>" 
                                                             class="img-fluid" 
                                                             alt="Dokumentasi Event">
                                                        <div class="mt-3">
                                                            <p class="mb-1"><strong>Tanggal Selesai:</strong> 
                                                                <?php echo date('d/m/Y', strtotime($event['tanggal_selesai'])); ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <!-- Modal Tindakan -->
                                        <div class="modal fade" id="actionModal<?php echo $event['event_id']; ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Tindakan Event</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="" method="POST">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="event_id" value="<?php echo $event['event_id']; ?>">
                                                            <div class="mb-3">
                                                                <label class="form-label">Status</label>
                                                                <select class="form-select" name="status" required>
                                                                    <option value="">Pilih Status</option>
                                                                    <option value="disetujui">Disetujui</option>
                                                                    <option value="ditolak">Ditolak</option>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Catatan</label>
                                                                <textarea class="form-control" name="catatan" rows="3"></textarea>
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

                                        <!-- Modal Detail -->
                                        <div class="modal fade" id="detailModal<?php echo $event['event_id']; ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Detail Event</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <dl class="row">
                                                            <dt class="col-sm-4">Judul Event</dt>
                                                            <dd class="col-sm-8"><?php echo htmlspecialchars($event['judul_event']); ?></dd>

                                                            <dt class="col-sm-4">Pengaju</dt>
                                                            <dd class="col-sm-8"><?php echo htmlspecialchars($event['nama_lengkap']); ?></dd>

                                                            <dt class="col-sm-4">Ekstrakurikuler</dt>
                                                            <dd class="col-sm-8"><?php echo htmlspecialchars($event['ekskul']); ?></dd>

                                                            <dt class="col-sm-4">Jenis Kegiatan</dt>
                                                            <dd class="col-sm-8"><?php echo htmlspecialchars($event['jenis_kegiatan']); ?></dd>

                                                            <dt class="col-sm-4">Total Biaya</dt>
                                                            <dd class="col-sm-8">Rp <?php echo number_format($event['total_pembiayaan'], 0, ',', '.'); ?></dd>

                                                            <dt class="col-sm-4">Tanggal Pengajuan</dt>
                                                            <dd class="col-sm-8"><?php echo date('d/m/Y', strtotime($event['tanggal_pengajuan'])); ?></dd>

                                                            <dt class="col-sm-4">Status</dt>
                                                            <dd class="col-sm-8">
                                                                <span class="badge <?php 
                                                                    echo $event['status'] === 'disetujui' ? 'bg-success' : 
                                                                        ($event['status'] === 'ditolak' ? 'bg-danger' : 
                                                                        ($event['status'] === 'selesai' ? 'bg-info' : 'bg-warning')); 
                                                                ?>">
                                                                    <?php echo ucfirst($event['status']); ?>
                                                                </span>
                                                            </dd>

                                                            <?php if($event['catatan']): ?>
                                                            <dt class="col-sm-4">Catatan</dt>
                                                            <dd class="col-sm-8"><?php echo nl2br(htmlspecialchars($event['catatan'])); ?></dd>
                                                            <?php endif; ?>

                                                            <?php if($event['status'] === 'selesai'): ?>
                                                            <dt class="col-sm-4">Tanggal Selesai</dt>
                                                            <dd class="col-sm-8"><?php echo date('d/m/Y', strtotime($event['tanggal_selesai'])); ?></dd>
                                                            <?php endif; ?>
                                                        </dl>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <?php if (empty($events)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada event yang diajukan</p>
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