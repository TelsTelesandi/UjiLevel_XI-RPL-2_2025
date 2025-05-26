<?php
session_start();
require_once 'db_connect.php';

// Cek apakah user sudah login sebagai admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Cek apakah ada ID foto
if (!isset($_GET['id'])) {
    header("Location: admin_manage_photos.php");
    exit();
}

$id = $_GET['id'];

// Ambil data foto
$sql = "SELECT * FROM photos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$photo = $result->fetch_assoc();

if (!$photo) {
    header("Location: admin_manage_photos.php");
    exit();
}

// Proses update foto
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_photo'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    
    // Jika ada file baru diupload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        // Hapus file lama
        if (file_exists($photo['file_path'])) {
            unlink($photo['file_path']);
        }
        
        // Upload file baru
        $target_dir = "uploads/photos/";
        $file_extension = strtolower(pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        // Validasi file
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
        if (!in_array($file_extension, $allowed_types)) {
            $error_message = "Hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.";
        } elseif ($_FILES["photo"]["size"] > 5000000) {
            $error_message = "Ukuran file terlalu besar. Maksimal 5MB.";
        } elseif (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            // Update database dengan file baru
            $sql = "UPDATE photos SET title = ?, description = ?, filename = ?, file_path = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $title, $description, $new_filename, $target_file, $id);
        } else {
            $error_message = "Terjadi kesalahan saat upload file.";
        }
    } else {
        // Update tanpa file baru
        $sql = "UPDATE photos SET title = ?, description = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $title, $description, $id);
    }
    
    if (!isset($error_message) && $stmt->execute()) {
        $success_message = "Foto berhasil diperbarui!";
        // Refresh data foto
        $sql = "SELECT * FROM photos WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $photo = $result->fetch_assoc();
    } else {
        $error_message = "Gagal memperbarui foto.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Foto - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Edit Foto</h2>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="title" class="form-label">Judul</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($photo['title']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($photo['description']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Foto Saat Ini</label>
                        <div>
                            <img src="<?php echo $photo['file_path']; ?>" alt="<?php echo $photo['title']; ?>" class="img-thumbnail" style="max-height: 200px;">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="photo" class="form-label">Ganti Foto (Opsional)</label>
                        <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                        <small class="text-muted">Biarkan kosong jika tidak ingin mengubah foto</small>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="admin_manage_photos.php" class="btn btn-secondary">Kembali</a>
                        <button type="submit" name="update_photo" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 