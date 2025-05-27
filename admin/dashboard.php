<?php
include '../config.php';
include 'check_admin.php';

// Handle event approval/rejection
if (isset($_POST['action']) && isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];
    $status = $_POST['action'] === 'approve' ? 'Disetujui' : 'Ditolak';
    
    $stmt = $conn->prepare("UPDATE event_pengajuan SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $event_id);
    $stmt->execute();
    
}

// Handle event deletion
if (isset($_POST['delete_event']) && isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];
    
    // First, get the proposal file name to delete it
    $stmt = $conn->prepare("SELECT proposal FROM event_pengajuan WHERE id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        if ($row['proposal']) {
            $proposal_path = "../uploads/" . $row['proposal'];
            if (file_exists($proposal_path)) {
                unlink($proposal_path); // Delete the proposal file
            }
        }
    }
    
    // Then delete the event record
    $stmt = $conn->prepare("DELETE FROM event_pengajuan WHERE id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
}

// Handle event status update
if (isset($_POST['update_status']) && isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];
    $new_status = $_POST['new_status'];
    $tanggal_verifikasi = date('Y-m-d');
    $catatan = $_POST['catatan'] ?? '';
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Update status di tabel event_pengajuan
        $stmt = $conn->prepare("UPDATE event_pengajuan SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $event_id);
        if (!$stmt->execute()) {
            throw new Exception("Gagal mengupdate status event");
        }
        $stmt->close();

        // Check if verifikasi record exists
        $check = $conn->prepare("SELECT id FROM verifikasi_event WHERE event_id = ?");
        if (!$check) {
            throw new Exception("Error preparing verification check: " . $conn->error);
        }
        $check->bind_param("i", $event_id);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            // Update existing verification
            $update = $conn->prepare("UPDATE verifikasi_event SET status = ?, tanggal_verifikasi = ?, catatan = ? WHERE event_id = ?");
            if (!$update) {
                throw new Exception("Error preparing verifikasi update: " . $conn->error);
            }
            $update->bind_param("sssi", $new_status, $tanggal_verifikasi, $catatan, $event_id);
            if (!$update->execute()) {
                throw new Exception("Gagal mengupdate data verifikasi: " . $update->error);
            }
        } else {
            // Insert new verification
            $stmt_verifikasi = $conn->prepare("INSERT INTO verifikasi_event (event_id, status, tanggal_verifikasi, catatan) VALUES (?, ?, ?, ?)");
            $stmt_verifikasi->bind_param("ssis", $event_id, $new_status, $tanggal_verifikasi, $catatan);
            if (!$stmt_verifikasi->execute()) {
                throw new Exception("Gagal menyimpan data verifikasi");
            }
        }

        $stmt_verifikasi->close();

        $conn->commit();
        $_SESSION['success'] = "Status event dan verifikasi berhasil diperbarui";
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}


// Handle user deletion
if (isset($_POST['delete_user']) && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    // Prevent admin from deleting themselves
    if ($user_id != $_SESSION['user_id']) {
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
    }
}

// Handle user role update
if (isset($_POST['update_role']) && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['new_role'];
    // Prevent admin from changing their own role
    if ($user_id != $_SESSION['user_id']) {
        $stmt = $conn->prepare("UPDATE users SET role = ? WHERE user_id = ?");
        $stmt->bind_param("si", $new_role, $user_id);
        $stmt->execute();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
        .stats-card {
            transition: transform 0.2s;
            cursor: pointer;
            border-radius: 15px;
            overflow: hidden;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .stats-card .card-body {
            padding: 1.5rem;
        }
        .card-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            opacity: 0.9;
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            background-color: #ffffff !important;
            padding: 1rem 0;
        }
        .navbar .nav-link {
            padding: 0.5rem 1.2rem;
            font-weight: 500;
            color: #6c757d;
            border-radius: 5px;
            transition: all 0.3s ease;
            margin: 0 0.2rem;
        }
        .navbar .nav-link:hover {
            color: #0d6efd;
            background-color: rgba(13, 110, 253, 0.05);
        }
        .navbar .nav-link.active {
            color: #0d6efd;
            background-color: rgba(13, 110, 253, 0.1);
        }
        .navbar .nav-link i {
            margin-right: 0.5rem;
        }
        .navbar-brand {
            font-weight: 600;
            font-size: 1.25rem;
            color: #212529;
        }
        .navbar-brand i {
            margin-right: 0.5rem;
            color: #0d6efd;
        }
        .table {
            vertical-align: middle;
        }
        .table th {
            font-weight: 600;
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }
        .table td {
            padding: 1rem 0.75rem;
        }
        .badge {
            padding: 0.5rem 0.8rem;
            font-weight: 500;
        }
        .btn-group .btn {
            padding: 0.375rem 1rem;
        }
        .btn-sm {
            padding: 0.25rem 0.8rem;
        }
        .search-box {
            max-width: 300px;
            margin-left: auto;
        }
        .search-box .input-group {
            border-radius: 20px;
            overflow: hidden;
        }
        .search-box .form-control {
            border-right: none;
        }
        .search-box .btn {
            border-left: none;
            background-color: #fff;
        }
        .modal-content {
            border: none;
            border-radius: 15px;
        }
        .modal-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        .modal-footer {
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }
        .status-badge {
            padding: 8px 12px;
            font-weight: 500;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .status-pending {
            background: linear-gradient(45deg, #ffc107 0%, #ff9800 100%);
            color: #fff;
        }
        .status-approved {
            background: linear-gradient(45deg, #28a745 0%, #20c997 100%);
            color: #fff;
        }
        .status-rejected {
            background: linear-gradient(45deg, #dc3545 0%, #c82333 100%);
            color: #fff;
        }
        .status-completed {
            background: linear-gradient(45deg, #6c757d 0%, #495057 100%);
            color: #fff;
        }
    </style>
</head>
<body>
    <?php include '../components/header.php'; ?>

    <!-- Navigation Menu -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="bi bi-shield-lock"></i>Admin Panel
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="bi bi-speedometer2"></i>Dashboard
                        </a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <span class="me-3 text-muted">
                        <i class="bi bi-person-circle"></i>
                        <?php echo htmlspecialchars($_SESSION['username']); ?>
                    </span>
                </div>
            </div>
        </div>
    </nav>

    <div class="container content-container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Quick Stats -->
        <div class="row g-4 mb-4">
            <div class="col">
                <div class="card stats-card bg-primary bg-gradient text-white">
                    <div class="card-body text-center">
                        <i class="bi bi-calendar-event card-icon"></i>
                        <h5 class="card-title mb-2">Total Events</h5>
                        <h3 class="mb-0">
                            <?php
                            $result = $conn->query("SELECT COUNT(*) as count FROM event_pengajuan");
                            echo $result->fetch_assoc()['count'];
                            ?>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card stats-card bg-warning bg-gradient text-dark">
                    <div class="card-body text-center">
                        <i class="bi bi-clock-history card-icon"></i>
                        <h5 class="card-title mb-2">Pending</h5>
                        <h3 class="mb-0">
                            <?php
                            $result = $conn->query("SELECT COUNT(*) as count FROM event_pengajuan WHERE status = 'Pending'");
                            echo $result->fetch_assoc()['count'];
                            ?>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card stats-card bg-success bg-gradient text-white">
                    <div class="card-body text-center">
                        <i class="bi bi-check-circle card-icon"></i>
                        <h5 class="card-title mb-2">Disetujui</h5>
                        <h3 class="mb-0">
                            <?php
                            $result = $conn->query("SELECT COUNT(*) as count FROM event_pengajuan WHERE status = 'Disetujui'");
                            echo $result->fetch_assoc()['count'];
                            ?>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card stats-card bg-danger bg-gradient text-white">
                    <div class="card-body text-center">
                        <i class="bi bi-x-circle card-icon"></i>
                        <h5 class="card-title mb-2">Ditolak</h5>
                        <h3 class="mb-0">
                            <?php
                            $result = $conn->query("SELECT COUNT(*) as count FROM event_pengajuan WHERE status = 'Ditolak'");
                            echo $result->fetch_assoc()['count'];
                            ?>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card stats-card bg-secondary bg-gradient text-white">
                    <div class="card-body text-center">
                        <i class="bi bi-flag card-icon"></i>
                        <h5 class="card-title mb-2">Selesai</h5>
                        <h3 class="mb-0">
                            <?php
                            $result = $conn->query("SELECT COUNT(*) as count FROM event_pengajuan WHERE status = 'Selesai'");
                            echo $result->fetch_assoc()['count'];
                            ?>
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons and Search -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEventModal">
                            <i class="bi bi-plus-circle me-1"></i>Tambah Event
                        </button>
                    </div>
                    <div class="col-md-6">
                        <div class="search-box">
                            <div class="input-group">
                                <input type="text" class="form-control" id="searchInput" placeholder="Cari event...">
                                <button class="btn btn-outline-secondary" type="button">
                                    <i class="bi bi-search"></i>
                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Events Table -->
        <div class="card mb-4">
            <div class="card-header py-3">
                <h5 class="card-title mb-0">Daftar Event</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Username</th>
                                <th>Judul Kegiatan</th>
                                <th>Event Ekskul</th>
                                <th>Total Biaya</th>
                                <th>Proposal</th>
                                <th>Status</th>
                                <th>Tanggal Verifikasi</th>
                                <th>Catatan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Query untuk mengambil data event dan verifikasi
                            $query = "SELECT 
                                        ep.*,
                                        u.username,
                                        ve.tanggal_verifikasi,
                                        ve.catatan
                                    FROM 
                                        event_pengajuan ep
                                        LEFT JOIN users u ON ep.user_id = u.user_id
                                        LEFT JOIN verifikasi_event ve ON ep.id = ve.event_id
                                    ORDER BY 
                                        ep.tanggal_pengajuan DESC";

                            // Debug: Log the query
                            $log_file = __DIR__ . '/debug.log';
                            file_put_contents($log_file, date('Y-m-d H:i:s') . " - Query: " . $query . "\n", FILE_APPEND);

                            $result = $conn->query($query);
                            if (!$result) {
                                file_put_contents($log_file, date('Y-m-d H:i:s') . " - Query error: " . $conn->error . "\n", FILE_APPEND);
                                die("Error in query: " . $conn->error);
                            }

                            // Debug: Log the number of rows returned
                            $num_rows = $result->num_rows;
                            file_put_contents($log_file, date('Y-m-d H:i:s') . " - Number of rows returned: " . $num_rows . "\n", FILE_APPEND);
                            
                            while ($row = $result->fetch_assoc()):
                                // Debug: Log each row
                                file_put_contents($log_file, date('Y-m-d H:i:s') . " - Row data: " . print_r($row, true) . "\n", FILE_APPEND);
                                
                                $status = $row['status'] ?? 'Pending';
                                $status_class = match($status) {
                                    'Disetujui' => 'status-approved',
                                    'Ditolak' => 'status-rejected',
                                    'Selesai' => 'status-completed',
                                    default => 'status-pending'
                                };
                                $status_icon = match($status) {
                                    'Disetujui' => 'bi-check-circle-fill',
                                    'Ditolak' => 'bi-x-circle-fill',
                                    'Selesai' => 'bi-check-square-fill',
                                    default => 'bi-clock-fill'
                                };
                            ?>
                            <tr>
                                <td><?php echo date('d-m-Y', strtotime($row['tanggal_pengajuan'] ?? '')); ?></td>
                                <td><?php echo htmlspecialchars($row['username'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($row['judul_kegiatan'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($row['event_ekskul'] ?? ''); ?></td>
                                <td>Rp <?php echo number_format($row['total_biaya'] ?? 0, 0, ',', '.'); ?></td>
                                <td>
                                    <?php if(isset($row['proposal']) && $row['proposal']): ?>
                                        <a href="../uploads/<?php echo htmlspecialchars($row['proposal']); ?>" class="btn btn-sm btn-info" target="_blank">
                                            <i class="bi bi-file-earmark-pdf"></i> Lihat
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="status-badge <?php echo $status_class; ?>">
                                        <i class="bi <?php echo $status_icon; ?>"></i>
                                        <?php echo $status; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    if (!empty($row['tanggal_verifikasi'])) {
                                        echo date('d-m-Y', strtotime($row['tanggal_verifikasi']));
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php if(!empty($row['catatan'])): ?>
                                        <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars($row['catatan']); ?>">
                                            <i class="bi bi-info-circle"></i> Lihat
                                        </button>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#verifikasiModal<?php echo $row['id']; ?>">
                                            <i class="bi bi-pencil-square"></i> Verifikasi
                                        </button>
                                        
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus event ini?');">
                                            <input type="hidden" name="event_id" value="<?php echo $row['id'] ?? ''; ?>">
                                            <button type="submit" name="delete_event" class="btn btn-danger btn-sm">
                                                <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    </div>

                                    <!-- Modal Verifikasi -->
                                    <div class="modal fade" id="verifikasiModal<?php echo $row['id']; ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">
                                                        <i class="bi bi-clipboard-check text-primary me-2"></i>
                                                        Verifikasi Event: <?php echo htmlspecialchars($row['judul_kegiatan']); ?>
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="verifikasi_event.php" method="POST">
                                                    <div class="modal-body">
                                                        <input type="hidden" name="event_id" value="<?php echo $row['id']; ?>">
                                                        
                                                        <div class="mb-3">
                                                            <label class="form-label">Status</label>
                                                            <select class="form-select" name="status" required>
                                                                <option value="">Pilih Status</option>
                                                                <option value="Pending" <?php echo ($row['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                                                <option value="Disetujui" <?php echo ($row['status'] == 'Disetujui') ? 'selected' : ''; ?>>Disetujui</option>
                                                                <option value="Ditolak" <?php echo ($row['status'] == 'Ditolak') ? 'selected' : ''; ?>>Ditolak</option>
                                                                <option value="Selesai" <?php echo ($row['status'] == 'Selesai') ? 'selected' : ''; ?>>Selesai</option>
                                                            </select>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <label class="form-label">Tanggal Verifikasi</label>
                                                            <input type="date" class="form-control" name="tanggal_verifikasi" 
                                                                value="<?php echo !empty($row['tanggal_verifikasi']) ? date('Y-m-d', strtotime($row['tanggal_verifikasi'])) : date('Y-m-d'); ?>" 
                                                                required>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <label class="form-label">Catatan</label>
                                                            <textarea class="form-control" name="catatan" rows="3"><?php echo htmlspecialchars($row['catatan'] ?? ''); ?></textarea>
                                                            <div class="form-text">Tambahkan catatan atau keterangan jika diperlukan</div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                            <i class="bi bi-x-circle me-1"></i>Batal
                                                        </button>
                                                        <button type="submit" class="btn btn-primary">
                                                            <i class="bi bi-check-circle me-1"></i>Simpan Verifikasi
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- User Management Section -->
        <div class="card">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Manajemen User</h5>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="bi bi-person-plus"></i> Tambah User
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT * FROM users ORDER BY user_id";
                            $result = $conn->query($query);
                            while ($row = $result->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?php echo $row['user_id']; ?></td>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td>
                                    <?php if($row['user_id'] != $_SESSION['user_id']): ?>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
                                        <select name="new_role" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                            <option value="user" <?php echo $row['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                                            <option value="admin" <?php echo $row['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                        </select>
                                        <input type="hidden" name="update_role" value="1">
                                    </form>
                                    <?php else: ?>
                                        <span class="badge bg-primary bg-gradient">Admin (You)</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($row['user_id'] != $_SESSION['user_id']): ?>
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
                                        <button type="submit" name="delete_user" class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-person-plus text-primary me-2"></i>
                        Tambah User Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="add_user.php" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="bi bi-x-lg me-1"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Event Modal -->
    <div class="modal fade" id="addEventModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-calendar-plus text-primary me-2"></i>
                        Tambah Event Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="add_event.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="judul_kegiatan" class="form-label">Judul Kegiatan</label>
                            <input type="text" class="form-control" id="judul_kegiatan" name="judul_kegiatan" required>
                        </div>
                        <div class="mb-3">
                            <label for="event_ekskul" class="form-label">Event Ekskul</label>
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
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="Pending">Pending</option>
                                <option value="Disetujui">Disetujui</option>
                                <option value="Ditolak">Ditolak</option>
                                <option value="Selesai">Selesai</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="bi bi-x-lg me-1"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let searchText = this.value.toLowerCase();
            let tableRows = document.querySelectorAll('tbody tr');
            
            tableRows.forEach(row => {
                let text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchText) ? '' : 'none';
            });
        });

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    </script>
</body>
</html> 