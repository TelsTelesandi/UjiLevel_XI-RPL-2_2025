<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

$auth = new Auth();
if (!$auth->isLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: my_events.php");
    exit();
}

$event_id = $_GET['id'];
$database = new Database();
$conn = $database->getConnection();

// Ambil data event milik user yang sedang login
$query = "SELECT * FROM event_pengajuan WHERE event_id = :event_id AND user_id = :user_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':event_id', $event_id);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    // Event tidak ditemukan atau bukan milik user ini
    header("Location: my_events.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Event - Sistem Pengajuan Event</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <a href="my_events.php" class="btn btn-secondary mb-3"><i class="fas fa-arrow-left"></i> Kembali ke Event Saya</a>
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Detail Event: <?php echo htmlspecialchars($event['judul_event']); ?></h4>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">Jenis Kegiatan</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($event['jenis_kegiatan']); ?></dd>

                    <dt class="col-sm-4">Total Pembiayaan</dt>
                    <dd class="col-sm-8">Rp <?php echo number_format($event['total_pembiayaan'], 0, ',', '.'); ?></dd>

                    <dt class="col-sm-4">Tanggal Pengajuan</dt>
                    <dd class="col-sm-8"><?php echo date('d/m/Y', strtotime($event['tanggal_pengajuan'])); ?></dd>

                    <dt class="col-sm-4">Status</dt>
                    <dd class="col-sm-8">
                        <?php
                        $badge_class = '';
                        switch($event['status']) {
                            case 'menunggu': $badge_class = 'bg-warning'; break;
                            case 'disetujui': $badge_class = 'bg-success'; break;
                            case 'ditolak': $badge_class = 'bg-danger'; break;
                        }
                        ?>
                        <span class="badge <?php echo $badge_class; ?> px-3 py-2 fs-6"><?php echo ucfirst($event['status']); ?></span>
                    </dd>

                    <dt class="col-sm-4">Deskripsi</dt>
                    <dd class="col-sm-8"><?php echo nl2br(htmlspecialchars($event['deskripsi'])); ?></dd>

                    <?php if (!empty($event['proposal'])): ?>
                    <dt class="col-sm-4">File Proposal</dt>
                    <dd class="col-sm-8">
                        <a href="../uploads/proposals/<?php echo htmlspecialchars($event['proposal']); ?>" target="_blank" class="btn btn-sm btn-outline-info">
                            <i class="fas fa-file-download"></i> Download Proposal
                        </a>
                    </dd>
                    <?php endif; ?>
                </dl>
            </div>
        </div>
    </div>
</body>
</html>
