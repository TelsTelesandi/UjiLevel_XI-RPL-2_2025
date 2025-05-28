<?php
require_once 'config/database.php';
require_once 'config/session.php';

requireAdmin();

$database = new Database();
$db = $database->getConnection();

$success = '';
$error = '';

// Handle verification actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_verification'])) {
        $verifikasi_id = $_POST['verifikasi_id'];
        $catatan_admin = trim($_POST['catatan_admin']);
        $status = $_POST['status'];
        
        if (empty($catatan_admin)) {
            $error = 'Catatan verifikasi harus diisi!';
        } else {
            $update_query = "UPDATE verifikasi_event SET catatan_admin = ?, Status = ? WHERE verifikasi_id = ?";
            $update_stmt = $db->prepare($update_query);
            
            if ($update_stmt->execute([$catatan_admin, $status, $verifikasi_id])) {
                $success = 'Verifikasi berhasil diperbarui!';
            } else {
                $error = 'Terjadi kesalahan saat memperbarui verifikasi!';
            }
        }
    } elseif (isset($_POST['add_verification_note'])) {
        $event_id = $_POST['event_id'];
        $catatan_admin = trim($_POST['catatan_admin']);
        $admin_id = $_SESSION['user_id'];
        
        if (empty($catatan_admin)) {
            $error = 'Catatan verifikasi harus diisi!';
        } else {
            // Check if verification record exists
            $check_query = "SELECT verifikasi_id FROM verifikasi_event WHERE event_id = ?";
            $check_stmt = $db->prepare($check_query);
            $check_stmt->execute([$event_id]);
            
            if ($check_stmt->rowCount() > 0) {
                // Update existing verification
                $verifikasi = $check_stmt->fetch(PDO::FETCH_ASSOC);
                $update_query = "UPDATE verifikasi_event SET catatan_admin = CONCAT(catatan_admin, '\n\n--- Update ', NOW(), ' ---\n', ?), tanggal_verifikasi = CURDATE() WHERE verifikasi_id = ?";
                $update_stmt = $db->prepare($update_query);
                $update_stmt->execute([$catatan_admin, $verifikasi['verifikasi_id']]);
            } else {
                // Create new verification record
                $insert_query = "INSERT INTO verifikasi_event (event_id, admin_id, tanggal_verifikasi, catatan_admin, Status) VALUES (?, ?, CURDATE(), ?, 'unclosed')";
                $insert_stmt = $db->prepare($insert_query);
                $insert_stmt->execute([$event_id, $admin_id, $catatan_admin]);
            }
            
            $success = 'Catatan verifikasi berhasil ditambahkan!';
        }
    }
}

// Filter functionality
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build query with filters
$where_conditions = ["ep.status = 'disetujui'"];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(ep.judul_event LIKE ? OR u.nama_lengkap LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
}

if (!empty($status_filter)) {
    if ($status_filter == 'verified') {
        $where_conditions[] = "ve.Status = 'Closed'";
    } elseif ($status_filter == 'unverified') {
        $where_conditions[] = "(ve.Status = 'unclosed' OR ve.Status IS NULL)";
    }
}

$where_clause = 'WHERE ' . implode(' AND ', $where_conditions);

// Get approved events with verification status
$query = "SELECT ep.*, u.nama_lengkap, u.username, u.Ekskul, 
          ve.verifikasi_id, ve.tanggal_verifikasi, ve.catatan_admin as catatan_verifikasi, ve.Status as status_verifikasi,
          admin.nama_lengkap as admin_verifikator
          FROM event_pengajuan ep 
          JOIN users u ON ep.user_id = u.user_id 
          LEFT JOIN verifikasi_event ve ON ep.event_id = ve.event_id
          LEFT JOIN users admin ON ve.admin_id = admin.user_id
          $where_clause
          ORDER BY 
            CASE WHEN ve.Status = 'unclosed' OR ve.Status IS NULL THEN 1 ELSE 2 END,
            ep.tanggal_pengajuan DESC";
$stmt = $db->prepare($query);
$stmt->execute($params);
$approved_events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get verification statistics
$stats_query = "SELECT 
    COUNT(DISTINCT ep.event_id) as total_approved,
    SUM(CASE WHEN ve.Status = 'Closed' THEN 1 ELSE 0 END) as verified,
    SUM(CASE WHEN ve.Status = 'unclosed' OR ve.Status IS NULL THEN 1 ELSE 0 END) as pending_verification
    FROM event_pengajuan ep 
    LEFT JOIN verifikasi_event ve ON ep.event_id = ve.event_id
    WHERE ep.status = 'disetujui'";
$stats_stmt = $db->prepare($stats_query);
$stats_stmt->execute();
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

// Get recent verification activities
$activity_query = "SELECT ve.*, ep.judul_event, u.nama_lengkap as pengaju, admin.nama_lengkap as admin_name
                   FROM verifikasi_event ve
                   JOIN event_pengajuan ep ON ve.event_id = ep.event_id
                   JOIN users u ON ep.user_id = u.user_id
                   JOIN users admin ON ve.admin_id = admin.user_id
                   ORDER BY ve.tanggal_verifikasi DESC
                   LIMIT 5";
$activity_stmt = $db->prepare($activity_query);
$activity_stmt->execute();
$recent_activities = $activity_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Event - Event Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
        }
        .verification-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .verification-card:hover {
            transform: translateY(-2px);
        }
        .status-verified {
            border-left: 4px solid #28a745;
        }
        .status-pending {
            border-left: 4px solid #ffc107;
        }
        .status-unverified {
            border-left: 4px solid #dc3545;
        }
        .activity-timeline {
            border-left: 2px solid #dee2e6;
            padding-left: 20px;
        }
        .activity-item {
            position: relative;
            margin-bottom: 20px;
        }
        .activity-item::before {
            content: '';
            position: absolute;
            left: -26px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #007bff;
        }
        .verification-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
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
                        <h5 class="mt-2"><?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?></h5>
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
                            <a class="nav-link text-white" href="admin_pengajuan.php">
                                <i class="fas fa-file-alt"></i> Kelola Pengajuan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white active" href="verifikasi.php">
                                <i class="fas fa-check-circle"></i> Verifikasi Event
                                <?php if ($stats['pending_verification'] > 0): ?>
                                    <span class="badge bg-warning ms-2"><?php echo $stats['pending_verification']; ?></span>
                                <?php endif; ?>
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
                    <h1 class="h2">Verifikasi Event</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <span class="badge bg-primary me-1">Total: <?php echo $stats['total_approved']; ?></span>
                            <span class="badge bg-warning me-1">Pending: <?php echo $stats['pending_verification']; ?></span>
                            <span class="badge bg-success">Verified: <?php echo $stats['verified']; ?></span>
                        </div>
                    </div>
                </div>

                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Filter and Search -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-6">
                                <label for="search" class="form-label">Cari Event</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control" id="search" name="search" 
                                           value="<?php echo htmlspecialchars($search); ?>" 
                                           placeholder="Judul event atau nama pengaju...">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="status" class="form-label">Filter Status Verifikasi</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">Semua Status</option>
                                    <option value="unverified" <?php echo $status_filter == 'unverified' ? 'selected' : ''; ?>>Belum Diverifikasi</option>
                                    <option value="verified" <?php echo $status_filter == 'verified' ? 'selected' : ''; ?>>Sudah Diverifikasi</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-filter"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </form>
                        <?php if (!empty($search) || !empty($status_filter)): ?>
                            <div class="mt-2">
                                <a href="verifikasi.php" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-times"></i> Reset Filter
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row">
                    <!-- Events List -->
                    <div class="col-lg-8">
                        <div class="row">
                            <?php if (count($approved_events) > 0): ?>
                                <?php foreach ($approved_events as $event): ?>
                                    <div class="col-md-6 mb-4">
                                        <div class="card verification-card 
                                            <?php 
                                            if ($event['status_verifikasi'] == 'Closed') {
                                                echo 'status-verified';
                                            } elseif ($event['status_verifikasi'] == 'unclosed') {
                                                echo 'status-pending';
                                            } else {
                                                echo 'status-unverified';
                                            }
                                            ?>">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-0"><?php echo htmlspecialchars($event['nama_lengkap']); ?></h6>
                                                    <small class="text-muted">@<?php echo htmlspecialchars($event['username']); ?></small>
                                                </div>
                                                <div>
                                                    <?php if ($event['status_verifikasi'] == 'Closed'): ?>
                                                        <span class="badge bg-success verification-badge">
                                                            <i class="fas fa-check-double"></i> Verified
                                                        </span>
                                                    <?php elseif ($event['status_verifikasi'] == 'unclosed'): ?>
                                                        <span class="badge bg-warning verification-badge">
                                                            <i class="fas fa-clock"></i> In Progress
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger verification-badge">
                                                            <i class="fas fa-exclamation"></i> Unverified
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <h5 class="card-title"><?php echo htmlspecialchars($event['judul_event']); ?></h5>
                                                <p class="card-text">
                                                    <small class="text-muted">
                                                        <i class="fas fa-tag"></i> <?php echo htmlspecialchars($event['jenis_kegiatan']); ?><br>
                                                        <i class="fas fa-money-bill-wave"></i> <?php if (isset($event['Total_pembiayaan'])): ?>
                                                            Rp <?php echo number_format($event['Total_pembiayaan'], 0, ',', '.'); ?>
                                                        <?php else: ?>
                                                            Rp 0
                                                        <?php endif; ?><br>
                                                        <i class="fas fa-calendar-check"></i> Disetujui: <?php echo date('d/m/Y', strtotime($event['tanggal_pengajuan'])); ?>
                                                        <?php if ($event['tanggal_verifikasi']): ?>
                                                            <br><i class="fas fa-calendar-alt"></i> Verifikasi: <?php echo date('d/m/Y', strtotime($event['tanggal_verifikasi'])); ?>
                                                        <?php endif; ?>
                                                    </small>
                                                </p>
                                                
                                                <?php if ($event['catatan_verifikasi']): ?>
                                                    <div class="alert alert-info alert-sm">
                                                        <strong>Catatan Verifikasi:</strong><br>
                                                        <?php echo nl2br(htmlspecialchars(substr($event['catatan_verifikasi'], 0, 100))); ?>
                                                        <?php if (strlen($event['catatan_verifikasi']) > 100): ?>...<?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="card-footer">
                                                <div class="btn-group w-100" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-info" 
                                                            data-bs-toggle="modal" data-bs-target="#detailModal<?php echo $event['event_id']; ?>">
                                                        <i class="fas fa-eye"></i> Detail
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-primary" 
                                                            data-bs-toggle="modal" data-bs-target="#verifyModal<?php echo $event['event_id']; ?>">
                                                        <i class="fas fa-check-circle"></i> Verifikasi
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Detail Modal -->
                                    <div class="modal fade" id="detailModal<?php echo $event['event_id']; ?>" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Detail Event - <?php echo htmlspecialchars($event['judul_event']); ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <strong>Pengaju:</strong><br>
                                                            <?php echo htmlspecialchars($event['nama_lengkap']); ?> 
                                                            (<?php echo htmlspecialchars($event['username']); ?>)
                                                            <?php if (!empty($event['Ekskul'])): ?>
                                                                <br><small class="text-muted">Ekstrakurikuler: <?php echo htmlspecialchars($event['Ekskul']); ?></small>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <strong>Status Verifikasi:</strong><br>
                                                            <?php if ($event['status_verifikasi'] == 'Closed'): ?>
                                                                <span class="badge bg-success fs-6">
                                                                    <i class="fas fa-check-double"></i> Verified
                                                                </span>
                                                                <?php if ($event['admin_verifikator']): ?>
                                                                    <br><small class="text-muted">oleh: <?php echo htmlspecialchars($event['admin_verifikator']); ?></small>
                                                                <?php endif; ?>
                                                            <?php elseif ($event['status_verifikasi'] == 'unclosed'): ?>
                                                                <span class="badge bg-warning fs-6">
                                                                    <i class="fas fa-clock"></i> In Progress
                                                                </span>
                                                            <?php else: ?>
                                                                <span class="badge bg-danger fs-6">
                                                                    <i class="fas fa-exclamation"></i> Unverified
                                                                </span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-md-8">
                                                            <strong>Judul Event:</strong><br>
                                                            <?php echo htmlspecialchars($event['judul_event']); ?>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <strong>Jenis Kegiatan:</strong><br>
                                                            <?php echo htmlspecialchars($event['jenis_kegiatan']); ?>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <strong>Total Pembiayaan:</strong><br>
                                                            <?php if (isset($event['Total_pembiayaan'])): ?>
                                                                <span class="text-success fs-5">Rp <?php echo number_format($event['Total_pembiayaan'], 0, ',', '.'); ?></span>
                                                            <?php else: ?>
                                                                <span class="text-muted">Rp 0</span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <strong>Tanggal Disetujui:</strong><br>
                                                            <?php echo date('d F Y', strtotime($event['tanggal_pengajuan'])); ?>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <strong>Deskripsi Event:</strong><br>
                                                    <div class="bg-light p-3 rounded">
                                                        <?php echo nl2br(htmlspecialchars($event['deskripsi'])); ?>
                                                    </div>
                                                    
                                                    <?php if ($event['catatan_verifikasi']): ?>
                                                        <hr>
                                                        <strong>Catatan Verifikasi:</strong><br>
                                                        <div class="bg-info bg-opacity-10 p-3 rounded">
                                                            <?php echo nl2br(htmlspecialchars($event['catatan_verifikasi'])); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    
                                                    <hr>
                                                    <?php if (isset($event['Proposal']) && $event['Proposal']): ?>
                                                        <strong>Proposal:</strong><br>
                                                        <a href="../uploads/proposals/<?php echo $event['Proposal']; ?>" 
                                                           target="_blank" class="btn btn-primary">
                                                            <i class="fas fa-download"></i> Download Proposal
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-primary" 
                                                            data-bs-toggle="modal" data-bs-target="#verifyModal<?php echo $event['event_id']; ?>"
                                                            data-bs-dismiss="modal">
                                                        <i class="fas fa-check-circle"></i> Verifikasi
                                                    </button>
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Verification Modal -->
                                    <div class="modal fade" id="verifyModal<?php echo $event['event_id']; ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title">
                                                        <i class="fas fa-check-circle"></i> Verifikasi Event
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form method="POST">
                                                    <div class="modal-body">
                                                        <?php if ($event['verifikasi_id']): ?>
                                                            <input type="hidden" name="verifikasi_id" value="<?php echo $event['verifikasi_id']; ?>">
                                                        <?php else: ?>
                                                            <input type="hidden" name="event_id" value="<?php echo $event['event_id']; ?>">
                                                        <?php endif; ?>
                                                        
                                                        <div class="alert alert-info">
                                                            <i class="fas fa-info-circle"></i>
                                                            Event: <strong><?php echo htmlspecialchars($event['judul_event']); ?></strong><br>
                                                            Pengaju: <strong><?php echo htmlspecialchars($event['nama_lengkap']); ?></strong>
                                                        </div>
                                                        
                                                        <?php if ($event['verifikasi_id']): ?>
                                                            <div class="mb-3">
                                                                <label for="status" class="form-label">Status Verifikasi</label>
                                                                <select class="form-select" name="status" required>
                                                                    <option value="unclosed" <?php echo $event['status_verifikasi'] == 'unclosed' ? 'selected' : ''; ?>>In Progress</option>
                                                                    <option value="Closed" <?php echo $event['status_verifikasi'] == 'Closed' ? 'selected' : ''; ?>>Completed</option>
                                                                </select>
                                                            </div>
                                                        <?php endif; ?>
                                                        
                                                        <div class="mb-3">
                                                            <label for="catatan_admin" class="form-label">
                                                                <?php echo $event['verifikasi_id'] ? 'Update Catatan Verifikasi' : 'Catatan Verifikasi'; ?> *
                                                            </label>
                                                            <textarea class="form-control" name="catatan_admin" rows="4" 
                                                                      placeholder="Berikan catatan verifikasi, instruksi pelaksanaan, atau feedback untuk event ini..." required><?php echo $event['verifikasi_id'] ? '' : ''; ?></textarea>
                                                            <?php if ($event['catatan_verifikasi']): ?>
                                                                <div class="form-text">
                                                                    <strong>Catatan sebelumnya:</strong><br>
                                                                    <small><?php echo nl2br(htmlspecialchars($event['catatan_verifikasi'])); ?></small>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" name="<?php echo $event['verifikasi_id'] ? 'update_verification' : 'add_verification_note'; ?>" class="btn btn-primary">
                                                            <i class="fas fa-save"></i> Simpan Verifikasi
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body text-center py-5">
                                            <i class="fas fa-clipboard-check fa-4x text-muted mb-4"></i>
                                            <h4 class="text-muted">Tidak ada event untuk diverifikasi</h4>
                                            <p class="text-muted">
                                                <?php if (!empty($search) || !empty($status_filter)): ?>
                                                    Coba ubah filter pencarian atau <a href="verifikasi.php">reset filter</a>
                                                <?php else: ?>
                                                    Belum ada event yang disetujui dan memerlukan verifikasi
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Sidebar Info -->
                    <div class="col-lg-4">
                        <!-- Statistics Card -->
                        <div class="card mb-4">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-chart-bar"></i> Statistik Verifikasi</h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-4">
                                        <h4 class="text-primary"><?php echo $stats['total_approved']; ?></h4>
                                        <small class="text-muted">Total Event</small>
                                    </div>
                                    <div class="col-4">
                                        <h4 class="text-warning"><?php echo $stats['pending_verification']; ?></h4>
                                        <small class="text-muted">Pending</small>
                                    </div>
                                    <div class="col-4">
                                        <h4 class="text-success"><?php echo $stats['verified']; ?></h4>
                                        <small class="text-muted">Verified</small>
                                    </div>
                                </div>
                                <div class="progress mt-3" style="height: 10px;">
                                    <?php 
                                    $verified_percentage = $stats['total_approved'] > 0 ? ($stats['verified'] / $stats['total_approved']) * 100 : 0;
                                    ?>
                                    <div class="progress-bar bg-success" style="width: <?php echo $verified_percentage; ?>%"></div>
                                </div>
                                <small class="text-muted">Progress Verifikasi: <?php echo number_format($verified_percentage, 1); ?>%</small>
                            </div>
                        </div>

                        <!-- Recent Activities -->
                        <?php if (count($recent_activities) > 0): ?>
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-history"></i> Aktivitas Verifikasi Terbaru</h6>
                            </div>
                            <div class="card-body">
                                <div class="activity-timeline">
                                    <?php foreach ($recent_activities as $activity): ?>
                                        <div class="activity-item">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($activity['judul_event']); ?></h6>
                                                    <p class="mb-1">
                                                        <small class="text-muted">
                                                            Pengaju: <?php echo htmlspecialchars($activity['pengaju']); ?><br>
                                                            Verifikator: <?php echo htmlspecialchars($activity['admin_name']); ?>
                                                        </small>
                                                    </p>
                                                    <small class="text-muted">
                                                        <?php echo date('d/m/Y', strtotime($activity['tanggal_verifikasi'])); ?>
                                                    </small>
                                                </div>
                                                <span class="badge bg-<?php echo $activity['Status'] == 'Closed' ? 'success' : 'warning'; ?> badge-sm">
                                                    <?php echo $activity['Status'] == 'Closed' ? 'Completed' : 'In Progress'; ?>
                                                </span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-submit form when filter changes
        document.getElementById('status').addEventListener('change', function() {
            this.form.submit();
        });

        // Real-time search
        let searchTimeout;
        document.getElementById('search').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.form.submit();
            }, 500);
        });

        // Confirmation for verification actions
        document.querySelectorAll('form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                if (this.querySelector('button[name="update_verification"]') || this.querySelector('button[name="add_verification_note"]')) {
                    const catatan = this.querySelector('textarea[name="catatan_admin"]').value.trim();
                    if (catatan.length < 10) {
                        e.preventDefault();
                        alert('Catatan verifikasi harus minimal 10 karakter!');
                        return false;
                    }
                }
            });
        });

        // Auto-resize textarea
        document.querySelectorAll('textarea').forEach(function(textarea) {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });
        });
    </script>
</body>
</html>