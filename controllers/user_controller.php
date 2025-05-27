<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}
include '../config/db.php';

// Fungsi untuk validasi input
function validateInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Tambah user
if (isset($_POST['tambah'])) {
    $nama_lengkap = validateInput($_POST['nama_lengkap']);
    $username = validateInput($_POST['username']);
    $password = validateInput($_POST['password']);
    $ekskul = validateInput($_POST['ekskul']);
    $role = validateInput($_POST['role']);

    // Cek username sudah ada atau belum
    $check = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    if (mysqli_num_rows($check) > 0) {
        header("Location: ../views/manajemen_user.php?error=username_exists");
        exit;
    }

    $query = "INSERT INTO users (nama_lengkap, username, password, ekskul, role) 
              VALUES ('$nama_lengkap', '$username', '$password', '$ekskul', '$role')";
    
    if (mysqli_query($conn, $query)) {
        header("Location: ../views/manajemen_user.php?success=tambah");
    } else {
        header("Location: ../views/manajemen_user.php?error=query");
    }
    exit;
}

// Update user
if (isset($_POST['update'])) {
    $user_id = $_POST['user_id'];
    $nama_lengkap = validateInput($_POST['nama_lengkap']);
    $username = validateInput($_POST['username']);
    $ekskul = validateInput($_POST['ekskul']);
    $role = validateInput($_POST['role']);

    // Cek username sudah ada atau belum (kecuali username sendiri)
    $check = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username' AND user_id != '$user_id'");
    if (mysqli_num_rows($check) > 0) {
        header("Location: ../views/manajemen_user.php?error=username_exists");
        exit;
    }

    $query = "UPDATE users SET 
              nama_lengkap = '$nama_lengkap',
              username = '$username',
              ekskul = '$ekskul',
              role = '$role'";
    
    // Update password jika diisi
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $query .= ", password = '$password'";
    }
    
    $query .= " WHERE user_id = '$user_id'";
    
    if (mysqli_query($conn, $query)) {
        header("Location: ../views/manajemen_user.php?success=update");
    } else {
        header("Location: ../views/manajemen_user.php?error=query");
    }
    exit;
}

// Hapus user
if (isset($_GET['hapus'])) {
    $user_id = $_GET['hapus'];
    
    // Cek apakah user memiliki event
    $check = mysqli_query($conn, "SELECT * FROM event_pengajuan WHERE user_id = '$user_id'");
    if (mysqli_num_rows($check) > 0) {
        header("Location: ../views/manajemen_user.php?error=user_has_events");
        exit;
    }

    $query = "DELETE FROM users WHERE user_id = '$user_id'";
    if (mysqli_query($conn, $query)) {
        header("Location: ../views/manajemen_user.php?success=hapus");
    } else {
        header("Location: ../views/manajemen_user.php?error=query");
    }
    exit;
}
?>
