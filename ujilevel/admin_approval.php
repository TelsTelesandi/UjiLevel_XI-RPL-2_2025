<?php
session_start();
require 'database/config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Proses approval
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = $_POST['event_id'];
    $status = $_POST['status'];
    $catatan = mysqli_real_escape_string($conn, $_POST['catatan']);
    
    // Update status event
    $update_query = mysqli_query($conn, "UPDATE event_pengajuan SET status = '$status' WHERE event_id = $event_id");
    
    if (!$update_query) {
        http_response_code(500);
        echo json_encode(['error' => 'Error updating event: ' . mysqli_error($conn)]);
        exit();
    }
    
    // Tambahkan verifikasi
    $admin_id = $_SESSION['user_id'];
    $tanggal_verifikasi = date('Y-m-d');
    $insert_query = mysqli_query($conn, "INSERT INTO verifikasi_event (event_id, admin_id, tanggal_verifikasi, status, catatan_admin) 
                         VALUES ($event_id, $admin_id, '$tanggal_verifikasi', '$status', '$catatan')");
    
    if (!$insert_query) {
        http_response_code(500);
        echo json_encode(['error' => 'Error inserting verification: ' . mysqli_error($conn)]);
        exit();
    }
    
    echo json_encode(['success' => 'Pengajuan berhasil diproses!']);
    exit();
}

// Check if event ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: admin_events.php');
    exit();
}

// Ambil data event
$event_id = intval($_GET['id']);
$event_query = mysqli_query($conn, "SELECT e.*, u.nama_lengkap, u.ekskul 
                                   FROM event_pengajuan e 
                                   JOIN users u ON e.user_id = u.user_id 
                                   WHERE e.event_id = $event_id");

if (!$event_query) {
    die("Error fetching event: " . mysqli_error($conn));
}

$event = mysqli_fetch_assoc($event_query);

// Check if event exists
if (!$event) {
    header('Location: admin_events.php');
    exit();
}

// Get previous verification if exists
$verification_query = mysqli_query($conn, "SELECT * FROM verifikasi_event 
                                         WHERE event_id = $event_id 
                                         ORDER BY verifikasi_id DESC LIMIT 1");
$verification = mysqli_fetch_assoc($verification_query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Pengajuan Event</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4b5aaf;
            --secondary-color: #90a4ae;
            --card-bg: #ffffff;
            --text-color: #2d3748;
            --shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            --border-color: #e0e0e0;
        }

        body {
            font-family: 'Inter', 'Roboto', system-ui, -apple-system, sans-serif;
            background: #ffffff;
            margin: 0;
            padding: 0;
            color: var(--text-color);
            font-size: 0.95rem;
            line-height: 1.7;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .sidebar {
            height: 100vh;
            background: #f5f5f5;
            color: #333;
            position: fixed;
            width: 220px;
            transition: all 0.3s;
            z-index: 1000;
            border-right: 1px solid var(--border-color);
        }

        .sidebar-header {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid var(--border-color);
        }

        .sidebar-header h3 {
            font-size: 1.25rem;
            font-weight: 500;
            margin: 0;
            color: var(--primary-color);
        }

        .sidebar-menu {
            padding: 0;
            list-style: none;
        }

        .sidebar-menu li {
            padding: 10px 15px;
            transition: all 0.3s;
        }

        .sidebar-menu li:hover {
            background: #e8ecef;
        }

        .sidebar-menu li a {
            color: var(--text-color);
            text-decoration: none;
            display: flex;
            align-items: center;
            font-size: 0.95rem;
            font-weight: 400;
        }

        .sidebar-menu li i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
            color: var(--primary-color);
        }

        .sidebar-menu li.active {
            background: #e0e7ff;
        }

        .main-content {
            margin-left: 220px;
            padding: 20px;
            min-height: 100vh;
            background: #ffffff;
        }

        .card {
            background: var(--card-bg);
            border-radius: 8px;
            border: 1px solid var(--border-color);
            padding: 15px;
            margin-bottom: 15px;
        }

        .card-header {
            background: var(--card-bg);
            color: var(--text-color);
            border-bottom: 1px solid var(--border-color);
            padding: 10px 15px;
            border-radius: 8px 8px 0 0;
        }

        .card-header h4 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 500;
        }

        .form-label {
            font-size: 0.9rem;
            font-weight: 500;
            color: #555;
        }

        .form-control, .form-select {
            border-radius: 5px;
            border: 1px solid var(--border-color);
            padding: 8px;
            font-size: 0.9rem;
            font-weight: 400;
        }

        .form-control:disabled {
            background-color: #f5f5f5;
            cursor: not-allowed;
        }

        .btn-primary {
            background: var(--primary-color);
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            color: white;
            font-size: 0.9rem;
            font-weight: 500;
            transition: background 0.3s;
        }

        .btn-primary:hover {
            background: #3f4a8f;
        }

        .btn-secondary {
            background: #e0e0e0;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            color: #333;
            font-size: 0.9rem;
            font-weight: 500;
            transition: background 0.3s;
        }

        .btn-secondary:hover {
            background: #d0d0d0;
        }

        .btn-info {
            background: var(--primary-color);
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            color: white;
            font-size: 0.9rem;
            font-weight: 500;
            transition: background 0.3s;
        }

        .btn-info:hover {
            background: #3f4a8f;
        }
    </style>
</head>
<body>
    <!-- Include sidebar -->
    <?php include 'includes/admin_sidebar.php'; ?>
    
    <div class="main-content">
        <div class="container">
            <div class="row">
                <div class="col-md-8 mx-auto">
                    <!-- Form Approval -->
                    <div class="card">
                        <div class="card-header">
                            <h4>Review Pengajuan: <?= htmlspecialchars($event['judul_event']) ?></h4>
                        </div>
                        <div class="card-body">
                            <form method="POST" id="reviewForm">
                                <input type="hidden" name="event_id" value="<?= $event['event_id'] ?>">
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Judul Event</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($event['judul_event']) ?>" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Ekstrakulikuler</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($event['ekskul']) ?>" readonly>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Jenis Kegiatan</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($event['jenis_kegiatan']) ?>" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Total Pembiayaan</label>
                                        <input type="text" class="form-control" value="Rp <?= number_format($event['total_pembiayaan'], 0, ',', '.') ?>" readonly>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Deskripsi</label>
                                    <textarea class="form-control" rows="3" readonly><?= htmlspecialchars($event['deskripsi']) ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Proposal</label>
                                    <?php if (!empty($event['proposal'])): ?>
                                        <div>
                                            <a href="Uploads/<?= $event['proposal'] ?>" target="_blank" class="btn btn-sm btn-info">
                                                <i class="fas fa-file-pdf"></i> Lihat Proposal
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted">Tidak ada proposal</p>
                                    <?php endif; ?>
                                </div>
                                
                                <hr>
                                
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" id="status" class="form-select" required>
                                        <option value="pending" <?= $event['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="approved" <?= $event['status'] == 'approved' ? 'selected' : '' ?>>Approved</option>
                                        <option value="rejected" <?= $event['status'] == 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Catatan Admin</label>
                                    <textarea name="catatan" id="catatan" class="form-control" rows="3" <?= $event['status'] == 'approved' ? 'disabled' : '' ?>><?= isset($verification['catatan_admin']) ? htmlspecialchars($verification['catatan_admin']) : '' ?></textarea>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <a href="admin_events.php" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Tambahkan efek aktif pada menu sidebar
        document.querySelectorAll('.sidebar-menu li').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelectorAll('.sidebar-menu li').forEach(i => i.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Kontrol Catatan Admin berdasarkan status
        document.addEventListener('DOMContentLoaded', function() {
            const statusSelect = document.getElementById('status');
            const catatanTextarea = document.getElementById('catatan');

            function toggleCatatan() {
                if (statusSelect.value === 'approved') {
                    catatanTextarea.disabled = true;
                    catatanTextarea.value = ''; // Kosongkan catatan saat status approved
                } else {
                    catatanTextarea.disabled = false;
                }
            }

            // Jalankan saat halaman dimuat
            toggleCatatan();

            // Dengarkan perubahan pada select status
            statusSelect.addEventListener('change', toggleCatatan);
        });
    </script>
</body>
</html>