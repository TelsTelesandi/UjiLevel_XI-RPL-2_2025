<?php
include '../config.php';
include 'check_admin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul_kegiatan = $_POST['judul_kegiatan'];
    $event_ekskul = $_POST['event_ekskul'];
    $tanggal_pengajuan = $_POST['tanggal_pengajuan'];
    $total_biaya = $_POST['total_biaya'];
    $status = $_POST['status'];
    
    // Handle file upload
    $proposal = null;
    if (isset($_FILES['proposal']) && $_FILES['proposal']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['proposal']['tmp_name'];
        $file_name = $_FILES['proposal']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Validate file type
        if ($file_ext !== 'pdf') {
            header("Location: dashboard.php?error=invalid_file");
            exit();
        }
        
        // Generate unique filename
        $new_filename = uniqid() . '_' . $file_name;
        $upload_path = '../uploads/' . $new_filename;
        
        // Create uploads directory if it doesn't exist
        if (!file_exists('../uploads')) {
            mkdir('../uploads', 0777, true);
        }
        
        // Move uploaded file
        if (move_uploaded_file($file_tmp, $upload_path)) {
            $proposal = $new_filename;
        } else {
            header("Location: dashboard.php?error=upload_failed");
            exit();
        }
    } else {
        header("Location: dashboard.php?error=no_file");
        exit();
    }
    
    // Insert into database
    $stmt = $conn->prepare("INSERT INTO event_pengajuan (user_id, judul_kegiatan, event_ekskul, tanggal_pengajuan, total_biaya, proposal, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssdss", $_SESSION['user_id'], $judul_kegiatan, $event_ekskul, $tanggal_pengajuan, $total_biaya, $proposal, $status);
    
    if ($stmt->execute()) {
        header("Location: dashboard.php?success=event_added");
    } else {
        // If insert fails, delete uploaded file
        if ($proposal && file_exists($upload_path)) {
            unlink($upload_path);
        }
        header("Location: dashboard.php?error=db_error");
    }
    exit();
}

// If not POST request, redirect to dashboard
header("Location: dashboard.php");
exit(); 