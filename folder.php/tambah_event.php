<?php
session_start();
include 'koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if(isset($_POST['submit'])) {
    $judul_event = $_POST['judul_event'];
    $jenis_kegiatan = $_POST['jenis_kegiatan'];
    $total_pembiayaan = $_POST['total_pembiayaan'];
    $deskripsi = $_POST['deskripsi'];
    $username = $_SESSION['username'];
    $tanggal_pengajuan = date('Y-m-d H:i:s');
    
    // Handle file upload
    $proposal_path = '';
    if(isset($_FILES['proposal']) && $_FILES['proposal']['error'] == 0) {
        $target_dir = "uploads/proposals/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES["proposal"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        // Hanya izinkan file PDF
        if($file_extension == "pdf") {
            if(move_uploaded_file($_FILES["proposal"]["tmp_name"], $target_file)) {
                $proposal_path = $target_file;
            } else {
                $error = "Maaf, terjadi kesalahan saat mengunggah file.";
            }
        } else {
            $error = "Maaf, hanya file PDF yang diizinkan.";
        }
    }
    
    if(!isset($error)) {
        try {
            // Simpan data ke database
            $query = "INSERT INTO events (username, judul_event, jenis_kegiatan, total_pembiayaan, proposal_path, deskripsi, tanggal_pengajuan) 
                     VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            
            if ($stmt === false) {
                throw new Exception("Error preparing statement: " . $conn->error);
            }
            
            $stmt->bind_param("sssdsss", $username, $judul_event, $jenis_kegiatan, $total_pembiayaan, $proposal_path, $deskripsi, $tanggal_pengajuan);
            
            if($stmt->execute()) {
                $_SESSION['success'] = "Event berhasil ditambahkan!";
                header("Location: dashboard.php");
                exit();
            } else {
                throw new Exception("Error executing statement: " . $stmt->error);
            }
        } catch (Exception $e) {
            $error = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Event Ekstrakurikuler</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f4f6f9;
            min-height: 100vh;
            padding: 2rem;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .form-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-header h2 {
            color: #2e7d32;
            margin-bottom: 0.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }

        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-group input[type="file"] {
            width: 100%;
            padding: 0.8rem;
            border: 2px dashed #e0e0e0;
            border-radius: 8px;
            cursor: pointer;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #4caf50;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
        }

        .btn-group {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
        }

        .btn-primary {
            background-color: #4caf50;
            color: white;
            flex: 1;
        }

        .btn-secondary {
            background-color: #e0e0e0;
            color: #333;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            text-align: center;
        }

        .success-message {
            background-color: #e8f5e9;
            color: #2e7d32;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-header">
            <h2>Pengajuan Event Ekstrakurikuler</h2>
            <p>Isi formulir di bawah ini untuk mengajukan event baru</p>
        </div>

        <?php if(isset($error)): ?>
        <div class="error-message">
            <?php echo $error; ?>
        </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['success'])): ?>
        <div class="success-message">
            <?php 
            echo $_SESSION['success'];
            unset($_SESSION['success']);
            ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="judul_event">Judul Event</label>
                <input type="text" id="judul_event" name="judul_event" required value="<?php echo isset($_POST['judul_event']) ? htmlspecialchars($_POST['judul_event']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="jenis_kegiatan">Jenis Kegiatan</label>
                <select id="jenis_kegiatan" name="jenis_kegiatan" required>
                    <option value="">Pilih Jenis Kegiatan</option>
                    <option value="Lomba" <?php echo (isset($_POST['jenis_kegiatan']) && $_POST['jenis_kegiatan'] == 'Lomba') ? 'selected' : ''; ?>>Lomba</option>
                    <option value="Pelatihan" <?php echo (isset($_POST['jenis_kegiatan']) && $_POST['jenis_kegiatan'] == 'Pelatihan') ? 'selected' : ''; ?>>Pelatihan</option>
                    <option value="Workshop" <?php echo (isset($_POST['jenis_kegiatan']) && $_POST['jenis_kegiatan'] == 'Workshop') ? 'selected' : ''; ?>>Workshop</option>
                    <option value="Pentas" <?php echo (isset($_POST['jenis_kegiatan']) && $_POST['jenis_kegiatan'] == 'Pentas') ? 'selected' : ''; ?>>Pentas</option>
                    <option value="Lainnya" <?php echo (isset($_POST['jenis_kegiatan']) && $_POST['jenis_kegiatan'] == 'Lainnya') ? 'selected' : ''; ?>>Lainnya</option>
                </select>
            </div>

            <div class="form-group">
                <label for="total_pembiayaan">Total Pembiayaan (Rp)</label>
                <input type="number" id="total_pembiayaan" name="total_pembiayaan" required min="0" value="<?php echo isset($_POST['total_pembiayaan']) ? htmlspecialchars($_POST['total_pembiayaan']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="proposal">Upload Proposal (PDF)</label>
                <input type="file" id="proposal" name="proposal" accept=".pdf" required>
            </div>

            <div class="form-group">
                <label for="deskripsi">Deskripsi Event</label>
                <textarea id="deskripsi" name="deskripsi" rows="5" required><?php echo isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : ''; ?></textarea>
            </div>

            <div class="btn-group">
                <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
                <button type="submit" name="submit" class="btn btn-primary">Simpan Event</button>
            </div>
        </form>
    </div>
</body>
</html> 