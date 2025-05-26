<?php
// Mulai session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Mengecek apakah user sudah login
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Mengecek apakah user adalah admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Jika belum login, redirect ke login.php
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

// Jika bukan admin, redirect ke dashboard.php
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header("Location: dashboard.php");
        exit();
    }
}

// Ambil user_id dari session
function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Ambil role dari session
function getUserRole() {
    return $_SESSION['role'] ?? null;
}

// Ambil nama lengkap dari session
function getUserName() {
    return $_SESSION['nama_lengkap'] ?? null;
}
?>
