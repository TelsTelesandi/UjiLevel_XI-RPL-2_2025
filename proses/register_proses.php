<?php
session_start();
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = clean_input($_POST['username']);
    $password = clean_input($_POST['password']);
    $confirm_password = clean_input($_POST['confirm_password']);
    $nama_lengkap = clean_input($_POST['nama_lengkap']);
    $email = clean_input($_POST['email']);

    // Validasi
    if ($password !== $confirm_password) {
        $_SESSION['error'] = 'Password dan konfirmasi password tidak sama!';
        header("Location: ../register.php");
        exit();
    }

    // Cek username sudah ada
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    
    if ($stmt->rowCount() > 0) {
        $_SESSION['error'] = 'Username atau email sudah terdaftar!';
        header("Location: ../register.php");
        exit();
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user baru
    try {
        $stmt = $conn->prepare("INSERT INTO users (username, password, nama_lengkap, email) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $hashed_password, $nama_lengkap, $email]);
        
        $_SESSION['success'] = 'Pendaftaran berhasil! Silahkan login.';
        header("Location: ../login.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Terjadi kesalahan saat mendaftar. Silahkan coba lagi.';
        header("Location: ../register.php");
        exit();
    }
}

header("Location: ../register.php");
exit();
?>