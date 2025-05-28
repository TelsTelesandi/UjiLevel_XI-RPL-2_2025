<?php
require_once 'config/database.php';
require_once 'config/session.php';

requireLogin();

$database = new Database();
$db = $database->getConnection();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul_event = $_POST['judul_event'];
    $jenis_kegiatan = $_POST['jenis_kegiatan'];
    $total_pembiayaan = $_POST['total_pembiayaan'];
    $deskripsi = $_POST['deskripsi'];
    $user_id = $_SESSION['user_id'];
    
    // Handle file upload
    $proposal_file = '';
    if (isset($_FILES['proposal']) && $_FILES['proposal']['error'] == 0) {
        $upload_dir = '../uploads/proposals/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['proposal']['name'], PATHINFO_EXTENSION);
        $proposal_file = 'proposal_' . $user_id . '_' . time() . '.' . $file_extension;
        $upload_path = $upload_dir . $proposal_file;
        
        if (move_uploaded_file($_FILES['proposal']['tmp_name'], $upload_path)) {
            // File uploaded successfully
        } else {
            $error = 'Gagal mengupload file proposal!';
        }
    }
    
    if (empty($error)) {
        $query = "INSERT INTO event_pengajuan (user_id, judul_event, jenis_kegiatan, Total_pembiayaan, Proposal, deskripsi, tanggal_pengajuan, status) VALUES (?, ?, ?, ?, ?, ?, CURDATE(), 'menunggu')";
        $stmt = $db->prepare($query);
        
        if ($stmt->execute([$user_id, $judul_event, $jenis_kegiatan, $total_pembiayaan, $proposal_file, $deskripsi])) {
            $success = 'Pengajuan event berhasil dikirim!';
        } else {
            $error = 'Terjadi kesalahan saat mengirim pengajuan!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajukan Event - Event Manager</title>
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
                            <a class="nav-link text-white active" href="user_pengajuan.php">
                                <i class="fas fa-plus-circle"></i> Ajukan Event
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="riwayat.php">
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
                    <h1 class="h2">Ajukan Event</h1>
                </div>

                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-plus-circle"></i> Form Pengajuan Event</h5>
                            </div>
                            <div class="card-body">
                                <?php if ($success): ?>
                                    <div class="alert alert-success"><?php echo $success; ?></div>
                                <?php endif; ?>
                                
                                <?php if ($error): ?>
                                    <div class="alert alert-danger"><?php echo $error; ?></div>
                                <?php endif; ?>
                                
                                <form method="POST" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label for="judul_event" class="form-label">Judul Event</label>
                                        <input type="text" class="form-control" id="judul_event" name="judul_event" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="jenis_kegiatan" class="form-label">Jenis Kegiatan</label>
                                        <select class="form-select" id="jenis_kegiatan" name="jenis_kegiatan" required>
                                            <option value="">Pilih Jenis Kegiatan</option>
                                            <option value="Seminar">Seminar</option>
                                            <option value="Workshop">Workshop</option>
                                            <option value="Kompetisi">Kompetisi</option>
                                            <option value="Pameran">Pameran</option>
                                            <option value="Konser">Konser</option>
                                            <option value="Olahraga">Olahraga</option>
                                            <option value="Lainnya">Lainnya</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="total_pembiayaan" class="form-label">Total Pembiayaan</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control" id="total_pembiayaan" name="total_pembiayaan" required>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="proposal" class="form-label">Upload Proposal</label>
                                        <input type="file" class="form-control" id="proposal" name="proposal" accept=".pdf,.doc,.docx" required>
                                        <div class="form-text">Format yang diizinkan: PDF, DOC, DOCX. Maksimal 5MB.</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="deskripsi" class="form-label">Deskripsi Event</label>
                                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" required></textarea>
                                    </div>
                                    
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <button type="reset" class="btn btn-secondary me-md-2">Reset</button>
                                        <button type="submit" class="btn btn-primary">Kirim Pengajuan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>