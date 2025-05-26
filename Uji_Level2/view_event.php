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

// Get event details
if (isset($_GET['id'])) {
    $event_id = $_GET['id'];
    
    // Get event data
    $stmt = $pdo->prepare("
        SELECT ep.*, u.nama_lengkap as pengaju 
        FROM event_pengajuan ep 
        JOIN users u ON ep.user_id = u.user_id 
        WHERE ep.event_id = ?
    ");
    $stmt->execute([$event_id]);
    $event = $stmt->fetch();

    // Check if event exists and user has permission
    if (!$event || ($role != 'admin' && $event['user_id'] != $user_id)) {
        header("Location: dashboard.php");
        exit();
    }
}

// Handle status updates (for admin)
if ($role == 'admin' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE event_pengajuan SET status = ? WHERE event_id = ?");
    $stmt->execute([$new_status, $event_id]);
    
    // Refresh event data
    header("Location: view_event.php?id=" . $event_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Details - Event Management System</title>
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

        .event-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            transition: all 0.3s ease;
            margin-top: 2rem;
        }

        .event-header {
            background: rgba(255, 255, 255, 0.8);
            padding: 2rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .event-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .event-meta {
            display: flex;
            gap: 2rem;
            margin-top: 1rem;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #666;
        }

        .meta-item i {
            color: #FF6B6B;
            font-size: 1.2rem;
        }

        .status-badge {
            position: absolute;
            top: 2rem;
            right: 2rem;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .status-menunggu {
            background: linear-gradient(45deg, #FFB347, #FFCC33);
            color: white;
        }

        .status-disetujui {
            background: linear-gradient(45deg, #4ECDC4, #2ECC71);
            color: white;
        }

        .status-ditolak {
            background: linear-gradient(45deg, #FF6B6B, #FF4757);
            color: white;
        }

        .event-body {
            padding: 2rem;
        }

        .info-section {
            background: rgba(255, 255, 255, 0.5);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .info-section h4 {
            color: #333;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
        }

        .info-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(45deg, #FF6B6B, #4ECDC4);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            color: white;
        }

        .info-content {
            flex: 1;
        }

        .info-label {
            font-weight: 500;
            color: #666;
            margin-bottom: 0.25rem;
        }

        .info-value {
            color: #333;
            font-size: 1.1rem;
        }

        .btn-action {
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0 0.5rem;
        }

        .btn-approve {
            background: linear-gradient(45deg, #4ECDC4, #2ECC71);
            border: none;
            color: white;
        }

        .btn-reject {
            background: linear-gradient(45deg, #FF6B6B, #FF4757);
            border: none;
            color: white;
        }

        .btn-back {
            background: linear-gradient(45deg, #6c757d, #495057);
            border: none;
            color: white;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .proposal-download {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(45deg, #FF6B6B, #4ECDC4);
            color: white;
            border-radius: 10px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .proposal-download:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            color: white;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease forwards;
        }

        .feedback-area {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1rem;
        }

        .feedback-textarea {
            width: 100%;
            min-height: 100px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 0.5rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="background-animation"></div>
    
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
                <i class="fas fa-calendar-alt me-2"></i>
                Event Management System
            </a>
            <a href="dashboard.php" class="btn btn-light btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
            </a>
        </div>
    </nav>

    <div class="container">
        <?php if (isset($_GET['edit_success'])): ?>
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i>Event has been successfully updated and resubmitted for review.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <div class="event-card animate-fade-in">
            <div class="event-header">
                <h1 class="event-title"><?php echo htmlspecialchars($event['judul_event']); ?></h1>
                <div class="event-meta">
                    <div class="meta-item">
                        <i class="fas fa-user"></i>
                        <span>Submitted by <?php echo htmlspecialchars($event['pengaju']); ?></span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-calendar"></i>
                        <span>Submitted on <?php echo date('d M Y', strtotime($event['tanggal_pengajuan'])); ?></span>
                    </div>
                </div>
                <span class="status-badge status-<?php echo $event['status']; ?>">
                    <i class="fas fa-<?php 
                        echo $event['status'] == 'disetujui' ? 'check' : 
                            ($event['status'] == 'ditolak' ? 'times' : 'clock'); 
                    ?>"></i>
                    <?php echo ucfirst($event['status']); ?>
                </span>
            </div>
            
            <div class="event-body">
                <div class="info-section">
                    <h4><i class="fas fa-info-circle me-2"></i>Event Information</h4>
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-tag"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Event Type</div>
                            <div class="info-value"><?php echo htmlspecialchars($event['jenis_kegiatan']); ?></div>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-money-bill"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Budget</div>
                            <div class="info-value">Rp <?php 
                                // Convert to float and handle formatting
                                $budget = floatval($event['Total_pembiayaan']);
                                echo number_format($budget, 0, ',', '.'); 
                            ?></div>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-align-left"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Description</div>
                            <div class="info-value"><?php echo nl2br(htmlspecialchars($event['deskripsi'])); ?></div>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Proposal</div>
                            <div class="info-value">
                                <a href="uploads/<?php echo htmlspecialchars($event['Proposal']); ?>" 
                                   class="proposal-download" target="_blank">
                                    <i class="fas fa-download"></i>
                                    Download Proposal
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($role == 'admin'): ?>
                    <div class="info-section">
                        <h4><i class="fas fa-tasks me-2"></i>Admin Actions</h4>
                        <?php if ($event['status'] == 'menunggu'): ?>
                            <form method="POST" class="d-flex justify-content-center gap-3 mb-3">
                                <button type="submit" name="status" value="disetujui" class="btn btn-action btn-approve">
                                    <i class="fas fa-check me-2"></i>Approve Event
                                </button>
                                <button type="submit" name="status" value="ditolak" class="btn btn-action btn-reject">
                                    <i class="fas fa-times me-2"></i>Reject Event
                                </button>
                            </form>
                        <?php endif; ?>
                        <form action="delete_event.php" method="POST" class="d-flex justify-content-center" onsubmit="return confirm('Are you sure you want to delete this event? This action cannot be undone.');">
                            <input type="hidden" name="event_id" value="<?php echo $event['event_id']; ?>">
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash me-2"></i>Delete Event
                            </button>
                        </form>
                    </div>
                <?php elseif ($role != 'admin' && $event['user_id'] == $user_id && $event['status'] == 'ditolak'): ?>
                    <div class="info-section">
                        <h4><i class="fas fa-tasks me-2"></i>Actions</h4>
                        <div class="d-flex justify-content-center">
                            <a href="edit_event.php?id=<?php echo $event['event_id']; ?>" class="btn btn-primary">
                                <i class="fas fa-edit me-2"></i>Edit and Resubmit Proposal
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 