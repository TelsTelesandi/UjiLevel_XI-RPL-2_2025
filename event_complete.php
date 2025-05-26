<?php
require_once 'includes/auth.php';
require_once 'config/database.php';

// Cek login
$auth = new Auth();
if (!$auth->isLoggedIn()) {
    header("Location: login.php");
    exit();
}

// Koneksi database
$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = $_POST['event_id'];
    $user_id = $_SESSION['user_id'];
    
    // Validasi kepemilikan event
    $query = "SELECT * FROM event_pengajuan WHERE event_id = :event_id AND user_id = :user_id AND status = 'disetujui'";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':event_id', $event_id);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        // Upload foto dokumentasi
        if (isset($_FILES['foto_dokumentasi']) && $_FILES['foto_dokumentasi']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['foto_dokumentasi'];
            $fileName = $file['name'];
            $fileType = $file['type'];
            $fileTmpName = $file['tmp_name'];
            $fileError = $file['error'];
            $fileSize = $file['size'];
            
            // Validasi tipe file
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!in_array($fileType, $allowedTypes)) {
                $_SESSION['message'] = "Tipe file tidak diizinkan. Harap upload file JPG/PNG.";
                $_SESSION['message_type'] = "danger";
                header("Location: user/my_events.php");
                exit();
            }
            
            // Validasi ukuran file (max 5MB)
            if ($fileSize > 5 * 1024 * 1024) {
                $_SESSION['message'] = "Ukuran file terlalu besar. Maksimal 5MB.";
                $_SESSION['message_type'] = "danger";
                header("Location: user/my_events.php");
                exit();
            }
            
            // Generate nama file unik
            $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
            $newFileName = uniqid() . '.' . $fileExt;
            $uploadPath = 'uploads/dokumentasi/' . $newFileName;
            
            // Buat direktori jika belum ada
            if (!file_exists('uploads/dokumentasi/')) {
                mkdir('uploads/dokumentasi/', 0777, true);
            }
            
            // Upload file
            if (move_uploaded_file($fileTmpName, $uploadPath)) {
                // Update status event
                $query = "UPDATE event_pengajuan SET 
                         status = 'selesai',
                         foto_dokumentasi = :foto,
                         tanggal_selesai = NOW()
                         WHERE event_id = :event_id";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':foto', $newFileName);
                $stmt->bindParam(':event_id', $event_id);
                
                if ($stmt->execute()) {
                    $_SESSION['message'] = "Event berhasil dikonfirmasi selesai.";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = "Terjadi kesalahan saat memperbarui status event.";
                    $_SESSION['message_type'] = "danger";
                }
            } else {
                $_SESSION['message'] = "Gagal mengupload file dokumentasi.";
                $_SESSION['message_type'] = "danger";
            }
        } else {
            $_SESSION['message'] = "Harap upload foto dokumentasi.";
            $_SESSION['message_type'] = "danger";
        }
    } else {
        $_SESSION['message'] = "Event tidak ditemukan atau tidak dapat dikonfirmasi selesai.";
        $_SESSION['message_type'] = "danger";
    }
} else {
    $_SESSION['message'] = "Metode request tidak valid.";
    $_SESSION['message_type'] = "danger";
}

header("Location: user/my_events.php");
exit(); 