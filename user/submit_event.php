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

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul_event = $_POST['judul_event'];
    $jenis_kegiatan = $_POST['jenis_kegiatan'];
    $total_pembiayaan = $_POST['total_pembiayaan'];
    $deskripsi = $_POST['deskripsi'];
    $tanggal_pengajuan = $_POST['tanggal_pengajuan'];
    
    // Handle file upload
    $proposal_file = '';
    if (isset($_FILES['proposal']) && $_FILES['proposal']['error'] === 0) {
        $upload_dir = '../uploads/proposals/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['proposal']['name'], PATHINFO_EXTENSION);
        $proposal_file = 'proposal_' . time() . '.' . $file_extension;
        $upload_path = $upload_dir . $proposal_file;
        
        if (!move_uploaded_file($_FILES['proposal']['tmp_name'], $upload_path)) {
            $error = 'Gagal mengupload file proposal!';
        }
    }
    
    if (!$error) {
        $status = 'menunggu';
        $query = "INSERT INTO event_pengajuan (user_id, judul_event, jenis_kegiatan, total_pembiayaan, proposal, deskripsi, tanggal_pengajuan, status) 
                  VALUES (:user_id, :judul_event, :jenis_kegiatan, :total_pembiayaan, :proposal, :deskripsi, :tanggal_pengajuan, :status)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->bindParam(':judul_event', $judul_event);
        $stmt->bindParam(':jenis_kegiatan', $jenis_kegiatan);
        $stmt->bindParam(':total_pembiayaan', $total_pembiayaan);
        $stmt->bindParam(':proposal', $proposal_file);
        $stmt->bindParam(':deskripsi', $deskripsi);
        $stmt->bindParam(':tanggal_pengajuan', $tanggal_pengajuan);
        $stmt->bindParam(':status', $status);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = 'Event berhasil diajukan!';
            $_SESSION['message_type'] = 'success';
            header("Location: my_events.php");
            exit();
        } else {
            $error = 'Gagal mengajukan event!';
        }
    }
}

// Jika ada error, simpan ke session
if ($error) {
    $_SESSION['message'] = $error;
    $_SESSION['message_type'] = 'danger';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajukan Event - Sistem Pengajuan Event</title>
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
            <div class="col-md-3 col-lg-2 sidebar text-white p-0">
                <div class="p-3">
                    <h4 class="mb-4">User Panel</h4>
                    <ul class="nav flex-column">
                        <li class="nav-item mb-2">
                            <a class="nav-link text-white" href="dashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link text-white active" href="submit_event.php">
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
                    <h2 class="mb-4">Ajukan Event Baru</h2>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?php echo $success; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="judul_event" class="form-label">Judul Event *</label>
                                        <input type="text" class="form-control" id="judul_event" name="judul_event" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="jenis_kegiatan" class="form-label">Jenis Kegiatan *</label>
                                        <select class="form-select" id="jenis_kegiatan" name="jenis_kegiatan" required>
                                            <option value="">Pilih Jenis Kegiatan</option>
                                            <option value="Olahraga">Olahraga</option>
                                            <option value="Seni">Seni</option>
                                            <option value="Akademik">Akademik</option>
                                            <option value="Sosial">Sosial</option>
                                            <option value="Kesehatan">Kesehatan</option>
                                            <option value="Lainnya">Lainnya</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="total_pembiayaan" class="form-label">Total Pembiayaan (Rp) *</label>
                                        <input type="number" class="form-control" id="total_pembiayaan" name="total_pembiayaan" min="0" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="tanggal_pengajuan" class="form-label">Tanggal Pengajuan *</label>
                                        <input type="date" class="form-control" id="tanggal_pengajuan" name="tanggal_pengajuan" 
                                               value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="proposal" class="form-label">File Proposal (PDF/DOC/DOCX)</label>
                                    <input type="file" class="form-control" id="proposal" name="proposal" 
                                           accept=".pdf,.doc,.docx">
                                    <div class="form-text">Maksimal ukuran file 5MB</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="deskripsi" class="form-label">Deskripsi Event *</label>
                                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="5" required></textarea>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane me-2"></i>Ajukan Event
                                    </button>
                                    <a href="dashboard.php" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Kembali
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
