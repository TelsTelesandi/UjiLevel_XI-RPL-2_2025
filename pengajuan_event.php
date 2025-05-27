<?php
session_start();
include 'config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Anda harus login terlebih dahulu.";
    header("Location: auth/login.php");
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $nama_event = mysqli_real_escape_string($conn, $_POST['nama_event']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    
    // Validate input
    if (empty($nama_event) || empty($deskripsi) || empty($tanggal)) {
        $_SESSION['error'] = "Semua field harus diisi!";
    } else {
        // Insert into database
        $query = "INSERT INTO event_pengajuan (user_id, nama_event, deskripsi, tanggal) 
                 VALUES ('$user_id', '$nama_event', '$deskripsi', '$tanggal')";
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['success'] = "Event berhasil diajukan!";
            header("Location: " . ($_SESSION['role'] === 'admin' ? 'admin/dashboard_admin.php' : 'user/dashboard_user.php'));
            exit();
        } else {
            $_SESSION['error'] = "Gagal mengajukan event: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Pengajuan Event - Aplikasi Ekskul</title>
    <link rel="stylesheet" href="assets/css/style.css">
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-light);
        }

        .page-wrapper {
            min-height: 100vh;
            padding: 2rem;
        }

        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--text-dark);
        }

        .form-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-header h1 {
            color: var(--primary-color);
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .form-header p {
            color: var(--secondary-color);
            opacity: 0.8;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        .form-control:focus {
            border-color: var(--accent-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }

        .btn {
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: 8px;
            background: var(--accent-color);
            color: white;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 8px;
            color: white;
            text-align: center;
        }

        .alert-error {
            background: var(--danger-color);
        }

        .alert-success {
            background: var(--success-color);
        }

        .nav-modern {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1rem 0;
            margin-bottom: 2rem;
        }

        .nav-list {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 0;
            justify-content: center;
        }

        .nav-link {
            color: var(--text-light);
            text-decoration: none;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <!-- Navigation -->
        <nav class="nav-modern">
            <div class="container">
                <ul class="nav-list">
                    <li><a href="<?php echo $_SESSION['role'] === 'admin' ? 'admin/dashboard_admin.php' : 'user/dashboard_user.php'; ?>" class="nav-link">Dashboard</a></li>
                    <li><a href="auth/logout.php" class="nav-link">Keluar</a></li>
                </ul>
            </div>
        </nav>

        <div class="form-container">
            <div class="form-header">
                <h1>Pengajuan Event</h1>
                <p>Silakan isi form di bawah ini untuk mengajukan event baru</p>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php 
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="nama_event">Nama Event</label>
                    <input type="text" class="form-control" id="nama_event" name="nama_event" required>
                </div>

                <div class="form-group">
                    <label for="deskripsi">Deskripsi Event</label>
                    <textarea class="form-control" id="deskripsi" name="deskripsi" required></textarea>
                </div>

                <div class="form-group">
                    <label for="tanggal">Tanggal Pelaksanaan</label>
                    <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                </div>

                <button type="submit" class="btn">Ajukan Event</button>
            </form>
        </div>
    </div>
</body>
</html> 