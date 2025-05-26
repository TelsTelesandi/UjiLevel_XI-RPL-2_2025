<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$nama_lengkap = $_SESSION['nama_lengkap'];

// Get event counts
$total_events_query = $pdo->query("SELECT COUNT(*) as total FROM event_pengajuan");
$total_events = $total_events_query->fetch()['total'];

$pending_events_query = $pdo->query("SELECT COUNT(*) as pending FROM event_pengajuan WHERE status = 'menunggu'");
$pending_events = $pending_events_query->fetch()['pending'];

$approved_events_query = $pdo->query("SELECT COUNT(*) as approved FROM event_pengajuan WHERE status = 'disetujui'");
$approved_events = $approved_events_query->fetch()['approved'];

// Handle event submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_event'])) {
    $judul_event = $_POST['judul_event'];
    $jenis_kegiatan = $_POST['jenis_kegiatan'];
    $total_pembiayaan = $_POST['total_pembiayaan'];
    $deskripsi = $_POST['deskripsi'];
    
    // Handle file upload
    $proposal = $_FILES['proposal']['name'];
    $target_dir = "uploads/";
    
    // Create uploads directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $target_file = $target_dir . basename($_FILES["proposal"]["name"]);
    
    // Check if file was successfully uploaded
    if (move_uploaded_file($_FILES["proposal"]["tmp_name"], $target_file)) {
        $stmt = $pdo->prepare("INSERT INTO event_pengajuan (user_id, judul_event, jenis_kegiatan, Total_pembiayaan, Proposal, deskripsi, tanggal_pengajuan, status) VALUES (?, ?, ?, ?, ?, ?, CURDATE(), 'menunggu')");
        $stmt->execute([$user_id, $judul_event, $jenis_kegiatan, $total_pembiayaan, $proposal, $deskripsi]);
        
        // Redirect to prevent form resubmission
        header("Location: dashboard.php?success=1");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Maaf, terjadi kesalahan saat mengupload file.</div>";
    }
}

// Get events based on role
if ($role == 'admin') {
    $stmt = $pdo->query("
        SELECT ep.*, u.nama_lengkap as pengaju 
        FROM event_pengajuan ep 
        JOIN users u ON ep.user_id = u.user_id 
        ORDER BY ep.tanggal_pengajuan DESC
    ");
} else {
    $stmt = $pdo->prepare("
        SELECT ep.*, u.nama_lengkap as pengaju 
        FROM event_pengajuan ep 
        JOIN users u ON ep.user_id = u.user_id 
        WHERE ep.user_id = ? 
        ORDER BY ep.tanggal_pengajuan DESC
    ");
    $stmt->execute([$user_id]);
}
$events = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Manajemen Event</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(45deg, #FF6B6B, #4ECDC4);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-bottom: 2rem;
        }

        /* Animated Background */
        .background-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .background-animation::before,
        .background-animation::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 10s infinite ease-in-out;
        }

        .background-animation::before {
            top: -100px;
            right: -100px;
        }

        .background-animation::after {
            bottom: -100px;
            left: -100px;
            animation-delay: -5s;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(50px, 50px) scale(1.2); }
        }

        .navbar {
            background: rgba(255, 255, 255, 0.1) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding: 1rem 0;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 600;
            color: white !important;
        }

        .user-profile {
            display: flex;
            align-items: center;
            color: white;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1.2rem;
        }

        .card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .welcome-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 2rem;
            color: white;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .welcome-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, rgba(255, 107, 107, 0.2), rgba(78, 205, 196, 0.2));
            z-index: -1;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-right: 1rem;
            background: linear-gradient(45deg, #FF6B6B, #4ECDC4);
            color: white;
        }

        .stat-info h3 {
            font-size: 1.8rem;
            margin: 0;
            background: linear-gradient(45deg, #FF6B6B, #4ECDC4);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .card-header {
            background: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
        }

        .form-control {
            border-radius: 10px;
            padding: 0.75rem 1rem;
            border: 2px solid rgba(0, 0, 0, 0.1);
        }

        .form-control:focus {
            border-color: #4ECDC4;
            box-shadow: 0 0 0 0.25rem rgba(78, 205, 196, 0.25);
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(45deg, #FF6B6B, #4ECDC4);
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .table {
            border-radius: 15px;
            overflow: hidden;
        }

        .table th {
            background: rgba(0, 0, 0, 0.05);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .badge {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 500;
        }

        .badge.bg-warning {
            background: linear-gradient(45deg, #FFB347, #FFCC33) !important;
        }

        .badge.bg-success {
            background: linear-gradient(45deg, #4ECDC4, #2ECC71) !important;
        }

        .badge.bg-danger {
            background: linear-gradient(45deg, #FF6B6B, #FF4757) !important;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease forwards;
        }
    </style>
</head>
<body>
    <div class="background-animation"></div>
    
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <i class="fas fa-calendar-alt me-2"></i>
                Event Management System
            </a>
            <div class="user-profile">
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <h6 class="mb-0"><?php echo htmlspecialchars($nama_lengkap); ?></h6>
                    <small class="text-white-50"><?php echo htmlspecialchars($role); ?></small>
                </div>
                <a href="auth/logout.php" class="btn btn-light btn-sm ms-3">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if (isset($_GET['delete_success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>Event has been successfully deleted.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <div class="welcome-section animate-fade-in">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2><i class="fas fa-tachometer-alt me-2"></i>Dashboard Overview</h2>
                    <p class="mb-0">Welcome to your Event Management Dashboard. Here's what's happening today.</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <span class="badge bg-light text-dark">
                        <i class="fas fa-calendar me-1"></i>
                        <?php echo date('l, d M Y'); ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="stats-container animate-fade-in">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $total_events; ?></h3>
                    <p>Total Events</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $pending_events; ?></h3>
                    <p>Pending Events</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-check"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $approved_events; ?></h3>
                    <p>Approved Events</p>
                </div>
            </div>
        </div>

        <?php if ($role != 'admin'): ?>
        <div class="card mb-4 animate-fade-in">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Submit New Event</h4>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-heading me-1"></i>Event Title
                            </label>
                            <input type="text" class="form-control" name="judul_event" required
                                   placeholder="Enter event title">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-tag me-1"></i>Event Type
                            </label>
                            <input type="text" class="form-control" name="jenis_kegiatan" required
                                   placeholder="Enter event type">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-money-bill me-1"></i>Budget
                            </label>
                            <input type="text" class="form-control" name="total_pembiayaan" required
                                   placeholder="Enter budget amount">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-file-pdf me-1"></i>Proposal (PDF)
                            </label>
                            <input type="file" class="form-control" name="proposal" accept=".pdf" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">
                                <i class="fas fa-align-left me-1"></i>Description
                            </label>
                            <textarea class="form-control" name="deskripsi" rows="3" required
                                      placeholder="Enter event description"></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" name="submit_event" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i>Submit Event
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <div class="card animate-fade-in">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fas fa-list me-2"></i>Event List</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th><i class="fas fa-calendar me-1"></i>Date</th>
                                <th><i class="fas fa-heading me-1"></i>Event Title</th>
                                <th><i class="fas fa-tag me-1"></i>Event Type</th>
                                <th><i class="fas fa-info-circle me-1"></i>Status</th>
                                <?php if ($role == 'admin'): ?>
                                    <th><i class="fas fa-user me-1"></i>Submitted By</th>
                                <?php endif; ?>
                                <th><i class="fas fa-cog me-1"></i>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($events as $event): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($event['tanggal_pengajuan']); ?></td>
                                <td><?php echo htmlspecialchars($event['judul_event']); ?></td>
                                <td><?php echo htmlspecialchars($event['jenis_kegiatan']); ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                        echo $event['status'] == 'disetujui' ? 'success' : 
                                            ($event['status'] == 'ditolak' ? 'danger' : 'warning'); 
                                    ?>">
                                        <i class="fas fa-<?php 
                                            echo $event['status'] == 'disetujui' ? 'check' : 
                                                ($event['status'] == 'ditolak' ? 'times' : 'clock'); 
                                        ?>"></i>
                                        <?php echo htmlspecialchars($event['status']); ?>
                                    </span>
                                </td>
                                <?php if ($role == 'admin'): ?>
                                    <td><?php echo htmlspecialchars($event['pengaju']); ?></td>
                                <?php endif; ?>
                                <td>
                                    <a href="view_event.php?id=<?php echo $event['event_id']; ?>" 
                                       class="btn btn-info btn-sm">
                                        <i class="fas fa-eye me-1"></i>Detail
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 