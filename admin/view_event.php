<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Anda harus login sebagai admin untuk mengakses halaman ini.";
    header("Location: ../auth/login.php");
    exit();
}

include '../config/db.php';

// Check if event ID is provided
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "ID Event tidak ditemukan!";
    header("Location: dashboard_admin.php");
    exit();
}

$event_id = mysqli_real_escape_string($conn, $_GET['id']);

// Fetch event data with user information
$query = "SELECT ep.*, u.nama_lengkap, u.email 
          FROM event_pengajuan ep 
          JOIN users u ON ep.user_id = u.user_id 
          WHERE ep.event_id = '$event_id'";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    $_SESSION['error'] = "Event tidak ditemukan!";
    header("Location: dashboard_admin.php");
    exit();
}

$event = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Detail Event - Aplikasi Ekskul</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --text-light: #ffffff;
            --text-dark: #2c3e50;
        }

        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: var(--text-dark);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .event-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--light-color);
        }

        .event-title {
            color: var(--primary-color);
            margin: 0 0 0.5rem 0;
        }

        .event-meta {
            color: var(--secondary-color);
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .event-details {
            margin-bottom: 2rem;
        }

        .detail-group {
            margin-bottom: 1.5rem;
        }

        .detail-label {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .detail-value {
            color: var(--text-dark);
            line-height: 1.6;
        }

        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
            color: white;
        }

        .status-pending {
            background: var(--warning-color);
        }

        .status-approved {
            background: var(--success-color);
        }

        .status-rejected {
            background: var(--danger-color);
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            color: white;
        }

        .btn-primary {
            background: var(--accent-color);
        }

        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: var(--secondary-color);
        }

        .btn-secondary:hover {
            background: #2c3e50;
            transform: translateY(-2px);
        }

        .action-buttons {
            margin-top: 2rem;
        }

        .action-buttons .btn {
            margin-right: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="event-header">
            <h1 class="event-title"><?php echo htmlspecialchars($event['nama_event']); ?></h1>
            <div class="event-meta">
                <span class="status-badge status-<?php echo $event['status']; ?>">
                    <?php echo ucfirst($event['status']); ?>
                </span>
            </div>
        </div>

        <div class="event-details">
            <div class="detail-group">
                <div class="detail-label">Tanggal Event</div>
                <div class="detail-value">
                    <?php echo date('d F Y H:i', strtotime($event['tanggal'])); ?>
                </div>
            </div>

            <div class="detail-group">
                <div class="detail-label">Deskripsi</div>
                <div class="detail-value">
                    <?php echo nl2br(htmlspecialchars($event['deskripsi'])); ?>
                </div>
            </div>

            <div class="detail-group">
                <div class="detail-label">Informasi Pengaju</div>
                <div class="detail-value">
                    <p><strong>Nama:</strong> <?php echo htmlspecialchars($event['nama_lengkap']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($event['email']); ?></p>
                </div>
            </div>
        </div>

        <div class="action-buttons">
            <a href="edit_event.php?id=<?php echo $event['event_id']; ?>" class="btn btn-primary">Edit Event</a>
            <a href="dashboard_admin.php" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
</body>
</html> 