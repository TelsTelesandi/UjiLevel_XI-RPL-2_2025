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

$success = '';
$error = '';
$event = null;

// Check if event ID is provided
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "ID Event tidak ditemukan!";
    header("Location: dashboard_admin.php");
    exit();
}

$event_id = mysqli_real_escape_string($conn, $_GET['id']);

// Fetch event data
$query = "SELECT * FROM event_pengajuan WHERE event_id = '$event_id'";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    $_SESSION['error'] = "Event tidak ditemukan!";
    header("Location: dashboard_admin.php");
    exit();
}

$event = mysqli_fetch_assoc($result);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_event = mysqli_real_escape_string($conn, $_POST['nama_event']);
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal_event']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $query = "UPDATE event_pengajuan 
              SET nama_event = '$nama_event', 
                  tanggal = '$tanggal', 
                  deskripsi = '$deskripsi',
                  status = '$status'
              WHERE event_id = '$event_id'";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Event berhasil diperbarui!";
        header("Location: dashboard_admin.php");
        exit();
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Event - Aplikasi Ekskul</title>
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
            min-height: 150px;
            resize: vertical;
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

        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 8px;
            color: white;
        }

        .alert-success {
            background: var(--success-color);
        }

        .alert-error {
            background: var(--danger-color);
        }

        .action-buttons {
            margin-top: 1rem;
        }

        .action-buttons .btn {
            margin-right: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Event</h1>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="nama_event">Nama Event</label>
                <input type="text" class="form-control" id="nama_event" name="nama_event" 
                       value="<?php echo htmlspecialchars($event['nama_event']); ?>" required>
            </div>

            <div class="form-group">
                <label for="tanggal_event">Tanggal Event</label>
                <input type="datetime-local" class="form-control" id="tanggal_event" name="tanggal_event" 
                       value="<?php echo date('Y-m-d\TH:i', strtotime($event['tanggal'])); ?>" required>
            </div>

            <div class="form-group">
                <label for="deskripsi">Deskripsi</label>
                <textarea class="form-control" id="deskripsi" name="deskripsi" required><?php echo htmlspecialchars($event['deskripsi']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="pending" <?php echo $event['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="approved" <?php echo $event['status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                    <option value="rejected" <?php echo $event['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                </select>
            </div>

            <div class="action-buttons">
                <button type="submit" class="btn btn-primary">Update Event</button>
                <a href="dashboard_admin.php" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</body>
</html> 