<?php
session_start();
require_once 'db_connect.php';

// Cek apakah user sudah login sebagai admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Fungsi untuk upload foto
function uploadPhoto($file) {
    $target_dir = "uploads/photos/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    // Validasi file
    $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
    if (!in_array($file_extension, $allowed_types)) {
        return array('success' => false, 'message' => 'Hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.');
    }
    
    if ($file["size"] > 5000000) { // 5MB limit
        return array('success' => false, 'message' => 'Ukuran file terlalu besar. Maksimal 5MB.');
    }
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return array('success' => true, 'filename' => $new_filename, 'file_path' => $target_file);
    }
    
    return array('success' => false, 'message' => 'Terjadi kesalahan saat upload file.');
}

// Proses tambah foto
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_photo'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $upload_result = uploadPhoto($_FILES['photo']);
        
        if ($upload_result['success']) {
            $sql = "INSERT INTO photos (title, description, filename, file_path) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $title, $description, $upload_result['filename'], $upload_result['file_path']);
            
            if ($stmt->execute()) {
                $success_message = "Foto berhasil ditambahkan!";
            } else {
                $error_message = "Gagal menambahkan foto ke database.";
            }
        } else {
            $error_message = $upload_result['message'];
        }
    }
}

// Proses hapus foto
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Ambil informasi file sebelum dihapus
    $sql = "SELECT file_path FROM photos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $photo = $result->fetch_assoc();
    
    // Hapus file fisik
    if ($photo && file_exists($photo['file_path'])) {
        unlink($photo['file_path']);
    }
    
    // Hapus dari database
    $sql = "DELETE FROM photos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $success_message = "Foto berhasil dihapus!";
    } else {
        $error_message = "Gagal menghapus foto.";
    }
}

// Ambil semua foto
$sql = "SELECT * FROM photos ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Foto - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Kelola Foto</h2>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <!-- Form Tambah Foto -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Tambah Foto Baru</h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="title" class="form-label">Judul</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="photo" class="form-label">Pilih Foto</label>
                        <input type="file" class="form-control" id="photo" name="photo" accept="image/*" required>
                    </div>
                    <button type="submit" name="add_photo" class="btn btn-primary">Upload Foto</button>
                </form>
            </div>
        </div>
        
        <!-- Daftar Foto -->
        <div class="row">
            <?php while ($photo = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="<?php echo $photo['file_path']; ?>" class="card-img-top" alt="<?php echo $photo['title']; ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $photo['title']; ?></h5>
                            <p class="card-text"><?php echo $photo['description']; ?></p>
                            <div class="d-flex justify-content-between">
                                <a href="admin_edit_photo.php?id=<?php echo $photo['id']; ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="?delete=<?php echo $photo['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus foto ini?')">
                                    <i class="fas fa-trash"></i> Hapus
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 