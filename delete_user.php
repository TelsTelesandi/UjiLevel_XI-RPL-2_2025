<?php
include 'config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Check if user_id is provided
if (!isset($_GET['user_id'])) {
    $_SESSION['error'] = "ID Pengguna tidak ditemukan.";
    header("Location: user_crud.php");
    exit;
}

$user_id = $_GET['user_id'];

// Prevent admin from deleting themselves
if ($user_id == $_SESSION['user_id']) {
    $_SESSION['error'] = "Anda tidak dapat menghapus akun Anda sendiri.";
    header("Location: user_crud.php");
    exit;
}

// Delete the user
$stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    $_SESSION['success'] = "Pengguna berhasil dihapus.";
} else {
    $_SESSION['error'] = "Gagal menghapus pengguna.";
}

header("Location: user_crud.php");
exit;
