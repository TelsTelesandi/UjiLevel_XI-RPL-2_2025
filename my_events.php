<?php
require_once 'includes/auth.php';
require_once 'config/database.php';

$auth = new Auth();
if (!$auth->isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$database = new Database();
$conn = $database->getConnection();

// Ambil event milik user yang login
$query = "SELECT * FROM event_pengajuan WHERE user_id = :user_id ORDER BY tanggal_pengajuan DESC";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Saya - Sistem Pengajuan Event</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Event Saya</h2>
            <a href="submit_event.php" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Ajukan Event Baru
            </a>
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

        <div class="row">
            <?php foreach ($events as $event): ?>
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($event['judul_event']); ?></h5>
                        <p class="card-text">
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i>
                                <?php echo date('d/m/Y', strtotime($event['tanggal_pengajuan'])); ?>
                            </small>
                        </p>
                        <p class="card-text">
                            <strong>Jenis Kegiatan:</strong> <?php echo htmlspecialchars($event['jenis_kegiatan']); ?><br>
                            <strong>Total Biaya:</strong> Rp <?php echo number_format($event['total_pembiayaan'], 0, ',', '.'); ?>
                        </p>
                        <div class="mb-3">
                            <span class="badge <?php 
                                echo $event['status'] === 'disetujui' ? 'bg-success' : 
                                    ($event['status'] === 'ditolak' ? 'bg-danger' : 
                                    ($event['status'] === 'selesai' ? 'bg-info' : 'bg-warning')); 
                            ?>">
                                <?php echo ucfirst($event['status']); ?>
                            </span>
                        </div>
                        
                        <?php if($event['status'] === 'disetujui'): ?>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" 
                                data-bs-target="#completeModal<?php echo $event['event_id']; ?>">
                            <i class="fas fa-check-circle me-2"></i>Konfirmasi Selesai
                        </button>
                        <?php endif; ?>

                        <?php if($event['status'] === 'selesai' && $event['foto_dokumentasi']): ?>
                        <div class="mt-3">
                            <strong>Dokumentasi:</strong><br>
                            <img src="uploads/dokumentasi/<?php echo $event['foto_dokumentasi']; ?>" 
                                 class="img-fluid mt-2" style="max-height: 200px;" 
                                 alt="Dokumentasi Event">
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if($event['status'] === 'disetujui'): ?>
            <!-- Modal Konfirmasi Selesai -->
            <div class="modal fade" id="completeModal<?php echo $event['event_id']; ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Konfirmasi Event Selesai</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="event_complete.php" method="POST" enctype="multipart/form-data">
                            <div class="modal-body">
                                <input type="hidden" name="event_id" value="<?php echo $event['event_id']; ?>">
                                <div class="mb-3">
                                    <label class="form-label">Upload Foto Dokumentasi</label>
                                    <input type="file" class="form-control" name="foto_dokumentasi" 
                                           accept="image/jpeg,image/png,image/jpg" required>
                                    <small class="text-muted">Format: JPG/PNG, Maksimal 5MB</small>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check me-2"></i>Konfirmasi Selesai
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <?php if (empty($events)): ?>
        <div class="text-center py-5">
            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
            <p class="text-muted">Anda belum memiliki event</p>
            <a href="submit_event.php" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Ajukan Event Baru
            </a>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 