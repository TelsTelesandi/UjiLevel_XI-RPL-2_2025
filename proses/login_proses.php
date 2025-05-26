<?php
session_start();
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = clean_input($_POST['username']);
    $password = clean_input($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
        $_SESSION['role'] = $user['role'];
        
        header("Location: ../" . ($user['role'] === 'admin' ? 'admin/dashboard.php' : 'user/dashboard.php'));
        exit();
    } else {
        $_SESSION['error'] = 'Username atau password salah!';
        header("Location: ../login.php");
        exit();
    }
}

header("Location: ../login.php");
exit();
?>