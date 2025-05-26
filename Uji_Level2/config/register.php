<?php
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nama_lengkap = $_POST['nama_lengkap'];
    $ekskul = $_POST['ekskul'];
    
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role, nama_lengkap, Ekskul) VALUES (?, ?, 'user', ?, ?)");
    $stmt->execute([$username, $password, $nama_lengkap, $ekskul]);
    
    header('Location: login.php');
    exit();
}
?>
<!-- Form registrasi HTML -->