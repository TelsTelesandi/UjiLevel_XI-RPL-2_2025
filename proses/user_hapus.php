<?php
session_start();
include '../config/database.php';
include '../includes/auth_check.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $user_id = intval($_POST['id']);
    // Cegah hapus diri sendiri
    if ($user_id == $_SESSION['user_id']) {
        $_SESSION['error'] = 'Anda tidak dapat menghapus akun Anda sendiri!';
        header('Location: ../admin/users.php');
        exit();
    }
    try {
        $stmt = $conn->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$user_id]);
        $_SESSION['success'] = 'User berhasil dihapus.';
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Terjadi kesalahan: ' . $e->getMessage();
    }
    header('Location: ../admin/users.php');
    exit();
}
header('Location: ../admin/users.php');
exit(); 