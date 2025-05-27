<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Anda harus login untuk mengakses halaman ini.";
    header("Location: ../auth/login.php");
    exit();
}

// Include database connection
require_once '../config/db.php';

// Get user information
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['nama_lengkap'];

// Get user's events
$query = "SELECT * FROM event_pengajuan WHERE user_id = ? ORDER BY created_at DESC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard User - Aplikasi Ekskul</title>
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .nav-modern {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            padding: 0.5rem 0;
        }

        .nav-list {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 0;
            align-items: center;
        }

        .nav-brand {
            color: var(--text-light);
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: bold;
            padding: 1rem;
            margin-right: 2rem;
        }

        .nav-link {
            color: var(--text-light);
            text-decoration: none;
            padding: 0.8rem 1.2rem;
            border-radius: 5px;
            transition: all 0.3s ease;
            margin: 0 0.5rem;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .nav-right {
            margin-left: auto;
        }

        .welcome-section {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .event-list {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            color: white;
        }

        .btn-primary {
            background: var(--accent-color);
        }

        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        .modern-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .modern-table th {
            background: var(--primary-color);
            color: var(--text-light);
            padding: 1rem;
            text-align: left;
        }

        .modern-table td {
            padding: 1rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .modern-table tr:hover {
            background: rgba(0, 0, 0, 0.02);
        }

        .status-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .status-pending {
            background: var(--warning-color);
            color: white;
        }

        .status-approved {
            background: var(--success-color);
            color: white;
        }

        .status-rejected {
            background: var(--danger-color);
            color: white;
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
            color: white;
        }

        .alert-success {
            background: var(--success-color);
        }

        .alert-error {
            background: var(--danger-color);
        }

        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.875rem;
        }

        .text-muted {
            color: #6c757d;
            font-style: italic;
        }
    </style>
</head>
<body>
    <nav class="nav-modern">
        <div class="container">
            <ul class="nav-list">
                <li><a href="dashboard_user.php" class="nav-brand">User Panel</a></li>
                <li><a href="dashboard_user.php" class="nav-link">Dashboard</a></li>
                <li><a href="history_pengajuan.php" class="nav-link">History Pengajuan</a></li>
                <li class="nav-right"><a href="../auth/logout.php" class="nav-link">Keluar</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php 
                echo htmlspecialchars($_SESSION['success']);
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php 
                echo htmlspecialchars($_SESSION['error']);
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <div class="welcome-section">
            <h1>Selamat Datang, <?php echo htmlspecialchars($user_name); ?>!</h1>
            <p>Kelola event ekskul Anda di sini.</p>
            <a href="create_event.php" class="btn btn-primary">Tambah Event Baru</a>
        </div>

        <div class="event-list">
            <h2>Daftar Event Anda</h2>
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Event</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>File</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['event_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['nama_event']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['tanggal']) . "</td>";
                            echo "<td><span class='status-badge status-" . htmlspecialchars($row['status']) . "'>" . 
                                 ucfirst(htmlspecialchars($row['status'])) . "</span></td>";
                            echo "<td>";
                            if (!empty($row['file_path'])) {
                                $file_name = basename($row['file_path']);
                                echo "<a href='../uploads/" . htmlspecialchars($file_name) . "' class='btn btn-primary btn-sm' target='_blank'>Download</a>";
                            } else {
                                echo "<span class='text-muted'>Tidak ada file</span>";
                            }
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' style='text-align: center;'>Belum ada event yang dibuat</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
