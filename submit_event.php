<?php
include 'config.php';
include 'session.php';

// Redirect jika user belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $judul_kegiatan = $_POST['judul_kegiatan'];
    $event_ekskul = $_POST['event_ekskul'];
    $tanggal_pengajuan = $_POST['tanggal_pengajuan'];
    $total_biaya = $_POST['total_biaya'];
    
    // Handle file upload
    $proposal = null;
    if (isset($_FILES['proposal']) && $_FILES['proposal']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['proposal']['tmp_name'];
        $file_name = $_FILES['proposal']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Validate file type
        if ($file_ext !== 'pdf') {
            header("Location: home.php?status=error&message=invalid_file");
            exit();
        }
        
        // Generate unique filename
        $new_filename = uniqid() . '_' . $file_name;
        $upload_path = 'uploads/' . $new_filename;
        
        // Create uploads directory if it doesn't exist
        if (!file_exists('uploads')) {
            mkdir('uploads', 0777, true);
        }
        
        // Move uploaded file
        if (move_uploaded_file($file_tmp, $upload_path)) {
            $proposal = $new_filename;
        } else {
            header("Location: home.php?status=error&message=upload_failed");
            exit();
        }
    } else {
        header("Location: home.php?status=error&message=no_file");
        exit();
    }
    
    // Insert into database
    $query = "INSERT INTO event_pengajuan (user_id, judul_kegiatan, event_ekskul, tanggal_pengajuan, total_biaya, proposal, status) 
              VALUES (?, ?, ?, ?, ?, ?, 'Pending')";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isssds", $user_id, $judul_kegiatan, $event_ekskul, $tanggal_pengajuan, $total_biaya, $proposal);
    
    if ($stmt->execute()) {
        header("Location: home.php?status=success");
        exit();
    } else {
        // Log the error for debugging
        error_log("Database Error: " . $stmt->error);
        header("Location: home.php?status=error&message=" . urlencode($stmt->error));
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajukan Event Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .content-container {
            padding: 20px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .form-section {
            max-width: 800px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <?php include 'components/header.php'; ?>

    <div class="container content-container">
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                        <li class="breadcrumb-item active">Ajukan Event Baru</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="form-section">
                    <h2 class="mb-4">Ajukan Event Baru</h2>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="judul_kegiatan" class="form-label">Judul Kegiatan</label>
                            <input type="text" class="form-control" id="judul_kegiatan" name="judul_kegiatan" required>
                        </div>

                        <div class="mb-3">
                            <label for="event_ekskul" class="form-label">Event Ekstrakurikuler</label>
                            <input type="text" class="form-control" id="event_ekskul" name="event_ekskul" required>
                        </div>

                        <div class="mb-3">
                            <label for="tanggal_pengajuan" class="form-label">Tanggal Pengajuan</label>
                            <input type="date" class="form-control" id="tanggal_pengajuan" name="tanggal_pengajuan" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="total_biaya" class="form-label">Total Biaya</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="total_biaya" name="total_biaya" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="proposal" class="form-label">File Proposal (PDF)</label>
                            <input type="file" class="form-control" id="proposal" name="proposal" accept=".pdf" required>
                            <div class="form-text">Upload file proposal dalam format PDF.</div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send"></i> Ajukan Event
                            </button>
                            <a href="home.php" class="btn btn-outline-secondary">
                                <i class="bi bi-x"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 