<?php
require_once 'config/database.php';
require_once 'config/session.php';

requireAdmin();

$database = new Database();
$db = $database->getConnection();

$success = '';
$error = '';

// Handle approval/rejection
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['approve_event'])) {
        $event_id = $_POST['event_id'];
        $admin_id = $_SESSION['user_id'];
        $catatan_admin = $_POST['catatan_admin'];
        
        // Update event status
        $update_query = "UPDATE event_pengajuan SET status = 'disetujui' WHERE event_id = ?";
        $update_stmt = $db->prepare($update_query);
        $update_stmt->execute([$event_id]);
        
        // Insert verification record
        $verify_query = "INSERT INTO verifikasi_event (event_id, admin_id, tanggal_verifikasi, catatan_admin, Status) VALUES (?, ?, CURDATE(), ?, 'unclosed')";
        $verify_stmt = $db->prepare($verify_query);
        $verify_stmt->execute([$event_id, $admin_id, $catatan_admin]);
        
        $success = 'Event berhasil disetujui!';
    } elseif (isset($_POST['reject_event'])) {
        $event_id = $_POST['event_id'];
        $admin_id = $_SESSION['user_id'];
        $catatan_admin = $_POST['catatan_admin'];
        
        // Update event status
        $update_query = "UPDATE event_pengajuan SET status = 'ditolak' WHERE event_id = ?";
        $update_stmt = $db->prepare($update_query);
        $update_stmt->execute([$event_id]);
        
        // Insert verification record
        $verify_query = "INSERT INTO verifikasi_event (event_id, admin_id, tanggal_verifikasi, catatan_admin, Status) VALUES (?, ?, CURDATE(), ?, 'Closed')";
        $verify_stmt = $db->prepare($verify_query);
        $verify_stmt->execute([$event_id, $admin_id, $catatan_admin]);
        
        $success = 'Event berhasil ditolak!';
    } elseif (isset($_POST['submit_event'])) {
        // ... proses input lain ...
        $upload_dir = 'uploads/proposals/';
        $file_name = '';

        if (isset($_FILES['proposal']) && $_FILES['proposal']['error'] == 0) {
            $ext = pathinfo($_FILES['proposal']['name'], PATHINFO_EXTENSION);
            $file_name = 'proposal_' . $user_id . '_' . time() . '.' . $ext;
            $target_path = $upload_dir . $file_name;

            // Pastikan folder ada
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            if (move_uploaded_file($_FILES['proposal']['tmp_name'], $target_path)) {
                // File berhasil diupload, simpan $file_name ke database
            } else {
                // Gagal upload
                $error = "Gagal upload file proposal.";
            }
        } else {
            $error = "File proposal wajib diupload.";
        }

        // ... simpan data event ke database, kolom Proposal diisi $file_name ...
    }
}

// Get all submissions
$query = "SELECT ep.*, u.nama_lengkap, u.username, ep.Total_pembiayaan FROM event_pengajuan ep 
          JOIN users u ON ep.user_id = u.user_id 
          ORDER BY ep.tanggal_pengajuan DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengajuan - Event Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
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
                            <a class="nav-link text-white" href="admin_dashboard.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="users.php">
                                <i class="fas fa-users"></i> Kelola User
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white active" href="admin_pengajuan.php">
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
                    <h1 class="h2">Kelola Pengajuan Event</h1>
                </div>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-file-alt"></i> Semua Pengajuan Event</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Pengaju</th>
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
                                            <td><?php echo htmlspecialchars($submission['nama_lengkap']); ?></td>
                                            <td><?php echo htmlspecialchars($submission['judul_event']); ?></td>
                                            <td><?php echo htmlspecialchars($submission['jenis_kegiatan']); ?></td>
                                            <td>
                                                <?php if (isset($submission['Total_pembiayaan'])): ?>
                                                    Rp <?php echo number_format($submission['Total_pembiayaan'], 0, ',', '.'); ?>
                                                <?php else: ?>
                                                    Rp 0
                                                <?php endif; ?>
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
                                                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal<?php echo $submission['event_id']; ?>">
                                                        <i class="fas fa-check"></i> Setujui
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal<?php echo $submission['event_id']; ?>">
                                                        <i class="fas fa-times"></i> Tolak
                                                    </button>
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
                                                                <strong>Pengaju:</strong><br>
                                                                <?php echo htmlspecialchars($submission['nama_lengkap']); ?> (<?php echo htmlspecialchars($submission['username']); ?>)
                                                            </div>
                                                            <div class="col-md-6">
                                                                <strong>Judul Event:</strong><br>
                                                                <?php echo htmlspecialchars($submission['judul_event']); ?>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <strong>Jenis Kegiatan:</strong><br>
                                                                <?php echo htmlspecialchars($submission['jenis_kegiatan']); ?>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <strong>Total Pembiayaan:</strong><br>
                                                                <?php if (isset($submission['Total_pembiayaan'])): ?>
                                                                    Rp <?php echo number_format($submission['Total_pembiayaan'], 0, ',', '.'); ?>
                                                                <?php else: ?>
                                                                    Rp 0
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <strong>Deskripsi:</strong><br>
                                                        <?php echo nl2br(htmlspecialchars($submission['deskripsi'])); ?>
                                                        <hr>
                                                        <?php if (isset($submission['Proposal']) && !empty($submission['Proposal'])): ?>
                                                            <strong>Proposal:</strong><br>
                                                            <a href="../uploads/proposals/<?php echo $submission['Proposal']; ?>" target="_blank" class="btn btn-sm btn-primary">
                                                                <i class="fas fa-download"></i> Download Proposal
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Approve Modal -->
                                        <div class="modal fade" id="approveModal<?php echo $submission['event_id']; ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Setujui Pengajuan</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="event_id" value="<?php echo $submission['event_id']; ?>">
                                                            <p>Yakin ingin menyetujui pengajuan event "<strong><?php echo htmlspecialchars($submission['judul_event']); ?></strong>"?</p>
                                                            <div class="mb-3">
                                                                <label for="catatan_admin" class="form-label">Catatan Admin</label>
                                                                <textarea class="form-control" name="catatan_admin" rows="3" placeholder="Berikan catatan untuk pengaju..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" name="approve_event" class="btn btn-success">Setujui</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Reject Modal -->
                                        <div class="modal fade" id="rejectModal<?php echo $submission['event_id']; ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Tolak Pengajuan</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="event_id" value="<?php echo $submission['event_id']; ?>">
                                                            <p>Yakin ingin menolak pengajuan event "<strong><?php echo htmlspecialchars($submission['judul_event']); ?></strong>"?</p>
                                                            <div class="mb-3">
                                                                <label for="catatan_admin" class="form-label">Alasan Penolakan</label>
                                                                <textarea class="form-control" name="catatan_admin" rows="3" placeholder="Berikan alasan penolakan..." required></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" name="reject_event" class="btn btn-danger">Tolak</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>