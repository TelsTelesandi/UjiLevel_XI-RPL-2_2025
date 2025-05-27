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

$error = '';
$success = '';

// Create uploads directory if it doesn't exist
$upload_dir = '../uploads';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_event = trim($_POST['nama_event']);
    $tanggal = date('Y-m-d', strtotime($_POST['tanggal']));
    $deskripsi = trim($_POST['deskripsi']);
    $user_id = $_SESSION['user_id'];
    $file_path = null;

    // Handle file upload
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['file'];
        $file_name = time() . '_' . basename($file['name']);
        $target_path = $upload_dir . '/' . $file_name;
        
        // Check file type
        $allowed_types = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png'];
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($file_ext, $allowed_types)) {
            $error = "Tipe file tidak diizinkan. File yang diizinkan: PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG";
        } else {
            // Check file size (max 5MB)
            if ($file['size'] > 5 * 1024 * 1024) {
                $error = "Ukuran file terlalu besar. Maksimal 5MB.";
            } else {
                if (move_uploaded_file($file['tmp_name'], $target_path)) {
                    $file_path = $file_name;
                } else {
                    $error = "Gagal mengupload file.";
                }
            }
        }
    }

    if (empty($error)) {
        // Insert event with file
        $query = "INSERT INTO event_pengajuan (user_id, nama_event, deskripsi, tanggal, file_path, status) 
                  VALUES (?, ?, ?, ?, ?, 'pending')";
        
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "issss", $user_id, $nama_event, $deskripsi, $tanggal, $file_path);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = "Event berhasil ditambahkan!";
            header("Location: dashboard_user.php");
            exit();
        } else {
            $error = "Gagal menambahkan event: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Event - Aplikasi Ekskul</title>
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

        .file-info {
            margin-top: 0.5rem;
            font-size: 0.9rem;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Tambah Event Baru</h1>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nama_event">Nama Event</label>
                <input type="text" class="form-control" id="nama_event" name="nama_event" required>
            </div>

            <div class="form-group">
                <label for="tanggal">Tanggal Event</label>
                <input type="date" class="form-control" id="tanggal" name="tanggal" required>
            </div>

            <div class="form-group">
                <label for="deskripsi">Deskripsi</label>
                <textarea class="form-control" id="deskripsi" name="deskripsi" required></textarea>
            </div>

            <div class="form-group">
                <label for="file">File Pendukung (Opsional)</label>
                <input type="file" class="form-control" id="file" name="file">
                <div class="file-info">
                    Format yang didukung: PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG<br>
                    Maksimal ukuran: 5MB
                </div>
            </div>

            <div class="action-buttons">
                <button type="submit" class="btn btn-primary">Simpan Event</button>
                <a href="dashboard_user.php" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</body>
</html> 